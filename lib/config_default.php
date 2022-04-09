<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

require(DIR . 'internal_config/config.php');

date_default_timezone_set('Asia/Shanghai');

function _C($key) {
	$cu = _CU();
	if(isset($cu[$key])) {
		return $cu[$key];
	} else {
		return [
			// 数据库文件
			'db_path' => "C:/Users/Administrator/AppData/SmartBoard/localData.db",
			'db_password' => '19260817',
			
			// 进程名
			'proc_name' => 'SmartBoard.exe',
			'proc_path' => 'C:/Program Files (x86)/SmartBoard/SmartBoard.exe',
			
			// 系统编码
			'system_locale' => 'GBK',

			// 允许打卡撤销
			'classroom_allow_revoke' => false,
			
			// 晨跑打卡机信息
			'hz2zrun_host' => 'http://192.168.1.180:8083', // 必须有协议标记，末尾不要有斜杠。
			'hz2zrun_name' => 'runhand20190816170251',
			'hz2zrun_type' => 0, // 0=进, 1=出, 2=未识别
		][$key];
	}
}
