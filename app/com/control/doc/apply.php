<?php
!defined('IN_SUPU') && exit('Forbidden');
$doc                                                     = load('data');

$doc->doc_left='supu';
$doc->doc_right='supur';
$cti_info                                                = $db->find_one('contract_item', array(
    'cti_id' => $_GET['cti_id']
), "*, scope as cti_scope");

$doc->file_name                                          = $cti_info['cti_code'].'-认证申请书';

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

//兼容旧版本
$cti_code=explode('<br>',$cti_info['cti_code']);
$cti_info['cti_code']=$cti_code[0];
//规格型号
if ($cti_info['status']!= '2') {
    $cti_info['appro_scope'] = $cti_info['scope'];
}
 //希望送检的实验室
$cti_info['test_name'] = load('set')->get_set_name_by_id('test_org', $cti_info['test_org_id']);
$ep_info=$db->find_one('enterprises',array('eid'=>$cti_info['eid']));
$manu_info=$db->find_one('enterprises',array('eid'=>$cti_info['ep_manu_id']));
$product_info=$db->find_one('enterprises',array('eid'=>$cti_info['ep_prod_id']));
/*
$ep_info                                                 = load('ep')->get(array(
    'eid' => $cti_info['eid']
));
$                                               = load('ep')->get(array(
    'eid' => $cti_info['ep_manu_id']
));
$product_info                                            = load('ep')->get(array(
    'eid' => $cti_info['ep_prod_id']
));*/
//替换数据
$data                                                    = array_merge($cti_info, $ep_info);
//复选框处理
$checkbox                                                = '□'; //未选中
$checked                                                 = '■';//选中
$data['ep_site_related_1001']                            = $data['ep_site_related_1002'] = $data['ep_site_related_1003'] = $data['audit_type_1001'] = $data['audit_type_1007']= $checkbox;
$data['ep_site_related_' . $cti_info['ep_site_related']] = $checked;

//申请类型
$data['apply_type_1001']=$data['apply_type_1002']=$data['apply_type_1003']=$data['apply_type_1004']=$data['apply_type_1007']=$checkbox;
if($cti_info['apply_type']=='1004-2'){
	$data['apply_type_1003']=$data['apply_type_1002']=$checked; 
}elseif($cti_info['apply_type']=='1004-3'){
	$data['apply_type_1003']=$data['apply_type_1004']=$checked;  
}
//申请时间
$data['apply_date']=date_db2view($cti_info['create_date']);

