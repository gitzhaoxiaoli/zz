<?php
!defined('IN_SUPU') && exit('Forbidden');
//再认证列表

	extract( $_GET, EXTR_SKIP );

	$status = (int)$status;

	$status_0 = $status_1 = '';
	${'status_'.$status} = ' ui-tabs-active ui-state-active"';

	$fields = $join = $where = '';


    //联表
	$join .= " LEFT JOIN sp_enterprises e ON cert.eid = e.eid";
	$join .= " 	LEFT JOIN sp_contract_item cti ON cert.cti_id = cti.cti_id";
	//要获取的字段
	$fields .= "cert.*,e.ep_name,e.ep_level,e.areacode,cti.signe_name,cti.use_code,cti.audit_code,cti.ct_id";//
	

	if( $ep_name=trim($ep_name) ){
		$_eids = array();
		$query = $db->query("SELECT eid FROM sp_enterprises WHERE ep_name LIKE '%$ep_name%'");
		while( $rt = $db->fetch_array( $query ) ){
			$_eids[] = $rt['eid'];
		}
		if( $_eids ){
			$where .= " AND i.eid IN (".implode(',',$_eids).")";
		} else {
			$where .= " AND i.id < -1";
		}
	}
//省份
	if( $areacode ){
		$pcode = substr($areacode,0,2) . '0000';
		$_eids = array(-1);
		$_query = $db->query("SELECT eid FROM sp_enterprises WHERE LEFT(areacode,2) = '".substr($areacode,0,2)."'");
		while( $rt = $db->fetch_array( $_query ) ){
			$_eids[] = $rt['eid'];
		}
		$where .= " AND i.eid IN (".implode(',',$_eids).")";
		unset( $_eids, $_query, $rt, $_eids );
		
		$province_select = str_replace( "value=\"$pcode\">", "value=\"$pcode\" selected>" , $province_select );
	}


	if( $ct_code=trim($ct_code) ){
		$ct_ids = array();
		$query = $db->query("SELECT ct_id FROM sp_contract WHERE ct_code = '$ct_code'");
		while( $rt = $db->fetch_array( $query ) ){
			$ct_ids[] = $rt['ct_id'];
		}
		if( $ct_ids ){
			$where .= " AND i.ct_id IN (".implode(',',$ct_ids).")";
		} else {
			$where .= " AND i.id < -1";
		}
	}

	if( $cti_code=trim($cti_code) ){
		$cti_ids = array();
		$query = $db->query("SELECT cti_id FROM sp_contract_item WHERE cti_code like '%$cti_code%'");
		while( $rt = $db->fetch_array( $query ) ){
			$cti_ids[] = $rt['cti_id'];
		}
		if( $cti_ids ){
			$where .= " AND i.cti_id IN (".implode(',',$cti_ids).")";
		} else {
			$where .= " AND i.id < -1";
		}
	}
	if($date_start=getgp("date_start")){
		$where .=" AND cert.e_date >= '$date_start'";
		}
	if($date_end=getgp("date_end")){
		$where .=" AND cert.e_date <= '$date_end 23:00'";
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
	$where .= " AND cert.ctfrom >= '$ctfrom' AND cert.ctfrom < '$ctfrom_e'";

	$ctfrom_select = str_replace( "value=\"$ctfrom\"", "value=\"$ctfrom\" selected" , $ctfrom_select );


	$where .= " AND cert.status IN ('01','02','05') AND cert.deleted = '0'";
	if( !$export ){//不导出 EXECL
		$sv_total = array(0,0,0,0,0,0);

		$sv_total[0] = $db->get_var("SELECT COUNT(*) total FROM sp_certificate cert WHERE 1 $where AND cert.ifcation_status='0' ");
		$sv_total[1] = $db->get_var("SELECT COUNT(*) total FROM sp_certificate cert WHERE 1 $where AND cert.ifcation_status='1' ");
		$sv_total[2] = $db->get_var("SELECT COUNT(*) total FROM sp_certificate cert WHERE 1 $where AND cert.ifcation_status='2' ");
		$sv_total[3] = $db->get_var("SELECT COUNT(*) total FROM sp_certificate cert WHERE 1 $where AND cert.ifcation_status='3' ");
		$sv_total[4] = $db->get_var("SELECT COUNT(*) total FROM sp_certificate cert WHERE 1 $where AND cert.ifcation_status='4' ");
		$pages = numfpage( $sv_total[$status]);
	}


	$where .= " AND cert.ifcation_status = $status";
	$resdb = array();
	$sql =  "SELECT $fields FROM sp_certificate cert $join WHERE 1 $where ORDER BY cert.id DESC $pages[limit]" ;
	$query = $db->query($sql);
	$enterprise=load("enterprise");
	while( $rt = $db->fetch_array( $query ) ){
		$rt['province'] = f_region_province( $rt['areacode'] );
		//$rt['province'] = read_cache("region",$rt['areacode']);
		$rt['audit_ver_V'] = f_audit_ver( $rt['audit_ver'] );
		$rt['iso_V'] = f_iso( $rt['iso'] );
		$rt['ctfrom_V'] = f_ctfrom( $rt['ctfrom'] );
		$rt['cert_status_V'] = f_certstate( $rt['status'] );
		$rt['audit_code'] = LongToBr($rt['audit_code'], array(
        ";",
        "；"
    ));

		$rt['use_code']=LongToBr($rt['use_code'],array('；',';'));
		$rt['sp_date'] = $db->get_var("SELECT sp_date FROM `sp_project` WHERE `cti_id` = '$rt[cti_id]' AND `deleted` = '0' AND `pd_type` = '1' ORDER BY `tid` DESC");
		$resdb[$rt['id']] = chk_arr($rt);
	}

	if( !$export ){//输出HTML页面
		tpl( 'audit/list_ifcation' );
	}else {//导出EXECL
	ob_start();
    tpl('xls/list_super');
    $data = ob_get_contents();
    ob_end_clean();
    export_xls('再认证维护项目列表', $data);
	}
?>