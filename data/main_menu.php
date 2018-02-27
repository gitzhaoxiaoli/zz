<?php
///////////////////////////////////////////////系统顶部导航 TOP.HTM /////////////////////////////////////
$top_nav           = array(
    'uc' => array(
        'name' => '个人中心',
        'icon' => 'a01',
        'src' => 'uc',
		'single' => array(
            array(
                'name' => '设置菜单',
                'url' => '?c=diy_menu',
            ),
			array(
                'name' => '修改密码',
                'url' =>'?c=sys&a=resetpw',
            ),
        ) 
    ),

    'main' => array( ///////////////系统主要业务
        'name' => '体系认证',		//业务名称
        'icon' => 'a00',			//业务图标
        'src' => 'main',			//暂时没有启用
        'single' => array(
            array(
                'name' => '人员行程',
                'url' => '?c=audit&a=list_hr_plan',
            ),
			array(
                'name' => '进度查询',
                'url' =>'?c=audit&a=progress',
            ),
            /* array(
                'name' => '项目查询',
                'url' =>'?c=audit&a=list_audit_project',
                'is_stop' => '',//权限，这个有什么用？
            ), */
        ) 
    ),
    'product' => array(
        'name' => '产品认证',		//业务名称
        'icon' => 'a00',			//业务图标
        'src' => 'main',			//暂时没有启用
        'single' => array(
            array(
                'name' => '人员行程',
                'url' => '?c=audit&a=list_hr_plan',
            ),
			array(
                'name' => '进度查询',
                'url' =>'?c=audit&a=progress',
            ),
        ) 
    ),


//OA业务

     'oasys' => array(
         'name' => 'OA业务',
         'icon' => 'a03',
         'src' => 'oasys',
    ),


);


/*************************企业 登陆***********************************/
$left_nav['en_enterprise']=array(
	'appName'=>'企业业务',
	'en_application'	=> array(
			'name'		=> '认证申请',
			'options'	=> array(
					array( '新申请', '?m=customer&c=contract&a=edit','1'),
                    array( '查看申请','?m=customer&c=contract&a=list','1'),
					array( '变更申请','?m=customer&c=change&a=alist','1'),
                    array( '查看变更申请','?m=customer&c=change&a=list','1'),
					array( '查看进度', '?m=customer&c=contract&a=list_audit_project' ,'0'),
					// array( 'word空表', '?m=customer&c=contract&a=list_word' ,'1'),
					array( '修改密码', '?m=customer&c=sys&a=resetpw' ,'1'),
					// array( '下载审核资料', '?m=customer&contract&a=file_list' ,'1'),
			)
	),
	
);

///////////////////////实验室///////////////////////////////////////////////////
$left_nav['testorg']=array(
	'appName'=>'实验室',
	'en_application'	=> array(
			'name'		=> '认证申请',
			'options'	=> array(
					array( '初始工厂检测', '?m=testorg&c=test&a=list_wait_test','1'),
                    array( '监督抽样任务','?m=testorg&c=test&a=sample_list','1'),
                    array( '检测资料复核','?m=testorg&c=test&a=review','1'),
					array( '修改密码', '?m=testorg&c=sys&a=resetpw' ,'1'),
					// array( '下载审核资料', '?m=customer&contract&a=file_list' ,'1'),
			)
	),
);
/////////////////////////////////////系统主要左部导航配置/////////////////////////


$left_nav['oasys'] = array(
    'appName' => 'OA管理',
    'oa' => array(
        'name' => 'OA管理',
        'options' => array(
            array(
                '公告管理',
                '?c=notice&a=list',
                '1',
            ),
            array(
                '文档管理(上传文档)',
                '?c=files&a=list',
                '1',
            ),
			array(
                '文档查询',
                '?c=files&a=dlist',
                '1',
            ),
			array(
                '文档列表',
                '?c=file_list',
                '1',
            ),

        )
    )
);

