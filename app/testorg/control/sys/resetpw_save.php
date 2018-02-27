<?php
!defined('IN_SUPU') && exit('Forbidden');


	$id = current_user('id');
	$passwd = $db->getField("settings_test_org","userPwd",array("id"=>$id));
	extract($_POST);
	if($passwd==md5($password1)){
		$db->update("settings_test_org",array("userPwd"=>md5($password2)),array("id"=>$id));
		echo "<script>alert('密码修改成功');history.back(-1); </script>";
	}else{
		echo "<script>alert('原始密码不正确');history.back(-1); </script>";
	}
	//tpl('sys/resetpw');