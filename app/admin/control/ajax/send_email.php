<?php
!defined('IN_SUPU') && exit('Forbidden');
  if($_POST)
   {
   $email_from = $_POST['email_from'];
   $email_to = $_POST['email_to'];
   $email_title = $_POST['email_title'];
   $email_cotent = $_POST['email_cotent'];
   $email_to=explode(";",str_replace(array("；",",","，"),";",$email_to));
   // p($email_to);
   // exit;
   //处理附件
   $upload = load('upload');
   $upload->savePath = get_option('upload_temp_dir'). '\\';
   if ($upload->upload()) {
	   $info = $upload->getUploadFileInfo();
	   $filePath=$info[0]['savepath'].iconv("UTF-8", "GB2312//IGNORE",$info[0]['name']);
	   $fileName=$info[0]['name'];
	   @rename($info[0]['savepath'].$info[0]['savename'],$filePath);//重命名
	   
	  
   }
	if(mailTo($email_to,$email_title,$email_cotent,$filePath,$fileName)===true)
		{
		echo "发送成功！！";
		unlink($filePath);
		//echo "<script type='text/javascript'>alert('发送成功！！');parent.location.reload();</script>";
		/* if($_POST['type']=='top')
			echo "<script type='text/javascript'>alert('发送成功！！');window.close();</script>";
		else
			echo "<script type='text/javascript'>alert('发送成功！！');window.parent.update()</script>";
		exit; */
		}
		else
		{
		echo "发送失败！！";
		}
  }else{
   $email_from = $_GET['email_from'];
   $email_to = $_GET['email_to'];
   tpl('ajax/send_email');
    }
?>