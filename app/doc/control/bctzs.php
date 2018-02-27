<?php
!defined('IN_SUPU') && exit('Forbidden');
require( DATA_DIR . 'cache/audit_ver.cache.php' );

$id = (int)getgp( 'pid' );
if (!$id) {
	exit("ERROR");
}

$p_info = $db->find_one('project' , array("id" => $id) , 'eid , cti_id , iso , audit_ver');
$cert = $db->find_one('certificate' , array("cti_id" => $p_info['cti_id'] , 'eid' => $p_info['eid'] , 'status' => '01') , 'cert_name , certno');
    
$iso_array = array('A01' => '质量' ,  'A02' => '环境' , 'A03' => '职业健康安全');
$data = $cert;
$data['iso'] = $iso_array[$p_info['iso']];
$data['audit_ver'] = read_cache('audit_ver' , $p_info['audit_ver'] , 'audit_basis');
$data['date'] = date('Y-m-d');  
// p($data);
// exit;

require(STYLESHEET_DIR.'word/PHPWord.php');
$PHPWord = new PHPWord();
$document = $PHPWord->loadTemplate(DOCTPL_PATH.'view/bctzs.docx');
foreach ($data as $k => $v) {
    
    $document->setValue($k,$v);

}

$filedir = DATA_DIR.'word/temp.docx';
$document->save($filedir);
$filename = $data['cert_name']."-监督保持通知书.docx";
export_word($filename, $filedir);
?>