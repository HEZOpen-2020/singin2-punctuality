<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

function is_terminal_running() {
	return process_exists(_C('proc_name'));
}

function get_lessons() {
    $arr = db_query(_C('db_path'), _C('db_password'), 'select [ID],[课程名称],[教室名称],[教师名称],[考勤开始时间],[考勤结束时间] from [课程信息];');
    if(!$arr['success']) {
        return null;
    }
    $arr = csv_map_key($arr['data'], ['id', 'name', 'classroom', 'teacher', 'start', 'end']);
    foreach($arr as &$item) {
        $item['start'] = date_timestamp_get(date_create($item['start']));
        $item['end'] = date_timestamp_get(date_create($item['end']));
    }
    
    return $arr;
}

function get_singing_lesson() {
    $arr = get_lessons();
    if(!$arr) {
        return null;
    }
    $t = time();
    foreach($arr as &$item) {
        if($item['start'] <= $t && $t <= $item['end']) {
            return $item;
        }
    }
    return null;
}

function get_students() {
    $arr_ = db_query(_C('db_path'), _C('db_password'), 'select [ID],[OID],[面部识别结果],[打卡方式],[打卡时间],[是否排除考勤],[是否请假],[学生照片],[学生名称],[KeChengXinXi],[OptimisticLockField] from [上课考勤] order by [ID]');
    if(!$arr_ || !$arr_['success']) {
        return null;
    }
    $arr_ = csv_map_key($arr_['data'], ['id', 'oid', 'face_score', 'dk_method', 'dk_time', 'dk_exclude', 'dk_leave', 'photo', 'name', 'lesson','op_lock']);
    foreach($arr_ as &$item) {
        if($item['dk_time']) {
            $item['dk_time'] = date_timestamp_get(date_create($item['dk_time']));
        } else {
            $item['dk_time'] = null;
        }
        $item['dk_exclude'] = '1' == $item['dk_exclude'];
        $item['dk_leave'] = '1' == $item['dk_leave'];
        $item['dk_method'] = '1' == $item['dk_method'] ? 'face' : 'card';
        $item['face_score'] = floatval($item['face_score']);
    }
    return $arr_;
}

function get_singing_students($lesson) {
    $arr_ = get_students();
    if($arr_ == null) {
        return null;
    }
    $arr = [];
    foreach($arr_ as &$item) {
        if($item['lesson'] == $lesson) {
            $arr[] = $item;
        }
    }
    return $arr;
}

function hz2zrun_prepare_data() {
    $lock_path = DATA_PATH . 'hz2zrun/sync.lock';
    if(file_exists($lock_path)) {
        return [false, 'Sync already in progress.'];
    }
    create_file($lock_path);
    
    wait_data(DATA_PATH . 'hz2zrun/student');
    lock_data(DATA_PATH . 'hz2zrun/student');
    
    foreach(dir_list(DATA_PATH . 'hz2zrun/student/') as $file) {
        if(!is_dir(DATA_PATH . 'hz2zrun/student/' . $file)) {
            unlink(DATA_PATH . 'hz2zrun/student/' . $file);
        }
    }
    
    $api = _C('hz2zrun_host1') . '/Services/Identification/GetAllCard/Json';
    $indata = ['tCode' => _C('hz2zrun_name1'), 'datamd5' => ''];
    $data = post_submit($api, [], $indata, true);
    
    @$data = json_decode($data, true);
    if(!$data) {
        unlink($lock_path);
        unlock_data(DATA_PATH . 'hz2zrun/student');
        return [false, 'Data get failed.'];
    }
    if(!isset($data['result']['data'])) {
        unlink($lock_path);
        unlock_data(DATA_PATH . 'hz2zrun/student');
        return [false, 'Bad data format.'];
    }

    $datapath = DATA_PATH . 'hz2zrun/participant/selection.json';
    $has_participant = file_exists($datapath);
    $participant_info = null;
    if($has_participant) {
        wait_data($datapath);
        lock_data($datapath);
        $participant_info = data_read($datapath);
    }
    
    $new_participant = [
        'active_classes' => [],
        'passive_classes' => [],
        'fixed_students' => []
    ];
    $classes_data = [];
    $student_data = [];
    $data = $data['result']['data'];
    foreach($data as &$item) {
        if(!isset($classes_data[$item['GradeID']])) {
            $classes_data[$item['GradeID']] = [
                'name' => $item['GradeName'],
                'departs' => []
            ];
        }
        $departs = &$classes_data[$item['GradeID']]['departs'];
        if(!isset($departs[$item['DepartID']])) {
            $departs[$item['DepartID']] = [
                'name' => $item['DepartName']
            ];
            if($has_participant) {
                if($participant_info['active_classes'][$item['DepartID']]) {
                    $new_participant['active_classes'][$item['DepartID']] = true;
                } else if($participant_info['passive_classes'][$item['DepartID']]) {
                    $new_participant['passive_classes'][$item['DepartID']] = true;
                }
            }
            $student_data[$item['DepartID']] = [];
        }
        
        $item['IsTeacher'] = ($item['UserType'] == '教师');
        unset($item['UserType']);
        $item['Period'] = [
            '走读生' => 'day',
            '住校生' => 'week',
            '常住生' => 'month'
        ][$item['ZDType']];
        unset($item['ZDType']);
        $item['SexChar'] = [
            '男' => 'B',
            '女' => 'G'
        ][$item['Sex']];
        unset($item['Sex']);
        if(null != $item['Photo']) {
            $item['Photo'] = str_replace("\\", '/', $item['Photo']);
        }
        if(strlen($item['UserIdNum']) == 18) {
            $item['BirthDay'] = substr($item['UserIdNum'], 6, 8);
        }
        unset($item['UserIdNum']);
        
        $student_data[$item['DepartID']][$item['UserID']] = $item;

        if($participant_info['fixed_students'][$item['UserID']]) {
            $new_participant['fixed_students'][$item['UserID']] = true;
        }
    }
    
    $path_classes = DATA_PATH . 'hz2zrun/student/_classes.json';
    create_file($path_classes);
    data_write($path_classes, $classes_data);
    
    foreach($student_data as $id => $items) {
        $path_students = DATA_PATH . 'hz2zrun/student/' . $id . '.json';
        create_file($path_students);
        data_write($path_students, $items);
    }

    data_write($datapath, $new_participant);
    
    unlink($lock_path);
    unlock_data(DATA_PATH . 'hz2zrun/student');
    unlock_data($datapath);
    return [true, 'Data synchronized.'];
}

