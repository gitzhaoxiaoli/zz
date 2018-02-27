<?php
/*
 *---------------------------------------------------------------
 * 自动生成下一个审核阶段
 *---------------------------------------------------------------
 */
// 半年前的时间段
$month_6 = thedate_add(date('Y-m-d H:i:s'), -6, 'month');
// 日志文件
set_time_limit(0);
$log_file = str_replace('\\', '/', dirname(__FILE__) . "/create_next-" . date('Y-m') . '.log');
// 本次生成记录数
$log_num  = 0;
// 下一阶段函数, 第一个参数：当前项目的审核类型，第二个参数：认证领域 CCC  工业品
function get_next_type($audit_type = '', $domain = '', $no_jd2 = 0)
{
    $result = '';
    if ($domain == 'b01001') {
        switch ($audit_type) {
            case '1001':
                $result = '1004-1-1';
                break;
            case '1004-1-1':
                $result = '1004-1-2';
                break;
            case '1004-1-2':
                $result = '1004-1-3';
                break;
            case '1004-1-3':
                $result = '1004-1-4';
                break;
            case '1004-1-4':
                $result = '1004-1-5';
                break;
            case '1004-1-5':
                $result = '1004-1-6';
                break;
            case '1004-1-6':
                $result = '1004-1-7';
                break;
            case '1004-1-7':
                $result = '1004-1-8';
                break;
            case '1004-1-8':
                $result = '1004-1-9';
                break;
            case '1004-1-9':
                $result = '1004-1-10';
                break;
            case '1004-1-10':
                $result = '1004-1-11';
                break;
            case '1004-1-11':
                $result = '1004-1-12';
                break;
            default:
                $result = '';
                break;
        }
    } elseif ($domain == 'b0200x') { //工业品生成下一阶段
        if ($no_jd2) {
            switch ($audit_type) {
                case '1001':
                    $result = '1004-1-1';
                    break;
            }
        } else {
            switch ($audit_type) {
                case '1001':
                    $result = '1004-1-1';
                    break;
                case '1004-1-1':
                    $result = '1004-1-2';
                    break;
            }
        }
    }
    //echo $result;
    return $result;
}
//$db->update('project',array('deleted'=>'1','create_next'=>'0'),array('audit_type'=>array('1004-1','1004-2','1004-3','1007')));
// ALTER TABLE `sp_project` ADD `create_next` TINYINT( 1 ) NOT NULL DEFAULT '0' COMMENT '是否生成下一阶段' AFTER `audit_type` 
/*
 *---------------------------------------------------------------
 * 下一轮监督
 *---------------------------------------------------------------
 */
