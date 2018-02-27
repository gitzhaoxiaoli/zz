<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
*检查合同号是否存在
*/
$ct_id=getgp("ct_id");
$ct_code = getgp('ct_code');
if($ct_code){
	$res=$db->get_row("SELECT ct_id FROM `sp_contract` WHERE `ct_code`='$ct_code' and deleted=0 and ct_id<>'$ct_id'");
	if($res)
		echo "ok";
	exit;

}
require ROOT.'/data/cache/audit_ver.cache.php';
$eid=getgp("eid");
$audit_ver=getgp("audit_ver");
$iso=$audit_ver_array[$audit_ver]['iso'];
$type=getgp("type");
if($type=='get-renum'){
	$certno=$db->get_var("SELECT certno FROM `sp_certificate` WHERE `eid` = '$eid' AND `iso` = '$iso' AND `deleted` = '0' AND `main_certno` = '' ORDER BY `s_date` DESC");
	$certno=substr($certno,-2);
	$certno=substr($certno,0,1);
	echo ++$certno;
	exit;
}
