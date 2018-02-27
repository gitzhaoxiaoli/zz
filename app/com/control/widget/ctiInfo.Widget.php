<?php
!defined('IN_SUPU') && exit('Forbidden');
//项目信息： 合同项目信息+项目信息
//合同项目信息：组织关系与产品范围
 
if ($args['cti_id']) { //合同受理:模板内部加载模式
    $cti_id = $args['cti_id'];
} elseif ($_GET['cti_id']) { //弹窗样式
    $cti_id = $_GET['cti_id'];
} elseif ($args['project_id']) { //模板加载方式
    $project_info = load('audit')->get(array(
        'id' => $args['project_id']
    ));
	 
    $cti_id       = $project_info['cti_id'];
	
} elseif ($_GET['project_id']) { //弹窗
    $project_info = load('audit')->get(array(
        'id' => $_GET['project_id']
    ));
    $cti_id       = $project_info['cti_id'];
}
//合同项目信息
$cti_info = load('cti')->get(array(
    'cti_id' => $cti_id
), false);

//审核类型:如果没有进行到认证阶段默认为受理阶段的审核范围
$audit_type=$project_info['audit_type']?$project_info['audit_type']:$cti_info['audit_type'];

//规格型号：
if ($cti_info['status']!== '2') {
    $cti_info['v_scope'] = $cti_info['scope'];
} else {
    $cti_info['v_scope'] = $cti_info['appro_scope'];
}
//判断是否发证
$cert_scope=$db->getField('certificate','cert_scope',array('cti_id'=>$cti_info['cti_id']));
 
 


//希望送检的实验室
$cti_info['test_name'] = load('set')->get_set_name_by_id('test_org', $cti_info['test_org_id']);
//是否采取已获证企业
if ($cti_info['isOldCert'] == '1') {
    $cti_info['cert_env'] = '是';
} else {
    $cti_info['cert_env'] = '否';
}
//有机+GAP存在基地信息与产品信息
$oga_infos = load('oga')->get_ogas(array(
    'cti_id' => $cti_id
));
if ($cti_info['prod_id'] == '01.01')
    $cycle = '生长周期-' . load('cti')->plant_year[$cti_info['oga_plant_year']];
else if ($cti_info['prod_id'] == '02.01')
    $cycle = '转换期-' . load('cti')->chang_date[$cti_info['oga_chang_date']];
else {
    $cycle = '';
}

//获取产品记录=================================================
$pros = load('item_pro')->gets(array('cti_id'=>$cti_id));

//=================================================

//产品文档
$cti_files = load('attachment')->gets(array(
    'type' => 'cti',
    'key_val' => $cti_info['cti_id'],
    'audit_ver' => $cti_info['audit_ver']
));
//echo load('attachment')->sql;
//=======================检验：实验室信息================================

 
if ($project_info['is_samp']) {
    $test_info           = $db->find_one('settings_test_org', array(
        'code' => $project_info['test_org_id']
    ));
    //实验室检查报告
    $att_cti_test_report = load('attachment')->gets(array(
        'type' => 'cti_test_report',
        'key_val' => $cti_info['cti_id']
    ));
	 
}
//变更信息
$chang_info=load('change')->get($project_info['change_id']);

 

$wh = $_GET['wh'];
if (!empty($wh)) {
    $args['width'] = $wh;
}
//p( $args['width']);
 
tpl();
