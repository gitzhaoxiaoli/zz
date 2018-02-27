<?php
!defined('IN_SUPU') && exit('Forbidden');

/*
*选择派人
*/

	require_once (ROOT . '/data/cache/audit_job.cache.php');
    require_once (ROOT . '/data/cache/ctfrom.cache.php');
    require_once (ROOT . '/data/cache/qualification.cache.php');
	require_once (ROOT .'/data/sys_cache/region.cache.php'); //省份
    
    $tid = getgp('tid');
    //取任务信息
    $t_info = $db->get_row("SELECT * FROM sp_task WHERE id = '$tid' and deleted = 0");
	$eid=$t_info[eid];
    $taskBeginDate = $t_info[tb_date];
    $taskEndDate = $t_info[tb_date];
    $query = $db->query("SELECT * FROM sp_project WHERE tid = '$tid' AND deleted = 0");
	//echo $tid."<br />";
	$rule=$p_info=$t_isos=$code_arr=array();
    while ($rt = $db->fetch_array($query)) {
        $_rule=$db->getField("settings_prod_xiaolei","use_code",array("code"=>$rt[prod_id]));
		$rule[$rt[iso]][]=$_rule;
		$code_arr[$_rule][]=$_rule;
		$t_isos[]=$rt[iso];
		$p_info[$rt[iso]]=$rt;
    };
	foreach($code_arr as $k=>$v){
		$code_arr[$k] = array_unique($v);
	}
    //人员性质下拉
    $audit_job_select = '';
    if ($audit_job_array) {
        foreach ($audit_job_array as $code => $item) {
            $audit_job_select.= "<option value=\"$code\">$item[name]</option>";
        }
        
    }
    //审核员资格下拉
    $qua_type = getgp('qua_type'); //审核员资格
    $qua_type_select = '';
    if ($qualification_array) {
        foreach ($qualification_array as $code => $item) {
            $qua_type_select.= "<option value=\"$code\">$item[name]</option>";
        }
        if($qua_type){
        	$qua_type_select.= str_replace("value=\"$qua_type\">", "value=\"$qua_type\" selected>", $qua_type_select);
        }
    }
    
    if ($areacode) {
        //@HBJ 2013年9月12日 09:04:37 areacode从人员表查询
        $where.= " AND LEFT(hr.areacode,2) = '" . substr($areacode, 0, 2) . "'";
		$province_select = str_replace( "value=\"$areacode\">", "value=\"$areacode\" selected>" , $province_select );
    }
	//省份下拉
    $province_select = f_province_select();
    $areacode = getgp('areacode'); //省份
    $ctfrom = getgp('ctfrom'); //合同来源
   
    $name = trim(getgp('name')); //用户
    $easycode = trim(getgp('easycode')); //易记码
    $audit_job = getgp('audit_job');
    $iso = getgp('iso'); //搜索 ISO
    $audit_code = getgp('audit_code'); //搜索 专业代码
    $use_code = getgp('use_code'); //搜索 专业代码
   
    $auditor_users = $codes = array();
    $fields = $join = $where = $q_where = '';

    //合同来源
	
    if ($ctfrom && '01000000' != $ctfrom) {
        $len = get_ctfrom_level(current_user('ctfrom'));
        if ($ctfrom && substr($ctfrom, 0, $len) == substr(current_user('ctfrom') , 0, $len)) {
            $_len = get_ctfrom_level($ctfrom);
            $len = $_len;
        } else {
            $ctfrom = current_user('ctfrom');
        }
        switch ($len) {
            case 2:
                $add = 1000000;
                break;

            case 4:
                $add = 10000;
                break;

            case 6:
                $add = 100;
                break;

            case 8:
                $add = 1;
                break;
        }
        $ctfrom_e = sprintf("%08d", $ctfrom + $add);
        $where .=" AND hr.ctfrom >= '$ctfrom' AND hr.ctfrom < '$ctfrom_e'";
		$ctfrom_select=str_replace("value=\"$ctfrom\"","value=\"$ctfrom\" selected",$ctfrom_select);
    }
	

    //审核员
    if ($name) {
        $_uids = array();
        $query = $db->query("SELECT id FROM sp_hr WHERE name LIKE '%$name%' and is_hire='1' ");
        while ($rt = $db->fetch_array($query)) {
            $_uids[] = $rt['id'];
        }
        if ($_uids) {
            $where.= " AND hqa.uid IN (" . implode(',', $_uids) . ")";
        } else {
            $where.= " AND hqa.id < -1";
        }
    }
    //易记码
    if ($easycode) {
        $_uids = array();
        $query = $db->query("SELECT id FROM sp_hr WHERE easycode LIKE '$easycode%' and is_hire='1'");
        while ($rt = $db->fetch_array($query)) {
            $_uids[] = $rt['id'];
        }
        if ($_uids) {
            $where.= " AND hqa.uid IN (" . implode(',', $_uids) . ")";
        } else {
            $where.= " AND hqa.id < -1";
        }
    }
    //专业特长
    $major = trim(getgp('major'));
    if( $major ){
    	$where .= " AND hr.major like '%$major%' ";
    }
    
    //人员分层
    $m_separate = getgp('m_separate');
    if($m_separate){
    	$where .= " AND hr.m_separate ='$m_separate'";
    }
    //审核员资格查询
    $qua_type = getgp('qua_type');
    if($qua_type){
    	$where .= " AND hqa.qua_type ='$qua_type'";
    }
	if($use_code=trim(getgp("use_code"))){
    	$_uids = array();
        $query = $db->query("SELECT uid FROM `sp_hr_audit_code` WHERE `use_code` = '$use_code'  and iso in('".join("','",$isos)."') and deleted=0");
        while ($rt = $db->fetch_array($query)) {
            $_uids[] = $rt['uid'];
        }
		$_uids=array_unique($_uids);
        if ($_uids) {
            $where.= " AND hqa.uid IN (" . implode(',', $_uids) . ")";
        } else {
            $where.= " AND hqa.id < -1";
        }
        
    }
    //专/兼职
    if ($audit_job) {
        $_uids = array();
        $query = $db->query("SELECT id FROM sp_hr WHERE audit_job = '$audit_job' and is_hire='1' ");
        while ($rt = $db->fetch_array($query)) {
            $_uids[] = $rt['id'];
        }
        if ($_uids) {
            $where.= " AND hqa.uid IN (" . implode(',', $_uids) . ")";
        } else {
            $where.= " AND hqa.id < -1";
        }
        $audit_job_select = str_replace("value=\"$audit_job\">", "value=\"$audit_job\" selected>", $audit_job_select);
    }
	/* 专业代码匹配 */
    //认证体系
    /**/
	$t_isos = array_unique($t_isos);
	$where .=" AND hqa.iso IN ('".join("','",$t_isos)."')";
    $hr_quas = array();
    $fields = $join = '';
    $fields = "hqa.*,hr.name,hr.sex,hr.audit_job,hr.areacode,hr.areacode_str,hr.tel,hr.ctfrom,hr.major,hr.m_separate,hr.day_quota";
    $join = " LEFT JOIN sp_hr hr ON hr.id = hqa.uid";
    $where.= " AND hqa.e_date >= '$taskEndDate'";
    $where.= " and hr.is_hire='1'  ";
    $total = $db->get_var("SELECT COUNT(*) FROM sp_hr_qualification hqa $join WHERE 1 $where");
	
    $pages = numfpage($total,10);
    $n_year = date('Y');
    $sql = "SELECT $fields FROM sp_hr_qualification hqa $join WHERE 1 $where ORDER BY hr.easycode,hr.areacode  $pages[limit]";
	$query = $db->query($sql);
    while ($rt = $db->fetch_array($query)) {
        $_audit_codes = array();
		$_use_codes=array();
        if ($rule[$rt['iso']]) {
            $query2 = $db->query("SELECT use_code,audit_code FROM sp_hr_audit_code WHERE iso = '$rt[iso]' AND uid = '$rt[uid]' AND use_code IN ('".join("','",$rule[$rt['iso']])."') AND deleted = 0");
            while ($rt2 = $db->fetch_array($query2)) {
				// $_audit_codes[] = $rt2['audit_code'];
                $_use_codes[] = join("；",$code_arr[$rt2['use_code']]);
            }
			
        }
        // $_audit_codes && $rt['audit_code'] = @implode('；', $_audit_codes);
        $_use_codes && $rt['use_code'] = implode('；', array_unique($_use_codes));
        $rt['sex'] = ($rt['sex'] == 1) ? '男' : '女';
        $rt['audit_job'] = f_audit_job($rt['audit_job']);
        $rt['is_leader'] = ($rt['is_leader']) ? '是' : '否';
        $rt['ctfrom'] = f_ctfrom($rt['ctfrom']);
        $rt['address'] = $db->get_var("SELECT meta_value FROM sp_metas_hr WHERE meta_name = 'address' AND used = 'user' AND ID = $rt[uid]");
		$s=$db->get_row("SELECT tat.uid,tat.taskBeginDate,tat.taskEndDate,e.ep_name FROM sp_task_audit_team tat
			LEFT JOIN sp_enterprises e ON e.eid = tat.eid
			WHERE tat.deleted = 0 AND (
				(tat.taskBeginDate >= '$taskBeginDate' AND tat.taskBeginDate <= '$taskEndDate')
			OR
				( tat.taskEndDate >= '$taskBeginDate' AND tat.taskEndDate <= '$taskEndDate' ) 
			OR ( tat.taskBeginDate <= '$taskBeginDate' AND tat.taskEndDate >= '$taskEndDate' )) and tat.uid='$rt[uid]' ");
		$temp = f_iso($s[iso]);
        $rt['is_plan'] = !$s[uid] ? '否' : "<a href=\"javascript:;\" title=\"{$temp}    审核企业：{$s[ep_name]} 审核时间：{$s[taskBeginDate]} - {$s[taskEndDate]}\">是</a>";
       $rt['province']		= f_region_province( $rt['areacode'] );
        
        $hr_quas[$rt['id']] = $rt;
    }
   
    $audit_code = str_replace(',', '；', $audit_code);
    tpl('ajax/select_auditor');
