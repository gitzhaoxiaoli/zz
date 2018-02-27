<?php
!defined('IN_SUPU') && exit('Forbidden');
$tid = (int)getgp('tid');

    $rect_finish = getgp('rect_finish');
    //评定设为已整改 无整改
    $db->update('project', array(
            'rect_finish' => $rect_finish,
            'pd_type'     => 5
        ) , array(
            'tid' => $tid 
        )); 
	$db->update('task', array(
            'rect_finish' => $rect_finish,
            'rect_date' => date("Y-m-d")
        ) , array(
            'id' => $tid 
        )); 
	$re_notes=getgp("re_note");
	foreach($re_notes as $id=>$re_note){
		$db->update("assess_notes",
					array(	"re_note"=>$re_note,
							"re_uid"=>current_user("uid"),
						),
					array("id"=>$id));
	
	
	}
    showmsg('success', 'success', "?c=auditor&a=task_edit&tid=$tid");