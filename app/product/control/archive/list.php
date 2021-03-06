<?php
!defined('IN_SUPU') && exit('Forbidden');
//资料回收列表
extract( $_GET, EXTR_SKIP );
	$fields = $join = $where = '';
	 $order=" ORDER BY p.id DESC";
//标签样式
	$redata_status = (int)$redata_status;

	$redata_0 = $redata_1 = '';
	${'redata_'.$redata_status} = " ui-tabs-active ui-state-active";

	if($sort=getgp("sort") and $sort_val=getgp("sort_val")){

		$sort_val=="1" && $order=" ORDER BY t.$sort DESC";
		$sort_val=="2" && $order=" ORDER BY t.$sort ASC";
	
	
	}
//搜索条件
	//上传时间 起
	if( $upload_date_start ){
		$tids=$db->get_col("SELECT id FROM `sp_task` WHERE `upload_file_date` >= '$upload_date_start 00:00:00' AND `deleted` = '0'");
		$tids=array_merge($tids,array(-1));
		$where .=" AND p.tid IN (".join(",",$tids).")";
	}
	// 上传时间 止
	if( $upload_date_end ){
		$tids=$db->get_col("SELECT id FROM `sp_task` WHERE `upload_file_date` <= '$upload_date_end 23:00:00' AND `deleted` = '0'");
		$tids=array_merge($tids,array(-1));
		$where .=" AND p.tid IN (".join(",",$tids).")";
	}
	if($fac_code=trim($fac_code)){
    $res = $db->find_one("enterprises",array('fac_code'=>$fac_code),"eid");
    $where .= "AND p.ep_prod_id = '$res[eid]'";
}


	//企业名称
	if( $ep_name ){
		$_eids = array(-1);
		$_query = $db->query("SELECT eid FROM sp_enterprises WHERE ep_name LIKE '%".str_replace('%','\%',trim($ep_name))."%'");
		while( $rt = $db->fetch_array( $_query ) ){
			$_eids[] = $rt['eid'];
		}
		$where .= " AND p.eid IN (".implode(',',$_eids).")";
	}
	//省份
if( $areacode=getgp("areacode") ){
	$pcode = substr($areacode,0,2) . '0000';
	$_eids = array(-1);
	$_query = $db->query("SELECT eid FROM sp_enterprises WHERE LEFT(areacode,2) = '".substr($areacode,0,2)."'");
	while( $rt = $db->fetch_array( $_query ) ){
		$_eids[] = $rt['eid'];
	}
	$where .= " AND p.eid IN (".implode(',',$_eids).")";
	unset( $_eids, $_query, $rt, $_eids );
	
	$province_select = str_replace( "value=\"$pcode\">", "value=\"$pcode\" selected>" , $province_select );
}


//删除合同编号


