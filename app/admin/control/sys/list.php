<?php
!defined('IN_SUPU') && exit('Forbidden');


$fields = $join = $where = $urls = '';



	extract( $_GET, EXTR_SKIP );
	${'status_'.$is_hire.'_tab'} = ' ui-tabs-active ui-state-active';
	$name = trim($name);
	if( $name ){
		$where .= " AND name like '%$name%' ";
		$urls .= '&name='.$name;
	}
	if( $easycode ){
		$where .= " AND easycode like '%$easycode%' ";
		$urls .= '&easycode='.$easycode;
	}
	if( $code ){
		$where .= " AND code like '%$code%' ";
		$urls .= '&code='.$code;
	}
	if(isset($is_stop)){
		$where .= " AND is_stop ='$is_stop' ";
		
	}
	$ctfrom_select = f_ctfrom_select();
	//合同来源限制
	$len = get_ctfrom_level( current_user( 'ctfrom' ) );

	if( $ctfrom && substr( $ctfrom, 0, $len ) == substr( current_user( 'ctfrom' ), 0, $len ) ){
		$_len = get_ctfrom_level( $ctfrom );
		$len = $_len;
	} else {
		$ctfrom = current_user( 'ctfrom' );
	}
	$last = substr($ctfrom,$len - 1,1);
	$ctfrom_e = substr( $ctfrom, 0, $len -1 ).($last+1);
	$_i = 8 - $len;
	for( $i = 0; $i < $_i; $i++ ){
		$ctfrom_e .= '0';
	}
	$where .= " AND ctfrom >= '$ctfrom' AND ctfrom < '$ctfrom_e'";
	$ctfrom_select = str_replace( "value=\"$ctfrom\"", "value=\"$ctfrom\" selected" , $ctfrom_select );

	$urls .= '&ctfrom='.$ctfrom;



	if( $audit_job || $audit_job=='0' ){
		$where .= " AND audit_job = '$audit_job' ";
		$audit_job_select = str_replace( "value=\"$audit_job\">", "value=\"$audit_job\" selected>" , $audit_job_select );
		$urls .= '&audit_job='.$audit_job;
	}

	$where .= " AND is_hire IN(1,3) AND deleted = 0";
	if( !$export ){
		$total = $db->get_var("SELECT COUNT(*) FROM sp_hr $join WHERE 1 $where  ");
		$pages = numfpage( $total );
	}
	$sql = "SELECT * FROM sp_hr $join WHERE 1 $where ORDER BY id DESC $pages[limit]" ;
	$query = $db->query( $sql);
	while( $rt = $db->fetch_array( $query ) ){

		$rt['ctfrom']		= f_ctfrom( $rt['ctfrom'] );
		$rt['audit_job']	= f_audit_job($rt['audit_job']);
		$rt['areacode']		= f_region_province( $rt['areacode'] );	//取省地址
		//$rt['sex']		= $rt['sex'] ;
		if ($rt['sex']=='1'){$rt['sex']='男';}elseif($rt['sex']=='2'){$rt['sex']='女';}
		$rt['is_hire']		= $rt['is_hire'];
		$rt['department'] 	= f_department($rt['department']);
		$rt['mail']			= $user->meta($rt['id'],'mail' );
		$rt['note']			= $user->meta($rt['id'],'note' );
		$users[$rt['id']]	= $rt;
	}
	if( !$export ){
		tpl('sys/hr_list');
	} else {
		ob_start();
		tpl( 'xls/list_sys_hr' );
		$data = ob_get_contents();
		ob_end_clean();
		export_xls( '人员列表', $data );
	}
?>