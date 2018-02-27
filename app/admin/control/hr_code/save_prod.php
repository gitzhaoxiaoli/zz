<?php
!defined('IN_SUPU') && exit('Forbidden'); 
//添加于编辑业务代码
$id = getgp('id');
$iso = getgp('iso'); 
if (!getgp('pass_date')) {
    echo "<script>alert('通过日期不能为空');history.go(-1);</script>";
    exit;
}
unset($_POST[id]);
if($id){
	$db->update("hr_audit_code",$_POST,array("id"=>$id));
	
}else{
	
	$qua = $db->find_one("hr_qualification",array("id"=>$_POST[qua_id]),"id qua_id,qua_type,ctfrom,uid,iso");
	$new_code = array_merge($qua,$_POST);
	$_id = $db->get_var("SELECT id FROM `sp_hr_audit_code` WHERE `uid` = '$new_code[uid]' AND `use_code` = '$new_code[use_code]' AND `deleted` = '0' AND `iso` = '$new_code[iso]' ");
	if($_id){
		echo "<script>alert('申请专业已存在');history.go(-1);</script>";
		exit;
	}else{
		
		$db->insert('hr_audit_code',$new_code);
	}
}
  $REQUEST_URI = "?c=hr_code&a=list";
  showmsg( 'success', 'success', $REQUEST_URI );

?>

