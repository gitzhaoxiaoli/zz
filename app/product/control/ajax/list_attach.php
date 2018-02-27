<?php
/*
*标签公共信息更多
*/
    extract($_GET, EXTR_SKIP);
	$ep_name=$db->get_var("SELECT ep_name FROM `sp_enterprises` WHERE `eid` = '$eid'");
    $attachs = array();
    if ($arctype) {
        $where.= " AND ea.ftype = '$arctype' ";
        $arctype_select = str_replace("value=\"$arctype\">", "value=\"$arctype\" selected>", $arctype_select);
    }
    if ($s_dates) {
        $where.= " and ea.postdate >= '$s_dates 00:00:00' ";
    }
    if ($s_datee) {
        $where.= " and ea.postdate <= '$s_datee 24:00:00' ";
    }
    $cti_ids = explode("|", $cti_id);
    array_push($cti_ids, -1);
    $where .= " AND ea.cti_id IN (".join(",",$cti_ids).")";
    //$join.= " LEFT JOIN sp_enterprises e ON e.eid = ea.eid";
    $total = $db->get_var("SELECT COUNT(*) FROM sp_attachments ea $join WHERE 1 $where");
    $pages = numfpage($total, 20, $url_param);
    $sql = "SELECT ea.* FROM sp_attachments ea $join WHERE 1 $where ORDER BY ea.id DESC $pages[limit]";
    $query = $db->query($sql);
    while ($rt = $db->fetch_array($query)) {
        $rt['ctfrom_V'] = f_ctfrom($rt['ctfrom']);
        $rt['ftype_V'] = f_arctype($rt['ftype']);
        $rt['postdate'] = mysql2date('Y-m-d', $rt['create_date']);
        $attachs[$rt['id']] = $rt;
    }
    tpl('ajax/list_attach');