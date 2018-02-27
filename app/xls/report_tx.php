<?php
!defined('IN_SUPU') && exit('Forbidden');
set_time_limit(0);
/*
 *---------------------------------------------------------------
 * 导出月报 2016-6-21
 * 附加说明
 *---------------------------------------------------------------
 */
// echo DATA_DIR."templ.xls";
// exit;
require_once ROOT . '/data/cache/audit_ver.cache.php';
$new_data = array();
$a71      = $begindate = getgp('s_date');
$a72      = $enddate = getgp('e_date');
if (!$a71 or !$a72)
    exit("ERROR");
$enddate .= " 23:59:59";
$a1      = get_option("zdep_id");
$a2      = get_option("zdep_name");
// [56] 证书使用的认可号
$iso_rkh = array(
    'A01' => '',
    'A02' => '',
    'A03' => ''
);
// 认证审核活动代码
function get_audit_type($audit_type)
{
    if ($audit_type == '1001' || $audit_type == '1002' || $audit_type == '1003') {
        return '01';
    } else if (strpos($audit_type, "1004") !== false) {
        return '03';
    } else if ($audit_type == '1007') {
        return '02';
    } else if ($audit_type == '1011') {
        return '05';
    } else {
        return '04';
    }
}
//注：还有一个类型是05变更，需要在程序里判断
// 认证审核阶段代码
function get_audit_type_auditor($audit_type)
{
    if ($audit_type == '1002') {
        return '0101'; //初审一阶段
    } else if ($audit_type == '1003') {
        return '0102';
    } else if ($audit_type == '1007') {
        return '0201';
    } else if (strpos($audit_type, "1004") !== false) {
        return '03';
    } else {
        return '04'; //变更在特殊监管  04
    }
}
// 取监督次数
function get_audit_num($audit_type)
{
    $num = abs(strstr($audit_type, "-"));
    return $num;
}
// 处理 小类代码
function get_code($code)
{
    $code  = str_replace(array(
        ";",
        ",",
        "，",
        " "
    ), "；", $code);
    $codes = explode("；", $code);
    foreach ($codes as $k => $v) {
        $codes[$k] = substr($v, 0, 8);
    }
    $codes = array_unique($codes);
    return join("；", $codes);
}
// 通过审核类型找到收费类型
// function get_cost_type($type){
// 	switch ($type) {
// 		case "1001":$cost_type = "cost_first";break;
// 		case "1002":$cost_type = "cost_first";break;
// 		case "1003":$cost_type = "cost_first";break;
// 		case "1007":$cost_type = "cost_first";break;
// 		case "1004-1":$cost_type = "cost_one";break;
// 		case "1004-2":$cost_type = "cost_two";break;
// 		case "1004-3":$cost_type = "cost_three";break;
// 		default :$cost_type = 'cost_other';
// 	}
// 	return $cost_type;
// }
// 生成excel
function do_excel($data_cert, $data_auditor, $data_energy, $title)
{
    require_once ROOT . '/theme/Excel/PHPExcel.php';
    require_once ROOT . '/theme/Excel/PhpExcel/Writer/Excel5.php';
    include_once ROOT . '/theme/Excel/PhpExcel/IOFactory.php';
    $objReader = new PHPExcel_Reader_Excel5;
    $objExcel  = $objReader->load(CONF . "templ.xls");
    $objExcel->setActiveSheetIndex(0);
    $objActSheet = $objExcel->getActiveSheet();
    $i           = 8;
    if ($data_cert)
        foreach ($data_cert as $_val) {
            $k = "C";
            foreach ($_val as $val) {
                $objActSheet->setCellValueExplicit($k . $i, $val, PHPExcel_Cell_DataType::TYPE_STRING);
                $k++;
            }
            $i++;
        }
    $objExcel->setActiveSheetIndex(1); //切换到新创建的工作表
    $objActSheet = $objExcel->getActiveSheet();
    $i           = 8;
    if ($data_auditor)
        foreach ($data_auditor as $_val) {
            $k = "C";
            foreach ($_val as $val) {
                $objActSheet->setCellValueExplicit($k . $i, $val, PHPExcel_Cell_DataType::TYPE_STRING);
                $k++;
            }
            $i++;
        }
    $objExcel->setActiveSheetIndex(2); //切换到新创建的工作表
    $objActSheet = $objExcel->getActiveSheet();
    $i           = 8;
    if ($data_energy)
        foreach ($data_energy as $_val) {
            $k = "C";
            foreach ($_val as $val) {
                $objActSheet->setCellValueExplicit($k . $i, $val, PHPExcel_Cell_DataType::TYPE_STRING);
                $k++;
            }
            $i++;
        }
    $objExcel->setActiveSheetIndex(0);
    $objWriter = PHPExcel_IOFactory::createWriter($objExcel, 'Excel5');
    $savedir   = "data/report_data/" . date("Y-m-d") . "-iso.xls";
    $objWriter->save($savedir);
    return $savedir;
}
$report_type = getgp('report_type');
// p($report_type);die;
//变更信息
if ($report_type == '1' || $report_type == '4') {
    $pids = $_pids = $e_pids = array();
    $sql  = "SELECT z.cert_name,z.status,z.s_date,z.e_date,z.cert_scope,z.main_certno ,z.first_date ,z.change_date,z.certno,z.cert_addr,z.mark,z.is_change,z.old_certno,z.old_cert_name,z.change_type,z.cert_name_e,
c.cg_type_report,c.pass_date,c.cgs_date,c.cge_date,c.cg_reason,c.cg_pid as pid,
e.ep_name,e.ep_name_e,e.work_code,e.statecode,e.cta_addrcode,e.xvalue,e.yvalue,e.areacode,e.ctfrom,e.ep_oldname,e.eid,e.ep_amount,e.capital,e.currency,e.nature,e.delegate,e.industry,e.union_count,e.ep_addr,e.ep_phone,e.ep_fax,
p.iso,p.audit_type,p.audit_ver,p.audit_code,p.ct_id,p.cti_id,p.ct_code,p.st_num,p.tid,p.comment_a_name,p.comment_b_name,p.sp_date
FROM `sp_certificate_change` c LEFT JOIN sp_certificate z on z.id=c.zsid LEFT JOIN sp_enterprises e on e.eid=z.eid left join sp_project p on p.id=c.cg_pid
WHERE c.deleted='0' and c.cgs_date >= '$begindate' and c.cgs_date <= '$enddate' AND c.zsid<>0 AND p.iso_prod_type=0  ORDER BY c.id DESC ";
    // AND  cg_type_report!='0197'  
    $res  = $db->query($sql);
    while ($row = $db->fetch_array($res)) {
        $pids[] = $row[pid];
        if ($row['iso'] == 'A09') {
            $e_pids[] = $row[pid];
        }
        $cti_info        = $db->get_row("SELECT total,renum,risk_level,base_num from sp_contract_item where cti_id='$row[cti_id]' ");
        $task_info       = $db->get_row("SELECT tb_date,te_date,tk_num,wsqs_date from sp_task where id='$row[tid]' and deleted=0");
        $cp_info         = $db->get_row("SELECT sp_date from sp_project where cti_id='$row[cti_id]' AND sp_date != '' ORDER BY id DESC");
        $arr             = array();
        $arr[1]          = $a1;
        $arr[2]          = $a2;
        $arr[3]          = $row['cert_name']; //
        $arr[4]          = $row['cert_name_e']; //组织机构英文名称
        $arr[5]          = $row['ep_oldname']; //组织机构原名称
        $arr[6]          = $row['work_code']; //6.组织机构代码
        $row['industry'] = str_replace(array(
            '、',
            ';'
        ), '；', $row['industry']);
        $arr[7]          = trim($row['industry'], "；"); //8.所属行业
        $arr[8]          = $row['statecode']; //10.组织机构所在地方代码
        $arr[9]          = $row['areacode']; //9.组织机构所在地区代码
        $arr[10]         = $row['ep_addr']; //11.证书地址
        $arr[11]         = $row['cta_addrcode']; //12.组织邮政编码
        $arr[12]         = $row['ep_phone']; //13.组织联系电话
        $arr[13]         = $row["ep_fax"]; //14.组织联系传真
        $arr[14]         = $row['delegate']; //15.组织法定代表人
        $arr[15]         = $row['nature']; //16.组织性质代码
        $arr[16]         = sprintf("%.4f", $row['capital']); //17.组织注册资本
        $arr[17]         = $row['currency']; //18.组织注册资本币种
        $arr[18]         = $row['ep_amount']; //19.组织组织人数
        $arr[19]         = $cti_info['total']; //20.体系人数
        $arr[20]         = (!$row['first_date'] || $row['first_date'] == '0000-00-00') ? $row[s_date] : $row['first_date']; //21.初次获证日期
        $arr[21]         = $row['certno']; //22.证书号码
        $arr[22]         = $row['audit_ver']; //26.认证项目代码
        $arr[23]         = get_code($row['audit_code']);
        $arr[24]         = $audit_ver_array[$row[audit_ver]][audit_basis]; //24、认证依据
        // @lyh 2016-07-05
        $arr[25]         = "0"; //$row[union_count]>0?"1":"0"; //25.是否多现场   
        $arr[26]         = $row['cert_addr']; //26.多现场名称地址多个用全角分号分隔。上报生产地址
        //if($arr[25]=='1'){
        //	$site_addr=$db->get_col("SELECT es_addr FROM `sp_enterprises_site` WHERE `eid` = $row[eid] AND `deleted` = '0' ");
        //	$arr[26] .= "；";
        //	$arr[26] .= join("；",$site_addr);
        //}
        $arr[27]         = $row['cert_scope']; //27.认证覆盖范围
        $arr[28]         = ''; //28、EC9000证书对应的QMS覆盖范围
        if ($row[audit_ver] == "A010201")
            $arr[28] = $row['cert_scope'];
        $arr[29] = ''; //29、获证组织能源管理体系边界
        if (strpos($row[audit_ver], "A09") !== false) {
            $arr[29] = $db->meta($row[eid], "energy_e", "", "enterprise");
        }
        $arr[30] = '05'; //30、认证审核活动代码 变更 05
        $arr[31] = $cti_info[renum]; //31.再认证次数
        $arr[32] = get_audit_num($row[audit_type]); //32、监督次数
        $arr[33] = substr($task_info['wsqs_date'], 0, 10); //33.审核开始日期
        $arr[34] = substr($task_info['te_date'], 0, 10); //34.审核结束日期
        $arr[35] = $task_info[tk_num]; //35、审核人日数 $row[st_num]
        $arr[36] = "01"; //36、结合审核类型
        $cti_ids = $db->get_col("SELECT cti_id FROM `sp_project` WHERE `tid` = '$row[tid]' AND tid != '' AND `deleted` = '0'");
        if (count(array_unique($cti_ids)) == 2)
            $arr[36] = "02";
        elseif (count(array_unique($cti_ids)) == 3)
            $arr[36] = "03";
        elseif (count(array_unique($cti_ids)) > 3)
            $arr[36] = "04";
        // $arr[37] = trim($row[comment_a_name]."；".$row[comment_b_name],'；'); //评定人员
        $arr[37] = "卢潮流";
        $arr[38] = $cp_info[sp_date]; //38、认证决定日期
        $arr[39] = $row['s_date']; //证书发证日期
        $arr[40] = $row['e_date']; //证书到期日期
        $arr[41] = $row['status']; //证书状态
        $arr[42] = ''; //暂停原因
        $arr[43] = ''; //暂停开始时间
        $arr[44] = ''; //暂停结束时间
        $arr[45] = ''; //撤销原因
        $arr[46] = ''; //撤销日期
        if ($row['status'] == '02') {
            $arr[42] = $row[cg_reason];
            $arr[43] = $row[cgs_date];
            $arr[44] = $row[cge_date];
        }
        if ($row['status'] == '03') {
            $arr[45] = $row[cg_reason];
            $arr[46] = $row[cgs_date];
        }
        $arr[47] = '0'; //47、是否是子证书
        $arr[48] = ''; //48、主认证证书号
        if ($row[main_certno]) {
            $arr[47] = '1';
            $arr[48] = $row[main_certno];
        }
        $arr[49] = $row['cgs_date']; //变更日期
        $arr[50] = $row[cg_type_report]; //50.变更类型代码
        $arr[51] = $row[is_change]; //51.shi否换证
        $arr[52] = ($row[change_date] == "0000-00-00") ? "" : $row[change_date];
        $arr[53] = $row[change_type]; //34.换证原因
        $arr[54] = $row[old_cert_name]; // 54、原颁证机构批准号
        $arr[55] = $row[old_certno]; // 55、原获认证的认证证书号
        if ($arr[51] == '0') {
            $arr[52] = $arr[53] = $arr[54] = $arr[55] = "";
        }
        $arr[56] = $iso_rkh[$row['iso']]; //56、证书使用的认可号
        if ($row[mark] != '01') {
            $arr[56] = "";
        }
        $arr[57] = $row[mark]; //57、证书使用的认可标志代码
        $arr[57] == '99' && $arr[57] = '';
        $arr[58] = ""; //风险系数 $cti_info[risk_level]
        // $cost_type = get_cost_type($row[audit_type]);
        // $arr[59] = $db->get_var("SELECT $cost_type FROM `sp_contract_cost` WHERE `cti_id` = '$row[cti_id]'   and deleted=0 ");	//收费金额
        $arr[59] = "0.00";
        $arr[60] = "01"; //60、实收的认证费用币种
        $arr[61] = $row[ct_code]; //收费发票号 (合同号$row[ct_code])
        $arr[62] = ""; //62、认证证书附件文件名
        $arr[63] = ""; //63、服务认证所属领域代码
        $arr[64] = "04"; //64、上报类型  变更 不换证 04
        if ($arr[51] == '1') {
            $arr[64] = "02"; //64、上报类型  变更 换证 02
        }
        $arr[65]    = ""; //65、备注
        $arr[66]    = "0"; //
        $arr[67]    = ""; //
        $arr[68]    = ""; //
        $arr[69]    = ""; //
        $arr[70]    = ""; //
        $arr[71]    = $cti_info['base_num']; //
        $arr[72]    = $a71; //取数开始时间
        $arr[73]    = $a72; //取数结束时间
        $arr[74]    = "变更"; // 变更
        $data_arr[] = $arr;
    }
}
//评定 （评定通过，监督类型,非换证(又取消 认监委的换证指的是证书号码变化)）
if ($report_type == '1' || $report_type == '3') {
    $sql = "SELECT 
e.ep_name,e.ep_name_e,e.work_code,e.statecode,e.cta_addrcode,e.xvalue,e.yvalue,e.areacode,e.ctfrom,e.ep_oldname,e.eid,e.ep_amount,e.capital,e.currency,e.nature,e.delegate,e.industry,e.union_count,e.ep_addr,e.ep_phone,e.ep_fax,
p.iso,p.audit_type,p.audit_ver,p.ct_id,p.cti_id,p.ct_code,p.st_num,p.tid,p.comment_a_name,p.comment_b_name,p.sp_date,p.audit_code,p.iso,p.scope,p.id as pid
FROM sp_project p LEFT JOIN sp_enterprises e on e.eid=p.eid  WHERE p.deleted='0' and p.tid !='0' and p.pd_type='1' and p.audit_type NOT IN ('1003','1007','1001','1002') and p.sp_date >= '$begindate' and p.sp_date <= '$enddate'  AND p.iso_prod_type=0    ORDER BY p.sp_date DESC";
    $res = $db->query($sql);
    while ($row = $db->fetch_array($res)) {
        $pids[]    = $row['pid'];
        $cti_info  = $db->get_row("SELECT total,renum,risk_level,base_num from sp_contract_item where cti_id='$row[cti_id]' ");
        $task_info = $db->get_row("SELECT t.tb_date,t.te_date,t.tk_num,t.wsqs_date from sp_task t  where t.id='$row[tid]' and deleted=0");
        $cert_info = $db->get_row("SELECT * FROM `sp_certificate` WHERE `cti_id` = '$row[cti_id]' AND `eid` = '$row[eid]' AND `deleted` = '0' and status in('01','02') ORDER BY e_date desc");
        // $arr[2] = $row['ep_name']; 		//组织机构名称
        if (!$cert_info)
            continue;
        $arr             = array();
        $arr[1]          = $a1;
        $arr[2]          = $a2;
        $arr[3]          = $cert_info['cert_name']; //
        $arr[4]          = $cert_info['cert_name_e']; //组织机构英文名称
        $arr[5]          = $row['ep_oldname']; //组织机构原名称
        $arr[6]          = $row['work_code']; //6.组织机构代码
        $row['industry'] = str_replace(array(
            '、',
            ';'
        ), '；', $row['industry']);
        $arr[7]          = rtrim($row['industry'], "；"); //8.所属行业
        $arr[8]          = $row['statecode']; //10.组织机构所在地方代码
        $arr[9]          = $row['areacode']; //9.组织机构所在地区代码
        $arr[10]         = $row['ep_addr']; //11.证书地址
        $arr[11]         = $row['cta_addrcode']; //12.组织邮政编码
        $arr[12]         = $row['ep_phone']; //13.组织联系电话
        $arr[13]         = $row['ep_fax']; //14.组织联系传真
        $arr[14]         = $row['delegate']; //15.组织法定代表人
        $arr[15]         = $row['nature']; //16.组织性质代码
        $arr[16]         = sprintf("%.4f", $row['capital']);
        ; //17.组织注册资本
        $arr[17] = $row['currency']; //18.组织注册资本币种
        $arr[18] = $row['ep_amount']; //19.组织组织人数
        $arr[19] = $cti_info['total']; //20.体系人数
        $arr[20] = (!$cert_info['first_date'] || $cert_info['first_date'] == '0000-00-00') ? $cert_info['s_date'] : $cert_info['first_date']; //21.初次获证日期
        $arr[21] = $cert_info['certno']; //22.证书号码
        $arr[22] = $row['audit_ver']; //26.认证项目代码
        $arr[23] = get_code($row['audit_code']);
        $arr[24] = $audit_ver_array[$row[audit_ver]][audit_basis]; //24、认证依据
        //@lyh 2016-07-05
        $arr[25] = "0"; //$row[union_count]>0?"1":"0"; 					//25.是否多现场
        $arr[26] = $cert_info['cert_addr']; //ZZ项目  10与26列 换了，25.多现场名称地址多个用全角分号分隔。
        //if($arr[25]=='1'){
        //	$site_addr=$db->get_col("SELECT es_addr FROM `sp_enterprises_site` WHERE `eid` = '2' AND `deleted` = '0' ");
        //	$arr[26] .= "；";
        //	$arr[26] .= join("；",$site_addr);
        //}
        $arr[27] = $row['scope']; //27.认证覆盖范围
        $arr[28] = ''; //28、EC9000证书对应的QMS覆盖范围
        if ($row[audit_ver] == "A010201")
            $arr[28] = $row['scope'];
        $arr[29] = ''; //29、获证组织能源管理体系边界
        if (strpos($row[audit_ver], "A09") !== false) {
            $arr[29] = $db->meta($row[eid], "energy_e", "", "enterprise");
        }
        $arr[30] = get_audit_type($row['audit_type']); //30、认证审核活动代码 变更 04
        $arr[31] = $cti_info[renum]; //31.再认证次数
        $arr[32] = get_audit_num($row[audit_type]); //32、监督次数
        $arr[33] = substr($task_info['tb_date'], 0, 10); //33.审核开始日期   //开始时间取文审时间
        $arr[34] = substr($task_info['te_date'], 0, 10); //34.审核结束日期
        $arr[35] = "";
        $arr[35] = $task_info[tk_num]; //35、审核人日数 $row[st_num]
        $arr[36] = "01"; //36、结合审核类型
        $cti_ids = $db->get_col("SELECT cti_id FROM `sp_project` WHERE `tid` = '$row[tid]' AND `deleted` = '0'");
        if (count(array_unique($cti_ids)) == 2)
            $arr[36] = "02";
        elseif (count(array_unique($cti_ids)) == 3)
            $arr[36] = "03";
        elseif (count(array_unique($cti_ids)) > 3)
            $arr[36] = "04";
        $arr[37] = trim($row[comment_a_name] . "；" . $row[comment_b_name], '；'); //评定人员
        $arr[38] = $row[sp_date]; //38、认证决定日期
        $arr[39] = $cert_info['s_date']; //
        $arr[40] = $cert_info['e_date']; //证书到期日期
        $arr[41] = $cert_info['status']; //证书状态
        $arr[42] = ''; //暂停原因
        $arr[43] = ''; //暂停开始时间
        $arr[44] = ''; //暂停结束时间
        $arr[45] = ''; //撤销原因
        $arr[46] = ''; //撤销日期
        $arr[47] = '0'; //47、是否是子证书
        $arr[48] = ''; //48、主认证证书号
        if ($cert_info[main_certno]) {
            $arr[47] = '1';
            $arr[48] = $cert_info[main_certno];
        }
        $arr[49] = ""; //变更日期
        $arr[50] = ""; //31.变更类型代码
        $arr[51] = $cert_info[is_change]; //33.shi否换证
        $arr[52] = ($cert_info[change_date] == "0000-00-00") ? "" : $cert_info[change_date];
        $arr[53] = $cert_info[change_type]; //34.换证原因
        $arr[54] = $row[old_cert_name]; // 54、原颁证机构批准号
        $arr[55] = $row[old_certno]; // 55、原获认证的认证证书号
        if ($arr[51] == '0') {
            $arr[52] = $arr[53] = $arr[54] = $arr[55] = "";
        }
        $arr[56] = $iso_rkh[$row['iso']]; //56、证书使用的认可号
        // switch($row['iso']){
        // 	case 'A01':
        // 	   $arr[56] = 'CNAS C014-Q';
        // 	   break;
        // 	case 'A02':
        // 	   $arr[56] = 'CNAS C014-E';
        // 	   break;
        // 	case 'A03':
        // 	   $arr[56] = 'CNAS C014-S';
        // 	   break;
        // }
        $arr[57] = $cert_info[mark]; //57、证书使用的认可标志代码
        $arr[57] == '99' && $arr[57] = '';
        $arr[58] = ""; //风险系数 $cti_info[risk_level]
        // $cost_type = get_cost_type($row[audit_type]);
        if ($row[audit_type] == '1004-1') {
            $cost_type = '1002';
        } elseif ($row[audit_type] == '1004-2') {
            $cost_type = '1003';
        }
        $arr[59] = $db->get_var("SELECT cost FROM `sp_contract_cost` WHERE `ct_id` = '$row[ct_id]' AND cost_type = '$cost_type'  and deleted=0 "); //收费金额
        $arr[59] = sprintf("%.2f", $arr[59]);
        $arr[60] = "01"; //收费币种
        $arr[61] = $row[ct_code]; //收费发票号
        $arr[62] = ""; //62、认证证书附件文件名
        $arr[63] = ""; //63、服务认证所属领域代码
        $arr[64] = "04"; //64、上报类型  变更 不换证 04
        if ($arr[51] == '1') {
            $arr[64] = "02"; //64、上报类型  变更 换证 02
        }
        $arr[65]    = ""; //65、备注
        $arr[66]    = "0"; //
        $arr[67]    = ""; //
        $arr[68]    = ""; //
        $arr[69]    = ""; //
        $arr[70]    = ""; //
        $arr[71]    = $cti_info['base_num']; //
        $arr[72]    = $a71; //取数开始时间
        $arr[73]    = $a72; //取数结束时间
        $arr[74]    = "监督";
        $data_arr[] = $arr;
    }
    //审核组信息
    $_pids = array_unique($pids);
    $pids  = array(
        -1
    );
    foreach ($_pids as $v) {
        if ($v)
            $pids[] = $v;
    }
    $auditors = array();
    $fields   = "tat.iso,tat.pid,tat.uid,tat.audit_type,tat.qua_type,tat.role,tat.witness,tat.taskBeginDate,tat.taskEndDate,tat.name,tat.use_code,";
    $fields .= "p.cti_id,p.eid,";
    //$fields .= "ta.taskBeginDate,ta.taskEndDate,ta.name,";
    $fields .= "hr.card_type,hr.card_no,hr.audit_job";
    //$fields .= "hqa.qua_no";
    $join = " LEFT JOIN sp_hr hr ON hr.id = tat.uid";
    $join .= " LEFT JOIN sp_project p ON p.id = tat.pid";
    $where = " AND tat.pid IN (" . implode(',', $pids) . ")    AND tat.deleted = 0";
    $sql   = "SELECT $fields FROM sp_task_audit_team tat $join WHERE 1 $where";
    $query = $db->query($sql);
    while ($rt = $db->fetch_array($query)) {
        //$a12=0;
        //if($rt[qua_type]!='03' and $rt[use_code])
        //	$a12=1;
        $a14 = "";
        if ($rt[witness])
            $a14 = "01";
        $_query = $db->query("SELECT witness_person FROM `sp_task_audit_team` WHERE `tid` = '$rt[tid]' AND `witness_person` IS NOT NULL AND `witness_person` <> '' AND deleted=0");
        while ($r = $db->fetch_array($_query)) {
            if (strpos($r[witness_person], $rt[name]))
                $a14 = "02";
        }
        unset($_query, $r);
        $cert_query = $db->query("SELECT certno FROM sp_certificate WHERE cti_id = $rt[cti_id] AND  eid='$rt[eid]' and deleted=0 AND status IN ('01','02')");
        while ($_r1 = $db->fetch_array($cert_query)) {
            $auditor = array(
                '1' => $a1,
                '2' => $_r1[certno],
                '3' => get_audit_type_auditor($rt[audit_type]),
                '4' => $rt['taskBeginDate'],
                '5' => $rt['taskEndDate'],
                '6' => $rt['name'],
                '7' => $rt['card_type'],
                '8' => $rt['card_no'],
                '9' => substr($rt[role], -2),
                '10' => $rt[qua_type],
                '11' => $db->get_var("SELECT qua_no FROM `sp_hr_qualification` WHERE `uid` = '$rt[uid]' AND `iso` = '$rt[iso]' AND status=1"),
                '12' => '1', //$a12 ,
                '13' => (1 == $rt['audit_job'] ? 1 : 0),
                '14' => $a14
            );
            if ($rt['role'] == '1004')
                $auditor[9] = '';
            if ($auditor[11])
                $auditors[] = $auditor;
        }
        unset($cert_query, $_r1);
    }
} // 监督 最上面的if括号
//exit;
// 新发证书 （）
if ($report_type == '1' || $report_type == '2') {
    $pids = array();
    $sql  = "SELECT c.*,
e.ep_name,e.ep_name_e,e.work_code,e.statecode,e.cta_addrcode,e.xvalue,e.yvalue,e.areacode,e.ctfrom,e.ep_oldname,e.eid,e.ep_amount,e.capital,e.currency,e.nature,e.delegate,e.industry,e.union_count,e.ep_addr,e.ep_phone,e.ep_fax,
p.id as pid,p.audit_code,p.audit_type,ct.is_site,p.st_num,p.comment_a_name,p.comment_b_name,p.sp_date,p.tid
 FROM sp_certificate c LEFT JOIN sp_project p ON c.pid=p.id   LEFT JOIN sp_enterprises e ON e.eid=c.eid LEFT JOIN sp_contract ct ON ct.ct_id = c.ct_id  WHERE c.s_date>='$begindate' AND c.s_date<='$enddate'  AND  p.deleted=0 AND c.iso_prod_type=0"; //p.ifchangecert='2' AND
    $res  = $db->query($sql);
    while ($row = $db->fetch_array($res)) {
        if ($row[audit_type] == '1003') {
            $p_info1 = $db->get_row("SELECT id,tid,st_num FROM `sp_project` WHERE `cti_id` = '$row[cti_id]' AND `audit_type` ='1002' and deleted=0 ");
            $tb_date = $db->get_var("SELECT tb_date from sp_task where id='$p_info1[tid]' ");
            $te_date = $db->get_var("SELECT te_date from sp_task where id='$row[tid]' ");
            $pids[]  = $p_info1[id];
            if ($row['is_site'] == '1') {
                $st_num = $p_info1['st_num'] + $row['st_num'];
            } else {
                $st_num = $row['st_num'];
            }
        } else {
            $task_info = $db->get_row("SELECT tb_date,te_date from sp_task where id='$row[tid]' ");
            $tb_date   = $task_info[tb_date];
            $te_date   = $task_info[te_date];
            $st_num    = $row['st_num'];
        }
        $pids[] = $row[pid];
        if ($row['iso'] == 'A09') {
            $e_pids[] = $row[pid];
        }
        $cti_info        = $db->get_row("SELECT total,renum,risk_level,base_num from sp_contract_item where cti_id='$row[cti_id]' ");
        $arr             = array();
        $arr[1]          = $a1;
        $arr[2]          = $a2;
        $arr[3]          = $row['cert_name']; //
        $arr[4]          = $row['cert_name_e']; //组织机构英文名称
        $arr[5]          = $row['ep_oldname']; //组织机构原名称
        $arr[6]          = $row['work_code']; //6.组织机构代码
        $row['industry'] = str_replace(array(
            '、',
            ';'
        ), '；', $row['industry']);
        $arr[7]          = rtrim($row['industry'], "；"); //8.所属行业
        $arr[8]          = $row['statecode']; //10.组织机构所在地方代码
        $arr[9]          = $row['areacode']; //9.组织机构所在地区代码
        $row[ep_addr]  = str_replace(array(
            '、',
            ';'
        ), '；', $row[ep_addr]);
        $arr[10]         = $row['ep_addr']; //11.证书地址
        $arr[11]         = $row['cta_addrcode']; //12.组织邮政编码
        $arr[12]         = $row['ep_phone']; //13.组织联系电话
        $arr[13]         = $row['ep_fax']; //14.组织联系传真
        $arr[14]         = $row['delegate']; //15.组织法定代表人
        $arr[15]         = $row['nature']; //16.组织性质代码
        $arr[16]         = sprintf("%.4f", $row['capital']); //17.组织注册资本
        $arr[17]         = $row['currency']; //18.组织注册资本币种
        $arr[18]         = $row['ep_amount']; //19.组织组织人数
        $arr[19]         = $cti_info['total']; //20.体系人数
        $arr[20]         = (!$row['first_date'] || $row['first_date'] == '0000-00-00') ? $row[s_date] : $row['first_date']; //21.初次获证日期
        $arr[21]         = $row['certno']; //22.证书号码
        $arr[22]         = $row['audit_ver']; //26.认证项目代码
        $arr[23]         = get_code($row['audit_code']);
        $arr[24]         = $audit_ver_array[$row[audit_ver]][audit_basis]; //24、认证依据
        $arr[25]         = "0"; //$row[union_count]>0?"1":"0"; 					//25.是否多现场
        $arr[26]         = $row['cert_addr']; //25.多现场名称地址多个用全角分号分隔。
        //if($arr[25]=='1'){
        //	$site_addr=$db->get_col("SELECT es_addr FROM `sp_enterprises_site` WHERE `eid` = '2' AND `deleted` = '0' ");
        //	$arr[26] .= "；";
        //	$arr[26] .= join("；",$site_addr);
        //}
        $arr[27]         = $row['cert_scope']; //27.认证覆盖范围
        $arr[28]         = ''; //28、EC9000证书对应的QMS覆盖范围
        if ($row[audit_ver] == "A010201")
            $arr[28] = $row['cert_scope'];
        $arr[29] = ''; //29、获证组织能源管理体系边界
        if (strpos($row[audit_ver], "A09") !== false) {
            $arr[29] = $db->meta($row[eid], "energy_e", "", "enterprise");
        }
        $arr[30] = get_audit_type($row['audit_type']); //30、认证审核活动代码 变更 04
        $arr[31] = $cti_info[renum]; //31.再认证次数
        $arr[32] = get_audit_num($row[audit_type]); //32、监督次数
        $arr[33] = substr($tb_date, 0, 10); //33.审核开始日期
        $arr[34] = substr($te_date, 0, 10); //34.审核结束日期
        $arr[35] = "";
        $arr[35] = $st_num; //35、审核人日数 $row[st_num]
        $arr[36] = "01"; //36、结合审核类型
        $cti_ids = $db->get_col("SELECT cti_id FROM `sp_project` WHERE `tid` = '$row[tid]' AND `deleted` = '0'");
        if (count(array_unique($cti_ids)) == 2)
            $arr[36] = "02";
        if (count(array_unique($cti_ids)) == 3)
            $arr[36] = "03";
        if (count(array_unique($cti_ids)) > 3)
            $arr[36] = "04";
        $arr[37] = trim($row[comment_a_name] . "；" . $row[comment_b_name], '；'); //评定人员
        $arr[38] = $row[sp_date]; //38、认证决定日期
        $arr[39] = $row['s_date']; //证书发证日期
        $arr[40] = $row['e_date']; //证书到期日期
        $arr[41] = $row['status']; //证书状态
        $arr[42] = ''; //暂停原因
        $arr[43] = ''; //暂停开始时间
        $arr[44] = ''; //暂停结束时间
        $arr[45] = ''; //撤销原因
        $arr[46] = ''; //撤销日期
        $arr[47] = '0'; //47、是否是子证书
        $arr[48] = ''; //48、主认证证书号
        if ($row[main_certno]) {
            $arr[47] = '1';
            $arr[48] = $row[main_certno];
        }
        $arr[49] = ""; //变更日期
        $arr[50] = ""; //31.变更类型代码
        $arr[51] = $row[is_change]; //33.shi否换证
        $arr[52] = $row[change_date];
        $arr[53] = $row[change_type]; //34.换证原因
        $arr[54] = $row[old_cert_name]; // 54、原颁证机构批准号
        $arr[55] = $row[old_certno]; // 55、原获认证的认证证书号
        if (!$row[is_change]) {
            $arr[51] = '0';
            $arr[52] = $arr[53] = $arr[54] = $arr[55] = '';
        }
        $arr[56] = $iso_rkh[$row['iso']]; //56、证书使用的认可号
        $arr[57] = $row[mark]; //57、证书使用的认可标志代码
        $arr[57] == '99' && $arr[57] = '';
        $arr[58] = ""; //风险系数 $cti_info[risk_level]
        // $cost_type = get_cost_type($row[audit_type]);
        $arr[59] = $db->get_var("SELECT cost FROM `sp_contract_cost` WHERE `ct_id` = '$row[ct_id]' AND cost_type = '1001'  and deleted=0 "); //收费金额 初审再认证收费就是1001
        $arr[59] = sprintf("%.2f", $arr[59]);
        $arr[60] = "01"; //收费币种
        $arr[61] = $row[ct_code]; //收费发票号
        $arr[62] = ""; //62、认证证书附件文件名
        $arr[63] = ""; //63、服务认证所属领域代码
        $arr[64] = "01"; //64、上报类型  变更 不换证 04
        if ($arr[51] == '1') {
            $arr[64] = "02"; //64、上报类型  变更 换证 02
        }
        $arr[65]    = ""; //65、备注
        $arr[66]    = "0"; //
        $arr[67]    = ""; //
        $arr[68]    = ""; //
        $arr[69]    = ""; //
        $arr[70]    = ""; //
        $arr[71]    = $cti_info['base_num']; //
        $arr[72]    = $a71; //取数开始时间
        $arr[73]    = $a72; //取数结束时间
        $arr[74]    = "新发证";
        $data_arr[] = $arr;
    }
    // do_excel($data_arr,"","1234");
    // p($data_arr);
    // exit;
    //审核组信息
    $_pids = array_unique($pids);
    $pids  = array(
        -1
    );
    foreach ($_pids as $v) {
        if ($v)
            $pids[] = $v;
    }
    $fields = "tat.iso,tat.pid,tat.uid,tat.audit_type,tat.qua_type,tat.role,tat.witness,tat.taskBeginDate,tat.taskEndDate,tat.name,tat.use_code,";
    $fields .= "p.cti_id,p.eid,";
    //$fields .= "ta.taskBeginDate,ta.taskEndDate,ta.name,";
    $fields .= "hr.card_type,hr.card_no,hr.audit_job";
    //$fields .= "hqa.qua_no";
    $join = " LEFT JOIN sp_hr hr ON hr.id = tat.uid";
    $join .= " LEFT JOIN sp_project p ON p.id = tat.pid";
    $where = " AND tat.pid IN (" . implode(',', $pids) . ")    AND tat.deleted = 0";
    $sql   = "SELECT $fields FROM sp_task_audit_team tat $join WHERE 1 $where";
    $query = $db->query($sql);
    while ($rt = $db->fetch_array($query)) {
        // $a12=0;
        // if($rt[qua_type]!='03' and $rt[use_code])
        // 	$a12=1;
        $a14        = "";
        // if($rt[witness])
        // 	$a14="01";
        // $_query=$db->query("SELECT witness_person FROM `sp_task_audit_team` WHERE `tid` = '$rt[tid]' AND `witness_person` IS NOT NULL AND `witness_person` <> '' AND deleted=0");
        // while($r=$db->fetch_array( $_query )){
        // 	if(strpos($r[witness_person],$rt[name]))
        // 		$a14="02";
        // }
        // unset($_query,$r);
        $cert_query = $db->query("SELECT certno FROM sp_certificate WHERE cti_id = $rt[cti_id] AND  eid='$rt[eid]' and deleted=0");
        while ($_r1 = $db->fetch_array($cert_query)) {
            $auditor = array(
                '1' => $arr['1'],
                '2' => $_r1[certno],
                '3' => get_audit_type_auditor($rt[audit_type]),
                '4' => $rt['taskBeginDate'],
                '5' => $rt['taskEndDate'],
                '6' => $rt['name'],
                '7' => $rt['card_type'],
                '8' => $rt['card_no'],
                '9' => substr($rt[role], -2),
                '10' => $rt[qua_type],
                '11' => $db->get_var("SELECT qua_no FROM `sp_hr_qualification` WHERE `uid` = '$rt[uid]' AND `iso` = '$rt[iso]' AND status=1"),
                '12' => '1', //$a12 ,
                '13' => (1 == $rt['audit_job'] ? 1 : 0),
                '14' => "----" //$a14
            );
            if ($rt['role'] == '1004')
                $auditor[9] = '';
            if ($auditor[11])
                $auditors[] = $auditor;
        }
        unset($cert_query, $_r1);
    }
} // 新发证 上面的if
// //能源绩效信息表
// $energy = array();
// if(empty($e_pids)) $e_pids = array(-1);
// $query=$db->query("SELECT * FROM `sp_energy` WHERE `pid` IN(".join(",",$e_pids).")");
// while($_row=$db->fetch_array($query)){
// 	$p_info=$db->get_row("SELECT cti_id,eid FROM `sp_project` WHERE id='$_row[pid]'");
// 	$certno=$db->get_var("SELECT certno FROM `sp_certificate` WHERE `cti_id` = '$p_info[cti_id]' AND `eid` = '$p_info[eid]' AND `deleted` = '0' ORDER BY `e_date` DESC ");
// 	$_row[id]=$arr[1];
// 	$_row[pid]=$certno;
// 	$energy[]=$_row;
// }
//输出Execl文件
/**/
// p($auditors);
echo do_excel($data_arr, $auditors, $energy, "获证组织基本信息表");
exit;
?>
