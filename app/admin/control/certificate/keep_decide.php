<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
*保持通知书
*/
extract($_GET);
$export = getgp('export');
$audit_type_select=f_select('audit_type','',array('1003','1004-1','1004-2','1004-3','1007'));
$send_type = getgp('send_type');
$keep_decide = (int)getgp("is_download") == 0 ? 1 : (int)getgp("is_download");
switch($keep_decide){
	case 1 :
	     $status_0_tab = " ui-tabs-active ui-state-active";
		 break;
    case 2 :
	     $status_1_tab = " ui-tabs-active ui-state-active";
		 break; 
}
//证书发送
	if($send_type)
	{   
		require DOCTPL_PATH . '209.php';
		$audit = load ( 'audit' );
		$id = getgp('id');
		$eid = getgp('eid');
		$sql = 'update sp_project set keep_decide = 2 where id = '.$id;
		$row = $audit->get ( array (
					'id' => $id 
			) );
		if($db->query($sql))
			{    
		         $sms_arr = array(
				            'pid' => $id,
							"temp_id" =>$db->get_var("SELECT id FROM sp_certificate WHERE cti_id = '$row[cti_id]' AND deleted=0"),//此处取证书id
							'eid' => $eid,
							'flag' => 2,
							"is_sms"=>0,
				   );
		          $id=$db->get_var("SELECT id FROM `sp_sms` WHERE `pid` = '$sms_arr[pid]' AND `flag` = '2' AND `deleted` = '0'");
					if($id)
						load ( "sms" )->edit ( $id,$sms_arr );
					else
						load ( "sms" )->add ( $sms_arr );
				
				
				showmsg( 'success', 'success', "?c=certificate&a=keep_decide" );
			     
			}
	}
//搜索条件
$fields = $join = $where = '';
//在组织名称搜索
	if($ep_name)
	{
		$eid_arr = $db->get_col("SELECT eid FROM sp_enterprises WHERE ep_name like '%$ep_name%' AND deleted = 0" );
		$eids = implode(',',$eid_arr);
		$where .= " AND eid in ($eids)";
	}
//项目编号搜索
   if($cti_code)
   {
	   $where .= " AND cti_code like '%$cti_code%'";
   }
 //审核类型搜索
   if($audit_type)
   {
	   $where .= " AND audit_type = '$audit_type'";
   }

   $total_arr = array(0,0,0);
if( !$export ){
	$total_arr[1] = $db->get_var("SELECT COUNT(*) FROM sp_project  WHERE 1 $where AND deleted = 0 AND keep_decide = 1 ");
	$total_arr[2] = $db->get_var("SELECT COUNT(*) FROM sp_project  WHERE 1 $where AND deleted = 0 AND keep_decide = 2 ");

}
	$pages = numfpage( $total_arr[$keep_decide]);
	$where .= " AND keep_decide = ".$keep_decide;

$sql = "SELECT * FROM sp_project WHERE deleted = 0 $where ";
$query = $db->query( $sql );
while( $rt = $db->fetch_array( $query ) ){
	// 换发证书
	$cert = $db->get_row("select certno,e_date from sp_certificate where cti_id='{$rt[cti_id]}' and status in('01','02')");
	$cert && $rt=array_merge($rt,$cert);//证书编号
	$rt['ep_name'] = $db->get_var( "SELECT ep_name FROM sp_enterprises WHERE deleted = 0 AND  eid = '{$rt['eid']}' " );//组织名称
	$rt['audit_ver_V'] = f_audit_ver( $rt['audit_ver'] );//标准
	$rt['leader'] = $db->get_var( "SELECT name FROM sp_task_audit_team WHERE pid = '$rt[id]' and role='1001'" );//组长
	$rt['audit_type_V'] = f_audit_type( $rt['audit_type'] );//审核类型

	$rt['te_date'] = $db->get_var("select taskEndDate from sp_task_audit_team where pid='{$rt[id]}'");//审核结束时间

	$datas[] = $rt;
}

if( !$export ){
 tpl();
} else {//应撤销证书列表
ob_start();
tpl( 'xls/re_decide' );
$data = ob_get_contents();
ob_end_clean();
export_xls( '保持通知书列表', $data );
}