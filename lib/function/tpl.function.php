<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

// 包含并打印网页模板（目前不包含任何特殊编译功能）
function tpl($n){
	$tplfile = TEMPLATE.$n.".tpl.php";
	if(file_exists($tplfile)) require($tplfile);
}

// 加载 js
function load_js($fn,$ver=VERSION) {
	echo '<script src="' . BASIC_URL . 'static/' . $fn . '.js?v=' . $ver . '"></script>' . "\n";
}
function load_js_e($fn) {
	echo '<script src="' . $fn . '"></script>' . "\n";
}

// 加载 css
function load_css_e($fn,$class='') {
	if(strlen($class) == 0) echo '<link rel="stylesheet" href="' . $fn . '">' . "\n";
	else echo '<link rel="stylesheet" href="' . $fn . '" id="' . $class . '">' . "\n";
}
function load_css($fn,$flag='',$ver=VERSION,$colorizer_class="") {
	$has_platform_specify = (strpos($flag,'w') !== false);

	load_css_e(BASIC_URL . 'static/' . $fn . '.css?v=' . $ver);
	if($has_platform_specify) {
		if(is_wap()) {
			load_css_e(BASIC_URL . 'static/' . $fn . '-mobile.css?v=' . $ver);
		} else {
			load_css_e(BASIC_URL . 'static/' . $fn . '-desktop.css?v=' . $ver);
		}
	}
}

/**
 * HTML 特殊字符转换
 */
function htmlspecial($str){
	return str_replace(
		array('&','<','>','"',"'"),
		array('&amp;','&lt;','&gt;','&quot;','&#039;'),
		$str
	);
}

/**
 * 显示页面标题
 */
function show_title($str) {
	echo '<script>';
	echo 'document.title="' . addslashes($str . ' - ' . APP_NAME) . '";';
	echo '</script>';
	echo "\n";
}

/**
 * 显示页面图标
 */
function show_icon($href) {
	echo '<script>';
	echo '$("#page-icon").attr("href", "' . addslashes($href) . '");';
	echo '</script>';
	echo "\n";
}

/**
 * 设置 MDUI 主题色
 */
function show_theme_color($primary, $accent) {
	echo '<script>';
	echo '$("body").addClass("mdui-theme-primary-' . addslashes($primary) . '");';
	echo '$("body").addClass("mdui-theme-accent-' . addslashes($accent) . '");';
	echo '</script>';
	echo "\n";
}
