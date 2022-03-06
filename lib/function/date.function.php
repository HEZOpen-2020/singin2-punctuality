<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

function ymdstr($time) {
	return date('Ymd', $time);
}

function time_of_day($time) {
	return ($time - date_timestamp_get(date_create('1970/01/01 00:00:00'))) % 86400;
}

function time_of_date_str($str) {
	if(!is_valid_datestr($str)) {
		return false;
	}
	$str1 = substr($str, 0, 4) . '/' . substr($str, 4, 2) . '/' . substr($str, 6, 2);
	return date_timestamp_get(date_create($str1 . ' 00:00:00'));
}

function is_leap_year($year) {
	$year = intval($year);
	return ($year % 4 == 0 && $year % 100 != 0) || $year % 400 == 0;
}

function is_all_digits($str) {
	for($i = 0; $i < strlen($str); $i++) {
		$c = $str[$i];
		if(ord($c) > ord('9') || ord($c) < ord('0')) {
			return false;
		}
	}
	return true;
}

function month_days($year, $month) {
	$year = intval($year);
	$month = intval($month);

	if(in_array($month, [4, 6, 9, 11])) {
		return 30;
	} else if($month == 2) {
		if(is_leap_year($year)) {
			return 29;
		} else {
			return 28;
		}
	} else if(in_array($month, [1, 3, 5, 7, 8, 10, 12])) {
		return 31;
	}

	return 0;
}

function is_valid_datestr($datestr) {
	if(strlen($datestr) != 8) {
		return false;
	}
	if(!is_all_digits($datestr)) {
		return false;
	}

	$y = intval(substr($datestr, 0, 4));
	$m = intval(substr($datestr, 4, 2));
	$d = intval(substr($datestr, 6, 2));

	return $d >= 1 && $d <= month_days($y, $m);
}

function datestr_parse($datestr) {
	$y = intval(substr($datestr, 0, 4));
	$m = intval(substr($datestr, 4, 2));
	$d = intval(substr($datestr, 6, 2));
	return [$y, $m, $d];
}

function datestr_make($y, $m, $d) {
	return sprintf("%04d%02d%02d", $y, $m, $d);
}
