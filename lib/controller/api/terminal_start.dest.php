<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

log_write('SYSTEM', 'Terminal start requested.');
if(!is_terminal_running()) {
	if(desklaunch_daemon_running()) {
		set_time_limit(8);
		desklaunch_launch_once('"' . (_C('proc_path')) . '"');
		sleep(2);
		log_write('SYSTEM', 'Terminal started.');
		show_json(200, 'Terminal launch scheduled.');
	} else {
		log_write('SYSTEM', 'Cannot start terminal because DesktopLaunchDaemon is not running.', 'E');
		show_json(500, 'Desktop Launch Daemon is not running.');
	}
} else {
	log_write('SYSTEM', 'Terminal is already running.');
	show_json(201, 'Terminal already started.');
}
