<?php

if($_POST){
	require_once "framework/fun.function.php";
	$uids = $_POST['uid'];
	$uids = array_unique($uids);
	$content = $_POST['message'];
    $res = sendWmsg($uids,$content);
	if($res)
        echo "<script type='text/javascript'>alert('发送信息成功！');parent.location.reload();</script>";
    else{
         echo "<script type='text/javascript'>alert('发送信息失败！');</script>";
    }
}

$tid=getgp('tid');

//$wid = $db->getCol("task_audit_team","uid",array('tid'=>$tid));
$sql = "SELECT tat.uid,tat.name,h.wstatus FROM sp_task_audit_team tat LEFT JOIN sp_hr h ON h.id = tat.uid  WHERE tat.tid = '$tid' AND tat.deleted='0'";
$query = $db->query($sql);
$openid =  array();
$name=array();
while($rt = $db->fetch_array($query)){
    $openid[] = $rt[uid];
    if($rt[wstatus])
         $name[] = $rt[name];
    else
         $name[] = "<span style='color:#ddd'>".$rt[name]."</span>";
}
$name = array_unique($name);
$name = join(" ",$name);
$openid = array_unique($openid);

$sql = "SELECT t.tb_date,t.te_date,e.ep_name FROM sp_task t LEFT JOIN sp_enterprises e ON t.eid = e.eid WHERE t.id = '$tid' and t.deleted = 0 ";
$row = $db->get_row($sql);

extract($row);


tpl();