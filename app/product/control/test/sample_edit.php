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
// p($_POST['is_qualified']);
// exit;
if ($_POST) {
    //写入检验信息
    $temp = array(
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
	if($_POST['testres'])
		foreach ($_POST['testres'] as $id => $v) {
            $db->update("sample", array(
                "testres" => $v,
                "test_id" => $test_id,
				"status"  => "1",
            ), array(
                "id" => $id
            ));
            
            
        }
    showmsg('success', 'success', "?m=product&c=test&a=sample_list");
}
$id = getgp("id");
$ep_prod_id = getgp("ep_prod_id");
$sample_info = $db->find_one("sample",array("id"=>$id));
extract($sample_info);

$projects = $db->get_results("select ss.*,c.certno from sp_sample ss LEFT JOIN sp_certificate c  ON c.id = ss.certid WHERE 1 AND tid = '$tid' AND ss.deleted = 0");

$test_info = $db->find_one("test",array("tid"=>$tid));
extract($test_info);
$preg = "/\?.+a=\w+/";
$page = pregStr($preg,$_SERVER[REQUEST_URI]);
$files_list = $db->get_results("SELECT * FROM `sp_attachments` WHERE `tid` = '$tid' AND `iso_prod_type` = '1' AND page = '$page'");

tpl();

?>