//合同认证申请编号
if( $cti_code=trim($cti_code) ){
	$where .= " AND p.cti_code like '%$cti_code%'";
}

	//认证体系
	if( $iso ){
		$where .= " AND p.iso = '$iso'";
		$iso_select = str_replace( "value=\"$iso\">", "value=\"$iso\" selected>" , $iso_select );
	}

	//审核类型
	if( $audit_type ){
		$where .= " AND p.audit_type = '$audit_type'";
		$audit_type_select = str_replace( "value=\"$audit_type\">", "value=\"$audit_type\" selected>" , $audit_type_select );
	}

	//标准版本
	$audit_ver = getgp( 'audit_ver' );
	if( $audit_ver ){
		$where .= " AND p.audit_ver = '$audit_ver'";
		$audit_ver_select = str_replace( "value=\"$audit_ver\">", "value=\"$audit_ver\" selected>" , $audit_ver_select );
	}
	if($t_date_s){
		$query=$db->query("SELECT id FROM `sp_task` WHERE `tb_date` > '$t_date_s' AND `deleted` = '0'  ORDER BY te_date DESC");
		$_tids=array(-1);
		while($rt=$db->fetch_array($query)){
			$_tids[]=$rt[id];
		
		
		}
		$where .=" AND p.tid in(".implode(",",$_tids).")";
	
	
	
	}
	if($t_date_e){
		$query=$db->query("SELECT id FROM `sp_task` WHERE `te_date` < '$t_date_e' AND `deleted` = '0'     ORDER BY te_date DESC");
		$_tids=array(-1);
		while($rt=$db->fetch_array($query)){
			$_tids[]=$rt[id];
		
		
		}
		$where .=" AND p.tid in(".implode(",",$_tids).")";
	
	
	
	}

	//合同来源限制
	$len = get_ctfrom_level( current_user( 'ctfrom' ) );
	if( $ctfrom && substr( $ctfrom, 0, $len ) == substr( current_user( 'ctfrom' ), 0, $len ) ){
		$_len = get_ctfrom_level( $ctfrom );
		$len = $_len;
	} else {
		$ctfrom = current_user( 'ctfrom' );
	}
	switch( $len ){
		case 2	: $add = 1000000; break;
		case 4	: $add = 10000; break;
		case 6	: $add = 100; break;
		case 8	: $add = 1; break;
	}

	$ctfrom_e = sprintf("%08d",$ctfrom+$add);
	$where .= " AND p.ctfrom >= '$ctfrom' AND p.ctfrom < '$ctfrom_e'";

	$ctfrom_select = str_replace( "value=\"$ctfrom\"", "value=\"$ctfrom\" selected" , $ctfrom_select );


	//要获取的字段
	$fields .= "p.id,p.cti_code,p.ctfrom,p.audit_type,p.redata_date,p.iso,p.redata_note,p.eid,p.tid,p.ct_id";
	$fields .=",hr.name,e.ep_name,e.areacode";
	//$fields .= ",c.pre_date";

	$fields .=",t.tb_date,t.te_date,cti.prod_id,cti.prod_name_chinese,cti.ep_manu_id,cti.ep_prod_id";

	//要关联的表
	$join .= " LEFT JOIN sp_task t ON t.id = p.tid";
	$join .= " LEFT JOIN sp_enterprises e ON e.eid = p.eid";
	$join .= " LEFT JOIN sp_hr hr ON hr.id = p.redata_uid";
	$join .= " LEFT JOIN sp_contract_item cti ON cti.cti_id = p.cti_id";

	//限制条件
	// $where .= " AND  p.audit_type <>'1002' "; //排除一阶段
	
	//检验不用收资料
	$where .= " AND p.deleted = '0' AND p.status = 3 AND p.iso_prod_type = 1";
    //$where .= " AND t.te_date < '".date("Y-m-d H:i:s")."'";

	if( !$export ){
		$restate_total = array(0,0);
		$restate_total[0]=$db->get_var("SELECT COUNT(*) total FROM sp_project p $join WHERE 1 $where AND p.redata_status =0 ");
		$restate_total[1]=$db->get_var("SELECT COUNT(*) total FROM sp_project p $join WHERE 1 $where AND p.redata_status !=0 ");
		/*
		$query = $db->query("SELECT p.redata_status,COUNT(*) total FROM sp_project p WHERE 1 $where GROUP BY p.redata_status");
		
		while( $rt = $db->fetch_array( $query ) ){
			$restate_total[$rt['redata_status']] = $rt['total'];
		}
		*/
		 $pages= numfpage( $restate_total[$redata_status], 20 );
	}
	// echo $redata_status;
    // p($pages);
	// exit;

	//$where .= " AND t.te_date < ".date("Y-m-d");
	$where .= " AND p.redata_status = '$redata_status'";
	


	$resdb = array();
    $sql = "SELECT $fields FROM sp_project p $join WHERE 1 $where $order $pages[limit]";

	$query = $db->query( $sql );
	// echo $db->sql;
	// exit;
	while( $rt = $db->fetch_array( $query ) ){
		$rt['province']		= f_region_province( $rt['areacode'] );
		$rt['audit_type_V'] = f_audit_type( $rt['audit_type'] );
		$rt['iso_V'] = f_iso( $rt['iso'] );
		$rt['ctfrom_V'] = f_ctfrom( $rt['ctfrom'] );
		$rt['tb_date'] = mysql2date( 'Y-m-d', $rt['tb_date'] );
		$rt['te_date'] = mysql2date( 'Y-m-d', $rt['te_date'] );
		$rt['ep_manu'] = $db->getField("enterprises","ep_name",array("eid"=>$rt[ep_manu_id]));
		$rt['ep_prod'] = $db->getField("enterprises","ep_name",array("eid"=>$rt[ep_prod_id]));
		$resdb[] = chk_arr($rt);
	}
	
	
	
	if( !$export ){
		tpl( 'archive/list' );
	} else { //导出excel表格
		ob_start();
		tpl( 'xls/list_archive' );
		$data = ob_get_contents();
		ob_end_clean();

		export_xls( '资料回收列表', $data );
	}