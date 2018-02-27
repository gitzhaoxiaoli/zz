<?php

if( 'save' == $a ) {
	// exit(p($_POST));
	$vers			= array_map( 'trim', getgp( 'ver' ) );
	$msgs			= array_map( 'trim', getgp( 'msg' ) );
	$nums			= array_map( 'trim', getgp( 'num' ) );
	$notes			= array_map( 'trim', getgp( 'note' ) );
	$order			= array_map( 'intval', getgp( 'vieworder' ) );
	$is_stops		= array_map( 'intval', getgp( 'is_stop' ) );

	$old_settings = array();
	$query = $db->query("SELECT * FROM sp_settings_credit");
	while( $rt = $db->fetch_array( $query ) ){
		$olds[$rt['vid']] = $rt;
	}

	if( $vers ){
		foreach( $vers as $id => $ver ){

			if( $ver				!= $olds[$id]['code'] || 
				$isos[$id]			!= $olds[$id]['name'] || 
				$nums[$id]			!= $olds[$id]['num'] ||
				$notes[$id]			!= $olds[$id]['note'] ||
				$is_stops[$id]		!= $olds[$id]['is_stop'] || 
				$order[$id]			!= $olds[$id]['vieworder'] ){
				$db->query("UPDATE sp_settings_credit SET 
				
					code	= '$ver',
					name			= '{$msgs[$id]}',
					num		= '{$nums[$id]}',
					note		= '{$notes[$id]}',
					is_stop		= '{$is_stops[$id]}',
					vieworder	= '{$order[$id]}' WHERE vid = '$id'");
			}
			
		}
	}

	$new = getgp( 'new' );
	// p($new);
	// exit;
	if( $new ){
		$ADDSQL = array();
		foreach( $new['ver'] as $key => $val ){
			if( !$val ) continue;
			$ver			= $val;
			$msg			= $new['msg'][$key];
			$num			= $new['num'][$key];
			$note			= $new['note'][$key];
			$is_stop		= (int)$new['is_stop'][$key];
			$vieworder		= $new['vieworder'][$key];
			$ADDSQL[]		= "(  '$ver', '$msg', '$num', '$note', '$vieworder', '$is_stop' )";
		}

		if( $ADDSQL ){
			$add_sql = "INSERT INTO sp_settings_credit (  code, name, num, note, vieworder, is_stop ) VALUES " . implode(',', $ADDSQL );

			$db->query( $add_sql );
		}
	}
	update_credit_cache();
	showmsg('success', 'success', "?c=setting_audit_credit");
}elseif('del'==$a){
  	$db->update( 'settings_credit', array( 'deleted' => '1' ), array( 'vid' => $_GET['id']) );
 	showmsg( 'success', 'success', "?c=setting_audit_credit" );

}else {
	$resdb = array();
	$query = $db->query( "SELECT * FROM sp_settings_credit WHERE deleted=0  ORDER BY vieworder ASC" );
	while( $rt = $db->fetch_array( $query ) ){
		$rt['iso_select'] = str_replace( "value=\"$rt[iso]\">", "value=\"$rt[iso]\" selected>" , $iso_select );
		$resdb[$rt['vid']] = $rt;
	}
	//update_ver_cache();
	tpl('setting/credit');

} 



?>