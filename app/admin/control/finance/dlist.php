<?php
!defined('IN_SUPU') && exit('Forbidden');
//2016-03-11 @cyf 
// 财务收费明细列表
require "data/cache/audit_type.cache.php";
extract($_GET);
if($ep_name){
  $where .=" and e.ep_name = '$ep_name'";  
}
if($cost_type){
 
  $where.=" and c.cost_type='$cost_type'";
}
if($s_date){
  $where.=" and d.dk_date>='$s_date'"; 
}
if($e_date){
  $where.=" and d.dk_date<='$e_date'"; 
}
  $where .=" and c.deleted = 0 and d.deleted=0";
/*列表*/
if (!$export) {
    $total = $db->get_var("SELECT COUNT(*) FROM sp_contract_cost c RIGHT JOIN sp_contract_cost_detail d ON c.id = d.cost_id LEFT JOIN sp_enterprises e ON c.eid=e.eid WHERE 1 $where ");
    $pages = numfpage($total);
}
  $sql = "SELECT * FROM sp_contract_cost c RIGHT JOIN sp_contract_cost_detail d ON c.id = d.cost_id LEFT JOIN sp_enterprises e ON c.eid=e.eid WHERE 1 $where order by d.id desc $pages[limit]";

$res = $db->query($sql);
while ($rt = $db->fetch_array($res)) {
         $rt['ep_name']=$db->get_var("select ep_name from sp_enterprises where eid='$rt[eid]' and deleted='0'");
         $rt['ctfrom']=$db->get_var("select ctfrom from sp_enterprises where eid='$rt[eid]' and deleted='0'");
         $rt['ct_code']=$db->get_var("select ct_code from sp_contract where ct_id='$rt[ct_id]' and deleted='0'");
         $rt['q']=$db->get_var("select sum(dk_cost) from sp_contract_cost_detail d LEFT JOIN sp_contract_cost c on d.cost_id=c.id where d.ct_id='$rt[ct_id]' and c.cost_type='$rt[cost_type]' and d.deleted='0' ");
         $rt['qf_cost']=$rt['cost']-$rt['q'];
         $rt['ctfrom'] = f_ctfrom( $rt['ctfrom'] );
         $rt['cost_type']=read_cache("cost_type",$rt['cost_type']);
         $datas[]=$rt;
}
    //收费类型
    $cost_type_select=f_select('cost_type',$cost_type);
if (!$export) {
    tpl('finance/dlist');
} else {
    ob_start();
    tpl('xls/list_finance_dlist');
    $data = ob_get_contents();
    ob_end_clean();
    export_xls('财务收费明细列表', $data);
}

