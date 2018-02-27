<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
 *财务收费保存
 */
$cti_id     = getgp('cti_id'); 
$ccd_id  = getgp('ccd_id'); //收费明细ID 
 
$cti = $db->find_one("contract_item",array("cti_id"=>$cti_id));



 
//财务费用区域 
$value   = array(
    'eid' => $cti[ep_prod_id],
    'ct_id' => $cti[ct_id],
    'cti_id' => $cti_id,
    'iso' => $cti[iso],
    'invoice' => getgp('invoice'),
    'invoice_cost' => getgp('invoice_cost'),
    'invoice_date' => getgp('invoice_date'),
    'currency' => getgp('currency'),
    'note' => getgp('note'),
    'dk_date' => getgp('dk_date'),
    'dk_cost' => getgp('dk_cost'), //
	'cost_type'=> getgp('cost_type'),
	'sy_cost'=> getgp('sy_cost'),
);
if ($ccd_id) {
    $af = $ctcf->get($ccd_id);
    $ctcf->edit($ccd_id, $value);
    $bf = $ctcf->get($ccd_id);
    log_add($cost_info[eid], current_user("uid"), "财务收费修改", serialize($af), serialize($bf));
	if($_POST[pid])
		$db->update("project",array("is_finance"=>'1'),array("id"=>$_POST[pid]));
} else {
    $ccd_id = $ctcf->add($value);
    $bf     = $ctcf->get($ccd_id);
    log_add($cost_info[eid], current_user("uid"), "财务收费添加", NULL, serialize($bf));
    
}
 
 
showmsg('success', 'success',"?c=finance&a=dlist");

	