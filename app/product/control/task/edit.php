<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
 * 审核安排
 */
$tid = (int) getgp('tid');
$ep_prod_id = (int) getgp('ep_prod_id');
if ($_POST) {
	extract($_POST);
    //添加/修改 
	$ctfrom=$db->getField("enterprises",'ctfrom',array("eid"=>$ep_prod_id));
	$tb_date=getgp("tb_date")." ".$tb_time;
	$te_date=getgp("te_date")." ".$te_time;
    $new_task = array(
        'eid' => $ep_prod_id,
        'ctfrom' => $ctfrom,
        'tk_num' => getgp('tk_num'), //总人天
        'tb_date' => $tb_date, //开始日期
        'te_date' => $te_date, //结束日期
		// 'tb_date' => $tb_date, //开始日期
        // 'te_date' => $te_date, //结束日期
		// 'is_sample'	=> $_POST['is_sample'],
        'note' => getgp('task_note'), //备注
        'self_note' => getgp('self_note'), //备注 
        'iso_prod_type' => 1,
		'task_status' => 2,

    );
    // p($new_task);die;
    if ($tid) {
     
		
        $task->edit($tid, $new_task, 1);
		
		
    } else {
		$ep_info = $db->get_row("SELECT fac_code ep_fac,audit_type FROM `sp_enterprises` WHERE `eid` = '$ep_prod_id'");
		extract($ep_info);
		$p_facs = $db->get_col("SELECT fac_code FROM `sp_project` WHERE `id` in(".join(",",$_POST[pid]).")");
		$p_facs = array_unique($p_facs);
		$p_fac = join(",",$p_facs);
		if(strpos($audit_type,"-")!== false){
			$last = abs(strrchr($audit_type,"-"));
			$last ++;
			$new_task[audit_type] = "1004-".$last;
			$last = sprintf("%02d",$last);
			$last = "F".$last;
		}elseif($audit_type == '1009'){
			$last = "TS";
			$new_task[audit_type] = "1009";
		}elseif($audit_type == '1001'){
			$last = "F01";
			$new_task[audit_type] = '1004-1';
		}
		$new_task[task_code] = date("Y")."-".$ep_fac."-".$p_fac;
		if($last)
			$new_task[task_code] .= "-".$last;
        $tid = $task->add($new_task);
		
		log_add($ep_prod_id,0,'新增任务-'.$new_task[task_code],NULL,NULL);

    }
		foreach($_POST[pid] as $id => $v)
		{
			$up = array('tid'		=> $tid,
						'status'	=> 1,
						);
			$db->update('project',$up,array("id"=>$id));	
			$db->update("progress",array(	
									"step5"	=> date("Y-m-d H:i:s"),
									"status"	=> "5",
								),array("pid"=>$id));			
		}
		
        
    showmsg('success', 'success', "?m=product&c=task&a=edit&tid=$tid&ep_prod_id=$ep_prod_id");
} else {
    //取信息
    $bm_8 = $bm_13 = $em_12 = $em_17 = ''; //选中使用
    $tb_h = 8; //8点上午 17是下午结束时间
    $te_h = 17; //8点上午 17是下午结束时间 
    $projects = array();
    if($tid) {
        $task = load('task');
        $row  = $task->get(array(
            'id' => $tid
        ));
        extract($row, EXTR_SKIP); //获取sp_task信息 
		$ep_prod_id=$eid;
        $tb_h    = date('G', strtotime($tb_date));
        $te_h    = date('G', strtotime($te_date));
        $tb_date = mysql2date('Y-m-d', $tb_date);
        $te_date = mysql2date('Y-m-d', $te_date);
        //$where   = " AND tid = '$tid'";
    }
    ${'bm_' . $tb_h} = ' selected';
    ${'em_' . $te_h} = ' selected';
	$where = " AND ep_prod_id ='$ep_prod_id' AND pd_type = '0'";
    $ct_projects = array();
    $query       = $db->query("SELECT * FROM sp_project WHERE   deleted=0 and iso_prod_type = 1 $where ORDER BY id DESC");
	// echo $db->sql;
    while ($rt = $db->fetch_array($query)) {
		$rt['ep_name'] = $db->getField('enterprises','ep_name',array('eid'=>$rt['eid']));
		$rt['ep_manu_name'] = $db->getField('enterprises','ep_name',array('eid'=>$rt['ep_manu_id']));
		$cert = $db->get_row("SELECT certno,s_date,e_date FROM `sp_certificate` WHERE `cti_id` = '$rt[cti_id]' AND `deleted` = '0' ORDER BY `s_date` DESC");
		if($cert)
			$rt = array_merge($rt,$cert);
        $ct_projects[$rt['id']] = $rt;
		!$audit_type && $audit_type = $rt['audit_type'];
    }


    tpl('task/edit');
}
?>
