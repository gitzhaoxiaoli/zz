<?php
/*
 *	将过期的证书设置为失效
 *
 *
 */


$db->query( "UPDATE sp_certificate SET status = '5' WHERE e_date < '".mysql2date('Y-m-d',nowTime('mysql'))."' AND status IN (1,2)" );

?>