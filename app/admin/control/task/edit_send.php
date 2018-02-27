<?php
!defined('IN_SUPU') && exit('Forbidden');
//////////////////新增派人////////////////
//多体系派人
$tid       = getgp("tid");
$task_info = $db->get_row(" SELECT * FROM sp_task WHERE id=$_GET[tid]"); //取任务信息

 
//合同信息
$query     = $db->query("select ct_id,iso,use_code,cti_code,audit_code from sp_project where tid='$tid' and deleted=0 and iso_prod_type = 0 order by iso");
$ct_ids    = array();
$use_code  = array();
while ($r = $db->fetch_array($query)) {
    $ct_ids[$r[iso]] = $r[ct_id];
    $use_code[$r[iso]] = $r;  
	$log_cti_codes.=$r['cti_code'].';'; //日志使用
}

$finance_require = $is_site_V = "";
foreach ($ct_ids as $iso => $ct_id) {
    $ct_info = $db->find_one("contract"," AND ct_id='$ct_id' and deleted=0 and iso_prod_type = 0","finance_require,is_site" );
    if ($ct_info[finance_require])
        $finance_require .= $ct_info[finance_require] . " ";
    if ($ct_info[is_site])
        $is_site_V .= f_iso($iso) . ":是  ";
    else
        $is_site_V .= f_iso($iso) . ":否  ";
}

