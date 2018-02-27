<?php
!defined('IN_SUPU') && exit('Forbidden');

// $a = getgp('a'); //不能删除 
// echo $a;
// exit;
// if(!$a){
	// $a = 'login';
// }
$user = load('login');
if($a=='login_in'){ //异步传输验证用户是否登录
	
	$username = getgp('uname');
	$password = getgp('upwd');
	$result = $user->userAuth($username,$password);

	log_add(0, 0, "帐号{$username}登录，结果：{$result}", NULL, NULL);
	echo $result;
}else if($a=='login_out'){

	log_add(0, 0, "帐号" . current_user('username') . "退出", NULL, NULL);
	$result = $user->userLogout();
	echo $result;
}else{
	$supuLoginName = $_COOKIE['loginName'];
	tpl('login');
} 
?>