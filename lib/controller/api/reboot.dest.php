<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

shell_exec('shutdown -r -t 0');
show_json(200, 'Reboot issued.');
