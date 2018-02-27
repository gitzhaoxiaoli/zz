<?php
!defined('IN_SUPU') && exit('Forbidden');
$tid = (int)getgp('tid');
	//$o_task = load( 'task' );
	$task_info=$task->get(array('id'=>$tid));
	
	
	log_add($task_info['eid'], 0, "[说明:删除任务]",'','');
	
	//审核项目恢复到未安排
	$up_pro = array(
				'status' => 0,
				'tid'=>0,
				'assess_review_status'=>0,
				'redata_uid'=>0,
				'redata_date'=>'',
				'redata_note'=>'',
				'redata_status'=>0,
				'comment_a_uid'=>0,
				'comment_a_name'=>'',
				'pd_type'=>'0',
				'assess_date'=>'',
				'sp_date'=>'',
				);
	$db->update( 'project',$up_pro , array( 'tid' => $tid ) );
	//删除派人
	$task->del_send( $tid );
	//删除任务
	$task->del( array( 'id' => $tid ) );
 
	showmsg( 'success', 'success', $_SERVER['HTTP_REFERER'] );