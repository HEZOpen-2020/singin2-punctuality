<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

show_json(200, [
	'system_namespace' => IN_SYSTEM,
	'app_name' => APP_NAME,
	'version' => VERSION
]);
