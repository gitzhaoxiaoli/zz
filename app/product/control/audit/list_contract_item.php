<?php
!defined('IN_SUPU') && exit('Forbidden');
//产品查询列表
$fields            = $join = $where = $item_join = $item_where = $page_str = '';

$contracts = array();
extract($_GET);
//搜索
if($cti_code){
    $where .= "AND cti_code = '$cti_code'";
}
if($ep_name){
    $res = $db->find_one("enterprises",array('ep_name'=>$ep_name),"eid");

    $where .= "AND eid = '$res[eid]'";
}
if($ep_manu_id){
    $res = $db->find_one("enterprises",array('ep_name'=>$ep_manu_id),"eid");
    $where .= "AND ep_manu_id = '$res[eid]'";
}
if($ep_prod_id){
    $res = $db->find_one("enterprises",array('ep_name'=>$ep_prod_id),"eid");
    $where .= "AND ep_prod_id = '$res[eid]'";
}
if($fac_code=trim($fac_code)){
    $res = $db->find_one("enterprises",array('fac_code'=>$fac_code),"eid");
    $where .= "AND ep_prod_id = '$res[eid]'";
}

if($prod_id){
   
    $where .= "AND prod_id = '$prod_id'";
}
if($is_app){
    
    $where .= "AND is_app = '$is_app'";
}
$status = getgp("status");
if(is_null($status)) $status = 0;
${'status_'.$status} = " ui-tabs-active ui-state-active";
$total = array();
$total[0] = $db->find_num("contract_item","$where and iso_prod_type = 1 and deleted = 0 and status = 1");
$pages = numfpage($total[$status]);
$where .= "AND status = 1";
$sql = "SELECT * FROM sp_contract_item WHERE 1 $where AND iso_prod_type = 1 and deleted = 0 order by cti_id desc $pages[limit] ";

$query = $db->query($sql);
while($rt = $db->fetch_array($query)){
    //$rt['ep_name'] = $db->get_var("select ep_name from sp_enterprises where eid=$rt[eid]");
    $rt['ep_name'] = $db->getField("enterprises","ep_name","eid = $rt[eid]");
    $rt['ep_manu'] = $db->getField("enterprises","ep_name","eid = $rt[ep_manu_id]");
    $rt['ep_prod'] = $db->getField("enterprises","ep_name","eid = $rt[ep_prod_id]");
    $rt[approval_user] && $rt[approval_user] = $db->get_var("select name from sp_hr where id=$rt[approval_user]");
    // if($status == '2' and $rt[app_finish] =="1")
		// $rt[color] = "red";
	// echo $rt[app_finish];
    $contracts[] =$rt;
}


if (!$export) {
    tpl();
} else {
    ob_start();
    tpl('xls/list_contract');
    $data = ob_get_contents();
    ob_end_clean();
    export_xls('单元列表', $data);
}