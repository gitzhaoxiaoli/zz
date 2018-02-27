<?php
//!defined('IN_SUPU') && exit('Forbidden');
header("Content-type:text/html;charset=utf-8");
require_once('../framework/models/db/db_mysql.class.php');
require_once('../framework/function.php');
$t=require_once('../data/db_config.php');


$db = new db_mysql;
$db->connect($t['db_host'], $t['db_user'] ,$t['db_pwd'], $t['db_name']);




  $ep_name = $_POST['ep_name'];
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
  

  $manu_ep_name = $_POST['manu_ep_name'];
  $manu_ep_name_e=$_POST['manu_ep_name_e'];
  $manu_work_code=$_POST['manu_work_code'];
  $manu_nature=$_POST['manu_nature'];
  $manu_statecode=$_POST['manu_statecode'];
  $manu_delegate=$_POST['manu_delegate'];
  $manu_ep_amount=$_POST['manu_ep_amount'];
  $manu_person=$_POST['manu_person'];
  $manu_person_tel=$_POST['manu_person_tel'];
  $manu_ep_phone=$_POST['manu_ep_phone'];
  $manu_ep_fax=$_POST['manu_ep_fax'];
  $manu_person_email=$_POST['manu_person_email'];
  $manu_areaaddr=$_POST['manu_areaaddr'];
  $manu_cta_addr=$_POST['manu_cta_addr'];
  $manu_cta_addr_e=$_POST['manu_cta_addr_e'];
  $manu_cta_addrcode=$_POST['manu_cta_addrcode'];


  $prod_ep_name = $_POST['prod_ep_name'];
  $prod_ep_name_e=$_POST['prod_ep_name_e'];
  $prod_work_code=$_POST['prod_work_code'];
  $prod_nature=$_POST['prod_nature'];
  $prod_statecode=$_POST['prod_statecode'];
  $prod_delegate=$_POST['prod_delegate'];
  $prod_ep_amount=$_POST['prod_ep_amount'];
  $prod_person=$_POST['prod_person'];
  $prod_person_tel=$_POST['prod_person_tel'];
  $prod_ep_phone=$_POST['prod_ep_phone'];
  $prod_ep_fax=$_POST['prod_ep_fax'];
  $prod_person_email=$_POST['prod_person_email'];
  $prod_areaaddr=$_POST['prod_areaaddr'];
  $prod_cta_addr=$_POST['prod_cta_addr'];
  $prod_cta_addr_e=$_POST['prod_cta_addr_e'];
  $prod_cta_addrcode=$_POST['prod_cta_addrcode'];

  /*
$prod_addr_e=$_POST['prod_addr_e'];
$prod_addr=$_POST['prod_addr'];
$iso=$_POST['iso'];
$name=$_POST['name'];
$manu_name=$_POST['manu_name'];
$pro_name=$_POST['pro_name'];
$total=$_POST['total'];
$prod_name_chinese=$_POST['prod_name_chinese'];
$prod_ver=$_POST['prod_ver'];
$prod_name=$_POST['prod_name'];
$scope=$_POST['scope'];


*/
  $sql = "SELECT * FROM sp_sp_enterprises WHERE ep_name = '$ep_name' and deleted = 0";
  $res = $db->get_var($sql);





 $inr=$db->insert('sp_enterprises',array(
'ep_name'=>$ep_name,
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
'ep_fax'=>$ep_fax,
'person_email'=>$person_email,
'areaaddr'=>$areaaddr,
'cta_addr'=>$cta_addr,
'cta_addr_e'=>$cta_addr_e,
'cta_addrcode'=>$cta_addrcode
                                                                ));



if($ep_name!=$manu_ep_name){


 $inr=$db->insert('sp_enterprises',array(

'ep_name'=>$manu_ep_name,
'prod_addr_e'=>$manu_prod_addr_e,
'prod_addr'=>$manu_prod_addr,
'ep_name_e'=>$manu_ep_name_e,
'work_code'=>$manu_work_code,
'nature'=>$manu_nature,
'statecode'=>$manu_statecode,
'delegate'=>$manu_delegate,
'ep_amount'=>$manu_ep_amount,
'person'=>$manu_person,
'person_tel'=>$manu_person_tel,
'ep_phone'=>$manu_ep_phone,
'ep_fax'=>$manu_ep_fax,
'person_email'=>$manu_person_email,
'areaaddr'=>$manu_areaaddr,
'cta_addr'=>$manu_cta_addr,
'cta_addr_e'=>$manu_cta_addr_e,
'cta_addrcode'=>$manu_cta_addrcode
                                                                  ));
}


if(($ep_name!=$manu_ep_name)&&($manu_ep_name!=$prod_ep_name)){


   $inr=$db->insert('sp_enterprises',array(

'ep_name'=>$prod_ep_name,
'prod_addr_e'=>$prod_$prod_addr_e,
'prod_addr'=> $prod_areaaddr,
'ep_name_e'=> $prod_ep_name_e,
'work_code'=>$prod_work_code,
'nature'=>$prod_nature,
'statecode'=>$prod_statecode,
'delegate'=>$prod_delegate,
'ep_amount'=>$prod_ep_amount,
'person'=>$prod_person,
'person_tel'=>$prod_person_tel,
'ep_phone'=>$prod_ep_phone,
'ep_fax'=>$prod_ep_fax,
'person_email'=>$prod_person_email,
'areaaddr'=>$prod_areaaddr,
'cta_addr'=>$prod_cta_addr,
'cta_addr_e'=>$prod_cta_addr_e,
'cta_addrcode'=>$prod_cta_addrcode
                                                   
                                                                      ));
}



if($inr){
	echo "<script>alert('注册完成，待公司确认信息后，会将用户名及密码发送到您填写的E-mail，登录到系统中完善其他信息。如有不明白，下载说明的文档，联系工作人员。');window.location.href='http://casc-cert.com/cn/index.php';</script>";
}else{
	echo "<script>alert('注册失败！！');history.go(-1);</script>";
}



?>