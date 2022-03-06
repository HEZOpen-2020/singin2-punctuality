<?php

function get_lock_path($path) {
	return $path . '.lock';
}

function lock_data($path) {
	if(file_exists($path)) {
		create_file(get_lock_path($path));
	}
}

function wait_data($path, $timeout = 20, $delay = 0.5) {
	$lock = get_lock_path($path);
	$start_wait = time();
	while($timeout == 0 || time() - $start_wait <= $timeout) {
		if(!file_exists($lock)) {
			return 0;
		}
		sleep($delay);
	}
	return -1;
}

function unlock_data($path) {
	$lock = get_lock_path($path);
	if(file_exists($lock)) {
		unlink($lock);
	}
}
