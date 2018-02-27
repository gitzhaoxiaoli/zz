<?php
!defined('IN_SUPU') && exit('Forbidden');

    $where = '';
	//来源
	$coo_from = getgp('coo_from');
	if($coo_from){
		$where .= " AND coo_from = '".$coo_from."'";
	};
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
    //是否合作  1-> 合作 2->不合作
	$where .= " AND coo_status = 1";
    $total = $db->find_num("cooperation",$where);
    $pages = numfpage( $total,15);

    $sql = "SELECT * FROM sp_cooperation WHERE 1 AND deleted = 0 $where $pages[limit]";
    //echo $sql;
	//exit;
	$res = $db->get_results($sql);

tpl();