<?php
//检查结果统计表
$data    = array();
$s_date=$_POST['s_date'];
$e_date=$_POST['e_date'];
$num  = 1;

$data = array(
    array(
        '序号',
        '工厂检查任务编号',
        '产品类别',
        '工厂检查开始日期',
        '工厂检查结束日期',
        '生产企业名称',
        '检查类型',
        '工厂检查结论',
        '不符合描述'
    )
    
);


$sql = "SELECT tb_date,te_date,task_code,id,check_result,sp_task.audit_type,s.ep_name FROM sp_task LEFT JOIN sp_enterprises s ON sp_task.eid=s.eid WHERE tb_date>='$s_date' AND tb_date<='$e_date' AND sp_task.deleted=0 AND sp_task.iso_prod_type=1";

$query = $db->query($sql);

while ($row = $db->fetch_array($query)) {
    
    $project_id = $db->getCol("project", 'prod_id', array(
        'tid' => $row['id']
    ));
    if (!empty($project_id)) {
        $project = join('；', $project_id);
    }
    

    $sql2 = "SELECT clause,note FROM  sp_task_note WHERE tid=$row[id]";
    
    $query2 = $db->query($sql2);
    
    $clause = $note = array();
    

    while ($row2 = $db->fetch_array($query2)) {
        
        if ($row2['clause']) {
            $clause[] = $row2['clause'];
        }
        if ($row2['note'])
            $note[] = $row2['note'];

    }
    

    if (!empty($note)) {
        $n1 = implode("/", $note);
        
    }
    if (!empty($clause)) {
        $n2 = implode("/", $clause);
    }
    
    $row['audit_type'] = read_cache('audit_type', $row['audit_type']);
    $arr               = array(
        $num,
        $row[task_code],
        $project,
        $row[tb_date],
        $row[te_date],
        $row[ep_name],
        $row[audit_type],
        $n2,
        $n1
    );
    $data[]=$arr;
    $num++;
}


export_excel($data, '检查结果统计表');