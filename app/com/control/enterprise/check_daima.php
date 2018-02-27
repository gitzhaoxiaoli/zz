<?php
!defined('IN_SUPU') && exit('Forbidden');
/*
 *组织代码检测-异步传输-登记企业的时候触发事件
 */
$work_code = trim(getgp('work_code'));


$total     = $db->get_var("SELECT COUNT(*) FROM sp_enterprises WHERE work_code = '$work_code'");
if ($_GET['a'] == 10) {
    $result = $db->query("SELECT eid FROM sp_enterprises WHERE work_code = '$work_code'");
    while ($row = mysql_fetch_array($result)) {
        $json[] = $row;
    }
    $total = json_decode($json);
    P($total);
    return $total;
}
echo $total;
exit();