<?php
set_time_limit(0);
/*
 *---------------------------------------------------------------
 * 导出59项月报
 * 附加说明
 *---------------------------------------------------------------
 */
$new_data = array();
$s_date= getgp('s_date');//取数开始日期
$e_date= getgp('e_date');//取数截止日期
$s_date.=' 00:00:00';
$e_date.=" 23:59:59";
$where =" AND t.tb_date>='$s_date' AND t.te_date<'$e_date'";
$code=get_option("zdep_id");
$name=get_option("zdep_name");
function do_excel($data_cert){
	require_once 'theme/Excel/PHPExcel.php';
	require_once 'theme/Excel/PhpExcel/Writer/Excel2007.php';
	require_once 'theme/Excel/PhpExcel/Writer/Excel5.php';
	include_once 'theme/Excel/PhpExcel/IOFactory.php'; 
	//$objExcel = new PHPExcel(); 
	$objReader = new PHPExcel_Reader_Excel5;
	$objExcel = $objReader->load("data/plan.xls");
	$objExcel->setActiveSheetIndex(0);  
	// 设置工作薄名称
	$objActSheet = $objExcel->getActiveSheet();
	$i=8;
	if($data_cert)
	foreach($data_cert as $_val){
		ksort($_val);
		// $objActSheet->setCellValueExplicit('AF'.$i,$result[1],PHPExcel_Cell_DataType::TYPE_STRING);
        // $objActSheet->getStyle('AF'.$i)->getNumberFormat()->setFormatCode("@");
		// $j=$i+1;
		$k="C";
		foreach($_val as $val){
			$objActSheet->setCellValueExplicit($k.$i,$val,PHPExcel_Cell_DataType::TYPE_STRING);
			$k++;
			}
		$i++;
	}
	$objWriter = PHPExcel_IOFactory::createWriter($objExcel, 'Excel5');
	/*
	$filename = date("Y-m-d").'-'.$title.'.xls';
	ob_end_clean();//清除缓冲区,避免乱码
	header("Content-Type: application/force-download");  
	header("Content-Type: application/octet-stream");  
	header("Content-Type: application/download");  
	header('Content-Disposition:inline;filename="'.iconv( 'UTF-8', 'gbk', $filename ).'"');  
	header("Content-Transfer-Encoding: binary");  
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");  
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");  
	header("Pragma: no-cache");  
	//$objWriter->save('php://output');   */
	$savedir="data/report_data/".date("Y-m-d")."-plan.xls";
	// if(file_exists($savedir))
		// @unlink($savedir);
	$objWriter->save($savedir);
	return $savedir;
}

//审核计划列表

$where .= " AND t.deleted = '0' ";
$where .= " AND t.status = '3'";
$where .= " AND p.tid != '0'";
$where .= " AND p.iso_prod_type = '0'";

$join .= " LEFT JOIN sp_project p ON p.tid = t.id";
$join .= " LEFT JOIN sp_enterprises e ON e.eid = t.eid ";

$task_projects = array(); 


$sql = "SELECT cti.cti_code,t.eid,t.ctfrom,t.id,t.tb_date,t.te_date,p.cti_id,p.bao_date,p.bao_uid,p.id pid,p.iso,p.audit_type,p.audit_ver,e.ep_name,e.person,e.person_tel,e.ep_fax,e.ep_phone,e.ep_addr,e.prod_addr,e.work_code,e.areacode,e.statecode FROM sp_project p LEFT JOIN sp_task t ON p.tid = t.id LEFT JOIN sp_enterprises e ON e.eid = p.eid LEFT JOIN sp_contract_item cti ON cti.cti_id=p.cti_id WHERE 1 $where ORDER BY t.tb_date DESC ";

$query = $db->query( $sql);
while( $rt = $db->fetch_array( $query ) ){
	
	$type="";
	switch( $rt['audit_type'] ){
		case '1002' : $type = '0101'; break;
		case '1003' : $type = '0102'; break;
		case '1004-1' : 
		case '1004-2' : $type = '03'; break;
		case '1007' : $type = '0202'; break;
		case '1008' : $type = '04'; break;
		default : $type = '04'; break;
	}
	
	$tat_query=$db->query("SELECT tat.uid,hr.name,hr.tel,hr.card_type,hr.card_no,tat.iso,tat.role FROM sp_task_audit_team tat LEFT JOIN sp_hr hr ON hr.id = tat.uid  WHERE 1 AND hr.deleted = 0 AND tat.deleted = 0 and tat.pid='$rt[pid]' and tat.role<>'' ORDER BY role");
	$s=$z=array();
	while($_r=$db->fetch_array($tat_query)){
		$quano=$db->get_var("SELECT qua_no FROM `sp_hr_qualification` WHERE `uid` = '$_r[uid]' AND `iso` = '$_r[iso]' AND `status` = '1'");
		if($_r['role']=='1003'){
			$str = $_r['name'];
			//证件类型
			$str .= "，".$_r['card_type'];
			//证件号码
			$str .= "，".$_r['card_no'];
			$str .= '，';
			$str .= $_r['tel'];
			//$str .= "(".f_iso($rt['iso']).":专家)";
			$z[] = $str;
		} else {
			$str = $quano;
			$str .= '，';
			$str .= $_r['name']; 
			$str .= '，';
			$str .= substr($_r['role'],-2); 
			$str .= '，';
			$str .= $_r['tel'];
			$s[] = $str;
		}
		
	
	}
	$rt['certno']=$db->get_var("SELECT certno FROM `sp_certificate` WHERE `cti_id` = '$rt[cti_id]' AND `deleted` = '0' and status in('01','02') ORDER BY `e_date`");
	$rt['work_code']=str_replace("-",'',$rt['work_code']);
	$data[]=array($rt['cti_code'] . $rt[audit_type],$type,$rt['tb_date'],$rt['te_date'],join("；",$s),join("；",$z),$rt['statecode'],$rt['areacode'],$rt['work_code'],$rt['ep_name'],$rt['person'],$rt['ep_phone']."、".$rt['ep_fax'],$rt['prod_addr'],$rt['cti_code'],$rt['certno'],$rt['audit_ver'],$code,'');
}
 

//输出Execl文件
/**/ 
// p($auditors);
// p($data);
echo do_excel($data);
exit;

?>
