<?php
!defined('IN_SUPU') && exit('Forbidden'); 
   /*
    *  文审派人
   */
   $tid = getgp('tid');
   
   if(getgp('step')){
    $ws_task_new = array(
	                      
						  'wenshen_status' => 2,
						);
	  $db->update('task',$ws_task_new,array('id'=>$tid));
	  showmsg('success','success',"?c=auditor&a=wshen");
   }
	//只是取ct_id
	if($tid){
		$ct_id=$db->get_var("SELECT ct_id FROM `sp_project` WHERE `tid` = '$tid' AND `deleted` = '0' AND iso_prod_type = 0 ORDER BY `ct_id` DESC ");
		$query= $db->query("SELECT wenshen_uid,wenshen_person,wenshen_sdate,wenshen_edate,wenshen_note FROM sp_task WHERE id = '$tid' AND deleted = 0");
		$old_res = $db->fetch_array($query);
	}
	else{
		$ct_id=$db->get_var("SELECT ct_id FROM `sp_project` WHERE `id` IN(".join(",",$pids).") AND `deleted` = '0' AND iso_prod_type = 0 ORDER BY `ct_id` DESC ");
	}
	//提取老数据
	
   tpl();
?>