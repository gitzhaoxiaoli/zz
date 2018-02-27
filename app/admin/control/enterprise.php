<?php
!defined('IN_SUPU') && exit('Forbidden'); 
//客户模块  主公司，关联公司（子公司） 分场所
$enterprise = load( 'enterprise' );
require_once( ROOT . '/data/cache/arctype.cache.php' );
$step = getgp('step');
$ctfrom_select = f_ctfrom_select(); //合同来源下拉
$ep_level_select=f_select('ep_level'); //客户级别
$ep_type_select=f_select('ep_type'); // 客户类别-搜索
$nature_select=f_select('nature');//企业性质
$statecode_select=f_select('statecode');//国家/地区代码  
$currency_select=f_select('currency');//注册资本币种 
$province_select = f_province_select();//省分下拉 (搜索用)
$prod_type_select = f_select('prod_type');//获证组织经济代码
$union_type_radios = f_select('union_type');//关联公司类型单选
//分场所类型下拉
$site_type_select =f_select('site_type');
//企业文档类型下拉
$arctype_select = '';
if( $arctype_array ){
	foreach( $arctype_array as $code => $item ){
		if( substr($code, 0,1) == '5' )
		$arctype_select .= "<option value=\"$code\">$item[name]</option>";
	}
}
unset( $code, $item );

if( empty( $a ) || 'list' == $a ){ //企业列表
 
	require_once( CTL_DIR. '/enterprise/list.php' );
	 
} elseif( 'add' == $a || 'edit' == $a ) {
	require_once( CTL_DIR. '/enterprise/edit.php' );
}elseif( 'zz_list' == $a ){
	require_once( CTL_DIR. '/enterprise/zz_list.php' );	
} elseif( 'upattach' == $a ){
	require_once( CTL_DIR. '/enterprise/upattach.php' );
} elseif( 'edit_site' == $a ){
	require_once( CTL_DIR. '/enterprise/edit_site.php' );
}elseif( 'up_files' == $a  ){
	require_once( CTL_DIR. '/enterprise/up_files.php' );
} elseif( 'apply_info' == $a ){
	require_once( CTL_DIR. '/enterprise/apply_info.php' );	
}elseif( 'list_site' == $a ){
	require_once( CTL_DIR. '/enterprise/list_site.php' );
} elseif( 'registr' == $a ){
	require_once( CTL_DIR. '/enterprise/registr.php' );
}elseif( 'update' == $a ){
	require_once( CTL_DIR. '/enterprise/update.php' );
}elseif( 'del_site' == $a ){
	$es_id = (int)getgp( 'es_id' );

	$eid = $db->get_var("select eid from sp_enterprises_site where es_id='$es_id' ");
	$db->query("DELETE FROM sp_enterprises_site WHERE es_id = '$es_id'");
	//更新主公司信息
	$sql = "update sp_enterprises set site_count = site_count - 1 where eid = $eid ";
	$db->query($sql); 
	showmsg( 'success', 'success', "?c=enterprise&a=list_site&eid={$eid}" );

}elseif( 'list_attach' == $a ) { //组织文档
	require_once( CTL_DIR. '/enterprise/list_attach.php' ); 
} elseif( 'delattach' == $a ) { //删除组织文档
	$aid = (int)getgp( 'aid' );
	$eid = (int)getgp( 'eid' );
	$attach = load( 'attachment' );
 	// 日志 
	$bf_str = $attach->get($aid);
	do {
		log_add($bf_str['eid'], 0, "[说明:组织文档-删除]", NULL, serialize($bf_str));
	}while(false);
	$attach->del( $aid );
	showmsg( 'success', 'success', $_SERVER['HTTP_REFERER'] );

} elseif( 'del' == $a ) {//删除企业
	$eid = (int)getgp( 'eid' );
	if( $eid ){
		// 日志
		do {
			log_add($eid, 0, "[说明:客户信息删除]", NULL, NULL);
		}while(false);
		$enterprise->del( array( 'eid' => $eid ) );
		
		showmsg( 'success', 'success', "?c=enterprise&a=list" );
	}   
}else{
	// echo  CTL_DIR. '/enterprise/'.$a.".php";
	
	require_once( CTL_DIR. '/enterprise/'.$a.".php" );
}

?>