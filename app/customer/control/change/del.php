<?php
/*
 * 
 */


!defined('IN_SUPU') && exit('Forbidden');
$id = getgp("id");
$db->del("change_app",array("id"=>$id));
showmsg("success","success","?m=customer&c=change&a=list");
?>