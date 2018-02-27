<?php
!defined('IN_SUPU') && exit('Forbidden');
//存档目录
require_once CONF . '/cache/fl_question.cache.php';
 

$file_arr = array(
     "1"=>"第一阶段审核报告",
	 "2"=>"第一阶段审核问题清单",
	 "3"=>"知识产权管理标准认证审核计划",
	 "4"=>"核查记录表",
	 "5"=>"不符合项报告",
     "6"=>"廉洁承诺书",
     "7"=>"现场审核通知",
     "8"=>"认证人员公正性、保密性承诺",
     "9"=>"审核报告",
     "10"=>"审核经历记录表",
     "11"=>"知识产权文件评审报告",
	 "12"=>"签到表",
	 "13"=>"观察项建议项清单",
     "14"=>"审核任务书",
	 "15"=>"证书内容确认件",


);


$tid      = getgp('tid');
if ($tid) {
    //审核文档
    $sql  = "select * from sp_attachments where tid='$tid' ORDER BY `sort`";
    $res  = $db->query($sql);
    $span = array();
    while ($rt = $db->fetch_array($res)) {
        $rt['uid']              = f_username($rt['create_uid']);
        $enterprises_archives[] = $rt;
        $span[$rt['sort']]      = $rt[name];
    }
    $task_info      = $db->find_results("task","AND id='$tid'","*");


    //判断是组长还是审核员
    $query = $db->query("SELECT role FROM sp_task_audit_team WHERE tid = '$tid' AND uid = '" . current_user('uid') . "' AND role != ''");
    while ($rt = $db->fetch_array($query)) {
        if ($rt[role] == '1001')
		{
	  $auditor_role = 1;
	   }
            
    }
    if (current_user("uid") == '1')
	{
		$auditor_role = 1;
		//评定问题
       $assess_notes = $db->get_results("select * from sp_assess_notes WHERE 1 AND tid='$tid' AND deleted='0' ORDER BY id");
    }else{
	   $assess_notes = $db->get_results("select * from sp_assess_notes WHERE 1 AND tid='$tid' AND deleted='0'  ORDER BY id");
    }
    // 上传文件表单   
    $form_file = $a_link = "";
    foreach ($file_arr as $k => $val) {
        $form_file .= '<tr><td width="300">';
        $form_file .= $k . " " . $val;
        $form_file .= "</td><td>";
        $form_file .= '<input type="hidden" name="sort[]" value="' . $k . '"/><input type="file" name="archive[]" />';
        $form_file .= "</td><td><span>";
        if ($span[$k])
            $form_file .= $span[$k] . "(已上传)";
        else
            $form_file .= "无";
        $form_file .= "</span><br/></td></tr>";
		$a_link .= "<li><a href='?m=product&c=doc&a=$k&tid=$tid'>$k $val</a></li>";
    }
    $cti_id = $db->getField('project','cti_id',array('tid' => $tid));
    
    
    tpl('auditor/task_edit');
}
?>