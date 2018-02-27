<?php

//删除已经注销的证书的相关项目

//$certs=$db->get();

  $sql=" SELECT cti_id FROM sp_certificate WHERE deleted='0' AND status='4'";
$q=$db->query($sql);
while($rt=mysql_fetch_assoc($q)){
	load('audit')->del(array('cti_id'=>$rt['cti_id']));
 
}