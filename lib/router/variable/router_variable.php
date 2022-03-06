<?php
namespace WMSDFCL\Singin2\Punctuality\router_variable;
?><?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

function NS() {
	return "WMSDFCL\\Singin2\\Punctuality\\router_variable\\";
}

function check_url_variable($type, $cont) {
	if(function_exists(NS() . 'check_url_variable_' . $type)) {
		return (NS() . 'check_url_variable_' . $type)($cont);
	}
	return false;
}
