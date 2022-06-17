<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

ignore_user_abort(true);

$lesson = get_singing_lesson();
$curr_time = time();
if(!$lesson || $lesson['id'] != $_REQUEST['lesson']) {
	show_json(431, 'Lesson ID is incorrect.');
}

// Get OptimisticLockField value
$students = get_students();
$op_lock = 2;
foreach($students as &$student_item) {
	if($student_item['dk_time'] != null) {
		$op_lock = max($op_lock, $student_item['op_lock'] + 1);
	}
}

// Check student exists
$student_exists = false;
$curr_state = false;
foreach($students as &$student_item) {
	if($student_item['lesson'] == $_REQUEST['lesson'] && $student_item['oid'] == $_REQUEST['oid']) {
		$student_exists = true;
		$curr_state = !!$student_item['dk_time'];
		break;
	}
}
if(!$student_exists) {
	show_json(432, 'Student with the oid does not exist.');
}

// Write item
// [打卡时间],[打卡方式],[面部识别结果]
$hash = $_REQUEST['oid'];
if(strlen($_REQUEST['oid']) >= 8) {
	$hash = substr($_REQUEST['oid'], 0, 8);
}
if($_REQUEST['mark'] == 'card') {
	if($curr_state) {
		show_json(201, 'Already signed in.');
	}
	$dk_time = date('Y-m-d H:i:s', $curr_time);
	$data = db_query(_C('db_path'), _C('db_password'),
		"update [上课考勤] set "
		. "[打卡时间]='" . $dk_time . "',"
		. "[打卡方式]=0,"
		. "[面部识别结果]=0,"
		. "[OptimisticLockField]=" . $op_lock
		. " where [KeChengXinXi]='" . $_REQUEST['lesson'] . "' and [OID]='" . $_REQUEST['oid'] . "';"
	);
	if($data['success']) {
		log_write('CLASSROOM', 'Written ' . $hash . ' as "card".');
		show_json(200, 'Record written.');
	} else {
		log_write('CLASSROOM', 'Failed writing ' . $hash . ' as "card".');
		show_json(500, $data['data']);
	}
} else if($_REQUEST['mark'] == 'face') {
	if($curr_state) {
		show_json(201, 'Already signed in.');
	}
	$dk_time = date('Y-m-d H:i:s', $curr_time);
	$face_score = (mt_rand(1,19260817) / 19260817) * 0.25 + 0.75;
	$data = db_query(_C('db_path'), _C('db_password'),
		"update [上课考勤] set "
		. "[打卡时间]='" . $dk_time . "',"
		. "[打卡方式]=1,"
		. "[面部识别结果]=" . $face_score . ","
		. "[OptimisticLockField]=" . $op_lock
		. " where [KeChengXinXi]='" . $_REQUEST['lesson'] . "' and [OID]='" . $_REQUEST['oid'] . "';"
	);
	if($data['success']) {
		log_write('CLASSROOM', 'Written ' . $hash . ' as "face".');
		show_json(200, 'Record written.');
	} else {
		log_write('CLASSROOM', 'Failed writing ' . $hash . ' as "face".');
		show_json(500, $data['data']);
	}
	
} else if($_REQUEST['mark'] == 'revoke') {
	if(!$curr_state) {
		show_json(434, 'Not signed in, but tried to revoke.');
	}
	if(!_C('classroom_allow_revoke')) {
		show_json(403, 'Revoking is not allowed.');
	}
	$data = db_query(_C('db_path'), _C('db_password'),
		"update [上课考勤] set "
		. "[打卡时间]=null,"
		. "[打卡方式]=0,"
		. "[面部识别结果]=0,"
		. "[OptimisticLockField]=" . $op_lock
		. " where [KeChengXinXi]='" . $_REQUEST['lesson'] . "' and [OID]='" . $_REQUEST['oid'] . "';"
	);
	if($data['success']) {
		log_write('CLASSROOM', 'Revoked ' . $hash . '.');
		show_json(200, 'Record written.');
	} else {
		log_write('CLASSROOM', 'Failed revoking ' . $hash . '.');
		show_json(500, $data['data']);
	}
} else {
	show_json(433, 'Unknown operation type.');
}
