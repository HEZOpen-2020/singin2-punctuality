<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

$data = get_lessons();
if(!$data) {
    show_json(500, 'Failed to get data.');
} else {
    show_json(200, $data);
}
