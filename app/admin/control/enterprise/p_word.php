<?php
/*说明:停止使用*/
/*@zys 2016-5-6*/
/*

$fields = $join = $where = $urls = '';
	extract( $_GET, EXTR_SKIP );
	${'status_'.$is_hire.'_tab'} = ' ui-tabs-active ui-state-active';

	if( $en_username ){
		$where .= " AND username like '%$en_username%' ";
		//$urls .= '&en_username='.$en_username;
	}
	if( $ep_name ){
		$where .= " AND ep_name like '%$ep_name%' ";
		//$urls .= '&en_username='.$en_username;
	}
	if( $code ){
		$where .= " AND code like '%$code%' ";
		//$urls .= '&code='.$code;
	}
	$where .= "AND deleted = 0  ";
	if( !$export ){
		$total = $db->get_var("SELECT COUNT(*) FROM sp_enterprises $join WHERE 1 $where  ");
		$pages = numfpage( $total );
	}
	$sql = "SELECT * FROM sp_enterprises $join WHERE 1 $where ORDER BY eid DESC $pages[limit]" ;
	$query = $db->query( $sql);
	while( $rt = $db->fetch_array( $query ) ){
		$rt['person_mail'] = $db->meta($rt['eid'],'person_mail','','enterprise');
		$rt['province']		= f_region_province( $rt['areacode'] );
		$users[]= $rt;
	}
	if( !$export ){
		tpl('enterprise/p_word');
	} else {
		ob_start();
		tpl( 'xls/list_p_word' );
		$data = ob_get_contents();
		ob_end_clean();
		export_xls( '企业账户信息', $data );
	}
?>