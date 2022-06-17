<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

log_write('SYSTEM', 'Reboot command issued.');
shell_exec('shutdown -r -t 0');
show_json(200, 'Reboot issued.');
