<?php
!defined('IN_SUPU') && exit('Forbidden');
$file_name=DATA_IMP."report_hr.xls";
$data=excel_read($file_name);
// exit(p($data));
$i=$j=$k=0;
foreach($data['Sheet1'] as $k=>$item){
	if($k<2)continue;
	$item['A'] = str_replace(" ", "", $item['A']);
	$new_hr=array(	"name" 		=> $item['A'],
					"card_type"	=> "01",
					"birthday"	=> $item['C'],
					"retire"	=> "1",
					"is_hire"	=> "1",
					"sex"		=> $item['B'] == '男'?'1':'2',
					"ctfrom"	=> "01000000"
					);


	$uid=$db->getField("hr","id",array("name"=>$new_hr['name']));
	if($uid)
		$db->update("hr",$new_hr,array("id"=>$uid));
	else{
		$db->insert("hr",$new_hr) && $i++;
	}
	
	
	
}
echo "success";
echo "<br/>";
echo "hr：".$i;
echo "<br/>";