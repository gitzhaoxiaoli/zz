<?php
set_time_limit(0);
/*
 *---------------------------------------------------------------
 * 导出59项月报
 * 附加说明
 *---------------------------------------------------------------
 */
// CCC认证活动类型代码
function get_audit_type_auditor($audit_type){
	if($audit_type=='1001'){
		return '01';//初审一阶段
	}else if($audit_type=='1007'){
		return '02';
	}else if(strpos($audit_type,"1004")!==false){
		return '03';
	}else{
		return '05';//变更在特殊监管  04
	}
}

$type=getgp("type");
$s_date= getgp('s_date');//取数开始日期
$e_date= getgp('e_date');//取数截止日期
if(!$s_date or !$e_date)exit("ERROR");
if($fac_code = trim(getgp("fac_code"))){
	$ep_prod_id = $db->getField("enterprises","eid",array("fac_code"=>$fac_code));
}
$s_date.=' 00:00:00';
$e_date.=" 23:59:59";
$code=get_option("zdep_id");
$name=get_option("zdep_name");
function do_excel($data_cert,$file,$name){
	require_once 'theme/Excel/PHPExcel.php';
	require_once 'theme/Excel/PhpExcel/Writer/Excel5.php';
	include_once 'theme/Excel/PhpExcel/IOFactory.php'; 
	$objReader = new PHPExcel_Reader_Excel5;
	$objExcel = $objReader->load($file);
	$objExcel->setActiveSheetIndex(0);  
	// 设置工作薄名称
	$objActSheet = $objExcel->getActiveSheet();
	$i=8;
	if($data_cert)
	foreach($data_cert as $_val){
		$k="C";
		foreach($_val as $val){
			$objActSheet->setCellValueExplicit($k.$i,$val,PHPExcel_Cell_DataType::TYPE_STRING);
			$k++;
			}
		$i++;
	}
	$objWriter = PHPExcel_IOFactory::createWriter($objExcel, 'Excel5');
	$savedir="data/report_data/prod/".$name."-".date("Y-m-d").".xls";
	$objWriter->save($savedir);
	return $savedir;
}
$data = array();
if($type=='1'){
	//工厂检查计划信息表
	$where =" AND t.auditor_tb_date>='$s_date' AND t.auditor_te_date<'$e_date'";
	$where .= " AND t.deleted = '0' ";
	$where .= " AND t.status = '3'";
	$where .= " AND t.iso_prod_type = '1'";
	if($ep_prod_id)
		$where .= " AND t.eid = '$ep_prod_id'";
	$task_projects = array(); 


	$sql = "SELECT t.eid,t.ctfrom,t.id,t.auditor_tb_date,t.auditor_te_date,task_code,t.audit_type,e.ep_name,e.ep_phone,e.person,e.ep_addr,e.work_code,e.statecode,e.fac_code FROM sp_task t  LEFT JOIN sp_enterprises e ON e.eid = t.eid  WHERE 1 $where ORDER BY t.auditor_tb_date DESC ";
	$query = $db->query( $sql);
	while( $rt = $db->fetch_array( $query ) ){
		
		$rt['auditor_tb_date'] =mysql2date( 'Y-m-d', $rt['auditor_tb_date'] );
		$rt['auditor_te_date'] =mysql2date( 'Y-m-d', $rt['auditor_te_date'] );
		$type = get_audit_type_auditor( $rt['audit_type'] );
		$tat_query=$db->query("SELECT tat.uid,hr.name,hr.tel,hr.card_type,hr.card_no,tat.iso,tat.role FROM sp_task_audit_team tat LEFT JOIN sp_hr hr ON hr.id = tat.uid  WHERE 1 AND hr.deleted = 0 AND tat.deleted = 0 and tat.tid='$rt[id]' and tat.role<>'' ORDER BY role");
		$s=$z=array();
		while($_r=$db->fetch_array($tat_query)){
			$quano=$db->get_var("SELECT qua_no FROM `sp_hr_qualification` WHERE `uid` = '$_r[uid]' AND `iso` = '$_r[iso]' AND `status` = '1'");
			if($_r['role']=='1003'){
				$str = $_r['name'];
				//证件类型
				$str .= "，".intval($_r['card_type']);
				//证件号码
				$str .= "，".$_r['card_no'];
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
		$_query = $db->query("SELECT * FROM `sp_project` WHERE `iso_prod_type` = '1' AND `deleted` = '0' AND `tid` = '$rt[id]'");
		$cti_codes = $certnos = $cti = array();
		while( $_rt = $db->fetch_array( $_query ) ){
			$cti_codes[] = $_rt[cti_code];
			$certnos[] =$db->get_var("SELECT certno FROM `sp_certificate` WHERE `cti_id` = '$_rt[cti_id]' AND `deleted` = '0' and status in('01','02') ORDER BY `e_date`");
			$cti = $db->get_row("SELECT pro_areaaddr,ep_prod_addr,ep_prod_addr_e FROM `sp_contract_item` WHERE `cti_id` = '$_rt[cti_id]'");
		
		}
		if($cti)
			$rt = array_merge($rt,$cti);
		// $rt['work_code']=str_replace("-",'',$rt['work_code']);
		$data[]=array("17".$rt['task_code'],$type,$rt['auditor_tb_date'],$rt['auditor_te_date'],join("；",$s),join("；",$z),$rt['statecode'],$rt['pro_areaaddr'],$rt['work_code'],$rt['ep_name'],$rt[fac_code],$rt['person'],$rt['ep_phone'],$rt['ep_prod_addr'],"","",join("；",$cti_codes),join("；",$certnos),'');
	}
	$file=DATA_DIR."excel_tpl/prod-1.xls";
	$name = "plan";
}elseif($type=='2'){
	//工厂检查结果信息表
	$where = " AND t.deleted = '0' ";
	$where .= " AND t.status = '3'";
	$where .= " AND t.iso_prod_type = '1'";
	$where .= " AND t.tb_date >= '$s_date' AND t.te_date < '$e_date'";
	if($ep_prod_id)
		$where .= " AND t.eid = '$ep_prod_id'";
	$task_projects = array(); 


	$sql = "SELECT t.eid,t.ctfrom,t.id,t.tb_date,t.te_date,task_code,t.audit_type,t.check_result,t.tk_num,t.bufuhe,e.ep_name,e.ep_phone,e.person,e.ep_addr,e.work_code,e.statecode,e.fac_code FROM sp_task t  LEFT JOIN sp_enterprises e ON e.eid = t.eid  WHERE 1 $where ORDER BY t.auditor_tb_date DESC ";

	$query = $db->query( $sql);
	while( $rt = $db->fetch_array( $query ) ){
		
		$rt['tb_date'] =mysql2date( 'Y-m-d', $rt['tb_date'] );
		$rt['te_date'] =mysql2date( 'Y-m-d', $rt['te_date'] );
		$type = get_audit_type_auditor( $rt['audit_type'] );
		
		$tat_query=$db->query("SELECT tat.uid,hr.name,hr.tel,hr.card_type,hr.card_no,tat.iso,tat.role FROM sp_task_audit_team tat LEFT JOIN sp_hr hr ON hr.id = tat.uid  WHERE 1 AND hr.deleted = 0 AND tat.deleted = 0 and tat.tid='$rt[id]' and tat.role<>'' ORDER BY role");
		$s=$z=array();
		while($_r=$db->fetch_array($tat_query)){
			$quano=$db->get_var("SELECT qua_no FROM `sp_hr_qualification` WHERE `uid` = '$_r[uid]' AND `iso` = '$_r[iso]' AND `status` = '1'");
			if($_r['role']=='1003'){
				$str = $_r['name'];
				//证件类型
				$str .= "，".intval($_r['card_type']);
				//证件号码
				$str .= "，".$_r['card_no'];
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
		$_query = $db->query("SELECT * FROM `sp_project` WHERE `iso_prod_type` = '1' AND `deleted` = '0' AND `tid` = '$rt[id]'");
		$certnos = $cti = array();
		while( $_rt = $db->fetch_array( $_query ) ){
			$certnos[] =$db->get_var("SELECT certno FROM `sp_certificate` WHERE `cti_id` = '$_rt[cti_id]' AND `deleted` = '0' and status in('01','02') ORDER BY `e_date`");
			$cti = $db->get_row("SELECT pro_areaaddr,ep_prod_addr,ep_prod_addr_e FROM `sp_contract_item` WHERE `cti_id` = '$_rt[cti_id]'");
		
		}
		if($cti)
			$rt = array_merge($cti,$rt);
		$data[] = array(
				"1"			=> "17".$rt['task_code'],
				"2"			=> get_audit_type_auditor($rt['audit_type']),
				"3"			=> $rt['tb_date'],
				"4"			=> $rt['te_date'],
				"5"			=> join("；",$s),
				"6"			=> join("；",$z),
				"7"			=> $rt['statecode'],
				"8"			=> $rt['pro_areaaddr'],
				"9"			=> $rt['work_code'],
				"10"		=> $rt['ep_name'],
				"11"		=> $rt['fac_code'],
				"12"		=> $rt['person'],
				"13"		=> $rt['ep_phone'],
				"14"		=> $rt['ep_prod_addr'],
				"15"		=> "",
				"16"		=> "",
				"17"		=> join("；",$certnos),
				"18"		=> $rt['tk_num'],//18、本次工厂检查实际人日数
				"19"		=> $rt['check_result'],//19、工厂检查结论
				"20"		=> $rt['bufuhe'],//20、工厂检查不符合项分类代码
				"21"		=> "",
				);
	}
	$file=DATA_DIR."excel_tpl/prod-2.xls";
	$name = "plan-res";
}elseif($type=='3'){
	if($ep_prod_id)
		$where = " AND z.ep_prod_id = '$ep_prod_id'";

	//证书信息表
	require ("data/cache/b01001_audit_tpl.cache.php");
	$_query = $db->query("SELECT * FROM `sp_certificate_change` WHERE `pass_date` >= '$s_date' AND `pass_date` <= '$e_date' AND deleted = 0  AND zsid !='' AND iso_prod_type = 1");
	while($rt=$db->fetch_array($_query)){
		$zsids=explode("|",$rt[zsid]);
		if(!$zsids)continue;
		foreach($zsids as $id){
			
			//变更信息
			$sql = "SELECT z.*,cti.eid,cti.ep_manu_id,cti.ep_prod_id,cti.prod_name_chinese,cti.prod_name_english,cti.scope,cti.scope_e,cti.prod_ver,cti.ep_prod_addr,ep_prod_addr_e,pro_areaaddr
			FROM  sp_certificate z  LEFT JOIN sp_contract_item cti on cti.cti_id=z.cti_id 
			WHERE z.id = '$id' $where";
			$row = $db->get_row($sql);
			if(!$row)continue;
			$w_info = $db->get_row("SELECT statecode,ep_name,ep_name_e,work_code,areacode,ep_addr,ep_addr_e,ep_addrcode,person,ep_phone,prod_type,nature,jy_range,industry FROM `sp_enterprises` WHERE `eid` = '$row[eid]' ");
			$manu_info = $db->get_row("SELECT statecode,ep_name,ep_name_e,work_code,areacode,ep_addr,ep_addr_e,ep_addrcode,person,ep_phone,prod_type,nature,jy_range,industry FROM `sp_enterprises` WHERE `eid` = '$row[eid]' ");
			$prod_info = $db->get_row("SELECT statecode,ep_name,ep_name_e,work_code,areacode,ep_addr,ep_addr_e,ep_addrcode,person,ep_phone,prod_type,nature,jy_range,fac_code,industry,xvalue,yvalue FROM `sp_enterprises` WHERE `eid` = '$row[eid]' ");
			$arr=array();
			$arr[1] = $row[cti_code];
			$arr[2] = $row['certno'];
			$arr[3] = ""; 		//3、互认标识
			$arr[4] = $row['status']; 	//
			$arr[5] = (!$row['first_date'] || $row['first_date'] == '0000-00-00')?$row[s_date]:$row['first_date']; 	//首次获证日期
			$arr[6] = $row['s_date'];		//6、证书发证日期
			$arr[7] = $row['e_date'];	//7、证书到期日期
			$arr[8] = "";	//8、认证模式
			$arr[9] = $row['prod_ver'];		//9、认证依据的标准和技术要求
			$arr[10] = $row['prod_name_chinese'];	//10、产品名称及单元(主)
			$arr[11] = $row['prod_name_english'];	//11、产品名称及单元(次)[英]
			$arr[12] = $row['scope'];			//12、型号规格
			$arr[13] = ""; 			//13、HS编码
			$arr[14] = $w_info['statecode']; 	//14、认证委托人所在国家地区
			$arr[15] = $w_info['ep_name']; 		//15、认证委托人名称（主）
			$arr[16] = $w_info['ep_name_e']; 		//16、认证委托人名称（次）
			$arr[17] = $w_info['work_code'];		//17、认证委托人组织机构代码
			$arr[18] = $w_info['areacode']; 	//18、认证委托人注册地行政区划
			$arr[19] = $w_info['ep_addr']; 	//19、认证委托人地址（主）
			$arr[20] = $w_info['ep_addr_e']; 	//20、认证委托人地址（次）
			$arr[21] = $w_info['ep_addrcode']; 		//21、认证委托人邮政编码
			$arr[22] = $w_info['person'];	//22、认证委托人联系人
			$arr[23] = $w_info['ep_phone'];	//23、认证委托人联系电话
			$arr[24] = $w_info['nature'];//24、认证委托人机构类型
			$arr[25] = $w_info['prod_type']; //25、认证委托人经济类型
			$arr[26] = $w_info['industry']; //26、认证委托人所属国民经济行业
			$arr[27] = $w_info['jy_range'];	//27、认证委托人经营范围

			$arr[28] = $manu_info['statecode']; 	//28、生产者（制造商）所在国家地区
			$arr[29] = $manu_info['ep_name']; 		//29、生产者（制造商）名称（主）
			$arr[30] = $manu_info['ep_name_e']; 		//30、生产者（制造商）名称（次）
			$arr[31] = $manu_info['work_code'];		//31、生产者（制造商）组织机构代码
			$arr[32] = $manu_info['areacode']; 	//32、生产者（制造商）注册地行政区划
			$arr[33] = $manu_info['ep_addr']; 	//33、生产者（制造商）地址（主）
			$arr[34] = $manu_info['ep_addr_e']; 	//34、生产者（制造商）地址（次）
			$arr[35] = $manu_info['ep_addrcode']; 		//35、生产者（制造商）邮政编码
			$arr[36] = $manu_info['person'];	//36、生产者（制造商）联系人
			$arr[37] = $manu_info['ep_phone'];	//37、生产者（制造商）联系电话
			$arr[38] = $manu_info['nature'];//38、生产者（制造商）机构类型
			$arr[39] = $manu_info['prod_type']; //39、生产者（制造商）经济类型
			$arr[40] = $manu_info['industry']; //40、生产者（制造商）所属国民经济行业
			$arr[41] = $manu_info['jy_range'];	//41、生产者（制造商）经营范围
			
			
			$arr[42] = $prod_info['statecode']; 	//42、生产企业所在国家地区
			$arr[43] = $prod_info['fac_code']; 	//43、生产企业编号（工厂编号）
			$arr[44] = $prod_info['ep_name']; 		//44、生产企业名称（主）
			$arr[45] = $prod_info['ep_name_e']; 		//45、生产企业名称（次）
			$arr[46] = $prod_info['work_code'];		//46、生产企业组织机构代码
			$arr[47] = $row['pro_areaaddr']; 	//47、生产企业注册地行政区划
			$arr[48] = $row['ep_prod_addr']; 	//48、生产企业地址（主）
			$arr[49] = $row['ep_prod_addr_e']; 	//49、生产企业地址（次）
			$arr[50] = $prod_info['ep_addrcode']; 		//50、生产企业邮政编码
			$arr[51] = $prod_info['person'];	//51、生产企业联系人
			$arr[52] = $prod_info['ep_phone'];	//52、生产企业联系电话
			$arr[53] = $prod_info['nature'];//53、生产企业机构类型
			$arr[54] = $prod_info['prod_type']; //54、生产企业经济类型
			$arr[55] = $prod_info['industry']; //55、生产企业所属国民经济行业
			$arr[56] = $prod_info['jy_range'];	//56、生产企业经营范围
			$arr[57] = $row['total'];	//57、生产企业人数
			$arr[58] = $prod_info['xvalue'];	//58、生产企业实际地址经度坐标
			$arr[59] = $prod_info['yvalue'];	//59、生产企业实际地址纬度坐标
			
			$arr[60] = '';					//暂停原因
			$arr[61] = '';					//暂停开始时间
			$arr[62] = '';					//暂停结束时间
			$arr[63] = '';					//撤销原因
			$arr[64] = '';					//撤销日期
			$arr[65] = '';					//65、注销原因
			$arr[66] = '';					//66、注销时间
			if($row['status']=='02'){
				$arr[60]=$rt[cert_value];
				$arr[61]=$rt[cgs_date];
				$arr[62]=$rt[cge_date];
			}
			if($row['status']=='03'){
				$arr[63]=$rt[cert_value];
				$arr[64]=$rt[cgs_date];
			}
			if($row['status']=='04'){
				$arr[65]=$rt[cert_value];
				$arr[66]=$rt[cgs_date];
			}
			$arr[67] = $rt['cg_date'];					//67、变更原因
			$arr[68] = $rt['cg_reason'];					//68、变更时间
			$arr[69] = "";					//69、认证决定人员名单
			$arr[70] = "";					//70、认证决定日期
			$arr[71] = '';					//71、转机构换证的转出机构原认证证书号
			$arr[72] = '';					//72、认证证书第1类附件文件名（认证证书扫描件）
			$arr[73] = '';					//73、认证证书第2类附件文件名（产品规格型号）
			$arr[74] = '';					//74、认证证书第3类附件文件名（型式试验报告）
			$arr[75] = '';					//75、认证证书第4类附件文件名（其他资料附件）
			$arr[76] = '';					//76、上报类型
			$arr[77] = '';					//77、备注
			if(in_array($row['status'],array("02","03","04"))){
				$arr[76] = '05';
				
			}
			
			$data[] = $arr;
	}
}



	

	
	// 新发证书 （）
		$sql = "SELECT z.*,cti.eid,cti.ep_manu_id,cti.ep_prod_id,cti.prod_name_chinese,cti.prod_name_english,cti.scope,cti.scope_e,cti.prod_ver,cti.ep_prod_addr,cti.audit_tpl,ep_prod_addr_e,pro_areaaddr
		FROM  sp_certificate z  LEFT JOIN sp_contract_item cti on cti.cti_id=z.cti_id 
		WHERE z.s_date>'$s_date' AND z.s_date<='$e_date' and z.deleted = 0 AND z.iso_prod_type = 1 $where";
	$res = $db->query($sql);
	while($row=$db->fetch_array($res)){
		$w_info = $db->get_row("SELECT statecode,ep_name,ep_name_e,work_code,areacode,ep_addr,ep_addr_e,ep_addrcode,person,ep_phone,prod_type,nature,jy_range,industry FROM `sp_enterprises` WHERE `eid` = '$row[eid]' ");
		$manu_info = $db->get_row("SELECT statecode,ep_name,ep_name_e,work_code,areacode,ep_addr,ep_addr_e,ep_addrcode,person,ep_phone,prod_type,nature,jy_range,industry FROM `sp_enterprises` WHERE `eid` = '$row[ep_manu_id]' ");
		$prod_info = $db->get_row("SELECT statecode,ep_name,ep_name_e,work_code,areacode,ep_addr,ep_addr_e,ep_addrcode,person,ep_phone,prod_type,nature,jy_range,fac_code,industry,xvalue,yvalue FROM `sp_enterprises` WHERE `eid` = '$row[ep_prod_id]' ");
		$p_info = $db->find_one("project",array("id"=>$row[pid]),"comment_a_name,sp_date");
		$arr=array();
		$arr[1] = $row[cti_code];
		$arr[2] = $row['certno'];
		$arr[3] = ""; 		//3、互认标识
		$arr[4] = $row['status']; 	//
		$arr[5] = (!$row['first_date'] || $row['first_date'] == '0000-00-00')?$row[s_date]:$row['first_date']; 	//首次获证日期
		$arr[6] = $row['s_date'];		//6、证书发证日期
		$arr[7] = $row['e_date'];	//7、证书到期日期
		$arr[8] = $b01001_audit_tpl_array[$row[audit_tpl]][name];	//8、认证模式
		$arr[9] = $row['prod_ver'];		//9、认证依据的标准和技术要求
		$arr[10] = $row['prod_name_chinese'];	//10、产品名称及单元(主)
		$arr[11] = $row['prod_name_english'];	//11、产品名称及单元(次)[英]
		$arr[12] = $row['scope'];			//12、型号规格
		$arr[13] = ""; 			//13、HS编码
		$arr[14] = $w_info['statecode']; 	//14、认证委托人所在国家地区
		$arr[15] = $row['cert_name']; 		//15、认证委托人名称（主）
		$arr[16] = $row['cert_name_e']; 		//16、认证委托人名称（次）
		$arr[17] = $w_info['work_code'];		//17、认证委托人组织机构代码
		$arr[18] = $w_info['areacode']; 	//18、认证委托人注册地行政区划
		$arr[19] = $w_info['ep_addr']; 	//19、认证委托人地址（主）
		$arr[20] = $w_info['ep_addr_e']; 	//20、认证委托人地址（次）
		$arr[21] = $w_info['ep_addrcode']; 		//21、认证委托人邮政编码
		$arr[22] = $w_info['person'];	//22、认证委托人联系人
		$arr[23] = $w_info['ep_phone'];	//23、认证委托人联系电话
		$arr[24] = $w_info['nature'];//24、认证委托人机构类型
		$arr[25] = $w_info['prod_type']; //25、认证委托人经济类型
		$arr[26] = $w_info['industry']; //26、认证委托人所属国民经济行业
		$arr[27] = $w_info['jy_range'];	//27、认证委托人经营范围

		$arr[28] = $manu_info['statecode']; 	//28、生产者（制造商）所在国家地区
		$arr[29] = $row['manu_name']; 		//29、生产者（制造商）名称（主）
		$arr[30] = $row['manu_name_e']; 		//30、生产者（制造商）名称（次）
		$arr[31] = $manu_info['work_code'];		//31、生产者（制造商）组织机构代码
		$arr[32] = $manu_info['areacode']; 	//32、生产者（制造商）注册地行政区划
		$arr[33] = $manu_info['ep_addr']; 	//33、生产者（制造商）地址（主）
		$arr[34] = $manu_info['ep_addr_e']; 	//34、生产者（制造商）地址（次）
		$arr[35] = $manu_info['ep_addrcode']; 		//35、生产者（制造商）邮政编码
		$arr[36] = $manu_info['person'];	//36、生产者（制造商）联系人
		$arr[37] = $manu_info['ep_phone'];	//37、生产者（制造商）联系电话
		$arr[38] = $manu_info['nature'];//38、生产者（制造商）机构类型
		$arr[39] = $manu_info['prod_type']; //39、生产者（制造商）经济类型
		$arr[40] = $manu_info['industry']; //40、生产者（制造商）所属国民经济行业
		$arr[41] = $manu_info['jy_range'];	//41、生产者（制造商）经营范围
		
		
		$arr[42] = $prod_info['statecode']; 	//42、生产企业所在国家地区
		$arr[43] = $prod_info['fac_code']; 	//43、生产企业编号（工厂编号）
		$arr[44] = $row['pro_name']; 		//44、生产企业名称（主）
		$arr[45] = $row['pro_name_e']; 		//45、生产企业名称（次）
		$arr[46] = $prod_info['work_code'];		//46、生产企业组织机构代码
		$arr[47] = $row['pro_areaaddr']; 	//47、生产企业注册地行政区划
		$arr[48] = $row['ep_prod_addr']; 	//48、生产企业地址（主）
		$arr[49] = $row['ep_prod_addr_e']; 	//49、生产企业地址（次）
		$arr[50] = $prod_info['ep_addrcode']; 		//50、生产企业邮政编码
		$arr[51] = $prod_info['person'];	//51、生产企业联系人
		$arr[52] = $prod_info['ep_phone'];	//52、生产企业联系电话
		$arr[53] = $prod_info['nature'];//53、生产企业机构类型
		$arr[54] = $prod_info['prod_type']; //54、生产企业经济类型
		$arr[55] = $prod_info['industry']; //55、生产企业所属国民经济行业
		$arr[56] = $prod_info['jy_range'];	//56、生产企业经营范围
		$arr[57] = $row['total'];	//57、生产企业人数
		$arr[58] = $prod_info['xvalue'];	//58、生产企业实际地址经度坐标
		$arr[59] = $prod_info['yvalue'];	//59、生产企业实际地址纬度坐标
		
		$arr[60] = '';					//暂停原因
		$arr[61] = '';					//暂停开始时间
		$arr[62] = '';					//暂停结束时间
		$arr[63] = '';					//撤销原因
		$arr[64] = '';					//撤销日期
		$arr[65] = '';					//65、注销原因
		$arr[66] = '';					//66、注销时间
		$arr[67] = "";					//67、变更原因
		$arr[68] = "";					//68、变更时间
		$arr[69] = $p_info[comment_a_name];					//69、认证决定人员名单
		$arr[70] = $p_info[sp_date];					//70、认证决定日期
		$arr[71] = '';					//71、转机构换证的转出机构原认证证书号
		$arr[72] = '';					//72、认证证书第1类附件文件名（认证证书扫描件）
		$arr[73] = '';					//73、认证证书第2类附件文件名（产品规格型号）
		$arr[74] = '';					//74、认证证书第3类附件文件名（型式试验报告）
		$arr[75] = '';					//75、认证证书第4类附件文件名（其他资料附件）
		$arr[76] = '01';					//76、上报类型
		$arr[77] = '';					//77、备注
		$data[] = $arr;
	}
	$file=DATA_DIR."excel_tpl/prod-3.xls";
	$name = "cert";

}else{
	echo "ERROR";
	EXIT;
	

}
echo do_excel($data,$file,$name);
exit;

?>
