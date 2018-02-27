<?php
!defined('IN_SUPU') && exit('Forbidden');
require( DATA_DIR . 'cache/audit_ver.cache.php' );

$id = (int)getgp( 'id' );
if (!$id) {
	exit("ERROR");
}

$iso_array = array('A01' => 'Quality' ,  'A02' => 'Environment' , 'A03' => 'Occupational Health and Safety');
$data = $db->find_one('certificate' , array("id" => $id));
$ep_info  = $db->find_one('enterprises' , array("eid" => $data[eid]));

$data['s_date'] = mysql2date( 'M.j Y' ,$data['s_date']) ; 
$data['e_date'] = mysql2date( 'M.j Y' ,$data['e_date']) ; 
$data['audit_ver'] = $audit_ver_array[$data['audit_ver']][audit_basis];
$data['iso'] = $iso_array[$data[iso]];
$data['work_code'] = $ep_info['work_code'];    
$data['ep_addr_e'] = $ep_info['ep_addr_e'];    
$data['prod_addr_e'] = $ep_info['prod_addr_e'];    
// p($data);
// exit;

require(STYLESHEET_DIR.'word/PHPWord.php');
$PHPWord = new PHPWord();
$document = $PHPWord->loadTemplate(DOCTPL_PATH.'view/ce.docx');
foreach ($data as $k => $v) {
    
    $document->setValue($k,$v);

}

$filedir = DATA_DIR.'word/temp.docx';
$document->save($filedir);
$filename = $ep_info[ep_name]."-证书英文.docx";
export_word($filename, $filedir);
?>