<?php
!defined('IN_SUPU') && exit('Forbidden');
// 劳务费导出
$s_date = getgp("s_date");
$e_date = getgp("e_date");
$num    = 1;
$data   = array(
    array(
        "序号",
        "审查类别",
        "工厂检查任务编号",
        "工厂名称",
        "人员",
        "组内身份",
        "实际审厂开始日期",
        "实际审厂完成日期",
        "个人人日数",
        "总人日数",
        "劳务费",
        "类型",
    )
    
);

$num_person = "SELECT COUNT(*) AS total,tid FROM `sp_task_audit_team` WHERE  `iso_prod_type` = '1' AND `deleted` = '0' GROUP BY tid";
$query2     = $db->query($num_person);
$arr        = array();
while ($row = $db->fetch_array($query2)) {
    $arr[$row[tid]] = $row['total'];
    
}

$sql = "SELECT team.name,team.role,team.tid,task_code,task.eid,task.audit_type,tk_num,task.tb_date,task.te_date FROM sp_task_audit_team team LEFT JOIN sp_task task on team.tid=task.id WHERE task.tb_date>='$s_date' AND task.te_date<'$e_date' AND team.`iso_prod_type` = '1' AND team.`deleted` = '0'";

$query = $db->query($sql);



while ($row = $db->fetch_array($query)) {
    $row['audit_type'] = read_cache('audit_type', $row['audit_type']);
    
    $en             = $db->getField("enterprises", "ep_name", array(
        "eid" => $row['eid']
    ));
    $row['ep_name'] = $en;
    $per            = $row['tk_num'] / $arr[$row[tid]];
    $fee            = $row['role'] == '1001' ? 220 * $per + 80 : 220 * $per;
    $fee            = round($fee, 2);
    $row['role']    = $row["role"] == '1001' ? '组长' : '组员';
	$row['is_verify'] = $row['is_verify']?'现场验证':'正常审核';
    $data[]         = array(
        "$num",
        "$row[audit_type]",
        "$row[task_code]",
        "$row[ep_name]",
        "$row[name]",
        "$row[role]",
        "$row[tb_date]",
        "$row[te_date]",
        "$per",
        "$row[tk_num]",
        "$fee",
		"$row[is_verify]"
        
        
    );
    $num++;
    
}

export_excel($data, "劳务费");
