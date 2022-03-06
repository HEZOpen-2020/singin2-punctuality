<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

$str = $_REQUEST['date'];

if(is_valid_datestr($str)) {
	show_json(200, explode("\n", log_get($str)));
} else {
	show_json(400, 'Invalid date!');
}
