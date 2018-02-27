<?php
!defined('IN_SUPU') && exit('Forbidden');
/**
 * 导入暂停
 * @var [type]
 */
$file_name=DATA_IMP."report_zt.xls";
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
			"cg_type"	=> "97_01",
			"cg_meta"	=> "97_01",
			"cg_type_report" => "0197",
			"cgs_date"	=> $item['AQ'],
			"cge_date"	=> $item['AR'],
			"cg_reason"	=> $item['AP'],
			"status"	=> 1,

			);
	$db->insert('certificate_change',$new_change);
	$db->update('certificate',array('status' => '02'),array('id' => $cert['id'])) && $i++;
	
	
	
}
echo "success";
echo "<br/>";
echo "hr：".$i;
echo "<br/>";