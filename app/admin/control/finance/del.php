<?php
!defined('IN_SUPU') && exit('Forbidden');
//2016-03-11 @cyf 
//收费删除
$id = getgp("id");
$cost_id = getgp("cost_id");
if($id){
	$db->update("contract_cost_detail",array("deleted"=>1),array("id"=>$id));
}
showmsg("success","success","?c=finance&a=dlist");