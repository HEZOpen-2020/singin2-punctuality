<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

$lesson = get_singing_lesson();
// $lesson = null;
$lesson_id = null;
$lesson_display = LNG('ui.none');
if($lesson) {
    $lesson_id = $lesson['id'];
    $lesson_display = substr($lesson['id'], 0, 8) . ' ' . $lesson['name'] . ' ' . $lesson['classroom'];
}

show_json(200, [
	'server_os' => php_uname('s') . ' ' . php_uname('v'),
	'php_version' => PHP_VERSION,
	'server_port' => $_SERVER['SERVER_PORT'],
	'document_root' => $_SERVER['DOCUMENT_ROOT'],
	'db_path' => _C('db_path'),
	'proc_name' => _C('proc_name'),
	'proc_path' => _C('proc_path'),
	'system_time' => time(),
	'terminal_running' => is_terminal_running(),
	'desklaunch_daemon' => desklaunch_daemon_running(),
	'namespace' => IN_SYSTEM,
	'version' => VERSION,
	'singing_lesson' => $lesson_display,
	'singing_lesson_id' => $lesson_id,
	'classroom_allow_revoke' => _C('classroom_allow_revoke')
]);
