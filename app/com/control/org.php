<?php
if($a=='edit'){
	
	$org=$db->get_row("SELECT * FROM `sp_settings_test_org` WHERE id='$_GET[id]'");
	// p($org);
	// exit;
	extract($org);
	tpl("setting/org_edit");
	exit;
}elseif($a=='save'){
	if($id=$_POST[id]){
		unset($_POST[id]);
		$_POST['userPwd']=md5($_POST['userPwd']);
		$db->update("settings_test_org",$_POST,array("id"=>$id));
		
	}else
	$_POST['userPwd']=md5($_POST['userPwd']);
		$db->insert("settings_test_org",$_POST);
	showmsg("success","success","?m=com&c=org");
}elseif($a=='del'){
	$db->update("settings_test_org",array("deleted"=>1),array("id"=>$_GET[id]));
	showmsg("success","success","?m=com&c=org");
	
}else{

	$where="";
	extract($_GET);
	if($nam=trim($name)){
		$where .=" and name like '%$name%'";
	}
	if($nam=trim($code)){
		$where .=" and code like '%$code%'";
	}
	if($nam=trim($person)){
		$where .=" and name person '%$person%'";
	}
	if($nam=trim($province)){
		$where .=" and province like '%$province%'";
	}
	$total=$db->get_var("SELECT COUNT(*) FROM `sp_settings_test_org` WHERE `deleted` = '0' $where");
	$pages = numfpage(total);
	$query=$db->query("SELECT * FROM `sp_settings_test_org` WHERE `deleted` = '0' $where order by update_date desc $pages[limit]");
	$res=array();
	while($rt=$db->fetch_array($query)){
		$res[]=$rt;
		
		
	}
	tpl("setting/orglist");
}