// 取原初审、监一未生成的数据
$audit_type = array(
    '1001', //初审
    '1004-1-1',
    '1004-1-2',
    '1004-1-3',
    '1004-1-4',
    '1004-1-5',
    '1004-1-6',
    '1004-1-7',
    '1004-1-8',
    '1004-1-9',
    '1004-1-10',
    '1004-1-11'
);
$join       = " LEFT JOIN sp_task t ON p.tid = t.id";
$join .= " LEFT JOIN sp_contract_item cti ON p.cti_id=cti.cti_id";
$fields = ' p.*, t.te_date,cti.audit_ver';
$where  = "WHERE 1 AND p.deleted=0 AND p.audit_type IN ('" . implode("','", $audit_type) . "')AND p.create_next=0";
//时间搜索条件
//$where.="   AND t.te_date <= '$month_6' ";
//审定通过之后
$where .= " AND p.assess_status=3";
$where .= " AND ( cti.audit_ver!='b02001' and cti.audit_ver!='b02004')";
$sql   = "SELECT $fields FROM sp_project p $join $where ";
//echo $sql.="  AND t.te_date <= '$month_6' ";
$query = $db->query($sql);
while ($rt = $db->fetch_array($query)) {
    //读取项目表所有数据
    //获取当前项目的认证领域
    $domian = load('cti')->getCtiFieldById($rt['cti_id'], 'audit_ver');
    $no_jd2 = load('cti')->getCtiFieldById($rt['cti_id'], 'no_jd2');
	if($rt['te_date']=='0000-00-00'){ $rt['te_date']=load('cti')->getCtiFieldById($rt['cti_id'], 'old_audit_e_date');}
	
    $sql    = "SELECT COUNT(*) FROM sp_project WHERE cti_id = '$rt[cti_id]' AND audit_type = '" . get_next_type($rt[audit_type], $domian, $no_jd2) . "' AND deleted = 0";
    //如果下一阶段数据已经存在则更新状态
    if ($db->get_var($sql)) {
        load('audit')->edit($rt['id'], array(
            'create_next' => 1
        ));
        //如果下一阶段数据不存在则生成
    } else {
        //计算检查类型
        $audit_type = get_next_type($rt[audit_type], $domian, $no_jd2);
        if (!$audit_type) {
            continue; //防止工业品监督二生成再认证；同时过滤正确数据
        }
        // 取证书的范围
        $rt['scope']   = $db->get_var("SELECT cert_scope FROM sp_certificate WHERE pid={$rt['id']} ");
        $rt['scope_e'] = $db->get_var("SELECT cert_scope_e FROM sp_certificate WHERE pid={$rt['id']} ");
		//计算用户等级
		
		
        //获取证书范围
        $new           = array(
            'eid' => $rt['eid'],
            'ct_id' => $rt['ct_id'], // 批次ID
            'cti_id' => $rt['cti_id'], // 批次项目ID
            'ctfrom' => $rt['ctfrom'], // 项目所属单位
            'iso' => $rt['iso'], // 体系
            'audit_ver' => $rt['audit_ver'], // 版本
            'audit_code' => $rt['audit_code'], // 审核代码
            'use_code' => $rt['use_code'], // 使用代码
            'mark' => $rt['mark'], // 
            'ctfrom' => $rt['ctfrom'], // 
            'scope' => load('cert')->getCertFieldById(), // 
            'scope_e' => $rt['scope_e'], // 
            'pre_date' => get_addday($rt['final_date'], 9, -1), //计划日期
            'final_date' => get_addday($rt['final_date'], 12, -1), // 最晚审核日期
            'audit_type' => $audit_type, // 认证类型
            'status' => 5
        );
		//防爆如果没有监督2是半年
		if($no_jd2){
			$new['final_date']=get_addday($rt['te_date'], 18, -1); // 最后监审日;
			
		}
        // 生成下一阶段
        $new_id        = load('audit')->add(magic_gpc($new, 1));
        // 更新当前项目状态
        load('audit')->edit($rt['id'], array(
            'create_next' => 1
        ));
        //  $log_num++;
    }
}
// 日志是同名文件 .txt 含执行时间、生成记录数
/*$file_res = fopen($log_file, "a");
fwrite($file_res, date('Y-m-d H:i:s') . "		$log_num" . "\r\n");//写入一行 \n为换行
fclose($file_res);*/
/*
 *---------------------------------------------------------------
 * 监二生成再认证： 生成再认证 :取有机+GAP生成复评信息与工业品生成复评信息
 *---------------------------------------------------------------
 */
// 取原监二未生成的数据：  
$audit_type = array(
    '1001',
	'1004-1-1',
    '1004-1-2'
);
$fields     = $join = $where = ''; //清空前面数据的影响
$join       = " LEFT JOIN sp_task t ON p.tid = t.id";
$join .= " LEFT JOIN sp_contract_item cti ON p.cti_id=cti.cti_id";
$where = "WHERE p.tid!=0 AND p.deleted=0 AND p.audit_type IN ('" . implode("','", $audit_type) . "')  AND t.te_date <= '$month_6' AND p.create_next=0";
$where .= " and cti.audit_ver!='b01001'"; //排除3C
//$where.=" AND t.te_date <= '$month_6'";
$fields = " p.*, t.te_date ";
$sql    = "SELECT $fields FROM sp_project p $join $where ";
$query  = $db->query($sql);
while ($rt = $db->fetch_array($query)) {
    //如果下一阶段数据已经存在则更新状态 
    if ($db->find_num('ifcation', '', " and cti_id = '$rt[cti_id]' AND status = 0")) {
        load('audit')->edit($rt['id'], array(
            'create_next' => 1
        ));
        //如果下一阶段数据不存在则生成
    } else {
        $o_cert       = $db->find_one('certificate', " and cti_id = '$rt[cti_id]' AND status = 1", 'id,certno');
        //再认证数据
        $new_ifcation = array(
            'ctfrom' => $rt['ctfrom'],
            'eid' => $rt['eid'],
            //    'ct_id' => $rt['ct_id'],
            'cti_id' => $rt['cti_id'],
            'pid' => $rt['id'],
            'zs_id' => $o_cert['id'],
            'certno' => $o_cert['certno'],
            'create_date' => date('Y-m-d'),
            // 'e_date'=> $rt['te_date'],
            'status' => 0
        );
        //测试程序
        /*		p($new_ifcation);
        continue;*/
        // 生成下一阶段（再认证）
        $db->insert('ifcation', magic_gpc($new_ifcation, 1));
        // 更新状态
        load('audit')->edit($rt['id'], array(
            'create_next' => 1
        ));
        //$log_num++;
    }
}
// 日志是同名文件 .txt 含执行时间、生成记录数
/*$file_res = fopen($log_file, "a");
fwrite($file_res, date('Y-m-d H:i:s') . "		$log_num" . "\r\n");//写入一行 \n为换行
fclose($file_res);*/
