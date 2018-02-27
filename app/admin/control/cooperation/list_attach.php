<?php
!defined('IN_SUPU') && exit('Forbidden'); 
   /*
   *合作单位列表
   */
   $where = '';
   $export		= getgp( 'export' );
   //合作单位名称
   $coo_name = getgp('coo_name');
   if($coo_name){
	   $where .= " AND coo_name LIKE '%".$coo_name."%'";
   }
   //合作单位编码
   $coo_code = getgp('coo_code');
   if($coo_code)
   {
	   $where .= " AND coo_code LIKE '%".$coo_code."%'";
   }
   //合作状态
   $coo_status = getgp('coo_status');
   if($coo_status)
   {
	   $where .= " AND coo_status = $coo_status";
   }
   //联系人
   $coo_person = getgp('coo_person');
   if($coo_person)
   {
	   $where .= " AND coo_person LIKE '%".$coo_person."%'";
   }
   //联系人手机
   $coo_mphone = getgp('coo_mphone');
   if($coo_mphone)
   {
	   $where .= " AND coo_mphone LIKE '%".$coo_mphone."%'";
   }
   //联系人座机
   $coo_tphone = getgp('coo_tphone');
   if($coo_tphone)
   {
	   $where .= " AND coo_tphone LIKE '%".$coo_tphone."%'";
   }
   //标记删除
    $coo_id = getgp('coo_id');
	if($coo_id)
	{
		$db->update('cooperation',array('deleted'=>1),array('coo_id'=>$coo_id));
	};
	//分页统计
	if(!$export){
		$total = $db->get_var( "SELECT COUNT(*) FROM sp_cooperation WHERE 1 $where" );
		$pages = numfpage( $total);
	}
    $sql = "SELECT * FROM sp_cooperation WHERE 1 AND deleted = 0 $where $pages[limit]";

	$res = $db->get_results($sql);
 	if( !$export ){
		tpl();
	} else {//导出客户文档列表
		ob_start();
		tpl( 'xls/list_attach' );
		$data = ob_get_contents();
		ob_end_clean();
		export_xls( '合作单位文档列表', $data );
	}
?>