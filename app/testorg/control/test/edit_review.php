<?php
!defined('IN_SUPU') && exit('Forbidden');
$test_id = getgp("test_id");
$pid = getgp("pid");
if ($_POST) {
    if ($test_id)
        $db->update("test", array("test_review_note"=>$_POST['test_review_note']), array(
            "id" => $test_id
        ));
	if($_POST[test_review_status]){
		$db->update("project",array("test_review_status" => '1'),array("test_id" => $test_id));
	}
	showmsg("success","success","?m=testorg&c=test&a=review");
    
}
$project_info = $db->get_row("SELECT * FROM  sp_test WHERE `id` = '$test_id'");

$p_info = $db->get_row("SELECT test_review_status,ep_prod_id FROM `sp_project` WHERE `test_id` = '$test_id' AND `deleted` = '0'");
extract($p_info);
//获取已上传文档
$files_list = $db->find_results("attachments", " AND test_id = '$test_id' AND iso_prod_type = 1  ");
// p($project_info);
$wordlist = array(
	array(
		"url"		=> "070701-01&pid=$pid",
		"filename"	=> "检测报告资料复核记录表",
		"status"	=> "0"
		),
	array(
		"url"		=> "070704-01&pid=$pid",
		"filename"	=> "CB测试报告资料复核记录表",
		"status"	=> "0"
		),
	);

tpl();
