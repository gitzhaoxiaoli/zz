<?php
/*
 * 企业登陆
 */
!defined('IN_SUPU') && exit('Forbidden');
$a = getgp('a'); 
$enter  = load('testorg');
if(!$a){
	$a = 'login';
}
if($a == 'login'){
	//p($_COOKIE);
	$supuLoginName = $_COOKIE['loginName'];
	tpl("login/login");
}else if($a=='login_in'){ //异步传输验证用户是否登录
	$username = getgp('uname');
	$password = getgp('upwd');
	$result = $enter->userAuth($username,$password);
	// 记录日志
	log_add(0, 0, "外网帐号{$username}登录，结果：{$result}", NULL, NULL);
	echo $result;
}else if($a=='login_out'){
	// 记录日志
	log_add(0, 0, "外网帐号" . current_user('username') . "退出", NULL, NULL);
	$result = $enter->userLogout();
	echo $result;
}


?>