<?php
/*
 * 企业密码恢复
 */
$en_username = getgp("en_username");
$tel = getgp("tel");
$e_mail = getgp("e_mail");

if($en_username){
	$eid = $db->get_var("select eid from sp_enterprises where en_username='{$en_username}' ");
	$uid = $db->get_var("select meta_id from sp_metas_ep where Id='{$eid}' and meta_name='person_mail' and meta_value='{$e_mail}'");
	if($uid){
 		$_POST['add']['password']=md5('123456');
		load('enterprise')->edit($eid,array('en_password' =>$_POST['add'][password] ));
		showmsg('已恢复为初始密码123456！','success', '?m=en_user&a=login');
	}else{
		showmsg('用户信息错误，请重新填写！', 'error', 'm=en_user&a=get_password'); 
	}
}
tpl("en_user/get_password");

