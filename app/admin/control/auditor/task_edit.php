<?php
!defined('IN_SUPU') && exit('Forbidden');

//存档目录
// require_once CONF . '/cache/arctype.cache.php';
// require_once CONF . '/cache/fl_question.cache.php';

//审核任务上传文档
$allow_ftype_select = "";
foreach ($arctype_array as $k => $item) {
    if ($k >= 3001 and $k < 4000)
        $allow_ftype_select .= "<option value=\"$k\">$item[name]</option>";
} 
//评分操作
if($_POST['access']){
	load('proj.access')->save_more($_POST['access']);  
	showmsg('success', 'success', "?c=auditor&a=task_edit&tid=$_GET[tid]".'#tab-result');
}
 
if($_GET['access_result_id']){
	 echo 'aaaaaa';
	 $db->update('access_result',array('deleted'=>1),array('id'=>$_GET['access_result_id']));
	 echo $db->sql;
}

$file_arr = array(
     "1"=>"审核任务书",
	 "2"=>"审核经历记录表",
	 "3"=>"认证人员公正性、保密性承诺",
	 "4"=>"知识产权文件评审报告",
	 "5"=>"第一阶段审核问题清单",
     "6"=>"第一阶段审核报告",
     "7"=>"签到表",
     "8"=>"现场审核通知",
     "9"=>"知识产权管理标准认证审核计划",
     "10"=>"核查记录表",
     "11"=>"不符合项报告",
	 "12"=>"观察项建议项清单",
	 "13"=>"审核报告",
     "14"=>"证书内容确认件",
     "15"=>"廉洁承诺书",
);

$tid      = getgp('tid');

