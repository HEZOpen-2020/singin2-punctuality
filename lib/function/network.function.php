<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

function httpopt($opt) {
	$res = [];
	foreach($opt as $k => $v) {
		$res[] = $k . ': ' . $v;
	}
	return $res;
}

function fetch_webpage($url, $opt = []) {
	$ch = curl_init();

	$ua = 'com.xh.zhitongyunstu/v3.59.11.20211026.2S (SM-P355C; android; 9; ABAABAABAABA)';
	if(isset($opt['User-Agent'])) {
		$ua = $opt['User-Agent'];
	}

	curl_setopt($ch,CURLOPT_FAILONERROR, true);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch,CURLOPT_TIMEOUT, 30);
	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch,CURLOPT_USERAGENT, $ua);
	curl_setopt($ch,CURLOPT_HTTPHEADER, httpopt($opt)); 
	
	$output = curl_exec($ch);
	
	curl_close($ch);
	
	return $output;
}

function random_sn() {
	$str = '0123456789QWERTYUIOPASDFGHJKLZXCVBNM';
	$ret = '';
	for($i = 0; $i < 11; $i++) {
		$ret .= $str[mt_rand(0, strlen($str) - 1)];
	}
	return $ret;
}

function post_url_encode($data) {
	$rdata = $data;
	$data = '';
	$first = true;
	foreach($rdata as $k => $v) {
		if($first) $first = false;
		else $data .= '&';
		$data .= $k . '=' . urlencode($v);
	}
	return $data;
}

function post_submit($url, $opt = [], $data = '', $encode_url = false) {
	$ch = curl_init();

	$ua = 'com.xh.zhitongyunstu/v3.59.11.20211026.2S (SM-P355C; android; 9; ABAABAABAABA)';
	if(isset($opt['User-Agent'])) {
		$ua = $opt['User-Agent'];
	}

	if(is_array($data) && $encode_url) {
		$data = post_url_encode($data);
	}

	curl_setopt($ch,CURLOPT_POST,true);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch,CURLOPT_TIMEOUT, 30);
	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch,CURLOPT_USERAGENT, $ua);
	curl_setopt($ch,CURLOPT_HTTPHEADER, httpopt($opt)); 
	
	$output = curl_exec($ch);
	
	curl_close($ch);
	
	return $output;
}
