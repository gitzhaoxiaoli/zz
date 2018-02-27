<?php
!defined('IN_SUPU') && exit('Forbidden');
$status_array=array("未安排","已安排","待审批","已审批","","监督维护","退回");

	/******************************
	 #			搜   索			  #
	 ******************************/


	$fields = $join = $where = $page_str = '';


	$ep_name		= getgp( 'ep_name' ); //企业名称
	$ctfrom			= getgp( 'ctfrom' ); //合同来源
	$audit_type		= getgp( 'audit_type' ); //审核类型
	$ct_code		= trim(getgp( 'ct_code' )); //合同编号
	$cti_code		= trim(getgp( 'cti_code' )); //合同项目编号
	$pre_date_start	= getgp('pre_date_start'); //计划时间
	$pre_date_end	= getgp('pre_date_end'); //计划时间
	$final_date_start	= getgp( 'final_date_start' ); //最后监察时间 起
	$final_date_end	= getgp( 'final_date_end' ); //最后监察时间 止

	$energy_status	= getgp( 'status' );
	$export			= getgp( 'export' );
	!$energy_status && $energy_status=0;
	${'status_'.$energy_status.'_tab'} = ' ui-tabs-active ui-state-active';

	//企业名称
	if( $ep_name ){
		$_eids = array();
		$_query = $db->query("SELECT eid FROM sp_enterprises WHERE ep_name LIKE '%".str_replace('%','\%',trim($ep_name))."%'");
		while( $rt = $db->fetch_array( $_query ) ){
			$_eids[] = $rt['eid'];
		}
		if( $_eids ){
			$where .= " AND p.eid IN (".implode(',',$_eids).")";
		} else {
			$where .= " AND p.id < -1";
		}
		unset( $_eids, $_query, $rt );
	}
	

	//合同编号
	if( $ct_code ){
	   $where .= " AND p.ct_code = '$ct_code'";
		
	}

	//合同项目编号
	if( $cti_code ){
		$where .= " AND p.cti_code like '%$cti_code%'";
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

	//审核结束 开始
	if( $pre_date_start ){
		$where .= " AND t.te_date >= '$pre_date_start'";
	}
	//审核结束 结束
	if( $pre_date_end ){
		$where .= " AND t.te_date <= '$pre_date_end'";
	}
	//最后监察日 开始
	if( $final_date_start ){
		$where .= " AND p.final_date >= '$final_date_start'";
	}
	//最后监察日 结束
	if( $final_date_end ){
		$where .= " AND p.final_date <= '$final_date_end'";
	}

	if($ct_ps_uid=getgp("ct_ps_uid")){
		$_ct_ids=$db->get_col("SELECT ct_id FROM `sp_contract` WHERE `ct_ps_uid` = '$ct_ps_uid' AND `deleted` = '0'");
		$_ct_ids=array_merge($_ct_ids,array(-1));
		$where .=" AND p.ct_id IN (".join(",",$_ct_ids).")";
		$ct_ps_select=str_replace("value=\"$ct_ps_uid\"","value=\"$ct_ps_uid\" selected",$ct_ps_select);
	
	}
	$where .=" AND p.status=3 AND p.iso='A09'";

	//要获取的字段
	$fields .= "p.*,e.ep_name,e.ctfrom,t.te_date";
	$type_allow=array("1003","1008","1009","1010","3001","2001","2002","2003");
	//要关联的表
	$join .= " LEFT JOIN sp_enterprises e ON e.eid = p.eid";
	$join .= " LEFT JOIN sp_task t ON t.id = p.tid";
	//$where .= " AND p.deleted = '0' AND p.audit_type IN ('".implode("','",$type_allow)."')";
	$where .= "  AND p.deleted = '0'";
	if( !$export ){
		$total[0] = $db->get_var("SELECT COUNT(*) FROM sp_project p WHERE 1 $where AND  energy_status=0 ");
		$total[1] = $db->get_var("SELECT COUNT(*) FROM sp_project p WHERE 1 $where AND  energy_status=1 ");
		$pages = numfpage($total[$energy_status]);
	}
	$where .= " AND p.energy_status='$energy_status'";
	$sql = "SELECT $fields FROM sp_project p $join WHERE 1 $where ORDER BY t.te_date DESC $pages[limit]";
	
	$projects = array();
	$query = $db->query( $sql );

	while( $rt = $db->fetch_array( $query ) ){
		$rt['audit_ver_V'] = f_audit_ver( $rt['audit_ver'] );
		$rt['audit_type_V'] = f_audit_type( $rt['audit_type'] );
		$rt['ctfrom_V'] = f_ctfrom( $rt['ctfrom'] );
		$rt['status_V'] = $status_array[$rt['status']];
		$rt[te_date]=mysql2date("Y-m-d",$rt[te_date]);
		$projects[$rt['id']] = chk_arr($rt);
	}
	
	
	if( !$export ){
		tpl();
	} else {
		ob_start();
		tpl('xls/list_wait_arrange');
		$data = ob_get_contents();
		ob_end_clean();
		export_xls('审核项目列表',$data);
	}
?>