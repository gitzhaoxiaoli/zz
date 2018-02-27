<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
*审核员审核任务123

88888888888888
*/
extract( $_GET, EXTR_SKIP );
$audit_finish = (int)$audit_finish;
$audit_finish_0_tab = $audit_finish_1_tab  = '';
${'audit_finish_'.$audit_finish.'_tab'} = ' ui-tabs-active ui-state-active';
$fields = $join = $where = '';
// 评定结果 1：通过 3：不通过 4：待整改 5:已整改
$rect_array=array("未评定","通过","","不通过","<span style='color:#00f'>待整改</span>","已整改");
//搜索条件
$ep_name = trim($ep_name);

if( $ep_name ){
	$_eids = array();
	$query = $db->query("SELECT eid FROM sp_enterprises WHERE ep_name LIKE '%{$ep_name}%'");
	while( $rt = $db->fetch_array( $query ) ){
		$_eids[] = $rt['eid'];
	}      
	if( $_eids ){
		$where .= " AND tat.eid IN (".implode(',',$_eids).")";
	} else {
		$where .= " AND tat.id < -1";
	}
}
$person = trim($person);
if( $person ){
	$_eids = array();
	$query = $db->query("SELECT eid FROM sp_enterprises WHERE person LIKE '%$person%'");
	while( $rt = $db->fetch_array( $query ) ){
		$_eids[] = $rt['eid'];
	}
	if( $_eids ){
		$where .= " AND tat.eid IN (".implode(',',$_eids).")";
	} else {
		$where .= " AND tat.id < -1";
	}
}
if($ct_code=trim($ct_code)){
	$where .=" AND p.ct_code LIKE '%".$ct_code."%'";

}
if($cti_code=trim($cti_code)){
	$where .=" AND p.cti_code like '%$cti_code%'";

}
if($use_code=trim($use_code)){

	$where .="  and tat.use_code like '%$use_code%'";

}
if($sh_date_s){
	$where .= " AND tat.taskBeginDate > '$sh_date_s'";

}
if($sh_date_e){
	$where .= " AND tat.taskEndDate < '$sh_date_e'";

}

//$join .= " LEFT JOIN sp_task_auditor ta ON ta.id = tat.auditor_id";
$join .= " LEFT JOIN sp_project p ON p.id = tat.pid";
//$join .= " LEFT JOIN sp_assess a ON a.pid = tat.pid";
$join .= " LEFT JOIN sp_task t ON t.id = tat.tid";
$join .= " LEFT JOIN sp_enterprises e ON e.eid = t.eid";


$fields .= "tat.*,p.prod_id,p.audit_type,p.cti_code,p.ct_code,p.ct_id,p.id,p.comment_pass_date,p.comment_pass,p.sp_date,p.sv_note,t.jh_sp_date,p.pd_type,t.jh_sp_note,t.jh_sp_name,t.bufuhe,t.upload_file_date,t.jh_sp_status,e.ep_name,t.last_rect_date";

$where .= "  AND tat.deleted =  '0' AND tat.role != ''";
if($_SESSION['userinfo']['username']!='admin'){
$where .= " AND tat.uid = '".current_user('uid')."'";
$where .=" AND t.status=3";
$where .=" AND p.deleted=0";
}

//$where .= " AND tat.taskBeginDate > '2012-01-01'";


//状态标签

$finish_total = array(0,0);
if( !$export ){
	$finish_total[0]=$db->get_var("SELECT  COUNT(*) total FROM sp_task_audit_team tat $join WHERE 1 $where AND p.pd_type != '1'" );
	$finish_total[1]=$db->get_var("SELECT  COUNT(*) total FROM sp_task_audit_team tat $join WHERE 1 $where AND p.pd_type= '1'" );
	$pages = numfpage( $finish_total[$audit_finish] );
}
if($audit_finish=='0')
	$where.=" AND p.pd_type!=1";
else
	$where.=" AND p.pd_type=1";
//时间限制审核结束4个月
$date=thedate_add(date("Y-m-d H:i:s"),-4,"month");
//当前审核员的派人信息
$projects = $pids = array();
$sql = "SELECT $fields FROM sp_task_audit_team tat $join WHERE 1 $where order by tat.taskBeginDate  $pages[limit]";

$query = $db->query( $sql );
    while( $rt = $db->fetch_array( $query ) ){
	if($audit_finish=='1'){
		$rt[rect_date]="";
	}
	if($rt[last_rect_date]!='0000-00-00'){
		//$rt[rect_date]= $rt[last_rect_date] . "(" . mkdate(date("Y-m-d"),$rt[last_rect_date]) . ")";
		$rt[rect_date]= $rt[last_rect_date] ;
		$rt[rect_date1]=mkdate(date("Y-m-d"),$rt[last_rect_date]) ;
	}
	$rt['upload_file_date'] = date('Y-m-d',strtotime($rt['upload_file_date']));
	
	$rt[audit_num]=mkdate($rt[taskBeginDate],$rt[taskEndDate]);
	if($rt[taskEndDate]<=date("Y-m-d"))
		$rt[num]=mkdate($rt[taskEndDate],date("Y-m-d")."17:00:00");
	$rt['ctfrom'] = f_ctfrom( $rt['ctfrom'] );
	$rt['audit_ver_V'] = f_audit_ver( $rt['audit_ver'] ) ; 
	$rt['audit_ver_V'] .=  '：'.f_audit_type( $rt['audit_type'] );
	$rt['audit_code']=LongToBr($rt['audit_code'],array('；','；'));
	$rt['use_code']=LongToBr($rt['use_code'],array('；','；'));
	if($rt['comment_pass'] == '1' ){
		$rt['comment_pass_V']='(是)';
		}
	elseif($rt['comment_pass'] == '2' )
		$rt['comment_pass_V']='(否)';
	if($rt[taskEndDate]<$date)
		$rt[f]=0;
	else
		$rt[f]=1;
	if(current_user("uid")==1 || $audit_finish==0)
		$rt[f]=1;
	if(!$audit_finish)
		if($rt['bufuhe']){
			if($rt[num]>40)
				$rt['color']="red";
		}else{
			if($rt[num]>25)
				$rt['color']="red";
		
		}
	if($rt[upload_file_date] && $rt[upload_file_date]>"0000-00-00 00:00:00")
		$rt['color']="";
	$rt[rect]=$rect_array[$rt[pd_type]];
	$rt[plan_status]=$rt[jh_sp_status]?"YES":"NO";
	$projects[] = chk_arr($rt);

}


if( !$export ){
		tpl();
	} else {//导出客户文档列表
		ob_start();
		tpl( 'xls/list_task_auditor' );
		$data = ob_get_contents();
		ob_end_clean();
		export_xls( '审核员任务列表', $data );
	}


?>