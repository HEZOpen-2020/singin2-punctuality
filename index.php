<?php

error_reporting(E_ALL & (~E_NOTICE));

define('IN_SYSTEM', "WMSDFCL\\Singin2\\Punctuality");
define('VERSION', '1.1.0');

define('DIR', rtrim(str_replace("\\",'/',__DIR__), '/') . '/');
define('DATA_PATH', DIR . 'data/');
define('LIB', DIR . 'lib/');
define('FUNC', LIB . 'function/');
define('CONTROLLER', LIB . 'controller/');
define('TEMPLATE', LIB . 'template/');
define('FONT', LIB . 'font/');
define('STAMP', LIB . 'stamp/');

require(LIB . 'config_default.php');
require(FUNC . 'index.php');
require(LIB . 'router/DataDrivenRouter.router.php');

// 检查操作系统
if(PHP_OS != 'WINNT') {
	show_json(500, 'The program only supports Windows NT');
}

if(!log_exists(ymdstr(time()))) {
    log_write('GREETING', ymdstr(time()) . '! F**k you, my neighbor!');
}

$router = new DataDrivenRouter();
$router->route();

