<?php
!defined('IN_SUPU') && exit('Forbidden');
$project_id = $_GET['project_id'];
if (!empty($project_id)) {
    $doc                  = load('data');
    $doc->doc_left        = 'supul';
    $doc->doc_right       = 'supur';
    //项目信息
    $proj_info            = load('audit')->get(array(
        'id' => $project_id
    ));
    $data['send_require'] = $proj_info['send_require'];
    $data['samp_note']    = $proj_info['samp_note'];
    //合同项目信息
    $cti_info             = $db->find_one('contract_item', array(
        'cti_id' => $proj_info['cti_id']
    ));
    $data['cti_code']     = $cti_info['cti_code'];
    $data['lab_type']     = '型式试验';
    $data['prod_ver_id']  = $cti_info['prod_ver_id'];
    $data['scope']        = $cti_info['appro_scope'];
    //判断是否是变更项目
    if ($proj_info['change_id']) {
        $change_info       = load('change')->get($proj_info['change_id']);
        $data['cti_code']  = $cti_info['cti_code'] . '-' . $change_info['chang_code'];
        $data['lab_type']  = '变更试验';
 		$des=explode('；',$change_info['des']); 
 		$tmp='';
		foreach($des as $v){
			if(!$v)continue; 
			$tmp.='<w:p w:rsidR="007941C2" w:rsidRDefault="007941C2" w:rsidP="007941C2"><w:pPr><w:rPr><w:rFonts w:ascii="宋体"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="宋体"/></w:rPr><w:t>'.$v.'</w:t></w:r></w:p>';
 		} 
		   $data['samp_note'] = '<w:p w:rsidR="007941C2" w:rsidRDefault="007941C2" w:rsidP="007941C2"><w:pPr><w:rPr><w:rFonts w:ascii="宋体"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="宋体"/></w:rPr><w:t>'.$proj_info['samp_note'].'</w:t></w:r></w:p>'.'<w:p w:rsidR="007941C2" w:rsidRDefault="007941C2" w:rsidP="007941C2"><w:pPr><w:rPr><w:rFonts w:ascii="宋体"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="宋体"/></w:rPr><w:t>变更明细:'.'</w:t></w:r></w:p>'.$tmp;
	 
        //型号规格
        if ($change_info['cg_type'] == '03') {
            $data['prod_ver_id'] = $change_info['format_cg_af']['prod_ver_id'];
        } elseif ($change_info['cg_type'] == '09') {
            $data['scope'] = $change_info['format_cg_af']['scope'];
        }
    }
	$data['scope']=xml_str($data['scope']);
    $data['brand']            = $cti_info['brand'];
    $data['ep_prod_addr']     = $cti_info['ep_prod_addr'];
    //认证委托人信息
    $ep_info                  = $db->find_one('enterprises', array(
        'eid' => $cti_info['eid']
    ));
    $data['ep_name']          = $ep_info['ep_name'];
    $data['person_tel']       = $ep_info['person_tel'];
    $data['ep_addr']          = $ep_info['ep_addr'];
    $data['ep_addrcode']      = $ep_info['ep_addrcode'];
    //生产企业信息
    $prd_ep_info              = $db->find_one('enterprises', array(
        'eid' => $cti_info['ep_prod_id']
    ));
    $data['pro_ep_name']      = $prd_ep_info['ep_name'];
    $data['pro_person_tel']   = $prd_ep_info['person_tel'];
    $data['pro_ep_fax']       = $prd_ep_info['ep_fax'];
    $data['pro_person']       = $prd_ep_info['person'];
    $data['pro_ep_addrcode']  = $prd_ep_info['ep_addrcode'];
    //产品名称//通过cti_id 获取产品名称 get_prod_name_by_cti_id
    $prod_info                = $db->find_one("settings_prod", array(
        "code" => $cti_info["prod_id"]
    ));
    $data['name']             = $prod_info['name'];
    //实验室信息 get_lab_info_by_pid
    $lab_info                 = $db->find_one('settings_test_org', array(
        'code' => $proj_info['test_org_id']
    ));
	
    $data['now_time']         = date('Y年m月d日');
    $data['test_org_name']    = preg_replace('/\d/',' ',$lab_info['name']);
	
    $data['lab_addr']         = $lab_info['addr'];
    $data['lab_person']       = $lab_info['person'];
    $data['lab_person_phone'] = $lab_info['person_phone'];
    $data['lab_fax']          = $lab_info['fax'];
    $data['lab_post_code']    = $lab_info['post_code'];
	
    //人员信息   
    //  $hr_info = $db->find_one('hr', array('id' => $cti_info['create_uid']));
    // $data['hr_name'] = $hr_info['name'];
    $doc->file_name           = $data['cti_code'] . '-委托单';
	
	 
    $doc->export_doc($data);
}
