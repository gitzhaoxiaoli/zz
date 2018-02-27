<?php
!defined('IN_SUPU') && exit('Forbidden');
//客户文档查询
$attachs = array();
	$export		= getgp( 'export' );


	extract( $_GET, EXTR_SKIP );

	if($arctype){
		$where .= " AND ea.ftype = '$arctype' ";
	}
	if($s_dates){
		$where .= " and ea.create_date >= '$s_dates' ";
	}
	if($s_datee){
		$s_datee = $s_datee . " 23:00";
		$where .= " and ea.create_date <= '$s_datee' ";
	}
	if($ep_name){
		$eids = array();
		$query = $db->query("SELECT eid FROM sp_enterprises WHERE ep_name LIKE  '%$ep_name%'");
		while( $rt = $db->fetch_array( $query ) ){
			$eids[] = $rt['eid'];
		}
		if( $eids ){
			$where .= " AND ea.eid IN (".implode(',',$eids).")";
		} else {
			$where .= " AND ea.id < -1";
		}
	}

	//限制合同来源
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
	$where .= " AND e.ctfrom >= '$ctfrom' AND e.ctfrom < '$ctfrom_e'";

	$ctfrom_select = str_replace( "value=\"$ctfrom\"", "value=\"$ctfrom\" selected" , $ctfrom_select );


	if($code){
		$eid = $db->get_var("SELECT eid FROM sp_enterprises WHERE code = '$code'");
		if( $eid ){
			$where .= " AND ea.eid = '$eid'";
		} else {
			$where .= " AND ea.id < -1";
		}
	}

	$join .= " LEFT JOIN sp_enterprises e ON e.eid = ea.eid";
	$join .= " LEFT JOIN sp_hr hr ON hr.id = ea.create_uid";
	if($up_name){
		$where .= " and hr.name like '%$up_name%' ";
	}
	if(!$export){
		$total = $db->get_var( "SELECT COUNT(*) FROM sp_attachments ea $join WHERE 1 $where" );

		$pages = numfpage( $total);
	}
	$sql = "SELECT ea.*,e.ep_name,e.ctfrom,hr.name author FROM sp_attachments ea $join WHERE 1 $where ORDER BY ea.id DESC $pages[limit]";

    //exit;
	$query = $db->query($sql);
	while( $rt = $db->fetch_array( $query ) ){
		$rt['ctfrom_V'] = f_ctfrom( $rt['ctfrom'] );
		$rt['ftype_V'] = f_arctype( $rt['ftype'] );

		$attachs[$rt['id']] = $rt;
	}


 	if( !$export ){
		tpl( 'enterprise/list_attach' );
	} else {//导出客户文档列表
		ob_start();
		tpl( 'xls/list_attach' );
		$data = ob_get_contents();
		ob_end_clean();
		export_xls( '客户文档列表', $data );
	}
?>