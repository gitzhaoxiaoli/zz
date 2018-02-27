<?php
/*
 * 
 */


!defined('IN_SUPU') && exit('Forbidden');
extract($_GET);
$status_array = array("未受理","已受理","退回");
if ($ep_name = trim($ep_name)) {
    $eids = $db->getCol('enterprises', 'eid', "  ep_name like  '%$ep_name%'  ");
    array_push($eids, '-1');
    $where .= " and eid in (" . join(',', $eids) . ")";
}
if ($manu_name = trim($manu_name)) {
    $mids = $db->getCol('enterprises', 'eid', "  ep_name like  '%$manu_name%'  ");
    array_push($mids, '-1');
    $where .= " and ep_manu_id in (" . join(',', $mids) . ")";
}
if ($prod_name = trim($prod_name)) {
    $pids = $db->getCol('enterprises', 'eid', "  ep_name like  '%$prod_name%'  ");
    array_push($pids, '-1');
    $where .= " and ep_prod_id in (" . join(',', $pids) . ")";
}
if ($cti_name = trim($cti_name)) {
    $where .= " and prod_id like  '%$cti_name%'";
}
if ($name = trim($name)) {
    $where .= " and prod_name_chinese like  '%$name%'  ";
}
$seid=$_SESSION[userinfo][eid];
$where.=" and create_uid=$seid and is_app = 1 and app_type != 1";
$total=$db->find_num('contract_item',$where);
$pages = numfpage($total,10);


$sql   = "select * from sp_contract_item where 1 $where $pages[limit]";
$query = $db->query($sql);
$array = array();
while ($row = $db->fetch_array($query)) {
    $row['ep_name']        = $db->getField('enterprises', 'ep_name', "eid=$row[eid]");
    $row['ep_manu_name']   = $db->getField('enterprises', 'ep_name', "eid=$row[ep_manu_id]");
    $row['ep_prod_name']   = $db->getField('enterprises', 'ep_name', "eid=$row[ep_prod_id]");
	$row['status_v'] = $status_array[$row['status']];
    $array[$row['cti_id']] = $row;
}


//p($pages);
//tpl('contract/list');


    if( !$export ){
        tpl( 'contract/list' );
    } else { //导出excel表格
        ob_start();
        tpl( 'xls/contract_list' );
        $data = ob_get_contents();
        ob_end_clean();

        export_xls( '查看申请', $data );
    }
?>