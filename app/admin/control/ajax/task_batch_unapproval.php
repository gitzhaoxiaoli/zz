<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
*任务审批（批量）
*/

$approval_date = getgp('approval_date');
    $tids = explode(',', getgp('tids'));
    $tids = array_unique($tids);
    if (!$tids) print_json(array(
        'state' => 'no',
        'msg' => '请选择要审批的项目！'
    )); 
    $audit = load('audit');
    //审核项目 状态变更
    //$pids = array();
    $query = $db->query("SELECT id FROM sp_project WHERE tid IN (" . implode(',', $tids) . ") and deleted=0");
    while ($rt = $db->fetch_array($query)) {
       // $pids[] = $rt['id']; 
	   
	   
        $audit->edit($rt['id'], array(
            'status' => 2,
			'is_bao' => '0',
			'bao_date' => '',
			'bao_uid' => '',
		));
    }
    //审核任务 状态更更
    $db->update('task', array(
        'status' => 2,
    ) , array(
        'id' => $tids
    ));
	//添加日志
	foreach($tids as $tid){
		$eid=$db->getField('task','eid',array('id'=>$tid));  
		log_add($eid,0,'批量撤销任务审批，任务ID：'.$tid);
	}
	
  
  
    print_json(array(
        'state' => 'ok',
        'msg' => 'success'
    ));