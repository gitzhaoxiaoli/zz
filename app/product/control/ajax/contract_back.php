<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
 * 申请退回
 */

    $cti_id = (int)getgp('cti_id');
	$note = getgp("note");
    $db->update('contract_item', array(
        'status' => 2,
        'app_finish' => 2,
		'approval_note'=> $note,
    ) , array(
        'cti_id' => $cti_id
    ));
$bf_info = $db->get_row("select * from sp_contract_item where cti_id='$cti_id' ");
// 日志
 
log_add($bf_info['ep_prod_id'], 0, "退回产品受理 申请编号:" . $bf_info['cti_code'] , NULL, serialize($bf_info ));


    print_json(array(
        'status' => 'ok',
        'msg' => 'success'
    ));