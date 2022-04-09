<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

if(!isset($_REQUEST['depart'])) {
	show_json(400, 'Parameter depart missing!');
}
$dep = $_REQUEST['depart'];

if(strpos($dep, '_') !== false || strpos($dep, '.') !== false) {
	show_json(400, 'Illegal department.');
}

$data_path = DATA_PATH . 'hz2zrun/student/';
$data_file = $data_path . $dep . '.json';

if(!file_exists($data_file)) {
	show_json(404, 'No such department ' . $dep . '.');
}

show_json(200, data_read($data_file));
