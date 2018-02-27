<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
 *
 */
$pid = (int) getgp('pid');
$p_info = $db->find_one("project",array("id"=>$pid),"assess_review_note,assess_review_status,ep_prod_id,cti_id,tid");
if ($_POST) {
    
    $db->update("project",array("assess_review_note"=>$_POST[assess_review_note],
								"assess_review_status"=>$_POST[assess_review_status],
						),array("id"=>$pid));
	// 如果是监督 则一起收回
	$t_info = $db->find_one("task",array("id"=>$p_info[tid]));
	if(strpos($t_info[audit_type],"1004" !== false)){
		$db->update("project",$new_project,array("tid"=>$p_info[tid]));
	}
    showmsg('success', 'success', "?m=product&c=assess&a=review");
} else {
    
    extract($p_info);

    tpl();
}