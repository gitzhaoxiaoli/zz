<?php
!defined('IN_SUPU') && exit('Forbidden');
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
	if( $ep_name ){
		$_eids = array();
		$_query = $db->query("SELECT eid FROM sp_enterprises WHERE ep_name LIKE '%".str_replace('%','\%',trim($ep_name))."%'");
		while( $rt = $db->fetch_array( $_query ) ){
			$_eids[] = $rt['eid'];
		}
		if( $_eids ){
			$where .= " AND ss.eid IN (".implode(',',$_eids).")";
		} else {
			$where .= " AND ss.eid < -1";
		}
	}
//省份
if( $areacode=getgp("areacode") ){
	$pcode = substr($areacode,0,2) . '0000';
	$_eids = array(-1);
	$_query = $db->query("SELECT eid FROM sp_enterprises WHERE LEFT(areacode,2) = '".substr($areacode,0,2)."'");
	while( $rt = $db->fetch_array( $_query ) ){
		$_eids[] = $rt['eid'];
	}
	$where .= " AND ss.eid IN (".implode(',',$_eids).")";
	unset( $_eids, $_query, $rt, $_eids );
	
	$province_select = str_replace( "value=\"$pcode\">", "value=\"$pcode\" selected>" , $province_select );
}

//合同认证申请编号
if( $cti_code=trim($cti_code) ){
	$where .= " AND ss.cti_code like '%$cti_code%'";
}


 
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
	$fields .= "ss.*,t.tb_date,t.te_date,t.eid,t.task_code,t.audit_type,c.certno";

	//要关联的表
	$join .= " LEFT JOIN sp_test st ON st.id = ss.test_id";
	$join .= " LEFT JOIN sp_task t ON t.id = ss.tid";
	$join .= " LEFT JOIN sp_certificate c ON c.id = ss.certid";

	$_test_org_id = current_user("id");
	$where .= " AND ss.test_org_id = '$_test_org_id'";
	$status_total = array(0,0,0,0);
	//$where .= " AND ss.status = 3 AND ss.deleted=0 AND ss.iso_prod_type = 1 AND ss.redata_status=1";
	$where .= " AND ss.deleted=0 ";
	if( !$export ){
		$status_total[0] = $db->get_var("SELECT COUNT(*) FROM sp_sample ss $join WHERE 1 $where AND ss.status = 0 ORDER BY t.te_date DESC");
		$status_total[1] = $db->get_var("SELECT COUNT(*) FROM sp_sample ss $join WHERE 1 $where AND ss.status = 1 ORDER BY t.te_date DESC");
		$status_total[2] = $db->get_var("SELECT COUNT(*) FROM sp_sample ss $join WHERE 1 $where AND ss.status = 2 ORDER BY t.te_date DESC");
		$status_total[3] = $db->get_var("SELECT COUNT(*) FROM sp_sample ss $join WHERE 1 $where AND ss.status = 3 ORDER BY t.te_date DESC");
		$pages = numfpage( $status_total[$status] );
	}

	$where .= " AND ss.status = '$status'";
	
	$resdb = array();
	$sql = "SELECT $fields FROM sp_sample ss $join WHERE 1 $where ORDER BY t.te_date DESC $pages[limit]";
	//echo $sql;
	//exit;
	$query = $db->query( $sql );
	while( $rt = $db->fetch_array( $query ) ){
		$rt['audit_type_V'] = f_audit_type( $rt['audit_type'] );
		//$rt['audit_ver_V'] = f_audit_ver( $rt['audit_ver'] );
		//$rt['iso_V'] = f_iso( $rt['iso'] );
		//$rt['ctfrom_V'] = f_ctfrom( $rt['ctfrom'] );
		//$rt['is_finance_V'] = ($rt['is_finance'] == '2')?'收完':'未收完';
		$rt['ep_name'] = $db->getField('enterprises','ep_name',array('eid'=>$rt['eid']));
		$resdb[$rt['id']] = $rt;
	}
		

	if( !$export ){
		tpl();
	} else {
		ob_start();
		tpl( 'xls/list_wait_test' );
		$data = ob_get_contents();
		ob_end_clean();

		export_xls( '检验安排', $data );
	}