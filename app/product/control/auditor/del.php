<?php
!defined('IN_SUPU') && exit('Forbidden');

	$tid = (int)getgp('tid');
	$step = getgp("type");
	$id = getgp("id");
	if($step == 'basic'){
		
		
	}elseif($step == 'plan'){
		
		
	}elseif($step == 'res'){
		$db->del("task_note",array("id"=>$id));
		$tab = "tab-res";
		
		
	}elseif($step == 'rect'){
		
	}elseif($step == 'sample'){
		$db->del("sample",array("id"=>$id));
		$tab = "tab-sample";
		
	}elseif($step == 'verify'){
		$db->del("task_audit_team",array("id"=>$id));
		$tab = "tab-verify";
	}
    showmsg('success', 'success', "?m=product&c=auditor&a=task_edit&tid=$tid#$tab");