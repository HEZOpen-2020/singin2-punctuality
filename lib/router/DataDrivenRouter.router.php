<?php
use function WMSDFCL\Singin2\Punctuality\router_variable\check_url_variable;
?><?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

require(LIB . 'router/variable/router_variable.php');

/**
 * 检查CSRF攻击（不返回，错误即终止）
 */
function checkCSRF($isCheck) {
	if($isCheck && !_C('csrf_bypass')) {
		if(!isset($_COOKIE['X-'.APP_PREFIX.'-csrf'][$_REQUEST['csrf-token-name']]) || $_COOKIE['X-'.APP_PREFIX.'-csrf'][$_REQUEST['csrf-token-name']] !== $_REQUEST['csrf-token-value']) {
			show_json(429, 'Missing CSRF token.');
			exit;
		}
	}
	if(!isset($_COOKIE['X-'.APP_PREFIX.'-csrf']) || !is_array($_COOKIE['X-'.APP_PREFIX.'-csrf']) || count($_COOKIE['X-'.APP_PREFIX.'-csrf'])==0) {
		$GLOBALS['sess'] = md5(rand()); // 创建新会话
		$GLOBALS['token'] = md5(rand());
		setcookie('X-'.APP_PREFIX.'-csrf['.$GLOBALS['sess'].']',$GLOBALS['token'],time()+86400,'/');
	} else {
		if(is_array($_COOKIE['X-'.APP_PREFIX.'-csrf'])) {
			foreach($_COOKIE['X-'.APP_PREFIX.'-csrf'] as $k=>$v) {
				$GLOBALS['sess']=$k;
				$GLOBALS['token']=$v;
				break;
			}
		}
	}
}

/**
 * 页面判断与分发
 * 采用数据驱动方式，详见 lib/router/map/root.json 和 lib/router/variable/router_variable.php
 * 添加新页面时，此程序无需修改。
 */
class DataDrivenRouter {
	function __construct() {}

	public function openController($id) {
		// echo $id;
		// echo "\n";
		// print_r($_GET);
		$dest = CONTROLLER . $id . '.dest.php';
		if(file_exists($dest)) {
			require($dest);
		} else {
			header('Content-Type: application/json');
			$param_txt = [];
			foreach($_GET as $key => $val) {
				if(!is_dev() || in_array($key,['_lnk'])) continue;
				$param_txt[] = $key . ': ' . json_encode($val,JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES);
			}
			set_response_code(500, 'Internal Error');
			show_json(500, 'Target controller not exist!' . "\n" . json_encode($param_txt));
		}
	}

	public function route() {
		if(!isset($_GET['_lnk'])) {
			$_GET['_lnk'] = '';
		}
		// 修复奇奇怪怪的问题
		$_GET['_lnk'] = str_replace(' ','+',$_GET['_lnk']);
		if($_GET['_lnk'] != '' && $_GET['_lnk'][strlen($_GET['_lnk']) - 1] == '/') {
			$_REQUEST['_dest'] = $_GET['_dest'] = 'error/404';
			$this->openController('error/404');
			return;
		}

		$currdir = __DIR__ . '/map/';
		$curr = json5_decode_file($currdir . 'root.json5', true);
		$curr_url = trim(strip_after($_GET['_lnk'],'?'));
		$require_csrf = false;

		while(true) {
			while(isset($curr['target'])) {
				if(isset($curr['param'])) {
					// Param 固定参数值设定（可能需要）
					foreach($curr['param'] as $key => $item) {
						$_REQUEST[$key] = $_GET[$key] = $item;
					}
				}
				if(isset($curr['csrf']) && $curr['csrf']) {
					$require_csrf = true;
				}
				// 文件调用
				$filename = $currdir . $curr['target'];
				$curr = json5_decode_file($filename, true);
				$currdir = dirname($filename) . '/';
			}
			if(isset($curr['param'])) {
				// Param 固定参数值设定
				foreach($curr['param'] as $key => $item) {
					$_REQUEST[$key] = $_GET[$key] = $item;
				}
			}
			if(isset($curr['csrf']) && $curr['csrf']) {
				$require_csrf = true;
			}
			if(strlen($curr_url) == 0) {
				// 完成。
				if(isset($curr['dest'])) {
					$_REQUEST['_dest'] = $_GET['_dest'] = $curr['dest'];
					checkCSRF($require_csrf);
					$this->openController($curr['dest']);
					return;
				}
			}
			$success = false;
			if($curr['children'] ?? null) foreach($curr['children'] as $cond => $next) {
				$firstword = strip_after($curr_url, '/');
				if($cond == '#path') {
					// 末尾路径参数，一步到位
					$curr = $next;
					if(isset($curr['variable'])) {
						$key = $curr['variable'];
						$_REQUEST[$key] = $_GET[$key] = $curr_url;
					}
					$curr_url = '';
					$success = true;
					break;
				} else if($cond[0] == '#') {
					$var_val = check_url_variable(substr($cond,1),$firstword);
					if($var_val !== false) {
						// 可变参数判定成功
						$curr = $next;
						if(isset($curr['variable'])) {
							$key = $curr['variable'];
							$_REQUEST[$key] = $_GET[$key] = $var_val;
						}
						$curr_url = strip_first($curr_url, '/');
						$success = true;
						break;
					}
				} else {
					// 固定参数
					if($cond == $firstword) {
						$curr = $next;
						if(isset($curr['variable'])) {
							$key = $curr['variable'];
							$_REQUEST[$key] = $_GET[$key] = $firstword;
						}
						$curr_url = strip_first($curr_url, '/');
						$success = true;
						break;
					}
				}
			}

			// 未成功。
			if(!$success) {
				$_REQUEST['_dest'] = $_GET['_dest'] = 'error/404';
				$this->openController('error/404');
				return;
			}
		}
	}
}
