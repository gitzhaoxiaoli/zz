<?php
require_once('../framework/models/db/db_mysql.class.php');
require_once('../framework/function.php');
$t=require_once('../data/db_config.php');


$db = new db_mysql;
$db->connect($t['db_host'], $t['db_user'] ,$t['db_pwd'], $t['db_name']);


$username=$_GET['username'];
$row=$db->get_row("select * from sp_enterprises where username='$username' AND deleted = 0 ");
if($row){
  echo 1;
  exit;
}
$work_code=$_GET['work_code'];
$row=$db->get_row("select * from sp_enterprises where work_code='$work_code' AND deleted = 0 ");
if($row){
  echo 2;
  exit;
}
$ep_name=$_GET['ep_name'];
$row=$db->get_row("select * from sp_enterprises where ep_name='$ep_name' AND deleted = 0 ");
if($row){
  echo 3;
  exit;
}




?>