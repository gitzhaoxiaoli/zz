<?php
    //2016-03-15 @cyf 
    !defined('IN_SUPU') && exit('Forbidden');
    //项目列表
    require "data/cache/audit_type.cache.php";
    extract($_GET);
    //搜索
    if($ep_name){
       $where.="AND e.ep_name='$ep_name'"; 
    }
    if($iso){
        
       $where.="AND p.iso='$iso'"; 
    }
    if($ct_code){
    $where.="AND p.ct_code='$ct_code'"; 
    }
    if($cti_code){
       $where.="AND p.cti_code='$cti_code'";
    }

    $is_finance = (int)$is_finance;
    ${"tab_".$is_finance} = " ui-tabs-active ui-state-active";
    //合同来源
    $ctfrom_select = f_ctfrom_select();
    //省分下拉 (搜索用)
    $province_select = f_province_select();
    $status = (int)getgp( 'status' );
    $where.=" and p.deleted='0'";
    //信息条数
    $is_finance_count = array(0,0);
    $query= $db->query("SELECT p.is_finance,COUNT(*) total FROM sp_project p LEFT JOIN sp_enterprises e on p.eid=e.eid WHERE 1 $where group by p.is_finance;");
    while ($rt = $db->fetch_array($query)) {
                $is_finance_count[$rt['is_finance']] = $rt['total'];
      }
    $pages= numfpage($is_finance_count[$is_finance], 20, $url_param);
    $where.="AND p.is_finance='$is_finance'";
   //数据信息
    $sql = "SELECT * FROM sp_enterprises e LEFT JOIN  sp_project p on p.eid=e.eid WHERE 1 $where ORDER BY p.id DESC $pages[limit] ";
    $res = $db->query($sql);
    while( $rt = $db->fetch_array( $res ) ){
        
        $rt[ctfrom]=f_ctfrom($rt[ctfrom]);
        $rt[iso]=f_iso($rt[iso]);
        $rt[audit_type]=f_audit_type($rt[audit_type]);
        $datas[] = $rt;
    }

    if (!$export) {
        tpl();
    } else {
        ob_start();
        tpl('xls/list_contract');
        $data = ob_get_contents();
        ob_end_clean();
        export_xls('财务收费登记', $data);
    }