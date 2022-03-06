<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php


// $arr = db_query(_C('db_path'), _C('db_password'), 'select [ID],[课程名称],[教室名称],[教师名称],[考勤开始时间],[考勤结束时间] from [课程信息];');
// $arr = csv_map_key($arr['data'], ['id', 'name', 'classroom', 'teacher', 'start', 'end']);
// show_json(200, $arr);

// $arr = db_query(_C('db_path'), _C('db_password'), 'select [ID],[OID],[面部识别结果],[打卡方式],[是否排除考勤],[学生照片],[学生名称],[KeChengXinXi] from [上课考勤] order by [ID]');
// show_json(200, $arr);

// $arr = get_singing_students('e69e2636-3075-4145-8d7a-13f57cd3a3e9');
// show_json(200, $arr);

// $arr = db_query(_C('db_path'), _C('db_password'), "update [上课考勤] set [是否请假]=true where [OID]='a37314b2-ea49-4456-9d87-7d7cc401e619' and [KeChengXinXi]='aa555d81-0616-45bf-afc0-59ecd69896d6';");
// show_json(200, $arr);

// $test = '"' . _C('proc_path') . '"';
// // $test = 'calc';
// log_write('DEBUG', 'Test start ' . $test);
// desklaunch_launch($test);
