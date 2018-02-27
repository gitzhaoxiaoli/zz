<?php
!defined('IN_SUPU') && exit('Forbidden');
//标签 
$_GET['status']               = $_GET['status'] ? $_GET['status'] : '0';
$cti_code                     = $_GET['cti_code'];
${'status' . $_GET['status']} = ' ui-tabs-active ui-state-active';
$where['p.is_notice']         = '1'; //已经安排实验室
//实验室搜索
if ($_GET['test_org_name']) {
    $_test_codes = $db->get_Col("SELECT code FROM sp_settings_test_org WHERE name LIKE '%" . str_replace('%', '\%', $_GET['test_org_name']) . "%'");
    if ($_test_codes) {
        $where['p.test_org_id'] = $_test_codes;
    } else {
        $where['p.id'] = 0;
    }
}
$where = " and " . $db->sqls($where, ' and ');
extract($_GET);
//组织关系 
//委托人名称
if ($_GET['apply_ep_name']) {
    $where .= load('ep')->search_ep_name($_GET['apply_ep_name'], 'eid');
}
//生产者
if ($_GET['manu_ep_name']) {
    $where .= load('ep')->search_ep_name($_GET['manu_ep_name'], 'ep_manu_id');
}
//生产企业
if ($_GET['pro_ep_name']) {
    $where .= load('ep')->search_ep_name($_GET['pro_ep_name']);
}
//合同项目编号
if ($cti_code) {
    $cti_id = $db->get_var("SELECT cti_id FROM sp_contract_item WHERE cti_code like '%$cti_code%'");
    if ($cti_id) {
        $where .= " AND " . $db->sqls(array(
            'p.cti_id' => $cti_id
        ));
    } else {
        $where .= " AND p.id < -1";
    }
}
//查项目所属单位
//if ($_GET['ctfrom']) {
$where .= load('ctfrom')->sCtfrom('cti');
//}
if ($audit_ver) { //认证领域
    $where .= " AND cti.audit_ver = '$audit_ver'";
}
//检查类型
if ($audit_type) {
    $where .= " AND p.audit_type = '$audit_type'";
}
//根据检测状态统计数量
$audit = load('audit');
$where .= load('ctfrom')->sCtfrom('cti');
$total          = $audit->getTotalByField('samp_status', $where);
$total_samp_ext = $audit->getTotalByField('samp_ext', $where);
$total[8]       = $total_samp_ext[1];
//统计是否合格
$total_samp_ext = $audit->getTotalByField('samp_pass', $where);
$total[9]       = $total_samp_ext[1];
if ($_GET['status'] == 8) {
    $where .= " AND p.samp_ext=1";
} elseif ($_GET['status'] == 9) {
    $where .= " AND p.samp_pass=1";
} else {
    $where .= " AND p.samp_status=$_GET[status]";
}
$pages      = numfpage($total[$_GET['status']]);
$auditLists = $audit->gets($where, '', '', $pages);
tpl();
 