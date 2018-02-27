<?php
!defined('IN_SUPU') && exit('Forbidden');
require_once(ROOT . '/data/cache/arctype.cache.php');
require_once(ROOT . '/data/cache/audit_type.cache.php');

//检验状态
$check_status = array(
    '--请选择--',
    '合格',
    '需整改',
    '不合格'
);
$step         = (int) getgp('step');
$ep_prod_id   = (int) getgp('ep_prod_id');
$pid          = (int) getgp('pid');
$cti_id       = (int) getgp('cti_id');
$is_qualified = (int) getgp('is_qualified');
$test_id      = getgp("test_id");
$temp_mark    = getgp('temp_mark');
//p($_POST);
//exit;
if ($step) {
    //写入检验信息
    $temp = array(
        'test_org_id' => $_POST['test_org_id'],
        'test_org_name' => $_POST['test_org_name'],
        'plan_date' => $_POST['plan_date'],
        'send_date' => $_POST['send_date'],
        'sample_reach_date' => $_POST['sample_reach_date'],
        'report_chuju_date' => $_POST['report_chuju_date'],
        'send_require' => $_POST['send_require'],
        'entrust_note' => $_POST['entrust_note']
    );
    if ($test_id) {
        $db->update("test", $temp, array(
            "id" => $test_id
        ));
    } else {
        $test_id = $db->insert("test", $temp);
    }
	if($_POST['is_qualified'])
		foreach ($_POST['is_qualified'] as $pid => $v) {
            $db->update("project", array(
                "is_qualified" => $v,
                "test_id" => $test_id,
				'test_org_id' => $_POST['test_org_id'],
				'test_org_name' => $_POST['test_org_name'],
            ), array(
                "id" => $pid
            ));
            
            $db->update("progress",array(	
									"step4"	=> date("Y-m-d H:i:s"),
									"status"	=> "4",
								),array("pid"=>$pid));
        }
	
    showmsg('success', 'success', "?m=product&c=test&a=list_wait_res");
}
if ($test_id) {
    $projects = $db->find_results('project', " AND ep_prod_id = $ep_prod_id AND iso_prod_type = 1 AND is_qualified = $is_qualified AND test_id = '$test_id'");
} else {
    $projects = $db->find_results('project', " AND ep_prod_id = $ep_prod_id AND iso_prod_type = 1 AND is_qualified = $is_qualified AND test_id = 0");
}

$res_jyap = $db->find_one('test', " AND id = '$test_id'");
if ($res_jyap) {
    extract(chk_arr($res_jyap));
}

$preg = "/\?.+a=\w+/";
$page = pregStr($preg,$_SERVER[REQUEST_URI]);
$files_list = $db->get_results("SELECT * FROM `sp_attachments` WHERE `tid` = '$tid' AND `iso_prod_type` = '1' AND page = '$page'");
//echo $db->sql;die;


tpl();

?>