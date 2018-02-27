<?php
!defined('IN_SUPU') && exit('Forbidden');
//页面注释： 添加和编辑同用一个模板 edit.htm

if($_GET['dealerId']){//显示编辑信息
    $dealerInfo=$db->get_row("select * from sp_ot_basedata where id=$_GET[dealerId]");
	$brand_select=str_replace("value=\"".$dealerInfo['father_id']."\"","value=\"".$dealerInfo['father_id']."\" selected",$brand_select);
}  
$area_select=f_select('area',$dealerInfo['area']); //区域下拉

if($_GET['dealerId'] and $_POST){//编辑经销商信息
	$_POST['update_user']=current_user('name'); //更新人
	$_POST['update_date']=current_time('mysql'); //更新时间
	
	$db->update('ot_basedata',$_POST,array('id'=>$_GET['dealerId']));
	$REQUEST_URI='?c=in_auditor&a=dealer_edit&dealerId='.$_GET['dealerId'];
	 
	 showmsg( 'success', 'success', $REQUEST_URI );
 
}else if($_POST){ //如果提交表单 
	$_POST['create_user']=current_user('name'); //创建人
	$_POST['create_date']=current_time('mysql'); //创建时间
	$rid=$db->insert('ot_basedata',$_POST);
	if($rid){ //是否插入成功
		$REQUEST_URI= $_SERVER['HTTP_REFERER'];
		showmsg( 'success', 'success', $REQUEST_URI );
 	} 
}else{//显示添加页面
	tpl();
}