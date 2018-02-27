<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
*审核经历查询、已派人项目查询
*/
set_time_limit(0);
$task_status = array(
0	=> '未安排',
1	=> '待派人',
2	=> '待审批',
3	=> '已审批'
);

$fields = $where = $join = '';

/* 搜索开始 */
extract($_GET);

/* if($name=trim($name)){

	$uid=$db->get_var("select id from sp_hr where name='$name' and is_hire='1' and deleted='0'");
	$qua='注册时间：';
	$query=$db->query("SELECT * FROM `sp_hr_qualification` WHERE `uid` = '$uid' AND `status` = '1' order by iso");
	while($r=$db->fetch_array($query)){
		$qua.=f_iso($r[iso]).":".$r[s_date]."  ";


	}

}
 */
$is_leader_radio='<input type="radio"  name="is_leader" value="1"/>是<input type="radio"  name="is_leader" value="2"/>否';
if($is_leader){
	$is_leader_radio=str_replace("value=\"$is_leader\"","value=\"$is_leader\" checked",$is_leader_radio);
	$_auditor_ids = array();
	if($is_leader==1)
		$where .=" and role='1001'";
	else
		$where .=" and role!='1001'";
  

	//p($_eids);
}

if( $ep_name = trim($ep_name) ){
	$where .= " AND e.ep_name like '%$ep_name%'";
}
if($fac_code=trim($fac_code)){
    $res = $db->find_one("enterprises",array('fac_code'=>$fac_code),"eid");
    $where .= "AND ta.eid = '$res[eid]'";
}

//省份
if( $areacode=getgp("areacode") ){
	$pcode = substr($areacode,0,2) . '0000';
	$_eids = array(-1);
	$_query = $db->query("SELECT eid FROM sp_enterprises WHERE LEFT(areacode,2) = '".substr($areacode,0,2)."'");
	while( $rt = $db->fetch_array( $_query ) ){
		$_eids[] = $rt['eid'];
	}
	$where .= " AND ta.eid IN (".implode(',',$_eids).")";
	unset( $_eids, $_query, $rt, $_eids );
	
	$province_select = str_replace( "value=\"$pcode\">", "value=\"$pcode\" selected>" , $province_select );
}


if( $name = trim($name) ){
	
	$where .= " AND ta.name like '%$name%'";
}

if( $task_code = trim($task_code) ){
	$where .= " AND t.task_code like '%$task_code%'";
}


if( $audit_type ){
	$where .= " AND t.audit_type = '$audit_type'";
	$audit_type_select = str_replace( "value=\"$audit_type\">", "value=\"$audit_type\" selected>" , $audit_type_select );

}
if($audit_start_s){
	$where .= " AND ta.taskBeginDate >= '$audit_start_s 00:00:00' ";
}
if($audit_start_e){
	$where .= " AND ta.taskBeginDate <= '$audit_start_e 23:00:00' ";
}
if($audit_end_s){
	$where .= " AND ta.taskEndDate >= '$audit_end_s 00:00:00' ";
}
if($audit_end_e){
	$where .= " AND ta.taskEndDate <= '$audit_end_e 23:00:00' ";
}
if($role=getgp("role")){
	$where .= " AND ta.role = '$role' ";
}
if($qua_type=getgp("qua_type")){
	$where .= " AND ta.qua_type = '$qua_type' ";
}
//合同来源限制
$len = get_ctfrom_level( current_user( 'ctfrom' ) );

if( $ctfrom && substr( $ctfrom, 0, $len ) == substr( current_user( 'ctfrom' ), 0, $len ) ){
	$_len = get_ctfrom_level( $ctfrom );
	$len = $_len;
}else {
	$ctfrom = current_user( 'ctfrom' );
}
switch( $len ){
	case 2	: $add = 1000000; break;
	case 4	: $add = 10000; break;
	case 6	: $add = 100; break;
	case 8	: $add = 1; break;
}
$ctfrom_e = sprintf("%08d",$ctfrom+$add);
if( '01000000' != $ctfrom )
	$where .= " AND ta.ctfrom >= '$ctfrom' AND ta.ctfrom < '$ctfrom_e'";

$ctfrom_select = str_replace( "value=\"$ctfrom\"", "value=\"$ctfrom\" selected" , $ctfrom_select );
$fields .= "ta.*,t.task_code,e.ep_name,e.person,e.ctfrom,e.person,e.person_tel,e.eid";
// $fields .=",p.cti_code,p.ct_id,p.prod_ver,p.use_code code";

$join .= " LEFT JOIN sp_enterprises e ON e.eid = ta.eid";
$join .= " LEFT JOIN sp_task t ON t.id = ta.tid";
// $join .= " LEFT JOIN sp_project p ON (p.id = ta.pid AND p.iso=ta.iso)";
// $join .= " JOIN sp_contract_item sc ON p.cti_id = sc.cti_id";

$where .= " AND ta.deleted = '0'";
$where .= " AND ta.data_for = '0' AND ta.iso_prod_type = 1";
if( !$export ){
	$total = $db->get_var("SELECT COUNT(*) FROM sp_task_audit_team ta $join  WHERE 1 $where ");
	$pages = numfpage( $total);
}

$resdb = $aids = array();
$sql = "SELECT $fields FROM sp_task_audit_team ta $join WHERE 1 $where order by ta.id desc $pages[limit]" ;
$query = $db->query( $sql );
while( $rt = $db->fetch_array( $query ) ){
	$rt['ctfrom_V'] = f_ctfrom( $rt['ctfrom'] );
	$rt['taskBeginDate'] = mysql2date( 'Y-m-d H:i', $rt['taskBeginDate'] );
	$rt['taskEndDate'] = mysql2date( 'Y-m-d H:i', $rt['taskEndDate'] );
	//获取组长信息
	$rt['leader']=$db->find_var('task_audit_team',array('tid'=>$rt['tid'],'role'=>'1001','deleted'=>0),'name');
    $rt[audit_type]=f_audit_type( $rt['audit_type'] );
    $rt['role']=read_cache('audit_role',$rt['role']);
	$resdb[$rt['id']] = $rt;
	
}
//p($resdb);
if( !$export ){
	tpl();
} else {
	ob_start();
	tpl( 'xls/project_send_query' );
	$data = ob_get_contents();
	ob_end_clean();

	export_xls( '项目派人查询', $data );
}
?>