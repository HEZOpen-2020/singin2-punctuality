<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

define('DEFAULT_LANG', 'en_us');

// 读取并解析“语言文件”
function readLNG($file) {
	$text = file_get_contents($file);
	$text = str_replace(["\r\n","\r"],["\n","\n"],$text);
	$lines = explode("\n",$text);

	$arr = [];
	foreach($lines as $item) {
		$item = trim($item);
		if(strpos($item,'=') === false && strpos($item,'- NULL -') !== false) {
			return null;
		}
		if($item == '') continue;
		
		if($item[0] == '#') {
			continue;
		}
		if(strlen($item) >= 2 && substr($item,0,2) == '//') {
			continue;
		}
		
		$pos = strpos($item,'=');
		if($pos === false) {
			$pos = -1;
		}

		if($pos == -1) {
			continue;
		}

		$lt = trim(substr($item,0,$pos));
		$rt = trim(substr($item,$pos+1));

		@$rtd = json_decode($rt,true);

		if(gettype($rtd) == 'string') {
			$rt = $rtd;
		}

		$arr[$lt] = $rt;
	}

	return $arr;
}

global $langCache;
$langCache = [];
// 获取语言数组
function getLangArray($lang = DEFAULT_LANG) {
	global $langCache;

	if(isset($langCache[$lang])) {
		return $langCache[$lang];
	}

	$d1 = readLNG(I18N . DEFAULT_LANG . '.lang');
	if(file_exists(I18N_USER . DEFAULT_LANG . '.lang')) {
		$d2 = readLNG(I18N_USER . DEFAULT_LANG . '.lang');
		$d1 = array_merge($d1,$d2);
	}

	if($lang != DEFAULT_LANG) {
		$f1 = readLNG(I18N . $lang . '.lang');
		if(file_exists(I18N_USER . $lang . '.lang')) {
			$f2 = readLNG(I18N_USER . $lang . '.lang');
			if($f1 == NULL || $f2 == null) {
				$f1 = NULL;
			} else {
				$f1 = array_merge($f1,$f2);
			}
		}

		if($f1 != NULL) {
			foreach($f1 as $k => $v) {
				$d1[$k] = $v;
			}
		} else {
			foreach($d1 as $k => $v) {
				$d1[$k] = $k;
			}
		}
	}

	$langCache[$lang] = $d1;
	return $d1;
}

// 获取受支持的语言
function getSupportedLanguage() {
	return [
		'en_us' => 'English',
		'zh_cn' => '简体中文',
		'ky_cd' => 'lang.ky_cd.locname'
	];
}

// 获取浏览器语言
function getBrowserLanguage() {
// 	$ret = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    $ret = 'en_us';
	if(isset($_COOKIE[APP_PREFIX.'-user-language'])) {
		$ret = $_COOKIE[APP_PREFIX.'-user-language'];
	}
	$ret = strtolower(strip_after($ret,','));
	$ret = str_replace('-','_',$ret);
	if(!isset(getSupportedLanguage()[$ret])) {
		return DEFAULT_LANG;
	}
	return $ret;
}

global $userLanguage;
$userLanguage = '';
function i18nInit() {
	global $userLanguage;
	$userLanguage = getBrowserLanguage();
	define("_FLAG_I18N_INIT",true);
}
i18nInit();

function userLanguage() {
	global $userLanguage;
	return $userLanguage;
}

global $currentLanguageArr;
$currentLanguageArr = getLangArray($userLanguage);
/**
 * 获取语言值
 */
function LNG($key = '', ...$templates) {
	global $currentLanguageArr;

	if(strlen($key) > 0) {
		if(isset($currentLanguageArr[$key])) {
			$text = $currentLanguageArr[$key];
			foreach($templates as $k => $v) {
				$text = str_replace('${'.$k.'}',$v,$text);
			}
			return $text;
		} else {
			return $key;
		}
	}
	return $currentLanguageArr;
}

function printLNGScript() {
	header('Content-Type: text/javascript');

	echo 'window.LNG_array = ';

	echo data_json_encode(LNG());

	echo ';';

	?>
		function LNG(key = "") {
			if(!LNG_array[key]) return key;
			var str = LNG_array[key];
			for(let i=1;i<arguments.length;i++) {
				str = str.replace(new RegExp("\\$\\{" + (i-1) + "\\}",'g'),arguments[i]);
			}
			return str;
		}
		function switchLanguage(lang) {
			document.cookie = G.app_prefix+"-user-language=" + lang + "; expires=" + new Date(+new Date() + 86400*365) + "; path=/";

			history.go(0);
		}
	<?php
}

/**
 * 语言键引号转义
 */
function LNGk($key = '', ...$templates) {
	return addslashes(LNG($key, ...$templates));
}

/**
 * 语言键引号转义
 */
function LNGj($key = '', ...$templates) {
	return jsspecial(LNG($key, ...$templates));
}

/**
 * 语言键文本输出
 */
function LNGe($key = '', ...$templates) {
	echo htmlspecial(LNG($key, ...$templates));
}

/**
 * 获取语言定义型列表
 * （以默认语言文件为依据，用户可以覆盖）
 */
function getListOf($t) {
	$arr = getLangArray(DEFAULT_LANG);
	$ret = [];
	foreach($arr as $k => $v) {
		$p = $k;
		if(substr($k,0,2) == '_.') {
			$k = substr($k,2);
			if(substr($k,0,strlen($t) + 1) == $t . '.') {
				$k = substr($k, strlen($t) + 1);
				$ret[$k] = LNG($p);
			}
		}
	}

	return $ret;
}

require(I18N . '../definition.php');
