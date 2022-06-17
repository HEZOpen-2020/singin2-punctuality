<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

$ret = hz2zrun_get_full();
if(!$ret) {
	show_json(404, 'Data not synchoronized.');
}
show_json(200, $ret);
