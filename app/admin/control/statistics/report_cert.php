<?php
!defined('IN_SUPU') && exit('Forbidden');
@set_time_limit(0);
@ini_set('memory_limit', '512M');
/**
 * 证书号	证书状态	注册到期	企业名称	标准	带标	项目号	专业代码	证书范围	
 * （一阶段）
 * 	标准	审核开始	审核结束	体系人数	现场人日	带标	专业代码	审核范围	评定日期	
 * （二阶段/再认证）
 * 	标准	审核开始	审核结束	体系人数	现场人日	带标	专业代码	审核范围	评定日期	
 * 监督一
 * 	标准	审核开始	审核结束	体系人数	现场人日	带标	专业代码	审核范围	评定日期	
 * 监督二
 * 	标准	审核开始	审核结束	体系人数	现场人日	带标	专业代码	审核范围	评定日期
 */
// print_const();
if ($_POST) {
	// pe($_POST);
	$s_date = getgp('s_date');
	$e_date = getgp('e_date');
	if ($s_date && $e_date) {
		$where = " AND s_date >= '$s_date' AND s_date <= '$e_date 23:00'";
		
	}
	$status_array = array('未安排','待派人','待审批','已审批');
	$data = array();
	$query = $db->query("SELECT * FROM sp_certificate  WHERE  `status` IN ('01','02') AND deleted = 0 $where");
	while ($rt = $db->fetch_array($query)) {
		$project = array();
		$_query = $db->query("SELECT p.`status`, p.audit_ver, p.st_num , p.mark , p.audit_code , p.scope , p.sp_date, p.audit_type  ,t.tb_date , t.te_date , cti.total FROM sp_project p ,sp_task t , sp_contract_item cti  WHERE t.id = p.tid AND p.cti_id = cti.cti_id AND p.deleted = 0 AND p.audit_type IN ('1002','1003','1007','1004-1','1004-2') AND p.cti_id =  $rt[cti_id]");
		while ($_rt = $db->fetch_array($_query)) {
			if ($_rt['audit_type'] == '1007') {
				$_rt['audit_type'] = '1003';
			}
			$_rt['audit_ver'] = f_audit_ver($_rt['audit_ver']);
 			$project[$_rt['audit_type']] = $_rt;
		}
		// p($project);
		unset($_query,$_rt);

		$_data = array($rt['certno'] , f_certstate($rt['status'] ), $rt['e_date'] , $rt['cert_name'] , f_audit_ver($rt['audit_ver']) , $rt['mark'] ,$rt['cti_code'] , $rt['audit_code'] , $rt['cert_scope'],$project['1002']['audit_ver'],$project['1002']['tb_date'],$project['1002']['te_date'],$project['1002']['total'],$project['1002']['st_num'],$project['1002']['mark'],$project['1002']['audit_code'],$project['1002']['scope'],$project['1002']['sp_date'],$project['1003']['audit_ver'],$project['1003']['tb_date'],$project['1003']['te_date'],$project['1003']['total'],$project['1003']['st_num'],$project['1003']['mark'],$project['1003']['audit_code'],$project['1003']['scope'],$project['1003']['sp_date'],$project['1004-1']['audit_ver'],$project['1004-1']['tb_date'],$project['1004-1']['te_date'],$project['1004-1']['total'],$project['1004-1']['st_num'],$project['1004-1']['mark'],$project['1004-1']['audit_code'],$project['1004-1']['scope'],$project['1004-1']['sp_date'],$project['1004-2']['audit_ver'],$project['1004-2']['tb_date'],$project['1004-2']['te_date'],$project['1004-2']['total'],$project['1004-2']['st_num'],$project['1004-2']['mark'],$project['1004-2']['audit_code'],$project['1004-2']['scope'],$project['1004-2']['sp_date']
			);
		// pe($_data);
		$data[] = $_data;
	}
	unset($query,$rt);
	    
	down_excel($data);
}


tpl();


function down_excel($data){
	require_once ROOT.'/theme/Excel/PHPExcel.php';
	require_once ROOT.'/theme/Excel/PhpExcel/Writer/Excel5.php';
	include_once ROOT.'/theme/Excel/PhpExcel/IOFactory.php'; 
	$objReader = new PHPExcel_Reader_Excel5;
	$objExcel = $objReader->load(DATA_DIR."excel_tpl/report_cert.xls");
	$objExcel->setActiveSheetIndex(0);  
	$objActSheet = $objExcel->getActiveSheet();
	$i=3;
	if($data)
	foreach($data as $_val){
		$k="A";
		foreach($_val as $val){
			$objActSheet->setCellValueExplicit($k.$i,$val,PHPExcel_Cell_DataType::TYPE_STRING);
			$k++;
			}
		$i++;
	}
	
	$objWriter = PHPExcel_IOFactory::createWriter($objExcel, 'Excel5');

	$filename = "证书信息". date("Y-m-d").'-'.$title.'.xls';
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
	$objWriter->save('php://output');
	exit;  
}

?>