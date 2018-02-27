<?php
!defined('IN_SUPU') && exit('Forbidden');
	$tid = (int)getgp('tid');
    
	$bufuhe= $_POST['bufuhe'];
    $jh_re_note= $_POST['jh_re_note'];
	$last_rect_date= $_POST['last_rect_date'];
    $sql="update sp_task set bufuhe='$bufuhe',jh_re_note='$jh_re_note',last_rect_date='$last_rect_date' where id='$tid'";
    $db->query($sql);
    $REQUEST_URI = '?c=auditor&a=task';
    showmsg('success', 'success', $REQUEST_URI);