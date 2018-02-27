<?php
!defined('IN_SUPU') && exit('Forbidden');
$join='';
$statu = (int)getgp('statu');
${"tab_".$statu}="ui-tabs-active ui-state-active";
if($_GET['name']){
	$wheres = "AND ob.name LIKE '%".trim($_GET['name'])."%'";
}
if($_GET['contract_no']) {
	$where .= " AND oc.contract_no LIKE '%".trim($_GET['contract_no'])."%' ";
}

$c_total = array(0,0,0,0);
$c_total[0] = $db->get_var("SELECT COUNT(*) total FROM sp_ot_contract oc LEFT JOIN sp_ot_basedata ob ON ob.id=oc.brand_id WHERE 1 $where $wheres AND ob.statu = '0'");
$c_total[1] = $db->get_var("SELECT COUNT(*) total FROM sp_ot_contract oc LEFT JOIN sp_ot_basedata ob ON ob.id=oc.brand_id WHERE 1 $where $wheres AND ob.statu = '1'");
$pages = numfpage($c_total[$statu]);
$where .= " AND ob.statu = '$statu'";

$dealer_list=$db->get_results("SELECT oc.*,ob.statu FROM sp_ot_contract oc LEFT JOIN sp_ot_basedata ob ON ob.id=oc.brand_id WHERE 1 $where $wheres ORDER BY oc.id DESC $pages[limit]");

foreach($dealer_list as $k=>$v){
	$dealer_list[$k]=$v;
	$dealer_list[$k]['name']=$db->get_var("SELECT name FROM sp_ot_basedata WHERE id=$v[brand_id]");


}


tpl();


