<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
* 微信保存
*/
require_once ("framework/fun.function.php");
$groupid = get_option('groupid');
$uids = getgp("uid");
if($uids)
	foreach($uids as $uid){
		$_hr = $db->get_row("SELECT * FROM sp_hr WHERE id = '$uid'");
		if(!$_hr[tel])continue;
		$res = addUser($_hr[tel],$_hr['name'],$groupid,$_hr[tel]);
		$res = json_decode($res,true);
		if($res['success']){
			$db->update("hr",array('wstatus'=>1),array('id'=>$uid));
		}
		
	}
	
showmsg("success","success","?c=wchat&a=wlist");