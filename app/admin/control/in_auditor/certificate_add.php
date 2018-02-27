<?php
!defined('IN_SUPU') && exit('Forbidden');
if($_POST){ //如果提交表单
    $dealerId = getgp('dealerId');
    $dealerId = $db->get_var("SELECT brand_id FROM sp_ot_contract WHERE id = '$dealerId'");
    $db->update("ot_basedata",array('statu'=>'1'),array('id'=>$dealerId));

 	if($_POST['train_names']) {
 		$train_names = explode("\n",$_POST['train_names']);
 		
 		$insert['class']=$_POST['class'];
 		$insert['date']=$_POST['cert_date'];
 		$insert['s_date']=$_POST['s_date'];
 		$insert['e_date']=$_POST['e_date'];
 		$insert['type']=$_POST['type'];
 		$insert['dealerId']=$_POST['dealerId'];
 		$insert['text']=$_POST['text'];
        $db->update("ot_traincert",array("deleted"=>"1"),array(' dealerId'=>"$dealerId"));
 		foreach($train_names as $key=>$value) {
 			$insert['name']=$value;
 			$db->insert('ot_traincert',$insert);
 		}
 	}
	$REQUEST_URI='?c=in_auditor&a=certificate_list';
	showmsg( 'success', 'success', $REQUEST_URI );
}else{//显示添加页面
    $dealerId = getgp('dealerId');
    $sql = "SELECT name FROM `sp_ot_traincert` WHERE `dealerId` = '$dealerId' AND deleted = 0 ";
    $ress = $db->get_col($sql);
    $names = join("\n",$ress);
    $sqls = "SELECT * FROM `sp_ot_traincert` WHERE `dealerId` = '$dealerId' AND deleted = 0 ";
    $res = $db->get_row($sqls);
    extract($res);
    if($type == '1')$type_Y = "selected";
    if($type == '2')$type_N = "selected";
	tpl('in_auditor/certificate_add');
}