<?php
!defined('IN_SUPU') && exit('Forbidden');
$cert_type=array("","结业证书","内部审核员证书");
$where = NULL;
if($_GET['name']) {
	$where .= " AND name LIKE '%".trim($_GET['name'])."%' ";
}

$where .= "  AND deleted='0'";
$total=$db->get_var("SELECT COUNT(*) total FROM sp_ot_traincert where 1 $where");
$pages = numfpage($total);
$certificate_list=$db->get_results("SELECT * FROM sp_ot_traincert where 1 $where $pages[limit]");

tpl('in_auditor/certificate_list');