<?php if(!defined('IN_SYSTEM')) exit;//Silence is golden ?><?php

// JSON5 编码器
require(FUNC . 'json5/index.php');

// YAML 编码器
require(FUNC . 'yaml/yaml.php');

// 基础功能（MIME、文件、返回代码）
require(FUNC . 'basic.function.php');

// 日期与时间
require(FUNC . 'date.function.php');

// 通用文件数据锁定（防止写入竞争）
require(FUNC . 'datalock.function.php');

// 应用列表
require(FUNC . 'app.function.php');

// 网络请求（cURL）
require(FUNC . 'network.function.php');

// 编码、URL 解析
require(FUNC . 'encoding.function.php');

// 页面模板、静态资源加载
require(FUNC . 'tpl.function.php');

// 绘图，和谐过参数顺序的封装
require(FUNC . 'picturing.function.php');

// API 数据
require(FUNC . 'metadata.function.php');

// 日志记录
require(FUNC . 'logdata.function.php');

// C++ 程序解析数据库（缝合怪了属于是）
require(FUNC . 'dbquery.function.php');

// 桌面交互命令
require(FUNC . 'desktop_launch.function.php');
