<?php
!defined('IN_SUPU') && exit('Forbidden');

	if( $_POST ){
		if($_POST[energy_status])
			$energy_status=1;
		else
			$energy_status=0;
		$db->update("project",array("energy_status"=>$energy_status),array("id"=>$_POST[pid]));
		unset($_POST[energy_status]);
		$pid=$db->get_var("SELECT pid FROM `sp_energy` where pid='$_POST[pid]'");
		if($pid)
			$db->update("energy",$_POST,array("pid"=>$pid));
		else
			$db->insert("energy",$_POST);
		
		showmsg( 'success', 'success', "?c=certificate&a=energy_list" );
	} else {
		$pid=getgp("pid");
		$energy_info = $db->get_row( "SELECT * FROM `sp_energy` where pid = '$pid'" );
		extract($energy_info);
		$p_info=$db->get_row("SELECT id,ct_id,energy_status,tid FROM `sp_project` where id = '$pid'");
		extract($p_info);
		if(!$energy_info){
			$task_info=$db->get_row("SELECT tb_date,te_date FROM `sp_task` where id = '$tid'");
		}
		!$CYCLE_IDENTIFY && $CYCLE_IDENTIFY='02';
		!$P_PRO_UNIT && $P_PRO_UNIT='01';
		!$P_PRO_NAME && $P_PRO_NAME='钢铁产品';

        tpl();

	}