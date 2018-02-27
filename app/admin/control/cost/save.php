<?php
//@cyf 2016-03-01
//合同费用修改删除
!defined('IN_SUPU') && exit('Forbidden');
	extract($_POST, EXTR_SKIP);
	$id = getgp('id');
	$ct_id = getgp('ct_id');
	$eid = $db->get_var("select eid from sp_contract where ct_id='$ct_id' ");
	$value = array(
		'eid'		=> $eid,
		'ct_id'		=> $ct_id,
		'cost_type' => $cost_type,
		'cost'      => $cost,
		'note'      => $note
	);
	if($id){
	$ctc->edit($id,$value);
	}else{
	$id = $ctc->add($value);
	}
	
	$REQUEST_URI='?c=cost&a=edit&ct_id='.$ct_id.'&id='.$id;
	showmsg( 'success', 'success', $REQUEST_URI );
	exit;