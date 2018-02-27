<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
* 群发邮件
*/
set_time_limit(0);
if($_POST){
	$title=getgp("email_title");
	$note=getgp("email_cotent");
	//处理附件
   $upload = load('upload');
   $upload->savePath = get_option('upload_temp_dir'). '\\';
   if ($upload->upload()) {
	   $info = $upload->getUploadFileInfo();
	   $filePath=$info[0]['savepath'].iconv("UTF-8", "GB2312//IGNORE",$info[0]['name']);
	   $fileName=$info[0]['name'];
	   @rename($info[0]['savepath'].$info[0]['savename'],$filePath);//重命名
	   
	  
   }
	$data=array(array("企业名称","联系人","E-mail"));
	$query=$db->query("SELECT * FROM `sp_periodical_email` WHERE `person_email` <> ' '");
	while($rt=$db->fetch_array($query)){
		
		if(mailTo($rt["person_email"],$title,$note,$filePath,$fileName)!==true){
			$ep_name=$db->get_var("SELECT ep_name FROM `sp_enterprises` WHERE `code` = '$rt[code]' AND `deleted` = '0'");
			$data[]=array($ep_name,$rt[person],$rt[person_email]);
		
		}

	}
	unlink($filePath);
	if($data[1])
		export_excel($data,"邮件发送错误数据");
	success("success","success","?c=batch_mail");
}else{
	// exit;
	tpl("batch_mail");
	
	
}
