<?php
!defined('IN_SUPU') && exit('Forbidden');
extract($_GET);

//查询审核项目+发送通知
$where = " AND p.is_samp='1'";
 
//委托人搜索

 //组织关系 
//委托人名称
if ($_GET['apply_ep_name']) { 
	$where.= load('ep')->search_ep_name($_GET['apply_ep_name'],'eid');
}
//生产者
if ($_GET['manu_ep_name']) { 
	 $where.= load('ep')->search_ep_name($_GET['manu_ep_name'],'ep_manu_id');
}
//生产企业
if ($_GET['pro_ep_name']) { 
	 $where.= load('ep')->search_ep_name($_GET['pro_ep_name']);
}
//合同项目编号
if ($cti_code) {
   // $cti_id = $db->get_var("SELECT cti_id FROM sp_contract_item WHERE cti_code like '%$cti_code%'");
	 $cti_id = $db->get_var("SELECT cti_id FROM sp_contract_item WHERE cti_code like '%$cti_code%'");
    if ($cti_id) {
        $where .= " AND ". $db->sqls(array('p.cti_id'=>$cti_id));
    } else {
        $where .= " AND p.id < -1";
    }
} 
//项目所属单位限制
 
$where .= load('ctfrom')->sCtfrom('cti');

//认证领域
$where.=load('cti')->search_domain();


//检查类型
if ($audit_type) {
    $where .= " AND p.audit_type = '$audit_type'";
}

$total=load('audit')->getTotalByField('is_notice',$where,$joins);

$tag=$_GET['status']?$_GET['status']:0;
$tag_tab[$tag]=' ui-tabs-active ui-state-active';

$pages  = numfpage($total[$tag]);
$where.=" AND is_notice=$tag";

$fields = ' cti.review_date,'; 
//读取配置内容 
$p_list = load('audit')->gets($where, $fields, $joins, $pages);

 //echo load('audit')->sql;
tpl();
 