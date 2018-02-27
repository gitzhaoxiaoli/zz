<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
 *认证评定：评定问题 @zxl 2013-11-25 11:15:47
*/
// echo "参考体系！！";
// exit;

$eid = (int)getgp('eid');

$p_tid = (int)getgp('p_tid');
$pd_id = (int)getgp('pd_id'); //审核项目
$auditor = getgp( 'auditor' ); //评定人员
$step = getgp('step');
$status=getgp("status");
if( $step ){
	extract( $_POST, EXTR_SKIP );
	//添加评定问题
		foreach( $comment_a as $pd_id => $val ){
			foreach($val as $key=>$note){
				if($note=="") continue;
					$new_problem = array(
						'note'	=>$note,
						'level'	=>$comment_a_level[$pd_id][$key],
						'tid'   =>$p_tid,        //fxd  2015/5/26/13:36
						'fl_question' => $fl_question[$pd_id][$key],
						'shzcy_uid' => $shzcy_uid[$pd_id][$key],
						'sure_rule' => $sure_rule[$pd_id][$key],
						'shzcy_name'	=> $db->getField('hr', 'name', array("id"=>$shzcy_uid[$pd_id][$key]) ),
					);
				$db->insert('assess_notes',$new_problem);
						//评定设为已整改 无整改
				$db->update('project', array(
						'pd_type' => '4',
						// 'status' => '3',
						// 'iso_prod_type' => '0',
						// 'redata_status'=>'1'
					) , array(
						'tid' => $p_tid 
					));
				$db->update('task', array(
						'rect_finish' => '2'
					) , array(
						'id' => $p_tid 
					));
				}


		}
 

		//更新评定是否通过
		/* foreach($comment_a_pass as $k=>$v){
			$comment_pass_date=$db->get_var("SELECT comment_pass_date FROM `sp_project` WHERE `id` = '$k'");
			if(!$comment_pass_date and $comment_pass_date!="0000-00-00")
			 $db->update( 'project', array('comment_pass'=>$v,"comment_pass_date"=>date("Y-m-d")), array( 'id' => $k ) );
			 else
			 $db->update( 'project', array('comment_pass'=>$v), array( 'id' => $k ) );
			 


		} */

	 showmsg( 'success', 'success', "?m=product&c=assess&a=list" );
	 exit();
}
if($status){
	//点击评定问题的通过时
	$note_id=getgp("note_id");
	
		foreach($note_id as $id){
			$db->update("assess_notes",array("status"=>1),array("id"=>$id));
		
		}
	
	
	showmsg( 'success', 'success', "?m=product&c=assess&a=edit&pd_id=$pd_id#tab-question" );
	exit;


}

//双击修改 ajax 异步传输@zxl 2013-11-26 11:28:27
	$note=getgp('note');
	$id=getgp('id');
	$level=getgp('jibie');

	if($id and $level){
		$db->update("assess_notes",array(	"level"=>$level,
											"up_uid"=>current_user('uid'),
											"up_user"=>current_user('name'),
											"up_date"=>current_time('mysql'),
										),array("id"=>$id)
			);
		//$db->query("update sp_assess_notes set level='$level' where id=$id");
	}
	if($id and $note){
		$db->update("assess_notes",array(	"note"=>$note,
											"up_uid"=>current_user('uid'),
											"up_user"=>current_user('name'),
											"up_date"=>current_time('mysql'),
										),array("id"=>$id)
			);
		//$db->query("update sp_assess_notes set note='$note' where id=$id");
	}



?>