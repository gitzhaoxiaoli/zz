<?php 

// 退回审核员
$tid = getgp("tid");
if (!$tid) {
	showmsg("数据错误！" ,"error");
	
}
$back_note = getgp("back_note");
$db->update('task' , array('back_note' => $back_note , 'upload_file_date' => '') , array("id" => $tid));

showmsg("success" ,"success" , "?c=archive&a=list");


 ?>