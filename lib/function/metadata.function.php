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
    
    $api = _C('hz2zrun_host') . '/Services/Identification/GetAllCard/Json';
    $indata = ['tCode' => _C('hz2zrun_name'), 'datamd5' => ''];
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
    }
    
    $path_classes = DATA_PATH . 'hz2zrun/student/_classes.json';
    create_file($path_classes);
    data_write($path_classes, $classes_data);
    
    foreach($student_data as $id => $items) {
        $path_students = DATA_PATH . 'hz2zrun/student/' . $id . '.json';
        create_file($path_students);
        data_write($path_students, $items);
    }
    
    unlink($lock_path);
    unlock_data(DATA_PATH . 'hz2zrun/student');
    return ['true', 'Data synchronized.'];
}

function hz2zrun_get_classes() {
    wait_data('hz2zrun/student');
    
    $path = DATA_PATH . 'hz2zrun/student/_classes.json';
    
    if(!file_exists($path)) {
        return null;
    }
    
    return data_read($path);
}