//体系认证项目配置信息=====================================================================================
/*

体系认证

*/
$left_nav['main']  = array(
    'appName' => '体系认证',
    'enterprise' => array(
        'name' => '企业管理',
        'options' => array(


			/*	
			**************仅仅用于权限**********

            array(
                '人员行程',
                '?c=audit&a=list_hr_plan',
                '0',
            ),
            array(
                '进度查询',
                '?c=audit&a=progress',
                '0',
            ),
			*/

            array(
                '企业信息登记',
                '?c=enterprise&a=add',
                '1',
            ),
            array(
                '企业信息查询',
                '?c=enterprise&a=list',
                '1',
            ),
            array(
                '资质证书到期',
                '?c=enterprise&a=zz_list',
                '1',
            ),
            array(
                '企业文档查询',
                '?c=enterprise&a=list_attach',
                '1',
            ),
            array(
                '企业信息编辑',
                '?c=enterprise&a=edit',
                '0',
            ),
            array(
                '企业信息删除',
                '?c=enterprise&a=del',
                '0',
            ),
            array(
                '企业文档删除',
                '?c=enterprise&a=delattach',
                '0',
            ),
           array(
                '合作单位登记',
                '?c=cooperation&a=add',
                '1',

            ),
            array(
                '合作单位查询',
                '?c=cooperation&a=list_attach',
                '1',

            ),
        )
    ),
    'contract' => array(
        'name' => '合同管理',
        'options' => array(
            array(
                '合同登记',
                '?c=contract&a=alist',
                '1',
            ),
            array(
                '合同编辑',
                '?c=contract&a=edit',
                '0',
            ),
            array(
                '合同查询',
                '?c=contract&a=list',
                '1',
            ),
            array(
                '变更评审及所有项目',
                '?c=contract&a=add_review',
                '1',
                '?c=contract&a=edit_scope',
            ),

            array(
                '合同费用登记',
                '?c=cost&a=add_list',
                '1',
            ),
            array(
                '合同费用查询',
                '?c=cost&a=list',
                '1',
            ),

			 /* array('专业对照','?c=contract&a=use_code','1',),*/

            array(
                '合同评审',
                '?c=contract&a=review',
                '0',
            ),
            array(
                '合同审批',
                '?c=contract&a=approval',
                '0',
            ),
            array(
                '合同删除',
                '?c=contract&a=del',
                '0',
            ),
            array(
                '企业文档上传',
                '?c=contract&a=upload',
                '0',
            )
        )
    ),
    'preserve' => array(
        'name' => '客服维护',
        'options' => array(
            array(
                '监督维护',
                '?c=audit&a=list_super',
                '1',
				'?c=audit&a=edit_super',
            ),
            array(
                '再认证维护',
                '?c=audit&a=list_ifcation',
                '1',
            ),

            array(
                '监督维护删除',
                '?c=audit&a=del',
                '0',
            ),
            array(
                '再认证维护登记',
                '?c=audit&a=add',
                '0',
            ),
            array(
                '再认证维护编辑',
                '?c=audit&a=edit_ifcation',
                '0',
            )
        )
    ),

    'auditarrange' => array(
        'name' => '审核安排',
        'options' => array(
            array(
                '未安排项目',
                '?c=audit&a=list_wait_arrange',
                '1',
            ),
            array(
                '审核计划',
                '?c=task&a=list&status=1',
                '1',
            ),
            /* array(
                '文审派人',
                '?c=task&a=wshen',
                '1',
            ),
            array(
                '文审查询',
                '?c=archive&a=ws_list',
                '1',
            ), */
            array(
                '审核员计划审批',
                '?c=task&a=list_plan',
                '1',
            ),
            array(
                '审核项目查询',
                '?c=audit&a=list_audit_project',
                '1',
            ),
            array(
                '项目派人查询',
                '?c=audit&a=project_send_query',
                '1',
            ),
			array(
                '增加特殊审核项',
                '?c=audit&a=list_contract_item',
                '1',
                '?c=audit&a=edit_item',
            ),
			/*   
			 array(
                '导出劳务费计算表',
                '?c=audit&a=create_labor_cost',
                '1'
            ),
			*/
            array(
                '审批项目',
                '?c=audit&a=edit_approval',
                '0',
            ),
            array(
                '审核项目删除',
                '?c=audit&a=del',
                '0',
            ),
            array(
                '项目派人',
                '?c=audit&a=edit_send',
                '0',
            ),

        )
    ),
    'assess' => array(
        'name' => '评定管理',
        'options' => array(
            array(
                '资料收回',
                '?c=archive&a=list',
                '1',
            ), 
            array(
                '资料分派',
                '?c=archive&a=flist',
                '1',
            ),
            array(
                '登记资料收回',
                '?c=archive&a=edit',
                '0',
            ),
            array(
                '认证评定',
                '?c=assess&a=list',
                '1',
            ),
            array(
                '认证决定',
                '?c=assess&a=list&acc=jd',
                '1',
            ),
            array(
                '认证评定操作',
                '?c=assess&a=edit',
                '0',
            ),
            array(
                '评定问题',
                '?c=assess&a=question',
                '1',
            ),
			/*  array('评分标准','?c=assess&a=ver_list','1'), */
        )
    ),
    'cert' => array(
        'name' => '证书管理',
        'options' => array(
            array(
                '证书登记',
                '?c=certificate&a=alist',
                '1',
                '?c=certificate&a=edit',
            ),
            /*array(
                '监督发证',
                '?c=certificate&a=list_super',
                '1',
                '?c=certificate&a=edit_super'
            ),
			array(
                '审批未通过',
                '?c=certificate&a=lists',
                '1',
                '?c=certificate&a=edit'
            ),
            array(
                '证书审批',
                '?c=certificate&a=approval_list',
                '1',
                '?c=certificate&a=edit'
            ),
			 */
			array(
                '证书查询',
                '?c=certificate&a=list',
                '1',
                '?c=certificate&a=edit',
            ),
            array(
                '证书邮寄',
                '?c=certificate&a=elist',
                '1',
            ),
            array(
                '证书邮寄操作',
                '?c=certificate&a=eedit',
                '0',
            ),

			/*array(
                '能源绩效',
                '?c=certificate&a=energy_list',
                '1',
		        'soft_tx'

            ),*/
			/*  array(
                'pdf扫描归档',
                '?c=certificate&a=save_file',
                '1',

            ),
			 array(
                '报告邮寄',
                '?c=task&a=task_elist',
                '1'
            ),
           array(
                '监督维护不接受',
                '?c=certificate&a=list_super',
                '1',
                '?c=change&a=add|?c=change&a=save'
            ),
			array(
                '再认证维护不接受',
                '?c=certificate&a=list_ifcation',
                '1',
                '?c=change&a=add|?c=change&a=save'
            ), */

			
			array(
                '应暂停项目',
                '?c=certificate&a=pushed',
                '1',
            ),

            array(
                '应恢复证书',
                '?c=certificate&a=restore',
                '1',
            ),
			
            array(
                '应撤销证书',
                '?c=certificate&a=annul',
                '1',
            ),
            array(
                '监督审核合格通知书',
                '?c=certificate&a=keep_decide',
                '1',
            ),
			/*
             array(
                '监督邮寄',
                '?c=certificate&a=audit_elist',
                '1',

            ), 
			*/
			array(
                '证书删除',
                '?c=certificate&a=del',
                '0',
            ),
        )
    ),
    'change' => array(
        'name' => '变更管理',
        'options' => array(
            array(
                '证书变更',
                '?c=certificate&a=clist',
                '1',
            ),
            array(
                '证书变更查询',
                '?c=change&a=list&status=0',
                '1',
            ),
            array(
                '证书变更删除',
                '?c=change&a=del',
                '0',
            ),
            array(
                '证书变更操作',
                '?c=change&a=add|?c=change&a=save',
                '0',
            ),
        )
    ),
    'finance' => array(
        'name' => '财务收费',
        'options' => array(
            array(
                '财务收费登记',
                '?c=finance&a=plist',
                '1',
                '?c=finance&a=edit|?c=finance&a=save',
            ),
            array(
                '财务收费明细',
                '?c=finance&a=dlist',
                '1',
                '?c=finance&a=edit|?c=finance&a=save',
            ),
            array(
                '费用收完标识',
                '?c=finance&a=Project',
                '1',
            ),
            /*array(
                '财务发票邮寄',
                '?c=finance&a=elist',
                '1',
            ),
			array(
                '培训收费',
                '?c=finance&a=p_list&is_finance=0',
                '1',
            )*/
        )
    ),
    'people' => array(
        'name' => '人力资源',
        'options' => array(
            array(
                '人员登记',
                '?c=hr&a=edit',
                '1',
            ),
            array(
                '人员查询',
                '?c=hr&a=list',
                '1',
            ),


			/*
			array( '人员信誉登记', '?c=hr&a=alist','0'),
			array( '人员信誉查询', '?c=hr&a=credit','0'),
			*/

            array(
                '注册资格登记',
                '?c=hr_qualification&a=alist',
                '1',
                '?c=hr_qualification&a=edit',
            ),
            array(
                '注册资格查询',
                '?c=hr_qualification&a=list&status=1',
                '1',
            ),
            array(
                '注册资格到期',
                '?c=hr_qualification&a=dq_list&status=1',
                '1',
            ),
            array(
                '业务代码登记',
                '?c=hr_code&a=alist',
                '1',
                '?c=hr_code&a=edit',
            ),
            array(
                '业务代码查询',
                '?c=hr_code&a=list',
                '1',
            ),

            array(
                '人员专业经历查询',
                '?c=hr_exp&a=glist',
                '1',
            ),
            array(
                '审核经历查询',
                '?c=audit&a=project_send_query',
                '1',
            ),
            array(
                '业务代码删除',
                '?c=hr_code&a=del',
                '0',
            ),
            array(
                '人员专业经历删除',
                '?c=hr_exp&a=gdel',
                '0',
            ),
            array(
                '人员删除',
                '?c=hr&a=del',
                '0',
            ),

			/*
            array(
                '业务代码申请管理',
                '?c=hr_code&a=clist',
                '1',
                '?c=hr_code&a=app_edit',
            ),
            array(
                '小类申请删除',
                '?c=auditor&a=app_del',
                '0',
            ),
			*/
			/*
			array(
                '专业管理人员/技术专家维护',
                '?c=hr&a=stff',
                '1',

            ),
			*/
			array(
                '请假登记',
                '?c=hr&a=leave_hr_list',
                '1',
            ),
            array(
                '请假查询',
                '?c=hr&a=leave_list',
                '1',
            ),
            array(
                '培训登记',
                '?c=hr&a=train_hr_list',
                '1',
            ),
            array(
                '培训查询',
                '?c=hr&a=train_list',
                '1',
            ),

        )
    ),
    'auditor' => array(
        'name' => '审核员',
        'options' => array(
            array(
                '我的任务',
                '?c=auditor&a=task',
                '1',
            ),
            array(
                '我的资料',
                '?c=auditor&a=my',
                '1',
            ),
            array(
                '注册资格',
                '?c=auditor&a=reg',
                '1',
            ),
            array(
                '专业能力',
                '?c=auditor&a=code',
                '1',
            ),
            array(
                '专业经历',
                '?c=experience&a=glist',
                '1',
            ),
            /* array(
                '请假登记',
                '?c=auditor&a=leave_edit',
                '0',

            ),
            array(
                '请假查询',
                '?c=auditor&a=leave_list',
                '0',

            ), */
            array(
                '审核任务操作',
                '?c=auditor&a=task_edit',
                '0',
            ),
            array(
                '审核任务文档上传',
                '?c=auditor&a=upfile',
                '0',
            ),
            array(
                '审核任务审核信息沟通',
                '?c=auditor&a=task_save',
                '0',
            ),
            array(
                '审核任务评定问题',
                '?c=auditor&a=task_finish',
                '0',
            ),
            array(
                '文档上传',
                '?c=auditor&a=upattach',
                '0',
            ),
            array(
                '添加专业经历',
                '?c=experience&a=gedit',
                '0',
                '?c=experience&a=gsave',
            ),
            array(
                '添加教育经历',
                '?c=experience&a=jedit',
                '0',
                '?c=experience&a=jsave',
            ),
            array(
                '添加审核经历',
                '?c=experience&a=sedit',
                '0',
                '?c=experience&a=ssave',
            ),
            array(
                '添加培训经历',
                '?c=experience&a=pedit',
                '0',
                '?c=experience&a=psave',
            ),
            array(
                '查看教育经历',
                '?c=experience&a=jlist',
                '0',
            ),
            array(
                '查看审核经历',
                '?c=experience&a=slist',
                '0',
            ),
            array(
                '查看培训经历',
                '?c=experience&a=plist',
                '0',
            ),
            array(
                '查看培训经历',
                '?c=experience&a=glist',
                '0',
            ),
            array(
                '审核员上传头像 ',
                '?c=hr&a=uphrphoto',
                '0',
            )
        )
    ),

	
	/* 'in_auditor' => array(
        'name' => '内审员培训',
        'options' => array(
			array(
                '企业信息登记',
                '?c=in_auditor&a=dealer_edit',
                '1',

            ),
			array(
                '企业信息查询',
                '?c=in_auditor&a=dealer_list',
                '1',

            ),
			array(
                '培训登记',
                '?c=in_auditor&a=contract_dealer_list',
                '1',

            ),
			array(
                '培训查询',
                '?c=in_auditor&a=contract_list',
                '1',

            ),
			array(
                '培训证书登记',
                '?c=in_auditor&a=certificate_dealer_list',
                '1',

            ),
			array(
                '培训证书查询',
                '?c=in_auditor&a=certificate_list',
                '1',

            ),
		),
	), */


	'export' => array(
        'name' => '报表管理',
        'options' => array(
            array(
                '月报导出',
                '?c=export&a=report',
                '1',
            ),
            array(
                '审核计划上报',
                '?c=export&a=plan_report',
                '1',
				'?c=audit&a=audit_plan_reoprt',
            ),
            array(
                '证书年报',
                '?c=export&a=year_report',
                '1',
            ),
            array(
                '审核员年报',
                '?c=export&a=auditor_report',
                '1',
            ),
            array(
                '合同同期比较',
                '?c=export&a=contract',
                '1',
            ),
            array(
                '证书同期比较',
                '?c=export&a=certificate',
                '1',

            ),
            array(
                '导出企业信息',
                '?c=xls&a=guide',
                '1',
            ),

			 /*array(
                '审核工作汇总表',
                '?c=task&a=task_report',
                '1',
				'soft_tx'

            ),*/
            /*array(
                '01小类同期比较',
                '?c=export&a=prod_01',
                '1',

            ),
            array(
                '03小类同期比较',
                '?c=export&a=prod_03',
                '1',

            ),
            array(
                '10小类同期比较',
                '?c=export&a=prod_10',
                '1',

            ),
			array(
                '证书小类比较',
                '?c=export&a=prod_id',
                '1',

            ),     array(
                '检查结果统计表',
                '?c=export&a=report_task',
                '1',

            ),      array(
                '上传证书信息',
                '?c=export&a=webset_cert',
                '1',

            ),*/ 

        )
    ),
    'system' => array(
        'name' => '系统管理',
        'options' => array(
			array(
                '系统配置',
                '?c=setting',
                '1',
            ),
            array(
                '权限管理',
                '?c=sys&a=list',
                '1'
            ),
            array(
                '系统日志',
                '?c=sys&a=loglist',
                '1'
            ),
            array(
                '计划任务',
                '?c=cron&a=list',
                '1'
            ),
            /*array(
                '数据导入',
                '?m=imp&c=imp',
                '1'
            ),

			
			array( '项目标准版本-体系', '?c=check&a=Project' ,'1'),
			array( '检测监察-计划时间', '?c=check&a=Super' ,'1'),
			*/
        )
    ),
    'backend' => array(
        'name' => '后台修改数据',
        'options' => array(
            array(
                '修改合同',
                '?c=backend&a=list_contract',
                '1',
            ),
            array(
                '修改项目',
                '?c=backend&a=list_project',
                '1',
            ),
            array(
                '修改任务',
                '?c=backend&a=list_task',
                '1'
            ),
            
        )
    )

);



