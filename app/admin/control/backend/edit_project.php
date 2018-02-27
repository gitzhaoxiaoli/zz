<?php
!defined('IN_SUPU') && exit('Forbidden');
//变成评审-修改项目信息
$pid   = getgp('pid');

$p_info = load('audit')->get(array(
    'id' => $pid
));


if ($_POST) {
	// p($_POST);
	// exit;
	$up_p = $_POST;
	unset($up_p['pid']);
    $up_p['comment_a_uid'] = $db->getField('hr' , 'id' , array("name" => trim($_POST['comment_a_name'])));
        
    $db->update('project' , $up_p , array("id" => $pid));
    if (in_array($p_inf['audit_type'], ['1002','1003'])) {
    	unset($up_p['st_num']);
    	$db->update('contract_item' , $up_p , array("cti_id" => $p_info['cti_id']));
    	    
    }
    $af  = $db->find_one('project' , array("id" => $pid));
	log_add($af['eid'],0,'修改项目信息:项目号'.$bf['cti_code'].'审核阶段：'.read_cache('audit_type',$af['audit_type']),serialize($p_info),serialize($af));

    showmsg('success', 'success', "?c=backend&a=list_project");
}
//项目信息
 
$ct_id  = $p_info['ct_id'];
// p($p_info);
extract($p_info);
//项目体系下排除当前的的所有标准 
$audit_vers = $db->find_results('settings_audit_vers', " and   iso='$p_info[iso]' and audit_ver!='$p_info[audit_ver]'", 'audit_ver,audit_basis');
//结合审核项目
$p_info['old_audit_ver'] = explode('；', $p_info['audit_type_note']);

require_once(ROOT.'/data/cache/audit_ver.cache.php');
foreach($audit_ver_array as $k => $v){
    if(substr("$v[audit_ver]",0,3) == $iso ) {
        $audit_ver_V .= "<option value=\"$v[audit_ver]\">" . $v[msg] . "</option>";
    }
}

//专业审核代码

$audit_codes             = array();
$codes                   = explode('；', $p_info['audit_code']);
$query                   = $db->query("SELECT code,shangbao,risk_level,mark FROM sp_settings_audit_code WHERE shangbao IN('" . implode("','", $codes) . "') AND iso='$p_info[iso]'");
while ($rt = $db->fetch_array($query)) {
    if (!$rt['shangbao'])
        continue;
    $marks     = explode(',', $rt['mark']);
    $new_marks = array();
    foreach ($marks as $mk) {
        $mark_V = f_mark($mk);
        if ($mark_V) {
            $new_marks[] = $mark_V;
        }
    }
    $rt['mark_V']       = implode(',', $new_marks);
    $rt['risk_level_V'] = f_risk($rt['risk_level']);
    $audit_ver_V = str_replace("value=\"$audit_ver\"","value=\"$audit_ver\" selected",$audit_ver_V);
    $audit_codes[]      = $rt;
    // p($audit_ver_V);die;
    $_audit_code[] = $rt['shangbao'];
    $_use_code[] = $rt['code'];
}
$p_info['audit_code'] = join("；",$_audit_code);
$p_info['use_code'] = join("；",$_use_code);

tpl();
 