$ct_id    = getgp('ct_id');
if ($tid) {
    //审核文档
    $sql  = "select * from sp_attachments where tid='$tid' ORDER BY `sort`";
    $res  = $db->query($sql);
    $span = array();
    while ($rt = $db->fetch_array($res)) {
        $rt['uid']              = f_username($rt['create_uid']);
        $enterprises_archives[] = $rt;
        $span[$rt['sort']]      = $rt[name];
    }
	$plan_check=$file_check='';
    $task_info      = $db->find_results("task","AND id='$tid'"," *");
//  p($task_info);
// exit; 
    $back_note = $task_info[0]['back_note'];
	if($task_info[0]['upload_plan_date'] and $task_info[0]['upload_plan_date']!="0000-00-00 00:00:00")
		$plan_check="checked";
	if($task_info[0]['upload_file_date'] and $task_info[0]['upload_file_date']!="0000-00-00 00:00:00")
		$file_check="checked";
	$eid=$task_info[0]['eid'];
    $ep_name   = $db->get_var("select * from sp_enterprises where eid='$eid' ");
    //审核计划通知书
    $task_docs = array();
    $query     = $db->query("SELECT id,name FROM sp_attachments WHERE ct_id = '$task_info[0][ct_id]' AND ftype = '1004-2'");
    while ($rt = $db->fetch_array($query)) {
        $task_docs[$rt['id']] = $rt;
    }
    //审核项目
    $project = array();
    $query   = $db->query("SELECT scope,id,audit_ver,audit_type,pd_type,cti_id,use_code FROM sp_project WHERE tid = '$tid' and iso_prod_type = 0 ");
    while ($rt = $db->fetch_array($query)) {
        $rt['audit_ver_V']   = f_audit_ver($rt['audit_ver']);
        $rt['audit_type_V']  = f_audit_type($rt['audit_type']);
        $pd_type = $rt['pd_type'];
		//计算合同项目id
		$cti_ids[$rt['cti_id']]=$rt['cti_id'];
        $projects[$rt['id']] = $rt;
    }
	//评分功能 
 
    //当前审核员 当前任务 审核的体系
    $isos  = array();
    $query = $db->query("SELECT iso FROM sp_task_audit_team WHERE tid = '$tid' AND uid = '" . current_user('uid') . "' AND role != ''");
    while ($rt = $db->fetch_array($query)) {
        $isos[] = $rt['iso'];
    }
    //已上传的文档
    //获取合同的体系
    $ct_isos      = array();
    $query        = $db->query("SELECT iso FROM sp_contract_item WHERE ct_id = '$ct_id' AND deleted = 0 AND iso_prod_type = 0 ");
    while ($rt = $db->fetch_array($query)) {
        $ct_isos[] = $rt['iso'];
    }
    $ct_isos   = array_unique($ct_isos);
    $where_arr = array();
    foreach ($ct_isos as $iso) {
        if ('A01' == $iso)
            $where_arr[] = "iso & 1";
        elseif ('A02' == $iso)
            $where_arr[] = "iso & 2";
        elseif ('A03' == $iso)
            $where_arr[] = "iso & 4";
        elseif ('F' == $iso)
            $where_arr[] = "iso & 8";
    }
    unset($iso);
    //审核要求
    $task_note     = $db->get_results("select step2,step3 from sp_task_note where 1 and tid='{$tid}'");
    $task_note1     = $db->get_results("select jh_re_note,jh_sp_note,last_rect_date from sp_task WHERE 1 and id='{$tid}'");
	foreach ($task_note1 as $key => $task) {
        
    }

    extract($task_note);
	extract($task_note1);
    //获取要上传的文档列表
    $xq_attachs    = array();
    if ($where_arr) {
        $query = $db->query("SELECT id,filename FROM sp_settings_attach WHERE 1 AND (" . implode(' OR ', $where_arr) . ") AND deleted = 0 AND type='audit_task_upload' order by vieworder ASC ");
        while ($rt = $db->fetch_array($query)) {
            $xq_attachs[$rt['id']] = $rt['filename'];
        }
    }
    //判断是组长还是审核员
    $query = $db->query("SELECT role FROM sp_task_audit_team WHERE tid = '$tid' AND uid = '" . current_user('uid') . "' AND role != ''");
    while ($rt = $db->fetch_array($query)) {
        if ($rt[role] == '1001')
		{
	  $auditor_role = 1;
	   }
            
    }
    if (current_user("uid") == '1')
	{
		$auditor_role = 1;
		//评定问题
       $assess_notes = $db->get_results("select * from sp_assess_notes WHERE 1 AND tid='$tid' AND deleted='0' ORDER BY id");
    }else{
	   $assess_notes = $db->get_results("select * from sp_assess_notes WHERE 1 AND tid='$tid' AND deleted='0' AND fl_question = '005' ORDER BY id");
    }
        
    !$auditor_role && $_where = " AND name='" . current_user("name") . "'";
    $report = $db->get_results("SELECT * FROM `sp_auditor_report` WHERE `tid` = '$tid' AND `deleted` = '0' $_where order by name");
    if ($rid = getgp("rid")) {
        $r_row = $db->get_row("SELECT * FROM `sp_auditor_report` WHERE id=$rid");
        extract($r_row, EXTR_SKIP);
        unset($r_row);
    }

    $form_file = $a_link = "";
    foreach ($file_arr as $key => $value) {
        $a_link .= "<li><a href='?c=doc&a=$key&tid=$tid&eid=$eid&ct_id=$ct_id'>$key $value</a></li>";
    }

	//仅上传
    $file_arr[16] = '资料整改';
	$file_arr[17] = '企业最新体系文件';

    foreach ($file_arr as $k => $val) {
        $form_file .= '<tr><td width="300">';
        $form_file .= $k . " " . $val;
        $form_file .= "</td><td>";
        $form_file .= '<input type="hidden" name="sort[]" value="' . $k . '"/><input type="file" name="archive[]" />';
        $form_file .= "</td><td><span>";
        if ($span[$k])
            $form_file .= $span[$k] . "(已上传)";
        else
            $form_file .= "无";
        $form_file .= "</span><br/></td></tr>";

		// $a_link .= "<li><a href='?c=doc&a=$k&tid=$tid'>$k $val</a></li>";
    }
    $query = $db->query("SELECT * FROM sp_task WHERE id = '$tid' AND iso_prod_type = 0");
    while ($rt = $db->fetch_array($query)) {
        $tb_h    = date('G', strtotime($rt[tb_date]));
        $te_h    = date('G', strtotime($rt[te_date]));

        $rt[tb_date] = mysql2date('Y-m-d', $rt[tb_date]);
        $rt[te_date] = mysql2date('Y-m-d', $rt[te_date]);
        $rt[wsqs_date] = mysql2date('Y-m-d', $rt[wsqs_date]);
        if($rt[tb_date] == '1970-01-01'){
            $rt[tb_date] = '';
        }
        if($rt[te_date] == '1970-01-01'){
            $rt[te_date] = '';
        }
        if($rt[wsqs_date] == '1970-01-01'){
            $rt[wsqs_date] = '';
        }
        if(empty($rt[te_date])){
            $te_h = '17';
        }
        // p($rt[te_date].'q');die;
        $t_date[] = $rt;
        ${'bm_' . $tb_h} = ' selected';
        ${'em_' . $te_h} = ' selected';
    }
    tpl('auditor/task_edit');
}
?>