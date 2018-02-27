<?php

!defined('IN_SUPU') && exit('Forbidden');
//下载分类：系统下载操作，企业，人员
$class = getgp('class') ? getgp('class') : 'enterprise';
//文档模型
$att = load("attachment");

if ($class == 'enterprise') {//企业文档下载路径
    $file_dir = get_conf('upload_ep_dir');
} else if ($class == 'audit') {
    $file_dir = get_conf('upload_audit_dir');
} elseif ($class == 'cti_test_report') {//工厂检查报告下载路径 
    $file_dir = get_conf('upload_cti_test_report');
} elseif($class == 'upload_photo'){
	 $file_dir = get_conf('upload_product_photo_dir');
}else {
    $file_dir = get_conf('upload_hr_dir');
    //$att->table='hr_archives';
}

$att->attachdir = $file_dir; // 

if ('down' == $a) { //单个文档下载
    $aid = getgp('aid');
     
    $att->down($aid);
} elseif ('batdown' == $a) { //批量下载文档
    $aids = getgp('aid');
    $aids = array_unique($aids);
    if ($aids) {
        $att->batdown($aids);
    } else {
        
    }
} else if ('del' == $a) { //删除上传的文档
    $aid = getgp('aid');
    $att->del($aid);
    echo "<script>history.go(-1);</script>";
}elseif($a=='list'){ //文档列表
	$where='';
	if($_GET['name']){
		$where.=" AND name like '%$_GET[name]%'"; 
	} 
	if($_GET['user']){ 
	 	$users=$db->get_Col(" SELECT id FROM sp_hr WHERE name LIKE '%" . str_replace('%', '\%', $_GET['user']) . "%'");
		
		$where.=" AND ".$db->sqls(array('upload_uid'=>$users));
	 
		
	}
	 
 	$total=$db->find_num('attachments','',$where);
	$pages      = numfpage($total); 
	 
	
	//$items=$att->gets($where,$pages['limit']);
  $sql="select * from sp_attachments WHERE 1  $where ORDER BY id DESC  $pages[limit] ";
  $query=$db->query($sql);
	while($row=$db->fetch_array($query)){
		$row=$att->_format_row($row);
	//	p($row);
		$items[]=$row;
		
		
	}
	
	tpl();
	
}



?>