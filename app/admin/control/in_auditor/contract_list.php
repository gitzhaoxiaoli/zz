<?php
!defined('IN_SUPU') && exit('Forbidden');
$status = (int)getgp('status');
${"tab_".$status}="ui-tabs-active ui-state-active";
if($_GET['contract_no']) {
	$where .= " AND contract_no LIKE '%".trim($_GET['contract_no'])."%' ";
}
if($_GET['name']) {
	
	$_eid=$db->get_col("SELECT id FROM sp_ot_basedata WHERE name like '%$_GET[name]%'");
	$_eid=array_merge($_eid,array(-1));
	$where .= " AND brand_id in (".join(",",$_eid).")";
}
$where .= " AND data_for='4' AND deleted='0'";


$t_total = $db->get_var("SELECT COUNT(*) total FROM sp_ot_contract WHERE 1 $where");
$pages = numfpage($t_total);

$dealer_list=$db->get_results("SELECT * FROM sp_ot_contract where 1 $where order by id desc $pages[limit]");

foreach($dealer_list as $k=>$v){
	$dealer_list[$k]=$v;
	$dealer_list[$k]['name']=$db->get_var("SELECT name FROM sp_ot_basedata WHERE id='$v[brand_id]'");
	//echo $dealer_list['class_arr'];
	$dealer_list[$k]['class_str']=f_checkbox('class',$v['class_arr']);

}


tpl('in_auditor/contract_list');


