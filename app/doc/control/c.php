<?php
!defined('IN_SUPU') && exit('Forbidden');
require( DATA_DIR . 'cache/audit_ver.cache.php' );

$id = (int)getgp( 'id' );
if (!$id) {
	exit("ERROR");
}

$iso_array = array('A01' => '质量' ,  'A02' => '环境' , 'A03' => '职业健康安全');
$data = $db->find_one('certificate' , array("id" => $id));
$ep_info  = $db->find_one('enterprises' , array("eid" => $data[eid]));

$data['s_date'] = mysql2date( 'Y年m月d日' ,$data['s_date']) ; 
$data['e_date'] = mysql2date( 'Y年m月d日' ,$data['e_date']) ; 
$data['audit_ver'] = $audit_ver_array[$data['audit_ver']][audit_basis];
$data['iso'] = $iso_array[$data[iso]];
$data['work_code'] = $ep_info['work_code'];    
$data['ep_addr'] = $ep_info['ep_addr'];    
$data['prod_addr'] = $ep_info['prod_addr'];    
// p($data);
// exit;

require(STYLESHEET_DIR.'word/PHPWord.php');
$PHPWord = new PHPWord();
$document = $PHPWord->loadTemplate(DOCTPL_PATH.'view/c.docx');
foreach ($data as $k => $v) {
    
    $document->setValue($k,$v);

}

$filedir = DATA_DIR.'word/temp.docx';
$document->save($filedir);
$filename = $ep_info[ep_name]."-证书中文.docx";
export_word($filename, $filedir);
?>