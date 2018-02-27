<?php
!defined('IN_SUPU') && exit('Forbidden');
//资料回收列表
extract( $_GET, EXTR_SKIP );
	$fields = $join = $where = '';
	 $order=" ORDER BY t.upload_file_date DESC";
//标签样式
	$redata_status = (int)$redata_status?(int)$redata_status:2;

	$redata_0 = $redata_1 = '';
	${'redata_'.$redata_status} = " ui-tabs-active ui-state-active";

	if($sort=getgp("sort") and $sort_val=getgp("sort_val")){

		$sort_val=="1" && $order=" ORDER BY t.$sort DESC";
		$sort_val=="2" && $order=" ORDER BY t.$sort ASC";
	
	
	}
//搜索条件
	//上传时间 起
	if( $upload_date_start ){
		$tids=$db->get_col("SELECT id FROM `sp_task` WHERE `upload_file_date` >= '$upload_date_start 00:00:00' AND `deleted` = '0'");
		$tids=array_merge($tids,array(-1));
		$where .=" AND p.tid IN (".join(",",$tids).")";
	}
	// 上传时间 止
	if( $upload_date_end ){
		$tids=$db->get_col("SELECT id FROM `sp_task` WHERE `upload_file_date` <= '$upload_date_end 23:00:00' AND `deleted` = '0'");
		$tids=array_merge($tids,array(-1));
		$where .=" AND p.tid IN (".join(",",$tids).")";
	}

	//企业名称
	if( $ep_name ){
		$_eids = array(-1);
		$_query = $db->query("SELECT eid FROM sp_enterprises WHERE ep_name LIKE '%".str_replace('%','\%',trim($ep_name))."%'");
		while( $rt = $db->fetch_array( $_query ) ){
			$_eids[] = $rt['eid'];
		}
		$where .= " AND p.eid IN (".implode(',',$_eids).")";
	}
	//省份
if( $areacode=getgp("areacode") ){
	$pcode = substr($areacode,0,2) . '0000';
	$_eids = array(-1);
	$_query = $db->query("SELECT eid FROM sp_enterprises WHERE LEFT(areacode,2) = '".substr($areacode,0,2)."'");
	while( $rt = $db->fetch_array( $_query ) ){
		$_eids[] = $rt['eid'];
	}
	$where .= " AND p.eid IN (".implode(',',$_eids).")";
	unset( $_eids, $_query, $rt, $_eids );
	
	$province_select = str_replace( "value=\"$pcode\">", "value=\"$pcode\" selected>" , $province_select );
}


//删除合同编号