function hz2zrun_get_classes() {
    wait_data('hz2zrun/student');
    
    $path = DATA_PATH . 'hz2zrun/student/_classes.json';
    
    if(!file_exists($path)) {
        return null;
    }
    
    return data_read($path);
}

function hz2zrun_get_full() {
    $datapath = DATA_PATH . 'hz2zrun/participant/selection.json';
    if(!file_exists($datapath)) {
        create_file($datapath);
        $nullarr = ['a' => 0];
        unset($nullarr['a']);
        file_put_contents($datapath, data_json_encode([
            'active_classes' => $nullarr,
            'passive_classes' => $nullarr,
            'fixed_students' => $nullarr
        ]));
    }
    
    $classpath = DATA_PATH . 'hz2zrun/student/_classes.json';
    if(!file_exists($classpath)) {
        return null;
    }

    wait_data($datapath);
    $participant_info = data_read($datapath);
    $classes_data = data_read($classpath);

    $ret = [];
    foreach($classes_data as $grade_id => $grade) {
        if($grade['name'] == '') continue;
        foreach($grade['departs'] as $depart_id => $depart) {
            $departpath = DATA_PATH . 'hz2zrun/student/' . $depart_id . '.json';
            $students = data_read($departpath);
            $count = 0;
            $students_meta = [];
            foreach($students as $stu_id => $stu) {
                $stu['State'] = 'excluded';
                if($participant_info['fixed_students'][$stu_id]) {
                    $count += 1;
                    $stu['State'] = 'fixed';
                }
                unset($stu['IsTeacher']);
                unset($stu['Master']);
                $students_meta[] = $stu;
            }
            $depart_meta = [
                'Name' => $depart['name'],
                'GradeID' => $grade_id,
                'DepartID' => $depart_id,
                'State' => (
                    $participant_info['active_classes'][$depart_id] ? 'active'
                    : ($participant_info['passive_classes'][$depart_id] ? 'passive'
                    : 'excluded')
                ),
                'TotalCount' => count($students_meta),
                'FixedCount' => $count,
                'Students' => $students_meta
            ];

            $ret[] = $depart_meta;
        }
    }

    return $ret;
}

function hz2zrun_save_selection($data) {
    $data1 = [
        'active_classes' => $data['active_classes'],
        'passive_classes' => $data['passive_classes'],
        'fixed_students' => $data['fixed_students']
    ];
    $datapath = DATA_PATH . 'hz2zrun/participant/selection.json';
    wait_data($datapath);
    lock_data($datapath);
    data_write($datapath, $data1);
    unlock_data($datapath);
}
