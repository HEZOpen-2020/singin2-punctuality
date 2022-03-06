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
