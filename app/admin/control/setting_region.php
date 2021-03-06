<?php
!defined('IN_SUPU') && exit('Forbidden');

if( empty( $a ) || 'list' == $a ) {
	$where='';
	$code=$_GET['code']; //编码
	$name=$_GET['name'];
	$is_stop=$_GET['is_stop'];
	if($_GET['code']){
		$where.=" AND code like '%$code%'";
 	};
	if($name){
		$where.=" AND name like '%$_GET[name]%'";
 	}
	if($is_stop){
		$where.=" AND  is_stop='$is_stop'";
 	} 
	$resdb = array();
	$total = $db->get_var("SELECT COUNT(*) FROM sp_settings_region WHERE 1  AND deleted=0 $where   " );

	$pages = numfpage( $total, 20, "?c=setting_region&a=list&code=$code&name=$name&is_stop=$is_stop");
	$query = $db->query( "SELECT * FROM sp_settings_region WHERE 1 $where  AND deleted=0  ORDER BY vieworder ASC,code ASC $pages[limit]" );
	
	while( $rt = $db->fetch_array( $query ) ){
		  
		$resdb[$rt['code']] = $rt;
	}

	tpl('setting/list_region');
} elseif( 'save' == $a ) {
	$codes = array_map( 'trim', getgp( 'code' ) );
	$names = array_map( 'trim', getgp( 'name' ) );
	$order = array_map( 'intval', getgp( 'vieworder' ) );
	$is_stops = array_map( 'intval', getgp( 'is_stop' ) );

	if( $codes ){
		foreach( $codes as $code ){
			$db->query("UPDATE sp_settings_region SET code = '{$code}',
						name = '{$names[$code]}',
						is_stop = '{$is_stops[$code]}',
						vieworder = '{$order[$code]}' WHERE code = '$code'");
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
			$ADDSQL[] = "( '$code','$name','$vieworder','$is_stop')";
		}

		if( $ADDSQL ){
			$add_sql = "INSERT INTO sp_settings_region ( code, name, vieworder, is_stop ) VALUES " . implode(',', $ADDSQL );

			$db->query( $add_sql );
		}
	}

	showmsg( 'success', 'success', "?c=$c&a=list&paged=$paged" );

}elseif( 'del' == $a ){
	$code = getgp( 'code' );

	if( '0000' == substr( $code ,2, 4 ) ){ //省
		$len = 2;
	} elseif( '00' == substr( $code, 4 ,2 ) ){
		$len = 4;
	}else {
		$len = 6;
	} 
	$where .= " AND LEFT(code, $len) = '" . substr($code, 0, $len) . "'";
	 
	$sql="SELECT code from sp_settings_region where 1 $where"; 
	$res=$db->query($sql);
	 while($rs=$db->fetch_array($res)){ 
		 $sql="update sp_settings_region set deleted=1 where code=$rs[code]";
		 $db->query($sql); 
	}
 	showmsg( 'success', 'success', "?c=setting_region&a=list" );

}



?>