//希望采取认证模式
$data['audit_tpl_1001']=$data['audit_tpl_1002']=$data['audit_tpl_1003']=$data['audit_tpl_1004']=$data['audit_tpl_1005']=$checkbox;
$data['audit_tpl_'.$cti_info['audit_tpl']]=$checked;


 
//处理三者关系--附表信息
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
//根据认证领域判断申请书模板
if ($cti_info['audit_ver'] == 'b01001') { //认证领域：强制性认证申请书模板
    $tpl_name = 'apply';
} elseif ($cti_info['audit_ver'] == 'b0200x') { //认证领域：一般工业品认证申请书模板
    $tpl_name = 'apply_fed';
}else{//有机+GAP 认证领域
	if($cti_info['oga_cert_type']){
		$data['oga_cert_type_1']=$checked;
		$data['oga_cert_type_0']=$checkbox;	
	}else{
		$data['oga_cert_type_1']=$checkbox;
		$data['oga_cert_type_0']=$checked;	
	}
	//标准
	$data['ver1']=$data['ver2']=$data['ver3']=$data['ver4']=$checkbox;
	$ver=explode('；',$cti_info['prod_ver_id']); 
	if(in_array('GB/T 19630.1-2011',$ver))$data['ver1']=$checked;
	if(in_array('GB/T 19630.2-2011',$ver))$data['ver2']=$checked;
	if(in_array('GB/T 19630.3-2011',$ver))$data['ver3']=$checked;
	if(in_array('GB/T 19630.4-2011',$ver))$data['ver4']=$checked;
	//认证种类
	$data['prod1']=$data['prod2']=$data['prod3']=$data['prod4']=$data['prod5']=$data['prod6']=$data['prod7']=$checkbox;	
	//基地产品
	$prod_info='';
	
	$ogas=load('oga')->get_ogas(array('cti_id'=>$cti_info['cti_id']));
	
 	foreach($ogas as $oga){
			foreach($oga['oga_info'] as $oga_info){
				$i++;
			$prod_info.='<w:tr w:rsidR="00603D51" w:rsidRPr="00D47E22" w:rsidTr="00603D51"><w:trPr><w:trHeight w:val="485"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="286" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00603D51" w:rsidRPr="00D47E22" w:rsidRDefault="00603D51" w:rsidP="0058038E"><w:pPr><w:spacing w:line="360" w:lineRule="exact"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:hAnsi="宋体"/><w:b/><w:sz w:val="18"/></w:rPr></w:pPr><w:r w:rsidRPr="00D47E22"><w:rPr><w:rFonts w:ascii="宋体" w:hAnsi="宋体" w:hint="eastAsia"/><w:b/><w:sz w:val="18"/></w:rPr><w:t>'.$i.'</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="425" w:type="pct"/></w:tcPr><w:p w:rsidR="00603D51" w:rsidRPr="003B67AD" w:rsidRDefault="00603D51" w:rsidP="0058038E"><w:pPr><w:spacing w:line="360" w:lineRule="exact"/><w:jc w:val="center"/><w:rPr><w:rFonts w:hAnsi="宋体"/><w:sz w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t>'.$oga['oga_name'].'</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="425" w:type="pct"/></w:tcPr><w:p w:rsidR="00603D51" w:rsidRPr="003B67AD" w:rsidRDefault="00603D51" w:rsidP="0058038E"><w:pPr><w:spacing w:line="360" w:lineRule="exact"/><w:jc w:val="center"/><w:rPr><w:rFonts w:hAnsi="宋体"/><w:sz w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t>'.$oga['oga_addr'].'</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="425" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00603D51" w:rsidRPr="003B67AD" w:rsidRDefault="00603D51" w:rsidP="0058038E"><w:pPr><w:spacing w:line="360" w:lineRule="exact"/><w:jc w:val="center"/><w:rPr><w:rFonts w:hAnsi="宋体"/><w:sz w:val="18"/></w:rPr></w:pPr><w:r w:rsidRPr="003B67AD"><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t>'.$oga_info['prod_id'].'</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="426" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00603D51" w:rsidRPr="003B67AD" w:rsidRDefault="00603D51" w:rsidP="0058038E"><w:pPr><w:spacing w:line="360" w:lineRule="exact"/><w:jc w:val="center"/><w:rPr><w:rFonts w:hAnsi="宋体"/><w:sz w:val="18"/></w:rPr></w:pPr><w:r w:rsidRPr="003B67AD"><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t>'.$oga_info['prod_note'].'</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="604" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00603D51" w:rsidRPr="003B67AD" w:rsidRDefault="00603D51" w:rsidP="0058038E"><w:pPr><w:spacing w:line="360" w:lineRule="exact"/><w:jc w:val="center"/><w:rPr><w:rFonts w:hAnsi="宋体"/><w:sz w:val="18"/></w:rPr></w:pPr><w:r w:rsidRPr="003B67AD"><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t>'.$oga['oga_area'].'</w:t></w:r></w:p><w:p w:rsidR="00603D51" w:rsidRPr="003B67AD" w:rsidRDefault="00603D51" w:rsidP="0058038E"><w:pPr><w:spacing w:line="360" w:lineRule="exact"/><w:jc w:val="center"/><w:rPr><w:rFonts w:hAnsi="宋体"/><w:sz w:val="18"/></w:rPr></w:pPr><w:r w:rsidRPr="003B67AD"><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t></w:t></w:r><w:r w:rsidRPr="003B67AD"><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t xml:space="preserve"> </w:t></w:r><w:r w:rsidRPr="003B67AD"><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t></w:t></w:r><w:r w:rsidRPr="003B67AD"><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t xml:space="preserve"> </w:t></w:r><w:r w:rsidRPr="003B67AD"><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t></w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="544" w:type="pct"/></w:tcPr><w:p w:rsidR="00603D51" w:rsidRPr="003B67AD" w:rsidRDefault="00603D51" w:rsidP="0058038E"><w:pPr><w:spacing w:line="360" w:lineRule="exact"/><w:jc w:val="center"/><w:rPr><w:rFonts w:hAnsi="宋体"/><w:sz w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t></w:t></w:r><w:r w:rsidRPr="003B67AD"><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t>'.$oga_info['prod_area'].'</w:t></w:r></w:p><w:p w:rsidR="00603D51" w:rsidRPr="003B67AD" w:rsidRDefault="00603D51" w:rsidP="0058038E"><w:pPr><w:spacing w:line="360" w:lineRule="exact"/><w:jc w:val="center"/><w:rPr><w:rFonts w:hAnsi="宋体"/><w:sz w:val="18"/></w:rPr></w:pPr><w:r w:rsidRPr="003B67AD"><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t></w:t></w:r><w:r w:rsidRPr="003B67AD"><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t xml:space="preserve"> </w:t></w:r><w:r w:rsidRPr="003B67AD"><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t></w:t></w:r><w:r w:rsidRPr="003B67AD"><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t xml:space="preserve"> </w:t></w:r><w:r w:rsidRPr="003B67AD"><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t></w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="604" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00603D51" w:rsidRPr="003B67AD" w:rsidRDefault="00603D51" w:rsidP="0058038E"><w:pPr><w:spacing w:line="360" w:lineRule="exact"/><w:jc w:val="center"/><w:rPr><w:rFonts w:hAnsi="宋体"/><w:sz w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t>'.$oga_info['prod_area'].'</w:t></w:r></w:p><w:p w:rsidR="00603D51" w:rsidRPr="003B67AD" w:rsidRDefault="00603D51" w:rsidP="0058038E"><w:pPr><w:spacing w:line="360" w:lineRule="exact"/><w:jc w:val="center"/><w:rPr><w:rFonts w:hAnsi="宋体"/><w:sz w:val="18"/></w:rPr></w:pPr><w:r w:rsidRPr="003B67AD"><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t></w:t></w:r><w:r><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t></w:t></w:r><w:r><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t>/</w:t></w:r><w:r><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t></w:t></w:r><w:r><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t>/</w:t></w:r><w:r><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t>只</w:t></w:r><w:r w:rsidRPr="003B67AD"><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t></w:t></w:r><w:r w:rsidRPr="003B67AD"><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t xml:space="preserve">   </w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="721" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00603D51" w:rsidRDefault="00603D51" w:rsidP="0058038E"><w:pPr><w:adjustRightInd w:val="0"/><w:snapToGrid w:val="0"/><w:spacing w:line="240" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:hAnsi="宋体"/><w:sz w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t>'.$oga_info['prod_output_num'].'</w:t></w:r></w:p><w:p w:rsidR="00603D51" w:rsidRPr="003B67AD" w:rsidRDefault="00603D51" w:rsidP="0058038E"><w:pPr><w:adjustRightInd w:val="0"/><w:snapToGrid w:val="0"/><w:spacing w:line="240" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:hAnsi="宋体"/><w:sz w:val="18"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t></w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="540" w:type="pct"/><w:gridSpan w:val="2"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00603D51" w:rsidRDefault="00603D51" w:rsidP="0058038E"><w:pPr><w:adjustRightInd w:val="0"/><w:snapToGrid w:val="0"/><w:spacing w:line="240" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:hAnsi="宋体"/><w:sz w:val="18"/></w:rPr></w:pPr><w:r w:rsidRPr="003B67AD"><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t>'.$oga_info['prod_output_val'].'</w:t></w:r></w:p><w:p w:rsidR="00603D51" w:rsidRPr="003B67AD" w:rsidRDefault="00603D51" w:rsidP="0058038E"><w:pPr><w:adjustRightInd w:val="0"/><w:snapToGrid w:val="0"/><w:spacing w:line="240" w:lineRule="atLeast"/><w:jc w:val="center"/><w:rPr><w:rFonts w:hAnsi="宋体"/><w:sz w:val="18"/></w:rPr></w:pPr><w:r w:rsidRPr="003B67AD"><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t></w:t></w:r><w:r w:rsidRPr="003B67AD"><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t></w:t></w:r><w:r w:rsidRPr="003B67AD"><w:rPr><w:rFonts w:hAnsi="宋体" w:hint="eastAsia"/><w:sz w:val="18"/></w:rPr><w:t></w:t></w:r></w:p></w:tc></w:tr>';		
 				
			} 
	} 
	$data['prod_info']=$prod_info; 
	$tpl_name='apply_oga';
	 
}
 
 
$doc->debug=0;
 //exit;
 
$doc->export_doc($data, $tpl_name);

 