<?php
!defined('IN_SUPU') && exit('Forbidden');
/**
 * 导入撤销
 * @var [type]
 */
$file_name=DATA_IMP."report_cx.xls";
$data=excel_read($file_name);
// exit(p($data));
$i=$j=$k=0;
foreach($data['Sheet1'] as $k=>$item){
	if($k<8)continue;
	$cert = $db->find_one('certificate',array('certno' => $item['U']),'*');
	if(!$cert) continue;
	$new_change = array(
			"zsid" 	=> $cert['id'],
			"cg_pid"	=> $cert['pid'],
			"ctfrom"	=> "01000000",
			"cg_type"	=> "97_03",
			"cg_meta"	=> "97_03",
			"cg_type_report" => "0197",
			"cgs_date"	=> $item['AT'],
			"cge_date"	=> $item['AT'],
			"cg_reason"	=> $item['AS'],
			"status"	=> 1,

			);
	$db->insert('certificate_change',$new_change);
	$db->update('certificate',array('status' => '03'),array('id' => $cert['id'])) && $i++;
	
	
	
}
echo "success";
echo "<br/>";
echo "hr：".$i;
echo "<br/>";