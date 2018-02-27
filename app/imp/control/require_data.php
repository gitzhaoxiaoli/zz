<?php
/**
 * 准备数据库
 * 
 */
$tbs = array(
    //企业
    'sp_enterprises',
    'sp_enterprises_site',
    'sp_attachments',
    'sp_metas_ep',
    'sp_periodical_email',
    'sp_attachments_pro',
    //合同
    'sp_contract',
    'sp_contract_item',
    'sp_contract_num',
    'sp_cooperation',
    //财务
    'sp_contract_cost',
    'sp_contract_cost_detail',
    //审核任务
    'sp_project',
    'sp_task',
    'sp_test',
    'sp_task_audit_team',
    'sp_assess_notes',
    'sp_sample',
    //证书
    'sp_certificate',
    'sp_certificate_change',
    'sp_change_app',
    //人员
    // 'sp_hr',
    // 'sp_hr_qualification',
    'sp_hr_archives',
    // 'sp_hr_audit_code',
    'sp_hr_audit_code_app',
    'sp_hr_experience',
    'sp_metas_ot',
    'sp_metas_hr',
	//内审员培训
	'sp_ot_basedata',
	'sp_ot_contract',
	'sp_ot_traincert',
	// 其他
	'sp_sms',
	'sp_cuccertgg',
	'sp_cuccertup',
	'sp_energy',
	'sp_log',
	'sp_user_menus',
	'sp_stff',
	'sp_progress',
	'sp_word',
);
$tbs = array_unique($tbs);
$db->drop_more($tbs);
$db->query("OPTIMIZE TABLE ".join(",",$tbs).""); 

// 删除人员表只留5条数据
$db->query("DELETE FROM sp_hr WHERE id >1 AND job_type NOT LIKE '1004'");
$db->query("DELETE FROM sp_hr WHERE id >90");
$db->query("DELETE FROM sp_hr_qualification WHERE uid >90");
$db->query("DELETE FROM sp_hr_audit_code WHERE uid >90");
$db->query("UPDATE sp_hr SET `name` = CONCAT(LEFT(`name`,1),'xx'),card_no = '' WHERE id >1");
echo "SUCCESS";