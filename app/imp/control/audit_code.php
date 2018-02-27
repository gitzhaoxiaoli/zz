<?php
/*
*
*/
$db->drop("sp_settings_audit_code");
$file= CONF."imp/audit_code.xlsx";
$data = excel_read($file);
$iso_array = array("QMS","EMS","OHSMS","EnMS");
foreach($data['Sheet1'] as $k => $item){
	if($k<2 or !in_array($item['B'],$iso_array))continue;
		$code = $item['C'];
		$msg = $item['D'];
		$iso = substr($item[A],0,3);
		$new = array(
				"code"		=> $code,
				"dalei"		=> substr($code,0,2),
				"zhonglei"		=> substr($code,3,2),
				"xiaolei"		=> substr($code,-2),
				"shangbao"	=> $code,
				"msg"		=> $msg,
				"iso"		=> $iso,
				);
		$db->insert("settings_audit_code",$new) && $i++;
	
	
	
	
	
}

echo "SUCCESS $i";



