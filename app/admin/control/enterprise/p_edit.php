<?php

if(!$_POST){
	$eid = getgp('eid');
	$sql = "SELECT * FROM sp_enterprises WHERE  eid='$eid' " ;
	$users = $db->get_row( $sql);
	tpl('enterprise/p_edit');

}else{
	$eid = getgp('eid');
	$en_username=getgp('en_username');
	$en_password=getgp('en_password');
	$en_password = md5($en_password);
	$_eid = $db->get_var("SELECT eid FROM `sp_enterprises` WHERE `username` = '$en_username' AND `deleted` = '0' and eid <> '$eid'");
	if($_eid){
		echo "<script>alert('用户名已存在！');window.history.back();</script>";
		exit;
	}
	$arr = array('username'=>$en_username,'passwd'=>$en_password);
	$db->update('enterprises',$arr,array('eid'=>$eid));
	$REQUEST_URI="?c=enterprise&a=p_word";
	showmsg( 'success', 'success', $REQUEST_URI );
}
