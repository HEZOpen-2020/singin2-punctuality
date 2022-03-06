<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

$query_exe = DIR . 'ext/dbmanip/dbquery.exe';

/**
 * 在加密的数据库内执行单个命令，给出结果。
 * （数据似乎不能太长，会逝）
 */
function db_query($db_file, $db_passwd, $db_query) {
    global $query_exe;
    
    $cmd_frag = '"' . $query_exe . '"';
    $cmd_frag .= ' ' . base64_encode($db_file);
    $cmd_frag .= ' ' . base64_encode($db_passwd);
    $cmd_frag .= ' ' . base64_encode($db_query);
    
    $result = shell_exec($cmd_frag);
    $result = explode("\n", str_replace(["\r\n", "\r"], ["\n", "\n"], $result));
    // 第一行 OK 或 FAIL 标志
    if($result[0] == 'OK') {
        // 解码数据
        $result = array_slice($result, 1);
        $endptr = count($result);
        while($endptr > 0 && '' == trim($result[$endptr - 1])) {
            $endptr -= 1;
        }
        $ret = [];
        for($i = 0; $i < $endptr; $i++) {
            $line = $result[$i];
            $ret[] = [];
            foreach(explode(',', $line) as $item) {
                $ret[count($ret) - 1][] = base64_decode($item);
            }
        }
        return ['success' => true, 'data' => $ret];
    } else if($result[0] == 'FAIL') {
        $result = array_slice($result, 1);
        return ['success' => false, 'data' => $result];
    } else {
        return ['success' => false, 'data' => $result];
    }
}
