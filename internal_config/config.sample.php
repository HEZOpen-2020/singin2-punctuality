<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

define("BASIC_URL", str_replace('__hostname__',$_SERVER['HTTP_HOST'],"http://__hostname__/dk/"));
define("APP_NAME", 'Singin! punctuality');
define("APP_PREFIX", 'singin2-punctuality');

function is_dev() {
	return true;
}

function _CU() {
	return [
		// 数据库文件
		'db_path' => "C:/Users/Administrator/AppData/SmartBoard/localData.db",
		'db_password' => '19260817',
		
		// 进程名
		'proc_name' => 'SmartBoard.exe',
		'proc_path' => 'C:/Program Files (x86)/SmartBoard/SmartBoard.exe',
		
		// 系统编码
		'system_locale' => 'GBK',

		// 教室打卡界面选项
		'classroom_allow_revoke' => false, // 允许教室打卡撤销
		'classroom_filter_force_consistency' => true, // 状态变更时保持筛选列表不变，防止误操作
		
		// 晨跑打卡机信息
		'hz2zrun_host' => 'http://192.168.1.180:8083', // 必须有协议标记，末尾不要有斜杠。
		'hz2zrun_name' => 'runhand20190816170251',
		'hz2zrun_type' => 0, // 0=进, 1=出, 2=未识别

		// 传统艺能设置
		'tradition_allow_teacher' => false, // 允许查看教职工部门
		'tradition_allow_other' => true, // 允许选择其他部门
		'tradition_here' => '00000000-0000-0000-0000-000000000000', // 默认班级ID，你应该知道的
		'tradition_nexts' => 5, // 显示“接下来”的数量（并列的人员一定一起显示，因此实际可能会大于此数）
		'tradition_allow_show_all' => false, // 允许查看全部
	];
}
