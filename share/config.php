<?php
// 配制文件
require "../framework/function.php";
require "../framework/page.fun.php";
require "../framework/models/db/db_mysql.class.php";
$key = file_get_contents("../data/orgcode.log"); //获取动态密钥
$s   = ecryptdString(date("Y-m-d"), $key);
if ($s !== $_GET[data])
    exit("ERROR");
$t  = require_once('../data/db_config.php');
$db = new db_mysql;
$db->connect($t['db_host'], $t['db_user'], $t['db_pwd'], $t['db_name']);

