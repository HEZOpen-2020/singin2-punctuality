<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

set_response_code(404, 'Not Found');

show_json(404, 'Page not found!');
