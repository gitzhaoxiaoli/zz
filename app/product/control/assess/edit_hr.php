<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
 *保存评定人员
 */

 

	$pid = (int)getgp( 'pd_id' );

	$comment_a_uid =getgp( 'comment_a_name');
	foreach( $comment_a_uid as $pd_id => $uid ){
		$db->update( 'project', array(  'comment_a_uid' => $uid,
										'comment_a_name' =>f_username($uid),
                                        ),
								array( 'id' => $pd_id ) );
	}


	
	/* $comment_b_uid = getgp( 'comment_b_name');
	foreach( $comment_b_uid as $pd_id => $uid ){
		if($uid)
			$comment_b_name[$pd_id] =f_zy_name($uid);
	}
	foreach( $comment_b_uid as $pd_id => $uid ){
		$db->update( 'project', array( //'comment_a_uid' => $uid,
										//'comment_a_name' =>$comment_a_name[$pd_id],
										'comment_b_uid'	=> $comment_b_uid[$pd_id],
										'comment_b_name'=> $comment_b_name[$pd_id],
										//'comment_plan_date' => current_time('mysql') 
                                        ),
								array( 'id' => $pd_id ) );
	} */
	showmsg( 'success', 'success', "?m=product&c=assess&a=edit&pd_id=$pid#tab-edit" );
 