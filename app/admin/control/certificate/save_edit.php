<?php
!defined('IN_SUPU') && exit('Forbidden');

$tid = (int)getgp( 'tid' );

if($_POST){

$db->update("task",array("save_date"=>date("Y-m-d"),"save_note"=>$_POST['save_note'],"save_status"=>'1'),array("id"=>$tid));
showmsg("success","success","?c=certificate&a=save_file");

}else{

	//取任务信息
	// $sql = "SELECT t.*,e.ep_name FROM sp_task t INNER JOIN sp_enterprises e ON e.eid = t.eid WHERE t.id = '$tid'";
	// $task = $db->get_row( $sql );
	$eid=$db->get_var("SELECT eid FROM `sp_task` WHERE `id` = '$tid'");
	$task_archives=$db->get_results("SELECT * FROM `sp_attachments`  where tid='$tid'");

	$ct_ids=$db->get_col("SELECT ct_id FROM `sp_project` WHERE `tid` = '$tid' AND `deleted` = '0'");
	$ct_ids=array_unique($ct_ids);
	$ct_id=join("|",$ct_ids);
	tpl();
}



 


?>