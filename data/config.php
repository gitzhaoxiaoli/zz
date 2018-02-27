<?php
//系统配置信息 //功能：适合新的环境
return array( 
 	//认证机构信息
 	'zdep_id'		=>'CNCA-R-2017-293',	//机构批准号
 	'zdep_name'		=>'奥鹏认证有限公司',	//机构批准号
	'proj_code'=>'bscc', //项目编码--数据库表名-数据源名称bscc_source
  	//////////////////////////////上传配置/////////////////////////////////////////// 
 	'upload_ep_dir'		=> 'uploads/ep/',//企业上传文档路径
	'upload_hr_dir'		=> 'uploads/hr/',//人员文档上传路径
	'upload_zz_dir'     => 'uploads/zz/',//资质证书上传路径
	'upload_pro_dir'     => 'uploads/pro/',//产品文档上传
 	'upload_hr_photo_dir'=> 'uploads/hr_photo/',//人员头像上传路径
	'upload_oa_file_dir' =>'uploads/file/',//oa文档上传路径
	'upload_notice_dir'	=>'uploads/notice/', //公告上传路径
	'upload_train_dir'	=>'uploads/train/', //内审员培训登记上传路径
	'prod_ver_url'	=>'uploads/setting/prod_ver/', //配置
	'upload_temp_dir'	=>'uploads/temp/', //临时文件
	'uploadExts'	=> array('jpg', 'jpeg', 'gif', 'png', 'xls', 'xlsx', 'zip', 'rar', 'doc', 'docx', 'pdf','rtf'),//上传类型限制
	'uploadSize'	=> 102400000,//上传大小 100mb
	 	/////////////////////////////软件信息配置////////////////////////////////////////
 	'softname'		=> '认证行业管理信息系统', //软件名称
	'version'		=>'5.0',			//软件版本 
		//////////////////////////////获取组织机构代码///////////////////
	'orgUser'		=>'ZhoZ1230',//账号
	'orgPasd'		=>"D29(dwx*pl",	//密码
	'orgToken'		=>"3261D8A2F7C14475AE34273487320BE4",	//固定密钥
	
	'zdep_ip'		=>'123.146.193.125',	
	/////////是否开启右上角菜单///////////////////////////
	'is_uc'  		=> '1',//1	个人中心,所有人都有权限
	'is_main'  		=> '1',//1	体系认证 
	'is_oasys'  		=> '1',//1	体系认证 

); 


 


  