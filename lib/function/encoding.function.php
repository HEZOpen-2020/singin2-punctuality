<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

/**
 * 寻找数组元素
 */
function index_in_array($needle,$arr) {
	foreach($arr as $key => $val) {
		if($val == $needle) {
			return $key;
		}
	}
	return false;
}

/**
 * 是否有tag
 */
function has_tag($object,$tag) {
	if(!isset($object['tag'])) {
		return false;
	}
	return in_array($tag,$object['tag']);
}

/**
 * 交换数值
 */
function swap(&$x, &$y) {
	$t = $x;
	$x = $y;
	$y = $t;
}

/**
 * 截取第一个 $f 后的所有东西。如果不存在，则返回空串
 * 主要用来解析网址
 */
function strip_first($u,$f="/") {
	$x=$u;
	if(strstr($u,$f)){
		return substr($u,strpos($u,$f) + 1);
	}
	return '';
}

/**
 * 截除第一个 $f 以及此后的所有东西。如果不存在，则返回原字符串
 * 主要用来解析网址
 */
function strip_after($u,$f="/") {
	$x=$u;
	if(strstr($u,$f)){
		$x=substr($u,0,strpos($u,$f));
	}
	return $x;
}

/**
 * 编码 js 数据（JSON5）
 */
function data_js_encode($data) {
	return json5_encode($data,JSON_PRETTY_PRINT * is_dev() + JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE);
}

/**
 * 编码 js 数据（JSON）
 */
function data_json_encode($data) {
	return json_encode($data,JSON_PRETTY_PRINT * is_dev() + JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE);
}

/**
 * 读取 JSON 数据
 */
function data_read($path) {
	return json_decode(file_get_contents($path), true);
}

/**
 * 写入 JSON 数据
 */
function data_write($path, $data) {
	file_put_contents($path, data_json_encode($data));
}
