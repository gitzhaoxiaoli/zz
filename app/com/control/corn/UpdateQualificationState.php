<?php
/*
 *	将过期的注册资格设置为失效
 *
 *
 */
$where="  WHERE e_date <  '".mysql2date('Y-m-d',nowTime('mysql'))."' AND status = '1' AND e_date!='0000-00-00' ";


$query = $db->query("SELECT uid,iso FROM sp_hr_qualification $where");

while( $rt = $db->fetch_array( $query ) ){
    //更新业务代码
	$db->query("UPDATE sp_hr_audit_code SET hqa_status = 0 WHERE uid = $rt[uid] AND iso = '$rt[iso]'");
}
//更新资格表
$db->query( "UPDATE sp_hr_qualification SET status = '0' $where" );


?>