//产品认证项目配置信息=====================================================================================
/********************
*********************
*********************@LYH 2016-03-20
*********************产品认证
*********************
************************/


$left_nav['product']  = array(
    'appName' => '产品认证',
    'enterprise' => array(
        'name' => '产品企业管理',
        'options' => array(
            array(
                '企业信息登记',
                '?c=enterprise&a=add',
                '1',
            ),
            array(
                '企业信息查询',
                '?c=enterprise&a=list',
                '1',
            ),
            array(
                '资质证书到期',
                '?c=enterprise&a=zz_list',
                '1',
            ),
            array(
                '企业文档查询',
                '?c=enterprise&a=list_attach',
                '1',
            ),
            array(
                '企业信息编辑',
                '?c=enterprise&a=edit',
                '0',
            ),
            array(
                '企业信息删除',
                '?c=enterprise&a=del',
                '0',
            ),
            array(
                '企业文档删除',
                '?c=enterprise&a=delattach',
                '0',
            ),
            /*
             array(
                '合作单位登记',
                '?c=cooperation&a=add',
                '1',

            ),
            array(
                '合作单位查询',
                '?c=cooperation&a=list_attach',
                '1',

            ), 
			
            array(
                '企业密码管理',
               '?c=enterprise&a=p_word',
                '1',
            ),
            */
        )
    ),
    'contract' => array(
        'name' => '申请受理',
        'options' => array(
			array(
                '申请单元',
                '?m=product&c=contract&a=alist',
                '1',
            ),
			array(
                '单元查询',
                '?m=product&c=contract&a=list',
                '0',
            ),
			array(
                '申请评审',
                '?m=product&c=contract&a=approval',
                '1',
            ),
			array(
                '认证方案',
                '?m=product&c=contract&a=protocol_list',
                '1',
            ),

			/*
            array(
                '变更评审及所有项目',
                '?c=contract&a=add_review',
                '1',
                '?c=contract&a=edit_scope',
            ),
			*/


            array(
                '合同费用登记',
                '?c=cost&a=add_list',
                '1',
            ),
            array(
                '合同费用查询',
                '?c=cost&a=list',
                '1',
            ),
             array(
                '企业文档上传',
                '?c=contract&a=upload',
                '0',
            )
        )
    ),
  
	'prodarrange' => array(
        'name' => '产品检验',
        'options' => array(
            array(
                '检验安排',
                '?m=product&c=test&a=list_wait_test',
                '1',
            ),
			array(
                '检验结果',
                '?m=product&c=test&a=list_wait_res',
                '1',
            ),
            /*说明:停止使用*/
            /*@zys 2016-5-6*/
            /*
			array(
                '产品抽样',
                '?m=product&c=test&a=sample_list',
                '1',
            ),
            */
			// array(
                // '检验评价',
                // '?m=product&c=test&a=list',
                // '1',
		        // 'soft_cp'
            // ),
			array(
                '检测资料复核',
                '?m=product&c=test&a=review',
                '1',
            ),
		)
	),
    'auditarrange' => array(
        'name' => '审核安排',
        'options' => array(
 			array(
                '检查安排',
                '?m=product&c=audit&a=list_wait_arrange',
                '1',
            ),
			/*array(
                '已安排项目',
                '?m=product&c=task&a=list_arrange',
                '1',
            ),*/
			array(
                '检查派人',
                '?m=product&c=task&a=list&status=1',
                '1',
            ),
            array(
                '产品项目查询',
                '?m=product&c=audit&a=list_audit_project',
                '1',
            ),
 			array(
                '派人查询',
                '?m=product&c=audit&a=project_send_query',
                '1',
            ),
            array(
                '增加特殊审核项',
                '?m=product&c=audit&a=list_contract_item',
                '1',
                '?m=product&c=audit&a=edit_item',
            ),
/* 
            array(
                '审批项目',
                '?c=audit&a=edit_approval',
                '0',

            ),
            array(
                '审核项目删除',
                '?c=audit&a=del',
                '0',

            ),
            array(
                '项目派人',
                '?c=audit&a=edit_send',
                '0',

            ),
            array(
                '项目派人查询(操作)',
                '?c=audit&a=project_send_query',
                '0',

            ),
            array(
                '人员行程',
                '?c=audit&a=list_hr_plan',
                '0',

            ),			
			 */
        )
    ),
    'assess' => array(
        'name' => '评定管理',
        'options' => array(
			array(
                '产品资料收回',
                '?m=product&c=archive&a=list',
                '1',
            ), 
            array(
                '产品资料分派',
                '?c=archive&a=flist',
                '0',
            ),
            array(
                '产品登记资料收回',
                '?m=product&c=archive&a=edit',
                '0',
            ),
			array(
                '检查资料复核',
                '?m=product&c=assess&a=review',
                '1',
            ),
            array(
                '资料分派',
                '?m=product&c=archive&a=flist',
                '1',
            ),
            array(
                '产品审定管理',
                '?m=product&c=assess&a=list',
                '1',
            ),
            array(
                '总经理审批',
                '?m=product&c=assess&a=approval',
                '1',
				'?m=product&c=assess&a=approval_edit',
            ),
        )
    ),
    'cert' => array(
        'name' => '证书管理',
        'options' => array(
			array(
                '产品打印证书',
                '?m=product&c=certificate&a=alist',
                '1',
                '?m=product&c=certificate&a=edit',
            ),
			array(
                '产品证书查询',
                '?m=product&c=certificate&a=list',
                '1',
                '?m=product&c=certificate&a=edit',
            ),

            array(
                '证书邮寄',
                '?c=certificate&a=elist',
                '1',
				'?c=certificate&a=eedit',
            ),


			/*  array(
                'pdf扫描归档',
                '?c=certificate&a=save_file',
                '1',

            ),
			 array(
                '报告邮寄',
                '?c=task&a=task_elist',
                '1'
            ),
           array(
                '监督维护不接受',
                '?c=certificate&a=list_super',
                '1',
                '?c=change&a=add|?c=change&a=save'
            ),
			array(
                '再认证维护不接受',
                '?c=certificate&a=list_ifcation',
                '1',
                '?c=change&a=add|?c=change&a=save'
            ), */
			/*
            array(
                '暂停后未处理项目',
                '?m=product&c=certificate&a=handle',
                '1',
                '?m=product&c=change&a=add',

            ),
			*/
			
			array(
                '应暂停项目',
                '?c=certificate&a=pushed',
                '1',
                '?m=product&c=change&a=add|?m=product&c=change&a=save',
            ),
            array(
                '应恢复证书',
                '?c=certificate&a=restore',
                '1',
                '?c=change&a=add',
            ),
            array(
                '应撤销证书',
                '?c=certificate&a=annul',
                '1',
                '?c=change&a=add',
            ),
            array(
                '保持通知书',
                '?c=certificate&a=keep_decide',
                '1',
                '?c=change&a=add',
            ),
			/*
             array(
                '监督邮寄',
                '?c=certificate&a=audit_elist',
                '1',
            ), 
			*/
			array(
                '证书删除',
                '?c=certificate&a=del',
                '0',
            ),

        )
    ),
    'change' => array(
        'name' => '变更管理',
        'options' => array(
			/* array(
                '企业申请变更',
                '?m=product&c=change&a=app_list',
                '1',
            ), */
			array(
                '产品证书变更',
                '?m=product&c=certificate&a=clist',
                '1',
            ),
            array(
                '产品证书变更查询',
                '?m=product&c=change&a=list&status=0',
                '1',
            ),
            array(
                '证书变更删除',
                '?c=change&a=del',
                '0',
            ),
            array(
                '证书变更操作',
                '?c=change&a=add|?c=change&a=save',
                '0',
            ),
        )
    ),
    'finance' => array(
        'name' => '财务收费',
        'options' => array(
            array(
                '财务收费登记',
                '?c=finance&a=plist',
                '1',
                '?c=finance&a=edit|?c=finance&a=save',
            ),
            array(
                '财务收费明细',
                '?c=finance&a=dlist',
                '1',
            ),
            array(
                '项目查询',
                '?c=finance&a=Project',
                '1',
            ),
            /*array(
                '财务发票邮寄',
                '?c=finance&a=elist',
                '1',
            ),
			array(
                '培训收费',
                '?c=finance&a=p_list&is_finance=0',
                '1',
            )*/
        )
    ),
    'people' => array(
        'name' => '人力资源',
        'options' => array(
            array(
                '人员登记',
                '?c=hr&a=edit',
                '1',
            ),
            array(
                '人员查询',
                '?c=hr&a=list',
                '1',
            ),


			/*
			array( '人员信誉登记', '?c=hr&a=alist','0'),
			array( '人员信誉查询', '?c=hr&a=credit','0'),
			*/

            array(
                '注册资格登记',
                '?c=hr_qualification&a=alist',
                '1',
                '?c=hr_qualification&a=edit',
            ),
            array(
                '注册资格查询',
                '?c=hr_qualification&a=list&status=1',
                '1',
            ),
            array(
                '注册资格到期',
                '?c=hr_qualification&a=dq_list&status=1',
                '1',
            ),
            array(
                '业务代码登记',
                '?c=hr_code&a=alist',
                '1',
                '?c=hr_code&a=edit',
            ),
            array(
                '业务代码查询',
                '?c=hr_code&a=list',
                '1',
            ),
			/*
            array(
                '业务代码申请管理',
                '?c=hr_code&a=clist',
                '1',
                '?c=hr_code&a=app_edit',

            ),
			*/
            array(
                '人员专业经历查询',
                '?c=hr_exp&a=glist',
                '1',
            ),
            array(
                '审核经历查询',
                '?c=audit&a=project_send_query',
                '1',
            ),
            array(
                '业务代码删除',
                '?c=hr_code&a=del',
                '0',
            ),
            array(
                '人员专业经历删除',
                '?c=hr_exp&a=gdel',
                '0',
            ),
            array(
                '人员删除',
                '?c=hr&a=del',
                '0',
            ),
			array(
                '请假登记',
                '?c=hr&a=leave_hr_list',
                '1',
            ),
            array(
                '请假查询',
                '?c=hr&a=leave_list',
                '1',
            ),
            array(
                '培训登记',
                '?c=hr&a=train_hr_list',
                '1',
            ),
            array(
                '培训查询',
                '?c=hr&a=train_list',
                '1',
            ),
        )
    ),
    'auditor' => array(
        'name' => '产品检查员',
        'options' => array(
			array(
                '产品审查任务',
                '?m=product&c=auditor&a=task',
                '1',
            ),
            array(
                '我的资料',
                '?c=auditor&a=my',
                '1',
            ),
            array(
                '注册资格',
                '?c=auditor&a=reg',
                '1',
            ),
            array(
                '专业能力',
                '?c=auditor&a=code',
                '1',
            ),
            array(
                '专业经历',
                '?c=experience&a=glist',
                '1',
            ),
            /* array(
                '请假登记',
                '?c=auditor&a=leave_edit',
                '0',

            ),
            array(
                '请假查询',
                '?c=auditor&a=leave_list',
                '0',

            ), */
            array(
                '审核任务操作',
                '?c=auditor&a=task_edit',
                '0',
            ),
            array(
                '评定任务操作',
                '?c=auditor&a=edit',
                '0',
            ),
            array(
                '审核任务文档上传',
                '?c=auditor&a=upfile',
                '0',
            ),
            array(
                '审核任务审核信息沟通',
                '?c=auditor&a=task_save',
                '0',
            ),
            array(
                '审核任务评定问题',
                '?c=auditor&a=task_finish',
                '0',
            ),
            array(
                '文档上传',
                '?c=auditor&a=upattach',
                '0',
            ),
            array(
                '添加专业经历',
                '?c=experience&a=gedit',
                '0',
                '?c=experience&a=gsave',
            ),
            array(
                '添加教育经历',
                '?c=experience&a=jedit',
                '0',
                '?c=experience&a=jsave',

            ),
            array(
                '添加审核经历',
                '?c=experience&a=sedit',
                '0',
                '?c=experience&a=ssave',
            ),
            array(
                '添加培训经历',
                '?c=experience&a=pedit',
                '0',
                '?c=experience&a=psave',
            ),
            array(
                '查看教育经历',
                '?c=experience&a=jlist',
                '0',
            ),
            array(
                '查看审核经历',
                '?c=experience&a=slist',
                '0',
            ),
            array(
                '查看培训经历',
                '?c=experience&a=plist',
                '0',
            ),
            array(
                '查看培训经历',
                '?c=experience&a=glist',
                '0',
            ),
            array(
                '审核员上传头像 ',
                '?c=hr&a=uphrphoto',
                '0',
            )
        )
    ),


	'export' => array(
        'name' => '报表管理',
        'options' => array(
			array(
                '产品报表',
                '?c=export&a=prod_report',
                '1',
            ),
            array(
                '劳务费导出',
                '?c=export&a=labor_fee',
                '1',
            ),
			array(
                '财务费用统计',
                '?m=product&c=finance&a=clist',
                '1',
            ),


            array(
                '合同同期比较',
                '?c=export&a=contract',
                '1',
            ),
            array(
                '证书同期比较',
                '?c=export&a=certificate',
                '1',
            ),
            /*array(
                '上传证书信息',
                '?c=export&a=webset_cert',
                '1',

            ),*/ 
			  

        )
    ),
    'system' => array(
        'name' => '系统管理',
        'options' => array(

			array(
                '产品系统配置',
                '?c=setting&a=com',
                '1',
            ),
			array(
                '检测机构',
                '?m=com&c=org',
                '1',
            ),
            array(
                '权限管理',
                '?c=sys&a=list',
                '1'
            ),
            array(
                '系统日志',
                '?c=sys&a=loglist',
                '1'
            ),
            array(
                '计划任务',
                '?c=cron&a=list',
                '1'
            ),
			   
        )
    )
	    

);