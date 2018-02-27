<?php
$audit_job_array = array(
    0 => '兼职',
    1 => '专职',
    9 => '无',
);
$fields = $join = $where = '';
extract($_GET, EXTR_SKIP);


if ($name=trim($name)) {
    $where.= " AND hr.name like '%$name%' ";
}

if ($easycode=trim($easycode)) {
    $where.= " AND hr.easycode like '%$easycode%' ";
}


if ($areacode) { //省份搜索
    $pcode = substr($areacode, 0, 2) . '0000';
    $where.= " AND LEFT(areacode,2) = '" . substr($areacode, 0, 2) . "'";
    $province_select = str_replace("value=\"$pcode\">", "value=\"$pcode\" selected>", $province_select);
}
if ($audit_job || $audit_job == '0') {
    $where.= " AND audit_job = '$audit_job' ";
    $audit_job_select = str_replace("value=\"$audit_job\">", "value=\"$audit_job\" selected>", $audit_job_select);
}
if($s_date)
	$where .=" and s_date>='$s_date'";
if($e_date)
	$where .=" and s_date<='$e_date'";

$where.= " AND hr.deleted = 0";
$where.= " AND is_hire = '1' ";
if (!$export) {
	$total=$db->get_var("SELECT COUNT(*) FROM `sp_hr_experience` he LEFT JOIN sp_hr hr ON he.add_hr_id=hr.id  WHERE `type` = 'c' AND he.`deleted` = '0' $where ");
	$pages = numfpage($total);
}
$rt[num]=10;
$datas=array();
$_query=$db->query("SELECT he.*,hr.name,hr.audit_job FROM `sp_hr_experience` he LEFT JOIN sp_hr hr ON he.add_hr_id=hr.id  WHERE `type` = 'c' AND he.`deleted` = '0' $where  order by s_date $pages[limit]");
while($rt=$db->fetch_array($_query)){
	$rt[num]-=$rt[position];
	$rt[audit_job]=$audit_job_array[$rt[audit_job]];
	$rt[area]=read_cache("credit",$rt[area]);
	$datas[]=$rt;
}
if (!$export) {
    tpl();
} else {
    ob_start();
    tpl('xls/credit');
    $data = ob_get_contents();
    ob_end_clean(); 
    export_xls($hire_array[$is_hire] . '人员', $data);
}
?>
