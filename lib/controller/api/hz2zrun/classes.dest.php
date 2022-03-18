<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

$data = hz2zrun_get_classes();
if(!$data) {
    show_json(404, 'Data not synchoronized.');
}
show_json(200, $data);
