<?php
!defined('IN_SUPU') && exit('Forbidden');
$where='';
//搜索条件
$ep_name = trim($_GET['ep_name']);
if($ep_name){
	$where.=" AND basedata.name like '%$ep_name%'";
}

$name = trim($_GET['name']);
if($name){
 	$where.=" AND auditor.name like '%$name%'";
}
$total = $db->get_var("SELECT COUNT(*) FROM sp_task_audit_team auditor  WHERE 1 $where AND auditor.data_for='6'");
$pages = numfpage($total);
$sql=" SELECT auditor.* FROM sp_task_audit_team auditor  WHERE 1 $where AND auditor.data_for='6' ORDER BY id DESC $pages[limit]";
$query=$db->query($sql);
while($r=$db->fetch_array($query)){
	$task_list[]=$r;
	
}
 
tpl();