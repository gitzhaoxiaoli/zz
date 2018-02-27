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
$is_qualified = (int) getgp('is_qualified');
$test_id      = getgp("test_id");
// p($_POST['is_qualified']);
// exit;
if ($step) {
    //写入检验信息
    $temp = array(
        'test_code' => $_POST['test_code'],
        'test_sdate' => $_POST['test_sdate'],
        'test_edate' => $_POST['test_edate'],
		'sample_reach_date' => $_POST['sample_reach_date'],
        'report_chuju_date' => $_POST['report_chuju_date'],
        'note' => $_POST['note']
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
                "test_id" => $test_id
            ), array(
                "id" => $pid
            ));
            
            
        }
	// 处理型式试验方案
	if($_POST[productname])
		foreach($_POST[productname] as $id => $v){
			
			$up = array(
					"productname"	=> $v,
					"productmodel"	=>$_POST[productmodel][$id],
					"productnum"	=>$_POST[productnum][$id],
					"productdate"	=>$_POST[productdate][$id],
					"status"	=>$_POST[status][$id],
					"testres"	=>$_POST[testres][$id],
					"note"	=>$_POST[productnote][$id],
					);
			$db->update("sample",$up,array("id"=>$id));
		}
	if($_POST['new']['productname'])
		foreach($_POST['new']['productname'] as $k => $v){
			if(!$v)continue;
			$up = array(
					"pid"			=> $pid,
					"productname"	=> $v,
					"productmodel"	=>$_POST['new'][productmodel][$k],
					"productnum"	=>$_POST['new'][productnum][$k],
					"productdate"	=>$_POST['new'][productdate][$k],
					"status"	=>$_POST['new'][status][$k],
					"testres"	=>$_POST['new'][testres][$k],
					"note"	=>$_POST['new'][note][$k],
					);
			$db->insert("sample",$up);
		}
    showmsg('success', 'success', "?m=testorg&c=test&a=list_wait_test");
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
$p_info = $db->find_one("project",array("id"=>$pid),"cti_id");
extract($p_info);
$files_list = $db->get_results("SELECT * FROM `sp_attachments` WHERE `test_id` = '$test_id' AND `iso_prod_type` = '1' ");
$sample = $db->get_results("SELECT * FROM `sp_sample` WHERE `pid` = '$pid'");
// p($_POST);
// exit;
$width = "1050";

		$wordlist = array(
				array(
					"url"		=> "070401-07&pid=$pid",
					"filename"	=> "样品测试通知",
					"status"	=> "0"
					),
				array(
					"url"		=> "070401-08&pid=$pid",
					"filename"	=> "送样通知和型式试验方案",
					"status"	=> "0"
					),
				array(
					"url"		=> "070401-09&pid=$pid",
					"filename"	=> "样品真实性审查结果报告",
					"status"	=> "0"
					),
				array(
					"url"		=> "070501-01&pid=$pid",
					"filename"	=> "补充资料与样品清单",
					"status"	=> "0"
					),
				// array(
					// "url"		=> "070501-02&pid=$pid",
					// "filename"	=> "撤销任务建议书",
					// "status"	=> "0"
					// ),
				array(
					"url"		=> "070501-03&pid=$pid",
					"filename"	=> "试验结果及费用上报表",
					"status"	=> "0"
					),
				array(
					"url"		=> "070501-05&pid=$pid",
					"filename"	=> "样品问题报告",
					"status"	=> "0"
					),
				array(
					"url"		=> "070501-06&pid=$pid",
					"filename"	=> "修改单元划分建议书",
					"status"	=> "0"
					),
				array(
					"url"		=> "070501-07&pid=$pid",
					"filename"	=> "异常情况上报表",
					"status"	=> "0"
					),
				array(
					"url"		=> "070501-08&pid=$pid",
					"filename"	=> "收样回执、检测计划表",
					"status"	=> "0"
					),
				array(
					"url"		=> "070501-09&pid=$pid",
					"filename"	=> "产品检测整改通知",
					"status"	=> "0"
					)
					);

tpl();

?>