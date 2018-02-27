<?php
//清空表数据
$tbs = array(
    'sp_assess_notes',
    'sp_attachments',
    'sp_attachments_pro',
    'sp_auditor_report',
    'sp_certificate',
    'sp_certificate_change',
    'sp_change_app',
    'sp_cnca_list',
    'sp_contract',
    'sp_contract_cost',
    'sp_contract_cost_detail',
    'sp_contract_item',
    'sp_contract_num',
    'sp_cooperation',
    'sp_cron',
    'sp_cuccertgg',
    'sp_cuccertup',
    'sp_energy',
    'sp_enterprises',
    'sp_enterprises_note',
    'sp_enterprises_site',
    'sp_files',
    'sp_hr',
    'sp_hr_archives',
    'sp_hr_audit_code',
    'sp_hr_audit_code_app',
    'sp_hr_experience',
    'sp_hr_qualification',
    'sp_log',
    'sp_metas_ep',
    'sp_metas_hr',
    'sp_metas_ot',
    'sp_notice',
    'sp_ot_basedata',
    'sp_ot_contract',
    'sp_ot_traincert',
    'sp_periodical_email',
    'sp_progress',
    'sp_project',
    'sp_project_cost',
    'sp_sample',
    'sp_sms',
    'sp_stff',
    'sp_task',
    'sp_task_audit_team',
    'sp_task_note',
    'sp_tat_temp',
    'sp_test',
    'sp_user_menus',
    'sp_wechat_log',
    'sp_word'
);
$tbs = array_unique($tbs);
$db->drop_more($tbs);
$db->query("OPTIMIZE TABLE ".join(",",$tbs).""); 

// 系统管理员
$admin_array = array(
	'username' => 'admin',
	'password' => 'd6718de68da2dd3653948c66ade465ec',
	'name' => '管理员',
	'ctfrom' => '01000000',
	'is_hire' => '0',
    'check_auth' => 1
); 
$db->insert( 'hr', $admin_array );

echo "SUCCESS";