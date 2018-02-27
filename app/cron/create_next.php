<?php
/*
 *---------------------------------------------------------------
 * 自动生成下一个审核阶段
 *---------------------------------------------------------------
 */
set_time_limit(0);
// 半年前的时间段
$month_6 = thedate_add(date('Y-m-d H:i:s'), -2, 'month');
// 生成全部的
// 日志文件
$log_file = APP_DIR . "cron/log/create_next-" . date('Y-m') . '.log';
// 本次生成记录数
$log_num  = 0;
// 下一阶段函数
function get_next_type($type = '')
{
    $result = '';
    if($type == '1003' || $type == '1007')
	{
		$result = "1004-1";
	}else
	{
		$num = strrchr($type,"-");
		$num = abs($num);
		$num +=1;
		$result = "1004-".$num;
		
	}
    return $result;
}

/*
 *---------------------------------------------------------------
 * 下一轮监督
 *---------------------------------------------------------------
 */
$audit      = load('audit');
// 取原二阶段、监一未生成的数据
$allow_type = array(
    "1003",
    "1004-1",
	"1007"
);
//array_pop($audit_type);
$join  = " LEFT JOIN sp_task t ON p.tid = t.id";
$sql   = "SELECT p.*, t.te_date FROM sp_project p $join WHERE p.tid!=0 AND p.deleted=0 AND p.audit_type  IN ('" . implode("','", $allow_type) . "') AND t.te_date <= '$month_6' AND p.create_next=0 AND p.pd_type = 1";
$query = $db->query($sql);
while ($rt = $db->fetch_array($query)) {
	// 取证书信息
    $zs_info = $db->get_row("SELECT * FROM `sp_certificate` WHERE `cti_id` = '$rt[cti_id]' AND `deleted` = '0' and eid='$rt[eid]' and status IN ('01','02') ORDER BY e_date desc");
    if (!$zs_info[id])
        continue;
    $_audit_type = get_next_type($rt[audit_type]);
    //if(!$$_audit_type)continue;
    $sql         = "SELECT id FROM sp_project WHERE cti_id = '$rt[cti_id]' AND audit_type = '$_audit_type' AND deleted = 0";
    //如果下一阶段数据已经存在则更新状态
    if ($db->get_var($sql)) {
        $audit->edit($rt['id'], array(
            'create_next' => 1
        ));
        //如果下一阶段数据不存在则生成
    } else { 
		
		$pre_date=thedate_add($rt['te_date'],12,'month');
		$final_date=thedate_add($rt['sp_date'],12,'month');
		
		/**********************************end*****************************************/
        $new      = array(
            'eid' => $rt['eid'],
            // 'ep_manu_id' => $rt['eid'],
            // 'ep_prod_id' => $rt['eid'],
            'ct_id' => $rt['ct_id'], // 合同ID
            'ct_code' => $rt['ct_code'], // 
            'cti_id' => $rt['cti_id'], // 合同项目 
            'cti_code' => $rt['cti_code'], // 
            'ctfrom' => $rt['ctfrom'], // 合同来源
            'st_num' => $rt['st_num'], // 取监督现场人日
            'iso' => $rt['iso'], // 体系
            'total' => $rt['total'], // 体系人数
            'audit_ver' => $zs_info['audit_ver'], // 标准版本
            'audit_code' => $rt['audit_code'], // 审核代码
            'use_code' => $rt['use_code'], // 使用代码
            'mark' => $zs_info['mark'], // 
            'scope' => $zs_info[cert_scope], // 
            'pre_date' => $pre_date, //预审日期
            'final_date' => $final_date, // 最后监审日
            'audit_type' => $_audit_type, // 认证类型
            'iso_prod_type' => 0, //
            'status' => 5 //监督维护
        );
        //生成下一阶段
        $audit->add(magic_gpc($new, 1)) && $log_num++;
        // 更新状态
        $audit->edit($rt['id'], array(
            'create_next' => 1
        ));
    }
}
// 日志是同名文件 .txt 含执行时间、生成记录数
$file_res = fopen($log_file, "a");
fwrite($file_res, date('Y-m-d H:i:s') . "		$log_num 监督" . "\r\n"); //写入一行 \n为换行
fclose($file_res);
