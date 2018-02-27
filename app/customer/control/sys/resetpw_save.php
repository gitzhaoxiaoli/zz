<?php
!defined('IN_SUPU') && exit('Forbidden');


	$eid = current_user('eid');
	$passwd = $db->getField("enterprises","passwd",array("eid"=>$eid));
	extract($_POST);
	if($passwd==md5($password1)){
		$db->update("enterprises",array("passwd"=>md5($password2)),array("eid"=>$eid));
		echo "<script>alert('密码修改成功');history.back(-1); </script>";
	}else{
		echo "<script>alert('原始密码不正确');history.back(-1); </script>";
	}
	//tpl('sys/resetpw');