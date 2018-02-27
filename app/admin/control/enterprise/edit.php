<?php
!defined('IN_SUPU') && exit('Forbidden');

$ftype = "";
//添加-编辑企业 获取页面请求，查询列表用extract获取get变量，修改与添加用 $_GET 或$_post  
$step=getgp("step");
$zz_id=getgp("zz_id");




if($zz_id)
		{
	$row_ = $db->find_one('attachments_pro',array('id'=>$zz_id));
    extract($row_, EXTR_SKIP); 	
		}	
if($step=='org'){
	$work_code=getgp("work_code");
	$_code=str_replace("-","",trim($work_code));
	$orgClass=getOrgInfo($_code);
	if($orgClass->message=='success'){
		$orgInfos=$orgClass->orgInfos;
		require( ROOT . '/data/cache/prod_type.cache.php' );
		require( ROOT . '/data/cache/nature.cache.php' );
		$new_prod_type = array();
		foreach($prod_type_array as $k=>$item){
			$new_prod_type[$item[name]] = $k;
		}
		$new_nature = array();
		foreach($nature_array as $k=>$item){
			$new_nature[$item[name]] = $k;
		}
	$b_name = str_replace("公司","",$orgInfos->businessTypeName);
	$ep_info=array(
		'ep_name'		=>$orgInfos->orgName,
		'areacode'		=>$orgInfos->areaCode,
		'areaaddr'		=>$orgInfos->areaName,
		'delegate'		=>$orgInfos->legalName,
		'ep_addr'		=>$orgInfos->orgAddress,
		'capital'		=>$orgInfos->registeredCapital,
		'ep_addrcode'	=>$orgInfos->zipCode,
		'prod_type'		=>$new_prod_type[$b_name],
		'nature'		=>$new_nature[$orgInfos->businessTypeName],
		'work_code'		=>$work_code,
		);
	extract($ep_info,EXTR_OVERWRITE);
	$statecode_select = str_replace("value=\"156\"", "value=\"156\" selected ", $statecode_select);
	//获证组织经济代码
    $prod_type_select  = str_replace("value=\"$prod_type\"","value=\"$prod_type\" selected",$prod_type_select);
	//企业性质
    $nature_select   = str_replace("value=\"$nature\">", "value=\"$nature\" selected>", $nature_select);
	tpl('enterprise/edit');
	}else{
		echo "<script>alert('请检查组织机构代码是否正确');window.history.go(-1);</script>";
		exit;
		}


}else{
 
$eid       = (int) getgp('eid');
$parent_id = (int) getgp('parent_id');
if ($_POST) { 
/* foreach(getgp('meta') as $key => $val){
	if(is_array($val)){
		$val = implode("||:||",$val);
	}
	echo $key."=>".$val."<br />";
}
exit; */
    $new_enterprise = $_POST; 
	$new_enterprise['frist_letter']=getFirstNameChar($_POST[ep_name]);
	$new_enterprise['parent_id']=$parent_id;
	$new_enterprise['person']=$_POST[meta][person][0];
	$new_enterprise['person_tel']=$_POST[meta][person_mph][0];
	$new_enterprise['person_email']=$_POST[meta][person_email][0];
	$new_enterprise['person_job']=$_POST[meta][person_job][0];
	//$new_enterprise['work_code']=str_replace("-","",trim($new_enterprise['work_code']));
	$new_enterprise['ep_name'] = trim($new_enterprise['ep_name']);
    unset($new_enterprise['step'], $new_enterprise['meta']);
	
	
    if ($eid) {
        //总部的人才能修改合同来源
        $u_ctfrom = current_user('ctfrom');                        
        if ('01000000' != $u_ctfrom) {
            unset($new_enterprise['ctfrom']);
        }
        $af_str = serialize($enterprise->get(array(
            'eid' => $eid
        )));
        $enterprise->edit($eid, $new_enterprise);
        $bf_str = $enterprise->get(array(
            'eid' => $eid
        ));
        // 日志： 统一写到控制器
        do {
            if ($bf_str['parent_id']) {
                $content = "[说明:关联公司修改]";
            } else {
                $content = "[说明:客户信息修改]";
            }
            log_add($eid, 0, $content, $af_str, serialize($bf_str));
        } while (false);
    } else {
    	// 判断企业名称是否存在数据库中
    	$eid = $db->getField("enterprises",'eid',array("ep_name"=>$_POST['ep_name']));
    	if($eid){
    		echo "<script>alert('企业名称已存在！');history.back(-1);</script>";
    		exit;
    	}
        $eid    = $enterprise->add($new_enterprise);

        $bf_str = $enterprise->get(array(
            'eid' => $eid
        ));
        // 日志
        do {
            if ($bf_str['parent_id']) {
                $content = "[说明:关联公司登记]";
            } else {
                $content = "[说明:客户信息登记]";
            }
            log_add($eid, 0, $content, NULL, serialize($bf_str));
        } while (false);
        if ($parent_id)
            $enterprise->union_count($parent_id, 1);
    }
	
       
    showmsg('success', 'success', "?c=enterprise&a=edit&eid=$eid");
}
if(getgp('eid')){
//获取企业资质证书
$sql = "SELECT * FROM sp_attachments_pro WHERE 1 AND deleted = 0  AND eid = ".$_GET['eid'];
$query = $db->query($sql);
$zz_files = array();
while($zz = $db->fetch_array($query))
 {
	 $zz_files[$zz['id']] = $zz;
 };
$sql = "SELECT * FROM sp_attachments WHERE 1 AND deleted = 0 AND ftype > 5000  AND eid = ".$_GET['eid'];
$query = $db->query($sql);
$epfiles = array();
while($files = $db->fetch_array($query))
 {
	 $epfiles[$files['id']] = $files;
 };
 };
 // echo $db->sql;
 // p($epfiles);die;
$enterprises_archives = array();
//$statecode            = '156';
$nav_title            = '企业登记';
 if ('edit' == $a or $parent_id) {
    //$eid = (int)getgp( 'eid' );
    $where_arr = ($parent_id) ? array(
        'eid' => $parent_id
    ) : array(
        'eid' => $eid
    );
    $row       = $enterprise->get($where_arr);
	//提取出联系人信息
	$persons = array();
	$person_arr = array('person','person_bumen','person_job','person_tel','person_mph','person_email','person_note');
	foreach($person_arr as $v){
		$persons[$v] = explode("||:||",$row[$v]);
		unset($row[$v]);
	}
    

	//联系人信息列表数组
	$person_list = array();
	$i = 0;
	  foreach($persons['person'] as $k => $v){
	  	  
		  foreach($person_arr as $val)
		  {
			  		  if($persons[$val][$i]==""){

			               $person_list[$i][$val] ="";

					  }else{
					  	 $person_list[$i][$val] = $persons[$val][$i];
					  }
		  };
		  $i++;
	  };
    extract($row, EXTR_SKIP); 
	if($parent_id)$work_code="";
  //  $statecode = $row['statecode'];
    //if ('edit' == $a)
        //$parent_id = $row['parent_id'];
    //合同来源
    $ctfrom_select   = str_replace("value=\"$ctfrom\"", "value=\"$ctfrom\" selected", $ctfrom_select);
    //客户级别
    $ep_level_select = str_replace("value=\"$ep_level\">", "value=\"$ep_level\" selected>", $ep_level_select);
    //企业性质
    $nature_select   = str_replace("value=\"$nature\">", "value=\"$nature\" selected>", $nature_select);
    //注册资本币种
    $currency_select = str_replace("value=\"$currency\">", "value=\"$currency\" selected>", $currency_select);
	//获证组织经济代码
    $prod_type_select  = str_replace("value=\"$prod_type\">","value=\"$prod_type\" selected>",$prod_type_select);

   //企业附件列表
    $archive_total   = $db->get_var("SELECT COUNT(*) FROM sp_attachments WHERE eid = '$eid'");
    $archive_join    = " LEFT JOIN sp_hr hr ON hr.id = ea.create_uid";
    $sql             = "SELECT ea.*,hr.name author FROM sp_attachments ea $archive_join WHERE ea.eid = '$eid' ORDER BY ea.id DESC LIMIT 10";
    $query           = $db->query($sql);

    while ($rt = $db->fetch_array($query)) {
        // $rt['ftype_V'] = f_arctype($rt['ftype']);
        $enterprises_archives[$rt['id']] = $rt;
    }
    
 }
 
//国家/地区代码
//$statecode_select = str_replace("value=\"$statecode\">", "value=\"$statecode\" selected>", $statecode_select);



tpl('enterprise/edit');
 }