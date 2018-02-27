<?php
!defined('IN_SUPU') && exit('Forbidden');
$iso = getgp("iso");
$uid = getgp("uid"); 
// 新增
if($qua_id = getgp("qua_id")){
	
	$tip_msg = "新增";
	
	
}

// 修改
if($id = getgp("id")){
	
	$tip_msg = "修改";
	
}	 
$code = $db->getCol("hr_audit_code","use_code",array("uid"=>$uid));
$ucode_str = join("；",$code);
tpl('hr/hr_code_edit_prod');
?>

