<?php
!defined('IN_SUPU') && exit('Forbidden');


// 更改数据
	if(isset($_GET['id'])) {
		$row = $db->get_row("SELECT * FROM sp_log WHERE id=" . $_GET['id']);
		$af_str = unserialize($row['af_str']);
		$bf_str = unserialize($row['bf_str']);
		tpl('sys/logdiff');
		exit;
	}
	$fields = $join = $where = '';
	extract($_GET);
	$where = "";
	if($e_name = trim($e_name)){
		$where .= " AND e.ep_name LIKE '%$e_name%'";
	}
	if($u_name = trim($u_name)){
		$where .= " AND hr.name LIKE '%$u_name%'";
	}
	if($up_name = trim($up_name)){
		$where .= " AND l.create_user LIKE '%$up_name%'";
	}
	if($content = trim($content)){
		$where .= " and content like '%$content%' ";
	}
	if($s_date){
		$where .= " and l.create_date >= '$s_date' ";
	}
	if($e_date){
		$where .= " and l.create_date <= '$e_date' ";
	}
	if($ip){
		$where .= " and l.ip like '%$ip%' ";
	}
	$join .= " LEFT JOIN sp_enterprises e ON e.eid = l.eid";
	$join .= " LEFT JOIN sp_hr hr ON hr.id = l.uid";
	$total = $db->get_var("SELECT COUNT(*) FROM sp_log l $join WHERE 1 $where");
	    
	$pages = numfpage( $total );
	$sql = "SELECT l.*,e.ep_name,hr.name FROM sp_log l $join WHERE 1 $where ORDER BY l.id desc $pages[limit]" ;
	$query = $db->query( $sql);
	$datas = array();
	while( $rt = $db->fetch_array( $query ) ){
		$datas[] = $rt;
	}
	tpl('sys/loglist');