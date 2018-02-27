<?php
!defined('IN_SUPU') && exit('Forbidden');
//添加-修改合同 :获取页面请求，查询列表用extract获取get变量，修改与添加用 $_GET 或$_post
$_iso_array=array("A01"=>"Q","A02"=>"E","A03"=>"O","A09"=>"En");  
$ct_id  = (int) getgp('ct_id');
$eid    = (int) getgp('eid');
$op     = (int) getgp('op'); //是否再认证登记
$status = (int) getgp('status');
$sp_f_select = f_select('sp_f');
$ctfrom = getgp('ctfrom');
extract($_POST);
if ($step) {
	$ct_code=getgp("ct_code");
    //sql查询  
    $ctfrom       = $db->get_var("SELECT ctfrom FROM sp_enterprises WHERE eid = '$eid'"); //从企业表中继承合同来源
    $new_contract = array(
        'eid' => $eid,
        'ct_code' => getgp('ct_code'),
        'is_first' => trim(getgp('is_first')), //是否初次
        'accept_date' => getgp('accept_date'), //爱理日期
        'pre_date' => getgp('pre_date'), //预审日期
        'note' => getgp('note'), //备注
        'mark_require' => getgp('mark_require'),
        'audit_require' => getgp('audit_require'),
        'finance_require' => getgp('finance_require'),
        'ctfrom' => $ctfrom,
        'status' => $status,
        'ep_date'=>$ep_date
    );
    if ($ct_id and !$op) { //op是否再认证登记
        $ct->edit($ct_id, $new_contract);
    } else {
        $ct_id = $ct->add($new_contract);
    }
    /* 处理体系项目 */
    $cti_codes   = @array_map('trim', getgp('cti_code'));
    $audit_vers  = @array_map('trim', getgp('audit_ver'));
    $sp_fs       = @array_map('trim', getgp('sp_f'));
    $signe_names       = @array_map('trim', getgp('signe_name'));
    $audit_types = @array_map('trim', getgp('audit_type'));
    $totals      = @array_map('intval', getgp('total'));
    $renums      = @array_map('intval', getgp('renum'));
    $is_turns    = @array_map('trim', getgp('is_turn'));
    $old_name    = @array_map('trim', getgp('old_name'));
    $cacn_code    = @array_map('trim', getgp('cacn_code'));
    $is_refund = @array_map('trim', getgp('is_refund'));
    $refund_note = @array_map('trim', getgp('refund_note'));
    $coo_id = @array_map('trim', getgp('coo_id'));
    $old_cert_no = @array_map('trim', getgp('old_cert_no'));
    $old_sdate   = getgp('old_sdate');
    $old_edate   = getgp('old_edate');
    $old_pddate  = getgp('old_pddate');
    $old_autype  = getgp('old_autype');
    //$marks		= getgp( 'marks' );
    $scopes      = @array_map('strip_tags', getgp('scope'));
    //修改合同项目信息
    if ($audit_types) {
        foreach ($audit_types as $cti_id => $audit_type) {
            $ep_amount = $totals[$cti_id];
            //读取基础人日
			if($audit_type=='1001')
				$type='1003';
			elseif($audit_type=='1007')
				$type="1007";
			elseif(in_array($audit_type,array("1004-1","1004-2","1004-3")))
				$type="1004-1";
            if ($audit_ver_array[$audit_vers[$cti_id]]['iso']=="A01") {
                $base_num = $db->get_var("SELECT num_m FROM `sp_enterprises_base` WHERE `ep_amount` >= '$ep_amount' and iso ='1' and type='$type' limit 1");
            } else {
                $base_num = $db->get_var("SELECT num_m FROM `sp_enterprises_base` WHERE `ep_amount` >= '$ep_amount' and iso ='2' and type='$type' limit 1");
            }
            $new_item = array(
                'cti_code' => $cti_codes[$cti_id],
                'iso' => $audit_ver_array[$audit_vers[$cti_id]]['iso'],
                'audit_ver' => $audit_vers[$cti_id],
                'audit_type' => $audit_types[$cti_id],
                'total' => $totals[$cti_id],
                'renum' => $renums[$cti_id],
                'is_turn' => $is_turns[$cti_id],
                'is_refund' => $is_refund[$cti_id],
                'coo_id' => $coo_id[$cti_id],
                'refund_note' => $refund_note[$cti_id],
                'old_name' => $old_name[$cti_id],
                'cacn_code' => $cacn_code[$cti_id],
                'old_cert_no' => $old_cert_no[$cti_id],
                'old_sdate' => $old_sdate[$cti_id],
                'old_edate' => $old_edate[$cti_id],
                'old_pddate' => $old_pddate[$cti_id],
                'old_autype' => $old_autype[$cti_id],
                //	'mark'		=> @ implode( ',', $marks[$cti_id] ),
                'scope' => trim($scopes[$cti_id]),
                'ctfrom' => $ctfrom,
                'base_num' => $base_num,
				'risk_level'	=> "02",
                'sp_f' => $sp_fs[$cti_id],
                'signe_name' => $signe_names[$cti_id],
            );
            // p($new_item);die;
            $cti->edit($cti_id, $new_item);
        }
    }
    //新增合同项目信息
    $add       = getgp('add');
   //p($add);
	//exit;
    $add_metas = getgp('add_meta');
    if ($add['audit_type']) {
        $ADDSQL = array();
        foreach ($add['audit_type'] as $k => $audit_type) {
            $ep_amount = $add[total][$k];
            if ($audit_ver_array[$add['audit_ver'][$k]]['iso'] == "A01") {
                if($audit_type=='1001')
					$type='1003';
				elseif($audit_type=='1007')
					$type="1007";
				elseif(in_array($audit_type,array("1004-1","1004-2","1004-3")))
					$type="1004-1";
                $base_num = $db->get_var("SELECT num_m FROM `sp_enterprises_base` WHERE `ep_amount` >= '$ep_amount' and iso ='1' and type='$type' limit 1");
            } else {
                $base_num = $db->get_var("SELECT num_m FROM `sp_enterprises_base` WHERE `ep_amount` >= '$ep_amount' and iso ='2' and type='$type' limit 1");
            }
            if (empty($audit_type))
                continue;
			$cti_code    = $add['cti_code'][$k];
            $iso         = $audit_ver_array[$add['audit_ver'][$k]]['iso'];
            $audit_ver   = $add['audit_ver'][$k];
            $audit_type  = $add['audit_type'][$k];
            $total       = $add['total'][$k];
            $renum       = $add['renum'][$k];
            $is_turn     = $add['is_turn'][$k];
            $refund_note = $add['refund_note'][$k];
            $coo_id      = $add['coo_id'][$k];
            $is_refund   = $add['is_refund'][$k];
            $old_name    = $add['old_name'][$k]; //原证书信息
            $cacn_code   = $add['cacn_code'][$k];
            $old_cert_no = $add['old_cert_no'][$k];
            $old_sdate   = $add['old_sdate'][$k];
            $old_edate   = $add['old_edate'][$k];
            $old_pddate  = $add['old_pddate'][$k];
            $old_autype  = $add['old_autype'][$k];
            $scope       = $add['scope'][$k];
            $scope_e       = $add['scope_e'][$k];
            $sp_f      = $add['sp_f'][$k];
            $signe_name       = $add['signe_name'][$k];
            $new_cti     = array(
                'eid' => $eid, //企业ID
                'ct_id' => $ct_id, //合同ID
                'cti_code' => $cti_code, //合同项目ID
                'iso' => $iso, //认证体系
                'audit_ver' => $audit_ver, //认证版本
                'audit_type' => $audit_type, //审核类型
                'total' => $total, //体系人数
                'renum' => $renum, //
                'is_turn' => $is_turn, //是否转机构
                'old_name' => $old_name, //
                'cacn_code' => $cacn_code, //
                'is_refund' => $is_refund, //
                'refund_note' => $refund_note, //
                'coo_id' => $coo_id, //
                'old_cert_no' => $old_cert_no, //
                'old_sdate' => $old_sdate, //
                'old_edate' => $old_edate, //
                'old_pddate' => $old_pddate, //
                'old_autype' => $old_autype, //原审核类型
                //'mark'		=> $mark,				//认可标志
                'scope' => trim($scope), //认证范围
                'scope_e' => trim($scope_e), //英文范围
                'ctfrom' => $ctfrom,
                'sp_f' => $sp_f,
                'signe_name' => $signe_name,
                'base_num' => $base_num,
                'risk_level'	=> "02",
                //'accept_date'=> getgp( 'accept_date' ),
                //'pre_date'	=> getgp( 'pre_date' )	//预审日期
            );
            $new_cti_id  = $cti->add($new_cti);
            log_add($eid, 0, "合同项目-增加 编号:" . $cti_code, NULL, serialize($new_cti));
        }
    }
    showmsg('success', 'success', "?c=contract&a=edit&ct_id=$ct_id&eid=$eid");
} else {
    //显示需要编辑的信息
    $is_first_Y = $is_first_N = '';
    $is_first   = 1;
    if ($ct_id) {
        $ct_id = (int) getgp('ct_id');
        $row   = $ct->get(array(
            'ct_id' => $ct_id
        ));
        extract(chk_arr($row), EXTR_SKIP);

        $status        = $row['status'];
        $is_first      = $row['is_first'];
        //是否禁用保存按钮
        //已经评审过的不允许再保存
        $save_disabled = ($status > 1 && !$op) ? ' disabled' : '';
        if ($op) { //var op再认证
            $audit_type  = '1007';
            $accept_date = '';
            $pre_date    = '';
            $ct_code     = '';
        }
        /* 项目取数 BEGIN */
        $projects = array();
        $query    = $db->query("SELECT * FROM sp_contract_item WHERE ct_id = '$ct_id' ORDER BY cti_id asc");
        while ($rt = $db->fetch_array($query)) {
            $rt['audit_ver_select']          = str_replace("value=\"$rt[audit_ver]\">", "value=\"$rt[audit_ver]\" selected>", $audit_ver_select);
            $rt['audit_type_select']         = str_replace("value=\"$rt[audit_type]\">", "value=\"$rt[audit_type]\" selected>", $audit_type_select);
            $rt['sp_f_select']         = str_replace("value=\"$rt[sp_f]\">", "value=\"$rt[sp_f]\" selected>", $sp_f_select);
            //$rt['signe_select']  = str_replace("value=\"$rt[signe_name]\"", "value=\"$rt[signe_name]\" selected", $signe_select);
            //是否转机构
            $rt['is_turn_0']                 = $rt['is_turn_1'] = '';
            $rt['is_turn_' . $rt['is_turn']] = 'selected';
            $rt['old_autype_select']         = str_replace("value=\"$rt[old_autype]\">", "value=\"$rt[old_autype]\" selected>", $audit_type_select);
            //$rt = array_merge( $rt );
            // 新增再认证取证书的范围（最后一张）
            if ($op) {
                $rt['scope'] = $db->get_var("SELECT cert_scope FROM sp_certificate WHERE cti_id={$rt['cti_id']} AND deleted=0 ORDER BY id DESC");
            }
            $projects[$rt['cti_id']] = chk_arr($rt);
        }
        /* 项目取数 END */
    }
    //是否初次
    if ('n' == $is_first) {
        $is_first_N = 'checked';
    } else {
        $is_first_Y = 'checked';
    }
    if ($status >= 1 && !$op)
        $checked = 'checked';
    else
        $status = 1;


    tpl('contract/edit');
}
