<?php
!defined('IN_SUPU') && exit('Forbidden');
//合同查询列表
$status_0          = '未登记完';
$status_1          = '待评审';
$status_2          = '待审批';
$status_3          = '已审批';
$status_4          = '审核安排退回';
$fields            = $join = $where = $item_join = $item_where = $page_str = '';
$status            = (int) getgp('status');
$ep_name           = trim(getgp('ep_name'));
$accept_user       = trim(getgp('accept_user'));
$ctfrom            = getgp('ctfrom');
$audit_type        = getgp('audit_type');
$accept_date_start = getgp('accept_date_start');
$accept_date_end   = getgp('accept_date_end');
$ct_code           = trim(getgp('ct_code'));
$cti_code          = trim(getgp('cti_code'));
$audit_code        = trim(getgp('audit_code'));
$is_first          = getgp('is_first');
$iso               = getgp('iso');
$audit_ver         = getgp('audit_ver');
$areacode          = getgp('areacode');
$export            = getgp('export');
$ct_id        	   = trim(getgp('ct_id'));

if ($ep_name) {
    $_eids  = array();
    $_query = $db->query("SELECT eid FROM sp_enterprises WHERE ep_name LIKE '%" . str_replace('%', '\%', trim($ep_name)) . "%'");
    while ($rt = $db->fetch_array($_query)) {
        $_eids[] = $rt['eid'];
    }
    if ($_eids) {
        $where .= " AND ct.eid IN (" . implode(',', $_eids) . ")";
    } else {
        $where .= " AND ct.ct_id < -1";
    }
    unset($_eids, $_query, $rt, $_eids);
}
//省份
if ($areacode) {
    $pcode  = substr($areacode, 0, 2) . '0000';
    $_eids  = array(
        -1
    );
    $_query = $db->query("SELECT eid FROM sp_enterprises WHERE LEFT(areacode,2) = '" . substr($areacode, 0, 2) . "'");
    while ($rt = $db->fetch_array($_query)) {
        $_eids[] = $rt['eid'];
    }
    $where .= " AND ct.eid IN (" . implode(',', $_eids) . ")";
    $province_select = str_replace("value=\"$pcode\">", "value=\"$pcode\" selected>", $province_select);
}
if ($accept_user) { //受理人
    $create_uid = $db->get_var("SELECT id FROM sp_hr WHERE name = '$accept_user'");
    if ($create_uid) {
        $where .= " AND ct.create_uid = '$create_uid'";
    } else {
        $where .= " AND ct.ct_id < -1";
    }
}
if ($signe_name = getgp("signe_name")) {
    $where .= " AND ct.signe_name='$signe_name'";
    $signe_select = str_replace("value=\"$signe_name\"", "value=\"$signe_name\" selected", $signe_select);
}
//合同来源限制
$len = get_ctfrom_level(current_user('ctfrom'));
if ($ctfrom && substr($ctfrom, 0, $len) == substr(current_user('ctfrom'), 0, $len)) {
    $_len = get_ctfrom_level($ctfrom);
    $len  = $_len;
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
$where .= " AND ct.ctfrom >= '$ctfrom' AND ct.ctfrom < '$ctfrom_e'";
$ctfrom_select = str_replace("value=\"$ctfrom\"", "value=\"$ctfrom\" selected", $ctfrom_select);
$page_str .= '&ctfrom=' . $ctfrom;
unset($len, $_len);
if ($audit_type) { //审核类型
    $audit_type_select = str_replace("value=\"$audit_type\"", "value=\"$audit_type\" selected", $audit_type_select);
    $_ct_ids           = array();
    //$query             = $db->query("SELECT ct_id FROM sp_contract_item WHERE audit_type = '$audit_type'");
	$wheres = "AND audit_type = '$audit_type' AND iso_prod_type = 0 ";
    while ($rt = $db->find_result("contract_item",$wheres)) {
        $_ct_ids[] = $rt['ct_id'];
    }
    if ($_ct_ids) {
        $where .= " AND ct.ct_id IN (" . implode(',', $_ct_ids) . ")";
    } else {
        $where .= " AND ct.ct_id < -1";
    }
    unset($_ct_ids, $query, $rt);
}
if ($audit_ver) { //标准版本
    $_ct_ids = array();
    //$query   = $db->query("SELECT ct_id FROM sp_contract_item WHERE audit_ver = '$audit_ver'");
	$fields = "ct_id";
	$wheres = "AND audit_ver = '$audit_ver' AND iso_prod_type = 0 AND deleted = 0 ";
    while ($rt = $db->find_result("contract_item",$wheres,$fields)) {
        $_ct_ids[] = $rt['ct_id'];
    }
    if ($_ct_ids) {
        $where .= " AND ct.ct_id IN (" . implode(',', $_ct_ids) . ")";
    }
    unset($_ct_ids, $query, $rt);
}
if ($accept_date_start) { //受理时间 起
    $where .= " AND ct.accept_date >= '$accept_date_start'";
}
if ($accept_date_end) { //受理时间 止
    $where .= " AND ct.accept_date <= '$accept_date_end'";
}
if ($ct_code) { //合同编码
    $where .= " AND ct.ct_code = '$ct_code' and ct.deleted=0";
}
if ($cti_code) { //合同项目编码
	$wheres = "AND cti_code like '%$cti_code%' AND deleted=0 AND iso_prod_type = 0";
	$fields = "ct_id";
    $ct_id = $db->find_one("contract_item",$wheres,$fields);
    if ($ct_id) {
        $where .= " AND ct.ct_id = '$ct_id'";
    } else {
        $where .= " AND ct.ct_id < -1";
    }
    unset($ct_id);
}
if ($audit_code) { //审核代码
    $_ct_ids     = array();
    $audit_codes = explode('；', $audit_code);
    $where_arr   = array();
    foreach ($audit_codes as $code) {
        $where_arr[] = "audit_code LIKE '%$code%'";
    }
    $query = $db->query("SELECT ct_id FROM sp_contract_item WHERE " . implode(' OR ', $where_arr) ." AND iso_prod_type = 0 ");
    while ($rt = $db->fetch_array($query)) {
        $_ct_ids[] = $rt['ct_id'];
    }
    if ($_ct_ids) {
        $where .= " AND ct.ct_id IN (" . implode(',', $_ct_ids) . ")";
    } else {
        $where .= " AND ct.ct_id < -1";
    }
    unset($ct_ids, $audit_codes, $where_arr, $query, $rt);
}
if ($iso) { //认证体系
    $_ct_ids = array();
    $query   = $db->query("SELECT ct_id FROM sp_contract_item WHERE iso = '$iso' AND iso_prod_type = 0 ");
    while ($rt = $db->fetch_array($query)) {
        $_ct_ids[] = $rt['ct_id'];
    }
    if ($_ct_ids) {
        $where .= " AND ct.ct_id IN (" . implode(',', $_ct_ids) . ")";
    } else {
        $where .= " AND ct.ct_id < -1";
    }
}


$where .= " AND ct.deleted = '0'";
$status_0_tab = $status_1_tab = $status_2_tab = $status_3_tab = $status_4_tab = '';
${'status_' . $status . '_tab'} = ' ui-tabs-active ui-state-active';
$fields .= ",e.ep_name,approval_note";
$join = " LEFT JOIN sp_enterprises e ON e.eid = ct.eid";
//$join .= " LEFT JOIN sp_hr hr ON hr.id = ct.create_uid";
//统计各状态记录数
if (!$export) {
    $status_count = array(0,0,0,0);
    $query        = $db->query("SELECT ct.status,COUNT(*) total FROM sp_contract ct WHERE 1 $where AND iso_prod_type = 0 GROUP BY ct.status");
   while ($rt = $db->fetch_array($query)) {
        $status_count[$rt['status']] = $rt['total'];
    }
}
$where .= " AND ct.status = '$status' AND iso_prod_type = 0";
$pages     = numfpage($status_count[$status], 20, $url_param);
//列表
$contracts = $contract_items = array();
$sql       = "SELECT ct.* $fields FROM sp_contract ct $join WHERE 1 $where ORDER BY ct.create_date DESC $pages[limit]";
//echo $sql;
//exit;
$query     = $db->query($sql);
while ($rt = $db->fetch_array($query)) {
    if ('y' == $rt['is_first']) {
        $rt['is_first'] = '是';
    } else {
        $rt['is_first'] = '否';
    }
    $rt['ctfrom_V']          = f_ctfrom($rt['ctfrom']);
    $contracts[$rt['ct_id']] = chk_arr($rt);

}
if ($contracts) {
    $item_where .= " AND cti.ct_id IN (" . implode(',', array_keys($contracts)) . ") AND cti.deleted = 0";
    $sql   = "SELECT cti.* FROM sp_contract_item cti $item_join WHERE 1 $item_where";
    $query = $db->query($sql);
    while ($rt = $db->fetch_array($query)) {

        $contracts[$rt['ct_id']]['audit_types'][] = f_audit_ver($rt['audit_ver']) . "：" . f_audit_type($rt['audit_type']);
        $contracts[$rt['ct_id']]['use_codes'][]   = f_audit_ver($rt['audit_ver']) . "：" . $rt['audit_code'];
        $contracts[$rt['ct_id']]['cti_codes'][]   = $rt['cti_code'];
	//收费状态
	   
	    if($rt[audit_type]=='1004-1'){
			$rt[audit_type]='1002';
			
		}elseif($rt[audit_type]=='1004-2'){
			$rt[audit_type]='1003';
			
		}elseif($rt[audit_type]=='1007'){
			$rt[audit_type]='1001';
			
		}else{
			$rt[audit_type]=$rt[audit_type];
			 
		}
		$contracts[$rt['ct_id']]['is_finance']=$db->get_var("select c.is_finance from sp_contract_cost c  LEFT JOIN sp_contract_item cti ON c.ct_id=cti.ct_id WHERE c.ct_id='$rt[ct_id]' AND c.cost_type='$rt[audit_type]'");
    }
}
if (!$export) {
    tpl('contract/list');
} else {
    ob_start();
    tpl('xls/list_contract');
    $data = ob_get_contents();
    ob_end_clean();
    export_xls(${'status_' . $status} . '_合同列表', $data);
}