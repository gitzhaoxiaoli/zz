<?php
$hire_array = array(
    1 => '在职',
    2 => '离职'
);
$fields = $join = $where = '';
extract($_GET, EXTR_SKIP);

$ {
    'status_' . $is_hire . '_tab'
} = ' ui-tabs-active ui-state-active';
$ct30 = (int)getgp('ct30');
if ($name) {
    $where.= " AND name like '%$name%' ";
}
if ($easycode) {
    $where.= " AND easycode like '%$easycode%' ";
}
 

if ($_GET['code']) {
    $where.= " AND code like '%". $_GET['code']."%' ";
}
//人员来源限制
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

$where.= " AND ctfrom >= '$ctfrom' AND ctfrom < '$ctfrom_e' ";

$ctfrom_select = str_replace("value=\"$ctfrom\">", "value=\"$ctfrom\" selected>", $ctfrom_select);

if ($areacode) { //省份搜索
    $pcode = substr($areacode, 0, 2) . '0000';
    $where.= " AND LEFT(areacode,2) = '" . substr($areacode, 0, 2) . "'";
    $province_select = str_replace("value=\"$pcode\">", "value=\"$pcode\" selected>", $province_select);
}
if ($audit_job || $audit_job == '0') {
    $where.= " AND audit_job = '$audit_job' ";
    $audit_job_select = str_replace("value=\"$audit_job\">", "value=\"$audit_job\" selected>", $audit_job_select);
}
$s_date=date("Y")."-01-01";
$e_date=date("Y")."-12-31";
if($fen){
$uids=$db->get_col("SELECT add_hr_id FROM `sp_hr_experience` WHERE `type` = 'c' AND `deleted` = '0'  and s_date>='$s_date' and s_date<='$e_date'");
$uids=array_unique($uids);
$uids=array_merge($uids,array(-1));
if($fen=='1')//10
{
	$where.= " AND id NOT IN (".join(",",$uids).")";
	
}else
	$where.= " AND id IN (".join(",",$uids).")";
}
$where.= " AND job_type LIKE '%1004%' AND is_hire = '1'";
if (!$export) {
$total=$db->get_var("SELECT COUNT(*) FROM sp_hr  WHERE 1 $where");
$pages = numfpage($total);
}
$sql = "SELECT * FROM sp_hr  WHERE 1 $where ORDER BY id DESC $pages[limit]";
$query = $db->query($sql);
while ($rt = $db->fetch_array($query)) {
   $rt['ctfrom'] = f_ctfrom($rt['ctfrom']);
    $rt['audit_job'] = f_audit_job($rt['audit_job']);
    $rt['areacode'] = f_region_province($rt['areacode']); //取省地址
    if ($rt['sex'] == '1') {
        $rt['sex'] = '男';
    } elseif ($rt['sex'] == '2') {
        $rt['sex'] = '女';
    } 
	$rt[num]=10;
	$_query=$db->query("SELECT position FROM `sp_hr_experience` WHERE `type` = 'c' AND `deleted` = '0' AND `add_hr_id` = '$rt[id]' and s_date>='$s_date' and s_date<='$e_date'");
	while($_r=$db->fetch_array($_query)){
		$rt[num]-=$_r[position];
		
	}
    $users[$rt['id']] = $rt;
}
if (!$export) {
    tpl('hr/alist');
} else {
    ob_start();
    tpl('xls/list_hr');
    $data = ob_get_contents();
    ob_end_clean(); 
    export_xls($hire_array[$is_hire] . '人员', $data);
}
?>
