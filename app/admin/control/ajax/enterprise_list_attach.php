<?php
/*
* 更新上传时间
*/

$id = getgp("id");
$create_date = getgp("create_date");
if($id && $create_date){
	$db->update("attachments",array("create_date"=>$create_date),array("id"=>$id));
}