<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

$lesson = get_singing_lesson();
if($lesson == null) {
    show_json(404, null);
}

$students = get_singing_students($lesson['id']);
if($students == null) {
    show_json(500, null);
}
show_json(200, $students);
