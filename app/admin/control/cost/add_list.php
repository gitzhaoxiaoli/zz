<?php
//@cyf 2016-03-01
//合同费用列表
//@cyf 2016-03-02 搜索

!defined('IN_SUPU') && exit('Forbidden');

	//合同来源
	$ctfrom_select = f_ctfrom_select();
	//省分下拉 (搜索用)
	$province_select = f_province_select();
	$status = (int)getgp( 'status' );
	$status_0_tab = $status_1_tab = $status_2_tab = $status_3_tab = '';
	${'status_'.$status.'_tab'} = ' ui-tabs-active ui-state-active';
	//搜索开始
	$a = getgp( 'a' );
	$ep_name = getgp( 'ep_name' );
	$code = getgp('code');
	$ct_code = getgp( 'ct_code' );
	$ctfrom	= getgp( 'ctfrom' );
	$areacode = getgp( 'areacode' );
	$status	= getgp( 'status' );
	$export=getgp('export');
	if($ep_name){
	$where .= " AND e.ep_name LIKE '%".$ep_name."%'";
	}
	if($code){
	$where .= " AND e.code= '$code'";	
	}
	if($ctfrom){
	$where .= " AND e.ctfrom= '$ctfrom'";		
	}
	if($areacode){
	$province_select = str_replace( "value=\"$areacode\">", "value=\"$areacode\" selected>" , $province_select );
	$where .= " and e.areacode like '".substr($areacode,0,2)."%' ";
	$page_str .= '&areacode='.$areacode;		
	}
	$where .= " AND c.deleted = '0'";
	//合同来源限制
	$len = get_ctfrom_level( current_user( 'ctfrom' ) );
	$status_arry = array('0'=>'未登记完','1'=>'已登记','2'=>'已评审','3'=>'已审批');
	$datas = array();
	/*列表*/
	if (!$export) {
		$total = $db->get_var("SELECT COUNT(*) FROM sp_contract c LEFT JOIN sp_enterprises e on c.eid=e.eid WHERE 1 $where");
		$pages = numfpage( $total);
		}

	$sql = "SELECT * FROM sp_contract c LEFT JOIN sp_enterprises e on c.eid=e.eid WHERE 1 $where ORDER BY ct_id DESC $pages[limit]";
	$res = $db->query($sql);
	while( $rt = $db->fetch_array( $res ) ){
		$rt['ctfrom_V'] = f_ctfrom( $rt['ctfrom'] );
		$rt['status'] = $db->get_var("SELECT status FROM sp_contract WHERE ct_id = ".$rt['ct_id']);
		$rt['status'] = $status_arry[$rt['status']];
		$rt['ep_name'] = $db->get_var("SELECT ep_name FROM sp_enterprises WHERE eid=".$rt['eid']);
		$rt['ep_type'] = $db->get_var("SELECT ep_type FROM sp_enterprises WHERE eid=".$rt['eid']);
		$rt['ct_code'] = $db->get_var("SELECT ct_code FROM sp_contract WHERE ct_id = ".$rt['ct_id']);
		$datas[] = $rt;
	}
	if( !$export ){
		tpl( 'contract/cost_add_list' );
	} else {//导出合同费用列表
		ob_start();
		tpl( 'xls/list_contract_cost_add' );
		$data = ob_get_contents();
		ob_end_clean();
		export_xls( '合同费用列表', $data );
	}