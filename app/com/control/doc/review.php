<?php
!defined('IN_SUPU') && exit('Forbidden');
//转换日期格式：将数据库数据转化为年-月-日
function date_db2view($str) {
    $temp = '';
    $date = explode('-', $str);
    $y = $date[0];
    $m = $date[1];
    $d = $date[2];
    $temp = $y . '年' . $m . '月' . $d . '日';
    return $temp;
}
//是否下载过受理通知
$db->update('contract_item',array('is_down_review'=>1),array('cti_id' =>$_GET[cti_id]));

$doc                                                     = load('data');
$doc->doc_left='supul';
$doc->doc_right='supur';


$cti_info                                                = $db->find_one('contract_item', array(
    'cti_id' => $_GET['cti_id']
), "*, scope as cti_scope");

$doc->file_name                                          = $cti_info['cti_code'].'-受理通知书';

//兼容旧版本
$cti_code=explode('<br>',$cti_info['cti_code']);
$cti_info['cti_code']=$cti_code[0];
 
//受理人员
$cti_info['review_user']=$db->getField('hr','name',array('id'=>$cti_info['review_uid']));
$cti_info['review_tel']=$db->getField('metas','meta_value',array('id'=>$cti_info['review_uid'],'meta_name'=>'phone','used'=>'hr'));
$cti_info['review_email']=$db->getField('hr','e_mail',array('id'=>$cti_info['review_uid']));
$cti_info['review_date']=date_db2view($cti_info['review_date']);

$sys_prod_name=load('set')->get_set_name_by_id('prod',$cti_info['prod_id']);
$cti_info['prod_name']=$cti_info['prod_name'].'('.$sys_prod_name.')';

$ep_info=$db->find_one('enterprises',array('eid'=>$cti_info['eid']));
$manu_info=$db->find_one('enterprises',array('eid'=>$cti_info['ep_manu_id']));
$product_info=$db->find_one('enterprises',array('eid'=>$cti_info['ep_prod_id']));
//替换数据
$data                                                    = array_merge($cti_info, $ep_info);
//处理合同项目信息
$checkbox                                                = '□';
$checked                                                 = '■';
$data['ep_site_related_1001']                            = $data['ep_site_related_1002'] = $data['ep_site_related_1003'] = $data['audit_type_1001'] = $data['audit_type_1007'] = $data['cti_is_ext'] = $data['cti_is_turn'] = $checkbox;
$data['ep_site_related_' . $cti_info['ep_site_related']] = $checked;
$data['audit_type_' . $cti_info['audit_type']]           = $checked;
//申请类型处理
$data['apply_type_1001']=$data['apply_type_1002']=$data['apply_type_1003']=$data['apply_type_1004']=$data['apply_type_1005']=$checkbox;
if($data['apply_type']=='1001'){//新申请
	$data['apply_type_1001']=$data['apply_type_1004']=$data['apply_type_1005']=$checked;
	
}elseif($data['apply_type']=='1003'){//扩项
	$data['apply_type_1001']=$data['apply_type_1005']=$checked;
	
}



if ($cti_info['is_ext']) {
    $data['cti_is_ext'] = $checked;
}
if ($cti_info['is_turn']) {
    $data['cti_is_turn'] = $checked;
}
//处理三者关系
$data['website']            = $ep_info['website'];
$data['person_job']         = $ep_info['person_job'];
$data['ep_phone']           = $ep_info['ep_phone'];
$data['manu_website']       = $manu_info['manu_website'];
$data['manu_person_job']    = $manu_info['manu_person_job'];
$data['manu_ep_phone']      = $manu_info['manu_ep_phone'];
$data['product_website']    = $product_info['product_website'];
$data['product_person_job'] = $product_info['product_person_job'];
$data['product_ep_phone']   = $product_info['product_ep_phone'];
 
foreach ($manu_info as $k => $v) {
    $data['manu_' . $k] = $v;
}
foreach ($product_info as $k => $v) {
    $data['product_' . $k] = $v;
}
//受理通知书
     $tpl_name = 'review';
 
$doc->export_doc($data, $tpl_name);

 