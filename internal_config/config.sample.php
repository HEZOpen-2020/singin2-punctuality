<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

define("BASIC_URL", str_replace('__hostname__',$_SERVER['HTTP_HOST'],"http://__hostname__/dk/"));
define("APP_NAME", 'Singin! punctuality');
define("APP_PREFIX", 'singin2-punctuality');

function is_dev() {
	return true;
}

function _CU() {
	return [
		'db_path' => DIR . 'localData.db',
		'db_password' => '123',
		'proc_name' => 'GS.Terminal.SmartBoard.exe',
		'proc_path' => DIR . 'GS.Terminal.SmartBoard.exe',
		'classroom_allow_revoke' => false
	];
}
