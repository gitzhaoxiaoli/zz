<?php
!defined('IN_SUPU') && exit('Forbidden');

/*
*计算基础人日
*/
$cti_id=getgp("cti_id");
$level=getgp("level");
if($cti_id && $level){

	$cti_info=$db->get_row("SELECT total,iso,audit_type FROM `sp_contract_item` where cti_id='$cti_id'");
	extract($cti_info);
	if($audit_type=='1001')
		$type='1003';
	elseif($audit_type=='1007')
		$type="1007";
	elseif(in_array($audit_type,array("1004-1","1004-2","1004-3")))
		$type="1004-1";
	$filed="num_m";
	if($level=="01")
		$filed="num_h";
	elseif($level=="02")
		$filed="num_m";
	else
		$filed="num_l";
	if($iso=='A01'){
		$base_num=$db->get_var("SELECT num_l FROM `sp_enterprises_base` WHERE `ep_amount` >= '$total' and iso ='1' and type='$type' limit 1");
	
	}else{
		$base_num=$db->get_var("SELECT $filed FROM `sp_enterprises_base` WHERE `ep_amount` >= '$total' and iso ='2'  and type='$type' limit 1");

	}
	echo $base_num;
	exit;

}
