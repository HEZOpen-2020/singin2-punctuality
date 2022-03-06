<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

log_write('SYSTEM', 'Terminal restart requested.');
if(desklaunch_daemon_running()) {
	set_time_limit(10);
	process_kill(_C('proc_name'));
	sleep(1);
	desklaunch_launch_once('"' . (_C('proc_path')) . '"');
	sleep(2);
	log_write('SYSTEM', 'Terminal restarted.');
	show_json(200, 'Terminal restarted.');
} else {
	log_write('SYSTEM', 'Cannot restart terminal because DesktopLaunchDaemon is not running.', 'E');
	show_json(500, 'Desktop Launch Daemon is not running.');
}
