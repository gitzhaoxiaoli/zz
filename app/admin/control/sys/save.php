<?php
!defined('IN_SUPU') && exit('Forbidden');

/*

//个人保存权限

*/


$check_sys = getgp('check_sys');
	$uid = getgp('uid');
	$sys = @ implode("|",$check_sys);
    $gro_check =@ implode("|",getgp('gro_check'));
	$value = '';
	if($uid){
		$value=array(
			'sys' => $sys,
			'gro_id' => $gro_check,
			'check_auth' => getgp('check_auth'),
			'fixed_ip' => getgp('fixed_ip'),
		);
		if(!empty($_POST['username']) and $_POST['username']!=$_POST['oldusername']) {
			$value['username'] = $_POST['username'];
		}
		if(!empty($_POST['newpassword'])) {
			$value['password'] = md5(trim($_POST['newpassword']));
		}
		$user->edit($uid, $value);
		$REQUEST_URI="?c=sys&a=edit&uid=$uid";
		showmsg( 'success', 'success', $REQUEST_URI );
	}else{
		echo 'error';
	}
