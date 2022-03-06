<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

function log_path($datestr) {
	return DATA_PATH . '/log/' . substr($datestr, 0, 4) . '/' . substr($datestr, 4, 2) . '/' . substr($datestr, 6, 2) . '.log';
}

function log_line($thread, $line, $level) {
	$frag = '';
	$frag .= '[' . date('H:i:s', time()) . ']';
	$frag .= '[' . $level . ']';
	$frag .= '[' . $thread . ']';
	$frag .= ' ';
	$frag .= $line;
	return $frag;
}

function log_write($thread, $line, $level = 'L') {
	$datestr = ymdstr(time());
	$file = log_path($datestr);
	if(!file_exists($file)) {
		create_file($file);
	}
	file_put_contents($file, log_line($thread, $line, $level) . "\n", FILE_APPEND);
}

function log_get($datestr) {
	if(!is_valid_datestr($datestr)) return '';
	$file = log_path($datestr);
	if(!file_exists($file)) {
		return '';
	}
	return file_get_contents($file);
}

function log_exists($datestr) {
    if(!is_valid_datestr($datestr)) return false;
	$file = log_path($datestr);
	if(!file_exists($file)) {
		return false;
	}
	return true;
}
