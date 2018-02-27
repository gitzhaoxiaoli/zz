<?php
!defined('IN_SUPU') && exit('Forbidden');
$org_id = $_GET['org_id'];
$links = load('test_org_hr')->gets(array('org_id' => $org_id));
if($_POST['flag'] && $_POST['org_id']){//判断是否要添加
	$data = array(
		'org_id' =>  $_POST['org_id'],
		'linkname' => $_POST['linkname'],
		'linktel' => $_POST['linktel'],
		'zhiwu' => $_POST['zhiwu'],
		'fanwei' => $_POST['fanwei']
	);
	$id = load('test_org_hr')->add($data);
	if($id){
		echo json_encode(array('status'=>1));
	}else{
		echo json_encode(array('status'=>0));
	}
	exit;
	//showmsg('success', 'success', "?m=com&c=setting&a=test_org_list");
}
tpl();
