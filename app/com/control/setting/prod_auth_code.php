<?php 
!defined('IN_SUPU') && exit('Forbidden');
$type = getgp('type');
$resdb = $db->find_results('settings_prod_code'," AND deleted = 0 AND type = '$type'");
if($step)
{
	$source = trim( getgp( 'source' ) );
	$type = trim( getgp( 'type' ) );
	$notes = getgp( 'note' );
	$codes = array_map( 'trim', getgp( 'code' ) );
	$names = array_map( 'trim', getgp( 'name' ) );
	$order = array_map( 'intval', getgp( 'vieworder' ) );
	$is_stops = array_map( 'intval', getgp( 'is_stop' ) );

	$old_settings = array();
	$query = $db->query("SELECT * FROM sp_settings_prod_code WHERE type = '$type'");
	while( $rt = $db->fetch_array( $query ) ){
		$olds[$rt['id']] = $rt;
	}

	if( $names ){
		foreach( $names as $id => $name ){
			if( $name != $olds[$id]['name'] || $codes[$id] != $olds[$id]['code'] || $is_stops[$id] != $olds[$id]['is_stop'] || $order[$id] != $olds[$id]['vieworder'] ){
				$db->query("UPDATE sp_settings_prod_code SET code = '{$codes[$id]}', type = '$type',
					name = '{$names[$id]}',
					is_stop = '{$is_stops[$id]}',
					vieworder = '{$order[$id]}' WHERE id = '$id'");
			}
		}
	}

	$new = getgp( 'new' );

	if( $new ){
		$ADDSQL = array();
		foreach( $new['name'] as $key => $val ){
			if( !$val ) continue;
			$name = $val;
			$code = $new['code'][$key];
			$is_stop = (int)$new['is_stop'][$key];
			$vieworder = $new['vieworder'][$key];
			$ADDSQL[] = "('$type', '$code','$name','$vieworder','$is_stop')";
		}

		if( $ADDSQL ){
			$add_sql = "INSERT INTO sp_settings_prod_code ( type, code, name, vieworder, is_stop ) VALUES " . implode(',', $ADDSQL );
			$db->query( $add_sql );
		}
	}

	update_cache( $type );
	showmsg( 'success', 'success', "?m=com&c=$c&a=$source&type=$type" );
}
tpl();
?>