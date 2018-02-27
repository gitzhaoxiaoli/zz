<?php
!defined('IN_SUPU') && exit('Forbidden');

/*
*选择派人
*/
    require_once (ROOT .'/data/sys_cache/region.cache.php'); //省份
	require_once (ROOT . '/data/cache/audit_job.cache.php');
    require_once (ROOT . '/data/cache/qualification.cache.php');
    $limit=$_GET[limit];
	if($limit==NULL){
		$limit=20;
	}
    $tid = getgp('tid');
    //取任务信息
    $t_info = $db->get_row("SELECT * FROM sp_task WHERE id = '$tid' and deleted = 0");
	$eid=$t_info[eid];
	$_eids=$db->get_col("SELECT eid FROM sp_enterprises WHERE eid='$eid' or parent_id='$eid' AND deleted=0");
    $taskBeginDate = getgp("taskBeginDate")." ".getgp("taskBeginTime");
    $taskEndDate = getgp("taskEndDate")." ".getgp("taskEndTime");
    //取任务的 体系、专业代码
    $t_isos = $t_use_codes = $t_projects = $rmp_audit_arr = array();
    $query = $db->query("SELECT * FROM sp_project WHERE tid = '$tid' AND deleted = 0");
	//echo $tid."<br />";
    while ($rt = $db->fetch_array($query)) {
        $t_isos[$rt['iso']] = f_iso($rt['iso']);
        $rmp_audit_arr[$rt['iso']] = $rt['audit_code'];
        $t_use_codes[$rt['iso']] = $rt['audit_code'];
		$p_info[$rt[iso]]=$rt;
    };
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
	if (!$iso) {
        $isos = array_keys($t_isos);
        
    }else
		$isos[]=$iso;
	$where .=" AND hqa.iso IN ('".join("','",$isos)."')";
	if($audit_code){
		$hqa_ids=$db->get_col("SELECT qua_id FROM `sp_hr_audit_code` WHERE `audit_code` = '$audit_code' AND `deleted` = '0'");
		$hqa_ids=array_merge($hqa_ids,array(-1));
		$where .=" AND hqa.id IN (".join(",",$hqa_ids).")";
	}
	if($use_code){
		$hqa_ids=$db->get_col("SELECT qua_id FROM `sp_hr_audit_code` WHERE `use_code` = '$use_code' AND `deleted` = '0'");
		$hqa_ids=array_merge($hqa_ids,array(-1));
		$where .=" AND hqa.id IN (".join(",",$hqa_ids).")";
	}
	//获取当前项目所有小类的数组
	$audit_code_alls = array();
	if($t_use_codes){
		foreach($t_use_codes as $k=>$val){
			$tmp = explode("；",$val);
			foreach($tmp as $val2){
				$audit_code_alls[$k][] = $val2;
			}
			$audit_code_alls[$k]=array_unique($audit_code_alls[$k]);
		}
		
	}
    $hr_quas = array();
    $fields = $join = '';
    $fields = "hqa.*,hr.name,hr.sex,hr.audit_job,hr.areacode,hr.tel,hr.ctfrom,hr.major,hr.m_separate,hr.day_quota";
    $join = " RIGHT JOIN sp_hr hr ON hr.id = hqa.uid";
    // $where.= " AND hqa.status = 1";
    $where.= " and hr.is_hire='1'  ";
    $total = $db->get_var("SELECT COUNT(*) FROM sp_hr_qualification hqa $join WHERE 1 $where");
	
    $pages = numfpage($total,$limit);
    
    $n_year = date('Y');
    $sql = "SELECT $fields FROM sp_hr_qualification hqa $join WHERE 1 $where ORDER BY hr.easycode,hr.areacode  $pages[limit]";
	$query = $db->query($sql);
    while ($rt = $db->fetch_array($query)) {
        $_audit_codes = array();
		$_use_codes=array();
        if ($audit_code_alls[$rt['iso']]) {
            $query2 = $db->query("SELECT use_code,audit_code FROM sp_hr_audit_code WHERE iso = '$rt[iso]' AND uid = '$rt[uid]' AND use_code IN ('".join("','",$audit_code_alls[$rt['iso']])."') AND deleted = 0");
            while ($rt2 = $db->fetch_array($query2)) {
				$_audit_codes[] = $rt2['audit_code'];
                $_use_codes[] = $rt2['use_code'];
            }
			
        }
        $_audit_codes && $rt['audit_code'] = @implode('；', $_audit_codes);
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
			OR ( tat.taskBeginDate <= '$taskBeginDate' AND tat.taskEndDate >= '$taskEndDate' )) and tat.uid='$rt[uid]' and tat.eid not in (".join(",",$_eids).")");
        
        $rt['is_plan'] = !$s[uid] ? '否' : "<a href=\"javascript:;\" title=\"审核企业：{$s[ep_name]} 审核时间：{$s[taskBeginDate]} - {$s[taskEndDate]}\">是</a>";
       $rt['province']		= f_region_province( $rt['areacode'] );
        
        $hr_quas[$rt['id']] = $rt;
    }
   
    $audit_code = str_replace(',', '；', $audit_code);
    tpl('ajax/select_auditor');
