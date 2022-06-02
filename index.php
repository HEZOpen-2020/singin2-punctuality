<?php

error_reporting(E_ALL & (~E_NOTICE));

define('IN_SYSTEM', "WMSDFCL\\Singin2\\Punctuality");
define('VERSION', '1.1.6-hf3');

define('DIR', rtrim(str_replace("\\",'/',__DIR__), '/') . '/');
define('DATA_PATH', DIR . 'data/');
define('LIB', DIR . 'lib/');
define('FUNC', LIB . 'function/');
define('CONTROLLER', LIB . 'controller/');
define('TEMPLATE', LIB . 'template/');
define('FONT', LIB . 'font/');
define('STAMP', LIB . 'stamp/');
define('I18N', LIB . 'i18n/lang/');
define('I18N_USER', DATA_PATH . 'i18n/');

// 检查数据目录和配置文件
if(!file_exists(DATA_PATH)) {
	die('Data path does not exist!');
}
if(!file_exists(DIR . 'internal_config/config.php')) {
	die('Config file does not exist!');
}

require(LIB . 'config_default.php');
require(FUNC . 'index.php');
require(LIB . 'i18n/i18nCore.php');
require(LIB . 'router/DataDrivenRouter.router.php');

create_dir(DATA_PATH . 'i18n');
create_dir(DATA_PATH . 'log');

// 检查操作系统
if(PHP_OS != 'WINNT') {
	show_json(500, 'The program only supports Windows NT');
}

if(!log_exists(ymdstr(time()))) {
    log_write('GREETING', ymdstr(time()) . '! F**k you, my neighbor!');
}

$router = new DataDrivenRouter();
$router->route();

