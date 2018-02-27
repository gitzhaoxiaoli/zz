<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
* 删除人员
*/
require_once ("framework/fun.function.php");
$groupid = get_option('groupid');
$uids = getgp("uid");
$uids = explode("|", $uids);
foreach($uids as $uid){
	if(!$uid)continue;
	$tel = $db->getField("hr",'tel',array("id"=>$uid));
	if($tel){
		delUser($tel,$groupid);
	}
	$db->update("hr",array("wstatus"=>0),array("id"=>$uid));
}

echo 'ok';