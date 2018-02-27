<?php
// 试验方案
$province_select = f_province_select();//省分下拉 (搜索用)

//认证评定
   extract($_GET, EXTR_SKIP);
//标签样式	
	$status = (int)$status;
	$status_0 = $status_1 = $status_2 = $status_3 = '';
	${'status_'.$status} = " ui-tabs-active ui-state-active";
//搜索条件
$fields = $join = $where = '';
	//企业名称
	if( $ep_name = trim($ep_name) ){
		$where .= " AND ep_name like '%$ep_name%'";
	}
//省份
if( $areacode=getgp("areacode") ){
	$pcode = substr($areacode,0,2) . '0000';
	$_eids = array(-1);
	$_query = $db->query("SELECT eid FROM sp_enterprises WHERE LEFT(areacode,2) = '".substr($areacode,0,2)."'");
	while( $rt = $db->fetch_array( $_query ) ){
		$_eids[] = $rt['eid'];
	}
	$where .= " AND pc.eid IN (".implode(',',$_eids).")";
	unset( $_eids, $_query, $rt, $_eids );
	
	$province_select = str_replace( "value=\"$pcode\">", "value=\"$pcode\" selected>" , $province_select );
}

//合同申请编号
if( $cti_code=trim($cti_code) ){
	echo $where .= " AND pc.cti_code like '%$cti_code%'";
}

/* 	//审核类型
	if( $audit_type ){
		$where .= " AND pc.audit_type = '$audit_type'";
		$audit_type_select = str_replace( "value=\"$audit_type\">", "value=\"$audit_type\" selected>" , $audit_type_select );
	}

	//标准版本
	$audit_ver = getgp( 'audit_ver' );
	if( $audit_ver ){
		$where .= " AND pc.audit_ver = '$audit_ver'";
		$audit_ver_select = str_replace( "value=\"$audit_ver\">", "value=\"$audit_ver\" selected>" , $audit_ver_select );
	}

	//审核时间
	$_tids = array();
	$task_where = '';
	if( $audit_start_s ){
		$task_where .= " AND tb_date >= '$audit_start_s'";
	}
	if( $audit_start_e ){
		$task_where .= " AND tb_date <= '$audit_start_e'";
	}
	if( $audit_end_s ){
		$task_where .= " AND te_date >= '$audit_end_s'";
	}
	if( $audit_end_e ){
		$task_where .= " AND te_date <= '$audit_end_e'";
	}
	if( $task_where ){
		$query = $db->query("SELECT id FROM sp_task WHERE 1 $task_where");
		while( $rt = $db->fetch_array( $query ) ){
			$_tids[] = $rt['id'];
		}
	}
	if( $_tids ){
		$where .= " AND pc.tid IN (".implode(',',$_tids).")";
	}
 */
 
	//合同来源限制
	$len = get_ctfrom_level( current_user( 'ctfrom' ) ); //获取当前用户合同来源

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
 	$where .= " AND pc.ctfrom >= '$ctfrom' AND pc.ctfrom < '$ctfrom_e'";
 
/* //审批时间
	if( $sp_start ){
		$where .= " AND pc.sp_date >= '$sp_start'";
	}
	if( $sp_end ){
		$where .= " AND pc.sp_date <= '$sp_end'";
	} */
  //生产企业搜索
  
	if($ep_prod_name){
		$ep_prod_ids = $db->getCol("enterprises",'eid',"ep_name LIKE '%$ep_prod_name%'");
		
		$where .= " AND ep_prod_id IN (".implode(',',$ep_prod_ids).")";
	};
	if($ep_manu_name)
    {
		$ep_manu_ids = $db->getCol("enterprises",'eid',"ep_name LIKE '%$ep_manu_name%'");
		$where .= " AND ep_manu_id IN (".implode(',',$ep_manu_ids).")";
	}		
	
	//要获取的字段
	$fields .= "pc.*,e.ep_name";

	//要关联的表
	$join .= " LEFT JOIN sp_enterprises e ON e.eid = pc.eid";


	$total = array(0,0);
	$where .= " AND pc.deleted=0 AND pc.iso_prod_type = 1 ";
	if( !$export ){
		$query = $db->query("SELECT COUNT(*) total ,protocol_status FROM `sp_project` pc $join WHERE 1  $where GROUP BY protocol_status");
		while($rt = $db->fetch_array($query)){
			$total[$rt['protocol_status']] = $rt['total'];
		}
		
		
		$pages = numfpage( $total[$status] );
	}
	$where .= " AND pc.protocol_status = '$status'";
	$resdb = array();
	$sql = "SELECT $fields FROM `sp_project` pc $join WHERE 1 $where ORDER BY pc.id DESC $pages[limit]";
	// echo $sql;
	//exit;
	$query = $db->query( $sql );
	while( $rt = $db->fetch_array( $query ) ){
		// $rt['audit_type_V'] = f_audit_type( $rt['audit_type'] );
		$rt['audit_ver_V'] = f_audit_ver( $rt['audit_ver'] );
		//$rt['iso_V'] = f_iso( $rt['iso'] );
		//$rt['ctfrom_V'] = f_ctfrom( $rt['ctfrom'] );
		//$rt['is_finance_V'] = ($rt['is_finance'] == '2')?'收完':'未收完';
		$rt['ep_prod_name'] = $db->getField('enterprises','ep_name',array('eid'=>$rt['ep_prod_id']));
		$rt['ep_manu_name'] = $db->getField('enterprises','ep_name',array('eid'=>$rt['ep_manu_id']));
		// $rt['prod_name_chinese'] = $db->getField('prod_item','prod_name_chinese',array('cti_id'=>$rt['cti_id']));
		$resdb[] = $rt;
	}	
// p($resdb);die;
	if( !$export ){
		tpl();
	} else {
		ob_start();
		tpl( 'xls/list_wait_test' );
		$data = ob_get_contents();
		ob_end_clean();

		export_xls( '检验安排', $data );
	}
	