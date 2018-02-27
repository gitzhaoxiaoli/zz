<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
 *资料回收
 */
$pid = (int) getgp('pid');
$p_info = $db->get_row("SELECT * FROM sp_project WHERE 1 AND id = '$pid'");
if ($_POST) {
    
	$new_project = array(
		'redata_date' => date("Y-m-d"),
		'redata_uid' => current_user('uid'),
		'redata_note' => $_POST[redata_note],
		'redata_status' => $_POST[redata_status],
	);
	$db->update("project",$new_project,array("id"=>$pid));
	// 如果是监督 则一起收回
	$t_info = $db->find_one("task",array("id"=>$p_info[tid]));
	if(strpos($t_info[audit_type],"1004" !== false)){
		$db->update("project",$new_project,array("tid"=>$p_info[tid]));
	}
	$db->update("progress",array(	
								"step10"	=> date("Y-m-d H:i:s"),
								"status"	=> "10",
							),array("pid"=>$pid));			
    
    showmsg('success', 'success', "?m=product&c=archive&a=list");
} else {
    
    //审核文档
    /* $sql = "select * from sp_attachments where tid='$tid' ORDER BY `sort`";
    $res = $db->query($sql);
    while ($rt = $db->fetch_array($res)) {
        $rt['uid']              = f_username($rt['create_uid']);
        $enterprises_archives[] = $rt;
    } 
    $task_info = $db->get_row("SELECT jh_sp_date,jh_sp_note,tb_date,te_date FROM `sp_task` WHERE `id` = '$tid'");
    extract($task_info);
    $tb_date = trim($tb_date, ":00:00");
    $te_date = trim($te_date, ":00:00");*/
	
    tpl();
}