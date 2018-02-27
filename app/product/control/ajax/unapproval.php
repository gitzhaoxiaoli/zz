<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
 * 合同评审撤销审批
 */

    $cti_id = (int)getgp('cti_id');
   $af_info = $db->get_row("select * from sp_contract_item where cti_id='$cti_id' ");
    //删除任务
    $db->del('project', array(
        'cti_id' => $cti_id
    ));
    $db->update('contract_item', array(
        'status' => 0
    ) , array(
        'cti_id' => $cti_id
    ));
    $ct_id = $db->getField("contract_item","ct_id",array('cti_id'=>$cti_id));
    $db->update('contract', array(
        'status' => 0
    ) , array(
        'ct_id' => $ct_id
    ));
 $bf_info = $db->get_row("select * from sp_contract_item where cti_id='$cti_id' ");
// 日志
 
log_add($bf_info['eid'], 0, "撤销产品受理 合同编号:" . $bf_info['cti_code'] , serialize($af_info), serialize($bf_info ));


    print_json(array(
        'status' => 'ok',
        'msg' => 'success'
    ));