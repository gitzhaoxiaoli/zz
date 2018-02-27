<?php
!defined('IN_SUPU') && exit('Forbidden');


// 更改数据
	if(isset($_GET['id'])) {
		$row = $db->get_row("SELECT * FROM sp_log WHERE id=" . $_GET['id']);
		$af_str = unserialize($row['af_str']);
		$bf_str = unserialize($row['bf_str']);
		template('sys/logdiff');
		exit;
	}
	$fields = $join = $where = '';
	foreach($_GET as $k=>$v){
		${$k} = getgp($k);
	}
	$where = "";
	if($e_name){
		$sql = "select eid from sp_enterprises where ep_name like '%$e_name%' ";
		$uids = array();
		$res = $db->query($sql);
		while($row = $db->fetch_array($res)){
			$uids[] = $row['eid'];
		}
		if($uids){
			$uid_str = implode("','",$uids);
			$where .= " and eid in ('$uid_str') ";
		}else{
			$where .= " and eid='' ";
		}
	}
	if($u_name){
		$sql = "select id from sp_hr where name like '%$u_name%' ";
		$uids = array();
		$res = $db->query($sql);
		while($row = $db->fetch_array($res)){
			$uids[] = $row['id'];
		}
		if($uids){
			$uid_str = implode("','",$uids);
			$where .= " and uid in ('$uid_str') ";
		}else{
			$where .= " and uid='' ";
		}
	}
	if($up_name){
		$sql = "select id from sp_hr where name like '%$up_name%' ";
		$uids = array();
		$res = $db->query($sql);
		while($row = $db->fetch_array($res)){
			$uids[] = $row['id'];
		}
		if($uids){
			$uid_str = implode("','",$uids);
			$where .= " and up_uid in ('$uid_str') ";
		}else{
			$where .= " and up_uid='' ";
		}
	}
	if($content){
		$where .= " and content like '%$content%' ";
	}
	if($s_date){
		$where .= " and up_date >= '$s_date' ";
	}
	if($e_date){
		$where .= " and up_date <= '$e_date' ";
	}
	if($ip){
		$where .= " and ip like '%$ip%' ";
	}
	$total = $db->get_var("SELECT COUNT(*) FROM sp_log $join WHERE 1 $where");
	$pages = numfpage( $total);
      
        
	$sql = "SELECT id,eid,uid,content,af_str,bf_str,up_uid,up_date,ip FROM sp_log $join WHERE 1 $where ORDER BY id desc $pages[limit]" ;
	$query = $db->query( $sql);
	$datas = array();
	while( $rt = $db->fetch_array( $query ) ){
		$rt['eid'] = f_en_name($rt['eid']);
		$rt['uid'] = f_username($rt['uid']);
		$rt['up_uid'] = f_username($rt['up_uid']);
		$datas[] = $rt;
	}
	template('sys/loglist');