unset($ct_ids, $query);

 
//默认任务时间
if (!$_GET['auditor_id']) {
    if (mysql2date('H', $task_info['tb_date']) == '08') {
        $swxw08 = ' selected="selected"';
    } else {
        $swxw13 = ' selected="selected"';
    }
    if (mysql2date('H', $task_info['te_date']) == '12') {
        $swxw12 = ' selected="selected"';
    } else {
        $swxw17 = ' selected="selected"';
    }
    $auditor_info['taskBeginDate'] = mysql2date('Y-m-d', $task_info['tb_date']);
    $auditor_info['taskEndDate']   = mysql2date('Y-m-d', $task_info['te_date']);
}
$isos_list = $audit_ver = $audit_type = array();
$_query    = $db->query("SELECT id,iso,audit_ver,audit_type FROM sp_project where tid='$_GET[tid]' and deleted=0 and iso_prod_type = 0 order by iso");
while ($_r = $db->fetch_array($_query)) {
    $isos_list[$_r[id]] = $_r[iso];
    $audit_type[]       = $_r[audit_type];
    $audit_ver[]        = $_r[audit_ver];
}
unset($_query, $_r);
$pid = $db->get_results(" SELECT id FROM sp_project where tid=$_GET[tid]"); //审核项目主键
////////////删除派人信息////////////
if ($_GET['type'] == 'del') {


	//日记 删除派人 find_one
	$bf_str = $db->find_one('task_audit_team', array('id' => $_GET['id']));
	log_add($bf_str['eid'], $bf_str['uid'], '删除派人', NULL, serialize($bf_str));


    //删除派人明细表
    $db->update('task_audit_team', array('deleted' => 1), array('id' => $_GET['id']));

   showmsg("success","success","?c=task&a=edit_send&tid=$tid");
}
////////编辑派人信息///////////////
if ($id = $_GET['id']) { //显示需要编辑的信息
    $auditor_info = $db->find_one('task_audit_team', array(
        'id' => $id
    ));
    if (mysql2date('H', $auditor_info['taskBeginDate']) == '08') {
        $swxw08 = ' selected="selected"';
        $swxw13 = '';
    } else {
        $swxw13 = ' selected="selected"';
        $swxw08 = '';
    }
    //ECHO mysql2date('H', $auditor_info['taskEndDate'])== '12';
    if (mysql2date('H', $auditor_info['taskEndDate']) == '12') {
        $swxw12 = ' selected="selected"';
        $swxw17 = '';
    } else {
        $swxw17 = 'selected="selected"';
        $swxw12 = '';
    }
    $query = $db->query("SELECT * FROM `sp_task_audit_team` WHERE `uid` = '$auditor_info[uid]' AND `taskBeginDate` = '$auditor_info[taskBeginDate]' AND `taskEndDate` = '$auditor_info[taskEndDate]'");
    while ($rt = $db->fetch_array($query)) {
        $task_audit_team_list[$rt['iso']] = $rt;
    }
    $auditor_info['taskBeginDate'] = mysql2date('Y-m-d', $auditor_info['taskBeginDate']);
    $auditor_info['taskEndDate']   = mysql2date('Y-m-d', $auditor_info['taskEndDate']);
	
    if ($_POST) { 
        //更新派人详细表
        foreach ($_POST['iso'] as $k => $v) {
            //role 审核员角色 1004-1 是空
            if (!$v || !$_POST['qua_type'][$k])
                continue;
            if ($_POST['role'][$k] == '1001') {
                $f = $db->get_var("SELECT uid FROM `sp_task_audit_team` WHERE `tid` = '$tid'  AND `iso` = '$v' AND `role` = '1001' and deleted=0 and iso_prod_type = 0");
                if ($f and $f != $_POST['uid']) {
                    showmsg('一个体系只能有一个组长', "error", "?c=task&a=edit_send&tid=$tid#tab-contract");
                    exit;
                }
            }
            $p_info        = $db->get_row("SELECT id,iso,audit_type,audit_ver,ctfrom FROM `sp_project` WHERE `id` = '$k' ");
            $up_item       = array(
                'uid' => $_POST['uid'],
                'sort' => $_POST['sort'],
                'name' => $_POST[name],
             	 'ctfrom' => $p_info[ctfrom], //合同来源从合同项目表
				 
                'audit_ver' => $p_info[audit_ver],
                'audit_type' => $p_info[audit_type],
                'role' => $_POST['role'][$k],
                'witness' => $_POST['witness'][$k],
                'witness_person' => $_POST['witness_person'][$k],
                'qua_type' => $_POST['qua_type'][$k],
                'audit_code' => $_POST['audit_code'][$k],
                'use_code' => $_POST['use_code'][$k],
                'taskBeginDate' => $_POST['taskBeginDate'] . ' ' . $_POST['taskBeginTime'],
                'taskEndDate' => $_POST['taskEndDate'] . ' ' . $_POST['taskEndTime']
            );

            $_auditor_info = $db->find_one('task_audit_team', array('id' => $id));

			$af_str = $db->find_one('task_audit_team', array('id' => $id));//日记 取修改前

			//更新派人信息
            $db->update('task_audit_team', $up_item, array(
                'uid' => $_auditor_info[uid],
                "tid" => $tid,
                "iso" => $v
            ));

			
			//日记 更新派人
            $bf_str = $db->find_one('task_audit_team', array('id' => $id));
			log_add($_auditor_info['eid'], $_auditor_info[uid], '更新派人', serialize($af_str), serialize($bf_str));


        }
     
		
        unset($_POST, $_auditor_info, $auditor_info, $task_audit_team_list, $_GET['id']);
        $auditor_info['taskBeginDate'] = mysql2date('Y-m-d', $task_info['tb_date']);
        $auditor_info['taskEndDate']   = mysql2date('Y-m-d', $task_info['te_date']);
        $swxw08                        = ' selected="selected"';
        $swxw13                        = '';
        $swxw17                        = ' selected="selected"';
        $swxw12                        = '';
    }
}
if ($_POST) { //保存新增派人信息 
    if ($uid = $_POST['uid']) {
        //派人明细表 
        foreach ($_POST['iso'] as $k => $v) {
            if (!$v || !$_POST['qua_type'][$k])//必须选择组内身份和资格
                continue;
            if ($_POST['role'][$k] == '1001') {
                $f = $db->get_var("SELECT uid FROM `sp_task_audit_team` WHERE `tid` = '$tid'  AND `iso` = '$v' AND `role` = '1001' and deleted=0 and iso_prod_type = 0");
                if ($f and $f != $_POST['uid']) {
                    showmsg('一个体系只能有一个组长', "error", "?c=task&a=edit_send&tid=$tid#tab-contract");
                    exit;
                }
            }
            $p_info   = $db->get_row("SELECT id,iso,audit_type,audit_ver,ctfrom FROM `sp_project` WHERE `id` = '$k' ");
            $new_item = array(
                'eid' => $task_info['eid'],
                'tid' => $_GET['tid'],
                'pid' => $p_info['id'],
                'uid' => $uid,
                'sort' => $_POST['sort'],
                'name' => $_POST[name],
                'ctfrom' => $p_info[ctfrom],
                'audit_ver' => $p_info[audit_ver],
                'audit_type' => $p_info[audit_type],
                'iso' => $v,
                'role' => $_POST['role'][$k],
                'witness' => $_POST['witness'][$k],
                'witness_person' => $_POST['witness_person'][$k],
                'qua_type' => $_POST['qua_type'][$k],
                'audit_code' => $_POST['audit_code'][$k],
                'use_code' => $_POST['use_code'][$k],
                'taskBeginDate' => $_POST['taskBeginDate'] . ' ' . $_POST['taskBeginTime'],
                'taskEndDate' => $_POST['taskEndDate'] . ' ' . $_POST['taskEndTime']
            );
            $id	=	$db->insert('task_audit_team', $new_item);


			//日记 新增派人
            $bf_str = $db->find_one('task_audit_team', array('id' => $id));
			log_add($task_info['eid'], $uid, '新增派人', NULL, serialize($bf_str));

        }
    }
	//1待派人 2已派人
  	$_POST['status']=$_POST['status']?$_POST['status']:1;
    $db->update('task', array(
        'status' => $_POST['status']
    ), array(
        'id' => $_GET['tid']
    ));
    $task_info['status'] = $_POST['status'];
	if($task_info['status']==2)
    log_add($task_info['eid'],'','已派人，项目号：'.$log_cti_codes);
}

$action       = $_GET['id'] ? '修改' : '新增';
//任务已派人列表
$auditor_list = $db->get_results(" SELECT * FROM sp_task_audit_team  WHERE tid=$_GET[tid] AND deleted=0 and iso_prod_type = 0 order by sort,iso");

$change_note = $db->get_col("SELECT audit_note FROM sp_project WHERE tid = $tid AND deleted = 0");
$change_text = join("<br/>",array_unique($change_note));
/* 说明:派人后可以勾选提交 否则不能勾选 */
/* @zys 2016-7-6 */
if(empty($auditor_list)){
    $disabled = 'disabled';
}else{
    $disabled = '';
}
 
tpl();
?>
