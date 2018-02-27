<?php
!defined('IN_SUPU') && exit('Forbidden');
// 财务收费明细
 require "data/cache/audit_type.cache.php";
$audit_type_select = '';
foreach($audit_type_array as $k=> $v){
	if(!in_array($k,array("1002","1003","1007","1008","1010","99")))
		$audit_type_select .= "<option value=\"$k\">$v[name]</option>";
}
$data = array(array('序号','认证申请编号','受理日期','工厂检查任务编号','产品类别','工厂检查日期','生产企业名称','检查类型','申请费','翻译费','初始工厂检查费','批准与注册费','产品检测费','实验室名称','证书工本费','监督检查费',' 年金','其他','业务来源','到款时间'));
// $c_type = array("申请费","注册费","年金","翻译费","工本费","其他","初次工厂检查费","监督工厂检查费","检测费");
extract($_GET, EXTR_SKIP);
$fileds = $join = $where = '';
if($ep_name = trim($ep_name)){
	$eid = $db->getCol("enterprises","eid",array('ep_name'=>$ep_name));
	array_push($eid,-1);
    $where .= "AND ep_prod_id in (".join(",",$eid).")";
}

if($cti_code=trim($cti_code)){
    $where .= " AND cti_code = '$cti_code'";

}

if($prod_id=trim($prod_id)){
    $where .= " AND prod_id = '$prod_id'";

}


if($test_org_name=trim($test_org_name)){
    $where .= " AND test_org_name = '$test_org_name'";

}


//合同来源限制
$len = get_ctfrom_level(current_user('ctfrom'));
if ($ctfrom && substr($ctfrom, 0, $len) == substr(current_user('ctfrom') , 0, $len)) {
    $_len = get_ctfrom_level($ctfrom);
    $len = $_len;
} else {
    $ctfrom = current_user('ctfrom');
}
switch ($len) {
    case 2:
        $add = 1000000;
        break;

    case 4:
        $add = 10000;
        break;

    case 6:
        $add = 100;
        break;

    case 8:
        $add = 1;
        break;
}
$ctfrom_e = sprintf("%08d", $ctfrom + $add);
//$where .= " AND p.ctfrom >= '$ctfrom' AND p.ctfrom < '$ctfrom_e'";
$ctfrom_select = str_replace("value=\"$ctfrom\"", "value=\"$ctfrom\" selected", $ctfrom_select);
if ($dk_date_start) {
    $where.= " AND dk_date >= '$invoice_date_start'";
}
if ($dk_date_end) {
    $where.= " AND dk_date <= '$invoice_date_end'";
}

if($accept_date_s){
	$cti_ids = $db->get_col("SELECT * FROM `sp_contract_item` WHERE `iso_prod_type` = '1' AND `deleted` = '0' AND  `approval_date` >= '$accept_date_s'");
	array_push($cti_ids,-1);
	$where .= " AND cti_id IN (".join(",",$cti_ids).")";
}
if($accept_date_e){
	$cti_ids = $db->get_col("SELECT * FROM `sp_contract_item` WHERE `iso_prod_type` = '1' AND `deleted` = '0' AND `approval_date` <= '$accept_date_e' ");
	array_push($cti_ids,-1);
	$where .= " AND cti_id IN (".join(",",$cti_ids).")";
}
if($tb_date_s){
	$tids = $db->get_col("SELECT * FROM `sp_task` WHERE `iso_prod_type` = '1' AND `deleted` = '0' AND  `tb_date` >= '$tb_date_s'");
	array_push($tids,-1);
	$where .= " AND tid IN (".join(",",$tids).")";
}
if($tb_date_e){
	$tids = $db->get_col("SELECT * FROM `sp_task` WHERE `iso_prod_type` = '1' AND `deleted` = '0' AND `tb_date` <= '$tb_date_e' ");
	array_push($tids,-1);
	$where .= " AND tid IN (".join(",",$tids).")";
}

if($audit_type){
   
    $where .= "AND audit_type = '$audit_type'";
	$audit_type_select = str_replace("value=\"$audit_type\"","value=\"$audit_type\" selected",$audit_type_select);
}



$where .=" and pc.deleted = 0";
/*列表*/
if (!$export) {
    $total = $db->get_var("SELECT COUNT(*) FROM sp_project_cost pc LEFT JOIN sp_project p ON pc.pid = p.id WHERE 1 $where order by pc.id desc");
    $pages = numfpage($total);
}
$sql = "SELECT pc.*,p.ep_prod_id,p.cti_code,p.audit_type,p.cti_id,p.ctfrom,p.tid,p.prod_id,p.test_org_name FROM sp_project_cost pc LEFT JOIN sp_project p ON pc.pid = p.id WHERE 1 $where order by pc.id desc $pages[limit]";

$res = $db->query($sql);
$i = 1;
while ($rt = $db->fetch_array($res)) {
    $rt['ctfrom'] = f_ctfrom($rt['ctfrom']);
	if($rt[audit_type] == '1001'){
		$rt[c_cost] = $rt[inspection];
	}else{
		$rt[j_cost] = $rt[inspection];
	}
	$rt[audit_type] = f_audit_type($rt[audit_type]);
    $rt['ep_prod_name'] = getEpName($rt[ep_prod_id]);
	$rt['accept_date'] = $db->getField("contract_item","approval_date",array("cti_id"=>$rt[cti_id]));
	if($rt['tid']){
		$t_info = $db->find_one("task",array("id"=>$rt['tid']),"task_code,tb_date");
		$rt['task_code'] = $t_info['task_code'];
		$rt['tb_date'] = mysql2date("Y-m-d",$t_info['tb_date']);
	}
	$rt = chk_arr($rt);
    $datas[$rt['id']] = $rt;
	if ($export) {
		$data[] = array($i++,$rt['cti_code'],$rt['accept_date'],$rt['task_code'],$rt['prod_id'],$rt['tb_date'],$rt['ep_prod_name'],$rt['audit_type'],$rt['application'],$rt['translation'],$rt['c_cost'],$rt['app_reg'],$rt['testing'],$rt['test_org_name'],$rt['certificate'],$rt['j_cost'],$rt['annuity'],$rt['others'],$rt['ctfrom'],$rt['dk_date']);
	}
}
    //收费类型
    $cost_type_select=f_select('cost_type',$cost_type);
if (!$export) {
    tpl();
} else {
    // exit(p($data));
    export_excel($data,'费用统计报表');
}

