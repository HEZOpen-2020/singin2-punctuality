<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

$procname = 'DesktopLaunchDaemon.exe';
$textpath = 'C:/SystemComponent/DesktopLaunch/stktsk/cmd.txt';
$backpath = 'C:/SystemComponent/DesktopLaunch/stktsk/cmd_backup.txt';

function desklaunch_daemon_running() {
	global $procname;
	return process_exists($procname);
}

function desklaunch_launch($value) {
	global $textpath, $backpath;
	file_put_contents($textpath, iconv("UTF-8", _C('system_locale') . "//IGNORE", $value) . "\n", FILE_APPEND);
}

function desklaunch_launch_once($value) {
	global $textpath, $backpath;
	$cont = file_get_contents($textpath);
	foreach(explode("\n", $cont) as $line) {
		if(trim($line) == $value) {
			return;
		}
	}
	file_put_contents($textpath, iconv("UTF-8", _C('system_locale') . "//IGNORE", $value) . "\n", FILE_APPEND);
}
