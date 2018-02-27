<?php
//!defined('IN_SUPU') && exit('Forbidden');
header("Content-type:text/html;charset=utf-8");
require_once('../framework/models/db/db_mysql.class.php');
require_once('../framework/function.php');
$t=require_once('../data/db_config.php');


$db = new db_mysql;
$db->connect($t['db_host'], $t['db_user'] ,$t['db_pwd'], $t['db_name']);

/*
$username=$_GET['username'];
$row=$db->query("select * from sp_enterprises where username=$username");
if($row){
  echo 1;
  exit;
}

*/

$areacode=$_POST['areacode'];
  $ep_name = $_POST['ep_name'];
  $username=$_POST['username'];
  $passwd=$_POST['passwd'];
  $passwd=md5($passwd);
  $ep_name_e=$_POST['ep_name_e'];
  $work_code=$_POST['work_code'];
  $nature=$_POST['nature'];
  $statecode=$_POST['statecode'];
  $delegate=$_POST['delegate'];
  $ep_amount=$_POST['ep_amount'];
  $person=$_POST['person'];
  $person_tel=$_POST['person_tel'];
  $ep_phone=$_POST['ep_phone'];
  $ep_fax=$_POST['ep_fax'];
  $person_email=$_POST['person_email'];
  $areaaddr=$_POST['areaaddr'];
  $cta_addr=$_POST['cta_addr'];
  $cta_addr_e=$_POST['cta_addr_e'];
  $cta_addrcode=$_POST['cta_addrcode'];
  
  $row=$db->get_row("select * from sp_enterprises where username='$username' AND deleted = 0 ");
  if($row){
	  echo "<script>alert('用户名已存在!');window.history.back();</script>";
	  exit;
  }

 $inr=$db->insert('enterprises',array(
'ep_name'=>$ep_name,
'username'=>$username,
'passwd'=>$passwd,
'prod_addr_e'=>$prod_addr_e,
'prod_addr'=>$prod_addr,
'ep_name_e'=>$ep_name_e,
'work_code'=>$work_code,
'nature'=>$nature,
'statecode'=>$statecode,
'delegate'=>$delegate,
'ep_amount'=>$ep_amount,
'person'=>$person,
'person_tel'=>$person_tel,
'ep_phone'=>$ep_phone,
'ep_fax'=>ep_fax,
'person_email'=>$person_email,
'areaaddr'=>$areaaddr,
'cta_addr'=>$cta_addr,
'cta_addr_e'=>$cta_addr_e,
'cta_addrcode'=>$cta_addrcode,
'areacode'=>$areacode
/*
'iso'=>$iso,
'name'=>$name,
'manu_name'=>$manu_name,
'pro_name'=>$pro_name,
'total'=>$total,
'prod_name_chinese'=>$prod_name_chinese,
'prod_ver'=>$prod_ver,
'prod_name'=>$prod_name,
'scope'=>$scope
*/                                                      
                                                                      ));




if($inr){
	echo "<script>alert('注册完成，登录到系统中登记产品。如有不明白，下载说明的文档，联系工作人员。');window.location.href='/ctc/?m=customer&c=login';</script>";
}else{
	echo "<script>alert('注册失败！！');history.go(-1);</script>";
}



?>