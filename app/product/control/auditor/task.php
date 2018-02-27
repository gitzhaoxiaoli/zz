    <?php
!defined('IN_SUPU') && exit('Forbidden');
/*
*审核员审核任务
*/
extract( $_GET, EXTR_SKIP );
$audit_finish = (int)$audit_finish;
$audit_finish_0_tab = $audit_finish_1_tab  = '';
${'audit_finish_'.$audit_finish.'_tab'} = ' ui-tabs-active ui-state-active';
$fields = $join = $where = '';
//搜索条件
/* $ep_name = trim($ep_name);

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

} */
//if($audit_type){
	
	//$where .= " AND t.audit_type like '$audit_type%'";
//}
if($sh_date_s){
	$where .= " AND t.tb_date >= '$sh_date_s'";

}
if($sh_date_e){
	$where .= " AND t.tb_date <= '$sh_date_e'";

}
if($task_code){
	$where .= " AND t.task_code = '$task_code'";
}
if($fac_code=trim($fac_code)){
    $res = $db->find_one("enterprises",array('fac_code'=>$fac_code),"eid");
    $where .= "AND tat.eid = '$res[eid]'";
}

if($ep_prod_name){
	$where .= " AND e.ep_name like '%$ep_prod_name%'";
}

$join .= " LEFT JOIN sp_task t ON t.id = tat.tid";
$join .= " LEFT JOIN sp_enterprises e ON e.eid = t.eid";


$fields .= "tat.*,t.tb_date,t.te_date,t.task_code,t.bufuhe,t.ctfrom,e.ep_name ep_prod_name";

$where .= "  AND tat.deleted =  '0'";
// $where .= " AND tat.role != ''";
if(current_user('uid')!=1)
$where .= " AND tat.uid = '".current_user('uid')."'";
$where .=" AND t.status=3";
$where .= " AND tat.iso_prod_type = 1";


//状态标签

$finish_total = array(0,0);
if( !$export ){
	$finish_total[0]=$db->get_var("SELECT  COUNT(*) total FROM sp_task_audit_team tat $join WHERE 1 $where AND t.is_finish != '1'" );
	$finish_total[1]=$db->get_var("SELECT  COUNT(*) total FROM sp_task_audit_team tat $join WHERE 1 $where AND t.is_finish= '1'" );
	$pages = numfpage( $finish_total[$audit_finish] );
}
$where.=" AND t.is_finish='$audit_finish'";
//当前审核员的派人信息
$projects = array();
$sql = "SELECT $fields FROM sp_task_audit_team tat $join WHERE 1 $where order by tat.taskBeginDate  $pages[limit]";

$query = $db->query( $sql );
    while( $rt = $db->fetch_array( $query ) ){
	$rt['ctfrom'] = f_ctfrom($rt['ctfrom']);
	$rt[tb_date] = substr($rt[tb_date],0,10);
	$rt[te_date] = substr($rt[te_date],0,10);
	/* 说明:查出project表检查类型 */
	/* @zys 2016-5-23 */
	$result=$db->get_results("select * from sp_project WHERE 1 AND tid=$rt[tid] AND deleted='0'");
	foreach ($result as $key => $value) {
		$project = $value;
	}
	$project['audit_type_V'] = f_audit_type( $project['audit_type'] );
	// echo $db->sql;
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