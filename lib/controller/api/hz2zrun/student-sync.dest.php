<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

$result = hz2zrun_prepare_data();
show_json($result[0] ? 200 : 502, $result[1]);
