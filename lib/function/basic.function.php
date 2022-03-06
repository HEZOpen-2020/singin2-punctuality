<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

/**
 * 键映射
 */
function csv_map_key(&$data, $headers) {
    $ret = [];
    foreach($data as &$line) {
        $ret[] = [];
        foreach($line as $k => &$v) {
            $ret[count($ret) - 1][$headers[$k]] = $v;
        }
    }
    return $ret;
}

/**
 * 设备是否移动端
 */
function is_wap(){
	if($_GET['wap']=='force-phone') return 1;
	if($_GET['wap']=='force-pc') return 0;
	if(!isset($_SERVER['HTTP_USER_AGENT'])){
		return false;
	}
	if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i',
		strtolower($_SERVER['HTTP_USER_AGENT']))){
		return true;
	}
	if((isset($_SERVER['HTTP_ACCEPT'])) &&
		(strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== false)){
		return true;
	}
	return false;
}

/**
 * 结束进程
 */
function process_kill($namee) {
	return exec('taskkill /f /im "' . addslashes($namee) . '"');
}

/**
 * 进程是否正在运行
 */
function process_exists($name) {
	return !!shell_exec('tasklist /FO list|find /i "' . addslashes($name) . '"');
}

/**
 * 创建文件
 */
function create_file($path) {
	$dir = dirname($path);
	if(!file_exists($dir)) {
		mkdir($dir, 0777, true);
	}
	file_put_contents($path, '');
}

/**
 * 设置返回代码
 * （仅用于 API 返回！如果客户端明确表示其不是浏览器，那么代码将不会被设置）
 */
function set_response_code($code,$text) {
	header("HTTP/1.1 " . $code . ' ' . $text);
}

/**
 * 显示 JSON
 */
function show_json($code,$data) {
	header("Content-Type: application/json");
	$success = false;
	if(100 <= $code && $code < 400) $success = true;
	print json_encode([
		'success' => $success,
		'code' => $code,
		'path' => $_GET['_lnk'],
		'data' => $data,
	],JSON_PRETTY_PRINT * is_dev() + JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE);
	exit;
}

/**
 * 显示错误
 */
function show_error($code, $lines, $debug = null) {
	show_json($code, ['extra' => $lines, 'debug' => $debug]);
}
