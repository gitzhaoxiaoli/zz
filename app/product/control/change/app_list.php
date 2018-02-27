<?php
/*
 * 
 */


!defined('IN_SUPU') && exit('Forbidden');
$status_array = array("未审批","通过","未通过");
extract($_GET);
$status = (int)$status;
${"tab_".$status} = "ui-tabs-active ui-state-active";
if ($ep_name = trim($ep_name)) {
    
    $where .= " and c.cert_name like '%$ep_name%'";
}
if ($manu_name = trim($manu_name)) {
    $where .= " and c.manu_name like '%$manu_name%'";
}
if ($prod_name = trim($prod_name)) {
    $where .= " and c.pro_name like '%$pro_name%'";
}
if($fac_code=trim($fac_code)){
    $res = $db->find_one("enterprises",array('fac_code'=>$fac_code),"eid");
    $where .= "AND ep_prod_id = '$res[eid]'";
}

if ($cti_name = trim($cti_name)) {
    $where .= " and cti.prod_id like  '%$cti_name%'";
}
if ($name = trim($name)) {
    $where .= " and cti.prod_name_chinese like  '%$name%'  ";
}
if(!$export){
	$total[0]=$db->get_var("select COUNT(*) from sp_change_app cp LEFT JOIN  sp_certificate c ON c.id = cp.zsid where 1 $where AND cp.status = 0");
	$total[1]=$db->get_var("select COUNT(*) from sp_change_app cp LEFT JOIN  sp_certificate c ON c.id = cp.zsid where 1 $where AND cp.status = 1");
	$pages = numfpage($total[$status],20);
}
$where .= " AND cp.status = '$status'";

$sql   = "select cp.*,c.certno,c.cert_name,c.pro_name,c.manu_name from sp_change_app cp LEFT JOIN  sp_certificate c ON c.id = cp.zsid where 1 $where ORDER BY cp.id desc $pages[limit] ";
$query = $db->query($sql);
$array = array();
while ($row = $db->fetch_array($query)) {
    $row[status_V] = $status_array[$row[status]];
    $array[$row['id']] = $row;
}


//p($pages);
//tpl('contract/list');


    if( !$export ){
        tpl();
    } else { //导出excel表格
        ob_start();
        tpl( 'xls/contract_list' );
        $data = ob_get_contents();
        ob_end_clean();

        export_xls( '查看申请', $data );
    }
?>