//合同认证申请编号
if( $cti_code=trim($cti_code) ){
	$where .= " AND p.cti_code like '%$cti_code%'";
}

	//认证体系
	if( $iso ){
		$where .= " AND p.iso = '$iso'";
		$iso_select = str_replace( "value=\"$iso\">", "value=\"$iso\" selected>" , $iso_select );
	}

	//审核类型
	if( $audit_type ){
		$where .= " AND p.audit_type = '$audit_type'";
		$audit_type_select = str_replace( "value=\"$audit_type\">", "value=\"$audit_type\" selected>" , $audit_type_select );
	}

	//标准版本
	$audit_ver = getgp( 'audit_ver' );
	if( $audit_ver ){
		$where .= " AND p.audit_ver = '$audit_ver'";
		$audit_ver_select = str_replace( "value=\"$audit_ver\">", "value=\"$audit_ver\" selected>" , $audit_ver_select );
	}
	if($t_date_s){
		$query=$db->query("SELECT id FROM `sp_task` WHERE `tb_date` > '$t_date_s' AND `deleted` = '0'  ORDER BY te_date DESC");
		$_tids=array(-1);
		while($rt=$db->fetch_array($query)){
			$_tids[]=$rt[id];
		
		
		}
		$where .=" AND p.tid in(".implode(",",$_tids).")";
	
	
	
	}
	if($t_date_e){
		$query=$db->query("SELECT id FROM `sp_task` WHERE `te_date` < '$t_date_e' AND `deleted` = '0'     ORDER BY te_date DESC");
		$_tids=array(-1);
		while($rt=$db->fetch_array($query)){
			$_tids[]=$rt[id];
		
		
		}
		$where .=" AND p.tid in(".implode(",",$_tids).")";
	
	
	
	}

	//合同来源限制
	$len = get_ctfrom_level( current_user( 'ctfrom' ) );
	if( $ctfrom && substr( $ctfrom, 0, $len ) == substr( current_user( 'ctfrom' ), 0, $len ) ){
		$_len = get_ctfrom_level( $ctfrom );
		$len = $_len;
	} else {
		$ctfrom = current_user( 'ctfrom' );
	}
	switch( $len ){
		case 2	: $add = 1000000; break;
		case 4	: $add = 10000; break;
		case 6	: $add = 100; break;
		case 8	: $add = 1; break;
	}

	$ctfrom_e = sprintf("%08d",$ctfrom+$add);
	$where .= " AND p.ctfrom >= '$ctfrom' AND p.ctfrom < '$ctfrom_e'";

	$ctfrom_select = str_replace( "value=\"$ctfrom\"", "value=\"$ctfrom\" selected" , $ctfrom_select );

	// 取消分派
	if(getgp('type') == 'quxiao'){
		$quxiao_arr = getgp('pid');
		foreach ($quxiao_arr as $k=>$v) { 
			$new_project = array(
                    'comment_a_uid' => 0,
                    'comment_a_name' => "",
                    'redata_status' => 2,
                );
			$db->update('project',$new_project,array('id'=>$v));
		}
	}

     //派人
	 if(getgp('type') == 'fenpai'){
		$fenpai_arr = getgp('comment_a_name');
		foreach($fenpai_arr as $k=>$v){
		  if($v == "---请选择---"){
			  unset($fenpai_arr[$k]);
		  }
		}
		foreach($fenpai_arr as $key=>$val)
		{
		   	$val_arr = explode('-',$val,3);
		       $new_project = array(
                    'comment_a_uid' => $val_arr[1],
                    'comment_a_name' => $val_arr[2],
                    'redata_status' => 1,
					'fenpai_date'   => date("Y-m-d H:i:s"),
                );

                $audit->edit($val_arr[0], $new_project);
				$mail=$db->meta($val_arr[1],"mail","","hr");
				if($mail){
					$eid=$db->getField("project","eid",array("id"=>$val_arr[0]));
					$ep_name=$db->get_var("SELECT ep_name FROM `sp_enterprises` WHERE `eid` = '$eid'");
					$email_to=$mail;
					$email_title="新务任务：".$ep_name;
					// $email_title = "=?UTF-8?B?".base64_encode($email_title)."?=";
					$email_cotent="您好，您有新的评定任务请注意查收<br/>";
					$email_cotent.=$ep_name;
					$email_cotent.="<br/>";
					$email_cotent.="审核时间：";
					$email_cotent.=$row[tb_date]."至".$row[te_date];
					$email_cotent.="<br/>";
					$email_cotent.="审核组成员：".join("；",$auditors);
					$email_cotent.="<br/>";
					$email_cotent.="提交时间：".date("Y-m-d H:i:s");
					$email_cotent.="<br/>";
					$email_cotent.="资料分派人：".current_user("name");
					$email_cotent.="<br/>";
					$email_cotent.="请及时到erp 中查看 ";
					// $email_from=current_user("mail");
					// $headers　= "MIME-Version: 1.0<br/>";  
					// $headers .= 'Content-type:text/html; charset=utf-8' . "<br/>";  
					// $headers .= "Content-Transfer-Encoding: 8bit<br/>";
					// $headers .= $email_from;
					mailTo($email_to,$email_title,$email_cotent);
				}
		}
	 }
	//要获取的字段
	$fields .= "p.fenpai_date,p.redata_date,p.id,p.ct_id,p.to_jwh_date,p.ct_code,p.redata_status,p.cti_code,t.upload_file_date,t.upload_plan_date,t.bufuhe,p.ctfrom,p.audit_type,p.audit_ver,p.iso,p.redata_note,p.eid,p.tid,p.comment_a_uid,p.comment_a_name";
	$fields .=",hr.name,e.ep_name,e.areacode";
	//$fields .= ",c.pre_date";

	$fields .=",t.tb_date,t.te_date,t.jh_sp_date";

	//要关联的表
	$join .= " LEFT JOIN sp_task t ON t.id = p.tid";
	$join .= " LEFT JOIN sp_enterprises e ON e.eid = p.eid";
	$join .= " LEFT JOIN sp_hr hr ON hr.id = p.redata_uid";

	//限制条件
	$where .= " AND  p.audit_type <>'1002' "; //排除一阶段

	$where .= " AND p.deleted = '0' AND p.status = 3  AND t.deleted=0";


	if( !$export ){
		$restate_total = array(0,0);
		$restate_total[0]=$db->get_var("SELECT COUNT(*) total FROM sp_project p $join WHERE 1 $where AND p.redata_status =2");
		$restate_total[1]=$db->get_var("SELECT COUNT(*) total FROM sp_project p $join WHERE 1 $where AND p.redata_status =1");
		$pages = numfpage( $restate_total[$redata_status], 20 );
	}

	$where .= " AND p.redata_status = '$redata_status'";


	$resdb = array();
    $sql = "SELECT $fields FROM sp_project p $join WHERE 1 $where $order $pages[limit]";

	$query = $db->query( $sql );
	while( $rt = $db->fetch_array( $query ) ){
		$rt['province']		= f_region_province( $rt['areacode'] );
		$rt['audit_type_V'] = f_audit_type( $rt['audit_type'] );
		$rt['iso_V'] = f_iso( $rt['iso'] );
		$rt['audit_ver_V'] = f_audit_ver( $rt['audit_ver'] );
		$rt['ctfrom_V'] = f_ctfrom( $rt['ctfrom'] );
		$rt['tb_date'] = mysql2date( 'Y-m-d', $rt['tb_date'] );
		$rt['te_date'] = mysql2date( 'Y-m-d', $rt['te_date'] );
		//证书状态
		$rt['cert_status'] = $db->getField('certificate','status',array('eid'=>$rt['eid'],'iso'=>$rt['iso']));
/*         echo "SELECT status FROM sp_certificate WHERE eid = ".$rt['eid']." AND iso = ".$rt['iso'];
		echo "<br />"; */
		//证书有截止
		$tmp_sql = "SELECT e_date FROM sp_certificate 	WHERE eid = '".$rt['eid']."' AND iso = '".$rt['iso']."'";
		$tmp_res = $db->query($tmp_sql);
		$tmp_arr = array();
		while($tmp_rt = $db->fetch_array($tmp_res))
		{
		

			$tmp_arr = $tmp_rt;
		}
		$rt['cert_enddate'] = $tmp_arr['e_date'];
		if($rt[te_date]<=date("Y-m-d"))
		$rt[num]=mkdate($rt[te_date],date("Y-m-d"));
		if( '0000-00-00' == $rt['redata_date'] )
			$rt['redata_date'] = '';
		if( 0 == $rt['redata_uid'] )
			$rt['redata_uid'] = '';
		if( '0000-00-00' == $rt['to_jwh_date'] )
			$rt['to_jwh_date'] = '';

		if( '0000-00-00 00:00:00' == $rt['upload_done_date'] )
			$rt['upload_done_date'] = '';
		$rt[leader]=$db->getField("task_audit_team","name",array("role"=>'1001',"tid"=>$rt[tid]));
		if(!$redata_status)
		if($rt['bufuhe']){
			if($rt[num]>40)
				$rt['color']="red";
		}else{
			if($rt[num]>25)
				$rt['color']="red";
		
		}
		$resdb[$rt['id']] = $rt;
	}

	//获取所有有评定人员的数据
	$hr_arr = array();
	$hr_sql = "SELECT id,name FROM `sp_hr` WHERE 1  AND deleted = 0 AND job_type LIKE '%1004%'";
	$hr_query = $db->query($hr_sql);
	while($hr_rt = $db->fetch_array($hr_query))
	{
		$hr_arr[$hr_rt['id']] = $hr_rt;
	}
	
	
	if( !$export ){
		tpl( 'archive/flist' );
	} else { //导出excel表格
		ob_start();
		tpl( 'xls/list_archive' );
		$data = ob_get_contents();
		ob_end_clean();

		export_xls( '资料回收列表', $data );
	}