<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

log_write('SYSTEM', 'Terminal stop requested.');
if(desklaunch_daemon_running()) {
	if(is_terminal_running()) {
		process_kill(_C('proc_name'));
		log_write('SYSTEM', 'Terminal stopped.');
		show_json(200, 'Terminal stopped.');
	} else {
		log_write('SYSTEM', 'Terminal already stopped.');
		show_json(201, 'Terminal already stopped.');
	}
} else {
	log_write('SYSTEM', 'DesktopLaunchDaemon is not running and terminal stop is ignored.', 'E');
	show_json(500, 'Desktop Launch Daemon is not running.');
}
