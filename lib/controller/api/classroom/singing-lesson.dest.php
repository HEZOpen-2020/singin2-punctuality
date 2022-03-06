<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

$data = get_singing_lesson();
if(!$data) {
    show_json(200, null);
} else {
    show_json(200, $data);
}
