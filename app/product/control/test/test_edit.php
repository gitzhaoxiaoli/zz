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
$pid          = (int) getgp('pid');
$test_id      = getgp("test_id");
// p($_POST['is_qualified']);
// exit;

if ($step) {

    if($_POST['new']['test_name']){
        
        foreach ($_POST['new']['test_name'] as $key => $value) {
         

            $new=array('productname'=>$value,'productmodel'=>$_POST['new']['test_scope'][$key],'productnum'=>$_POST['new']['test_num'][$key],'note'=>$_POST['new']['test_ver'][$key],'pid'=>$pid);     
               $db->insert('sample',$new);
    
    }
}


    if($_POST['test_name']){
         foreach ($_POST['test_name'] as $key => $value) {
         
            $update=array('productname'=>$value,'productmodel'=>$_POST['test_scope'][$key],'productnum'=>$_POST['test_num'][$key],'note'=>$_POST['test_ver'][$key],'pid'=>$pid);     
               $db->update('sample',$update,array('id'=>$key));
    
    }

}


    //写入检验信息
    $temp = array(
        'test_org_id' => $_POST['test_org_id'],
        'test_org_name' => $_POST['test_org_name'],
        'plan_date' => $_POST['plan_date'],
        'send_date' => $_POST['send_date'],
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
	if($_POST['pid'])
		$db->update("project", array(
                "test_id" => $test_id,
				'test_org_id' => $_POST['test_org_id'],
				'test_org_name' => $_POST['test_org_name'],
            ), array(
                "id" => $_POST['pid']
            ));
	$db->update("progress",array(	
										"step3"	=> date("Y-m-d H:i:s"),
										"status"	=> "3",
								),array("pid" => $_POST['pid']));
    showmsg('success', 'success', "?m=product&c=test&a=test_edit&pid=$pid&test_id=$test_id");
}

$res_jyap = $db->find_one('test', " AND id = '$test_id'");
if ($res_jyap) {
    extract(chk_arr($res_jyap));
}
$cti_id = $db->getField("project","cti_id",array("id"=>$pid));
/* 说明:停止使用 */ 
/* @zys 2016-5-24 */
// $files_list = $db->get_results("SELECT * FROM `sp_attachments` WHERE `test_id` = '$test_id' AND `iso_prod_type` = '1' ");

// $sql="select productnum,productmodel,productname,note,id from sp_sample where pid='$pid'";

/*说明:停止使用*/
/*@zys 2016-5-6*/
/*
$q=$db->query($sql);
$arr=array();
while ($row=$db->fetch_array($q)) {
    $arr[]=$row;
}
*/

// p($_POST);
// exit;
		

tpl();

?>