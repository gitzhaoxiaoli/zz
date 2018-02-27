<?php
/*
 * 
 */
!defined('IN_SUPU') && exit('Forbidden');

$cti_id=$_GET['cti_id'];
if($cti_id){
$prod = $db->get_row("select * from sp_contract_item cti where cti_id = '$cti_id'");
$new = unserialize($prod['new']);
extract($new);
$prod = array_merge($prod,$new);
$cti_id= $prod['cti_id'];
$eid= $prod['eid'];
$ep_manu_id= $prod['ep_manu_id'];
$ep_prod_id= $prod['ep_prod_id'];

$ep_name=$db->find_one('enterprises',array('eid'=>$eid));
$ep_manu=$db->find_one('enterprises',array('eid'=>$ep_manu_id));
$ep_prod=$db->find_one('enterprises',array('eid'=>$ep_prod_id));
$prod_id=$prod['prod_id'];
$fac_code=$prod['fac_code'];
$xiaolei=$db->find_one('settings_prod_xiaolei',array('code'=>$prod_id,'fac_code'=>$fac_code));


$ep_nature=$ep_name['nature'];
$ep_statecode=$ep_name['statecode'];
$ep_manu_nature=$ep_manu['nature'];
$ep_manu_statecode=$ep_manu['statecode'];
$ep_prod_nature=$ep_prod['nature'];
$ep_prod_statecode=$ep_prod['statecode'];




$file_list = $db->get_results("SELECT * FROM `sp_attachments` WHERE `cti_id` = '$cti_id' AND `ftype` = '1001'");
// p($file_list);
${"checked".$prod[audit_type]} = "checked";
}else{
	$eid = current_user("eid");
	$ep_name=$db->find_one('enterprises',array('eid'=>$eid));
	$ep_nature=$ep_name['nature'];
	$ep_statecode=$ep_name['statecode'];
	$payment_name = $db->meta($eid,"name_ac",'',"enterprise");
	$payment_addr = $db->meta($eid,"r_add",'',"enterprise");

	
	
}
tpl('contract/edit');

?>