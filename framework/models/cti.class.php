<?php
//批次项目表操作模型
class cti extends model
{
    private $tbl = 'contract_item'; //表名全称
    private $short_tbl = 'cti'; //表名简写
    //有机产品产品设置 
    public $oga_prod_set = array();
    //有机产品植物生产下面小类
    public $plant_year = array('1' => '一年生', 'n' => '多年生');
    //有机产品下面养殖转换期问题
    public $chang_date = array('1' => ' 肉用牛、马属动物，驼，12个月', '2' => ' 肉用羊和猪，6个月', '3' => '乳用畜，6个月', '4' => ' 肉用家禽,10周', '5' => '蛋用家禽，6周', '6' => '其他种类的转换期长于其养殖期的四分之三');
     //获取有机产品
    function get_oga_prod()
    {
        return $this->oga_prod_set = load('set')->get_set_datas('settings_prod', array(
            'prod_type' => 'b02001'
        ), '*', 'vieworder desc,');
    }
    //从配置中获取GAP产品
    function get_gap_prod()
    {
        return $this->oga_prod_set = load('set')->get_set_datas('settings_prod', array(
            'prod_type' => 'b02004'
        ), '*', 'vieworder desc,');
    }
    //根据批次项目表主键获取单个属性字段值，默认为 cti_codes
    public function getCtiFieldById($id_val, $field_name = 'cti_code')
    {
        $rs = $this->get_var(" SELECT $field_name from sp_$this->tbl WHERE cti_id='$id_val'"); 
        return $rs;
    }
    //数量统计
    public function getTotalByField($field_name = '', $where = '', $joins = '')
    {
        if (is_array($where)) {
            foreach ($where as $key => $val) {
                $wheresql[] = " AND {$key}='{$val}'";
            }
            $where = implode(' ', $wheresql);
        }
        $where .= " AND {$this->short_tbl}.deleted = 0";
        $query = $this->query("SELECT {$this->short_tbl}.{$field_name},COUNT(*) total FROM sp_{$this->tbl} {$this->short_tbl} {$joins} WHERE 1  {$where} GROUP BY {$this->short_tbl}.{$field_name}");
        while ($rt = $this->fetch_array($query)) {
            $total[$rt[$field_name]] = $rt['total'];
        }
        return $total;
    }
	//认证领域搜索
	
	function search_domain(){
		//表示认证领域的两个字段： audit_ver type
		$temp=''; 
		if ($_GET['audit_ver']) {
			$temp= " AND cti.audit_ver='" . $_GET['audit_ver'] . "'";
		} 
		if($_GET['type']){
			$temp= " AND cti.audit_ver='" . $_GET['type'] . "'";
 		} 
		return $temp;
 	}
 	
    //列表--工厂方法： 抽象接口： 具体实现类 cti_org  cti_ccc 调用模板对应的
    public function gets($where = '', $fields = '', $joins = '', $pages = '', $order = '')
    {
        $result = array();
        if (is_array($where)) { 
            foreach ($where as $key => $val) {
                $wheresql[] = " AND {$key}='{$val}'";
            }
            $where = implode(' ', $wheresql);
		//	$where=" AND ".$this->sqls($where);
        }
        //默认排序
        if (!$order) {
            $order = '  order by cti.cti_id desc';
        }
        $fields .= ' cti.* ';
        // 外联批次项目表 
        $where .= " AND {$this->short_tbl}.deleted = 0";
        $query = $this->query("SELECT {$fields} FROM sp_{$this->tbl} {$this->short_tbl} {$joins}  WHERE 1 {$where} {$order} {$pages['limit']}");
      	$this->error=$this->sql;
        while ($rt = $this->fetch_array($query)) {
           
			//组织关系
            $rt['applyer']      = load('ep')->getEpFieldById($rt['eid']); //委托人
            $rt['proder']       = load('ep')->getEpFieldById($rt['ep_manu_id']); //生产者
            $rt['prodEp']       = load('ep')->getEpFieldById($rt['ep_prod_id']); //生产企业 
			$rt['unitName']='';
			//产品信息相关
            $rt['unitName']     = load('set')->get_set_name_by_id('prod', $rt['prod_id']); //产品小类
			//合同来源
            $rt['ctfrom_id']    = $rt['ctfrom']; //合同来源变成点选的，兼容原有代码	
            $rt['ctfrom']       = f_ctfrom($rt['ctfrom']); //读取合同项目来源 
			
			
			 //活动类型--继承的下一个流程
            $rt['audit_type_V'] = read_cache('audit_type', $rt['audit_type']);	
			
			       
            //业务规则：新申请状态没有合同项目编号
			//项目号登记后就显示@wgl 2015-4-7
          /*  if ($rt['status'] == 0) {
                $rt['cti_code'] = '';
            }*/
            //规则描述： 策略设计模式
            ///////////////////////////////////////////////////////根据认证领域分别处理//////////////////////////////////
            if ($rt['audit_ver'] == 'b02001') {//有机产品
                //认证类型处理
                $ogas               = $this->get_oga_prod();
                //p($ogas);
                $rt['oga_cert_tag'] = $ogas[$rt['prod_id']]['name'];
                //植物生长周期问题
                if ($rt['prod_id'] == '01.01') {
                    $rt['oga_cert_tag'] .= ':生长周期-' . $this->plant_year[$rt['oga_plant_year']];
                    //转换期	
                } elseif ($rt['prod_id'] == '02.01') {
                    $rt['oga_cert_tag'] .= ':转换期-' . $this->chang_date[$rt['oga_chang_date']];
                }
                ;
            } elseif ($rt['audit_ver'] == 'b01001') { //ccc产品 
                $rt['prod_vers']         = explode('；', $rt['prod_ver_id']);
                 $rt['ep_manu_name']      = $this->get_var(" SELECT ep_name from sp_enterprises WHERE eid={$rt['ep_manu_id']}");
				//认证规则
                $audit_rule_index        = load('set')->get_set_name_by_id('prod', $rt['prod_id'], 'audit_rule_id');
                $audit_rule_index        = str_replace(':', '：', $audit_rule_index);
                $rt['audit_rule_index']  = $audit_rule_index;
				//认证细则
                $rt['audit_rule_detail'] = load('set')->get_set_name_by_id('prod', $rt['prod_id'], 'audit_rule_detail_id');
				
             } else if ($rt['audit_ver'] == 'b02004') { //GAP
                //认证类型处理
                $gaps               = $this->get_gap_prod();
                $rt['oga_cert_tag'] = $gaps[$rt['prod_id']]['name'];
            }elseif($rt['audit_ver'] == 'b0200x'){//一般工业品处理
			 	 $rt['prod_vers']         = explode('；', $rt['prod_ver_id']);
				   $rt['unitName']     =$rt['unitName']. load('set')->get_set_name_by_id('prod', $rt['prod_cate']); //产品名称处理
				
			}
			
            $result[$rt['cti_id']] = $rt;
        }
        return $result;
    }
	
	
	
	
	//添加合同项目
    function add($args)
    {
        $default = array(
            'create_uid' => nowUsr('uid'),
            'accept_date' => nowTime('mysql'),//申请日期,提交申请日期
			'create_date'=>nowTime('mysql'),
			'status'	=> 8    //默认是未分配
        );
        $args    = parse_args($args, $default);
		//判断是否重复申请
		$cti_id=$this->getField('contract_item','cti_id',$args);
		if($cti_id)return false;
         $id      = $this->insert('contract_item', $args);
	 
/*		$this->sql;
		echo '<br>';*/
		//echo $this->sql;
        return $id;
    }
 
	
 
    //读取单条合同项目信息
    function get($args, $meta = true,$field='*')
    {
        if (empty($args) || !is_array($args))
            return false;
        $where            = $this->sqls($args, 'AND');									  
		
        $row              = $this->get_row("SELECT {$field} FROM sp_$this->tbl WHERE $where");
		//$row['unitName']     = load('set')->get_set_name_by_id('prod', $row['prod_id']); //产品小类
        //转换大小写,统一采用小写的方式
        $row['audit_ver'] = strtolower($row['audit_ver']);
        //业务规则：是否可以编辑，防止数据混乱，project表中如果已经安排则不能再编辑合同项目信息
        $project_info     = load('audit')->get(array(
            'cti_id' => $row['cti_id'],'deleted'=>0
        ), false, 'tid');
        if ($project_info['tid']) { //如果检查项目已经安排则不能修改合同项目
           // $row['is_disabled'] = 'disabled';
        }
        //$row['prod_ver_id']= ;//把字符转成换行显示
        $metas  = ($meta) ? $this->getMeta($this->short_tbl, $row['cti_id']) : array();
        $result = @array_merge($row, $metas);
        return $row;
    }
    //根据主键更新数据库数据
    function edit($cti_id, $args, $status = NULL)
    {
        $id = $this->update($this->tbl, $args, array(
            'cti_id' => $cti_id
        ));
        // echo $this->sql;;
        return $id;
    }
    function del($args)
    {
        if (empty($args) or !is_array($args))
            return false;
        $cti_id  = $args['cti_id'];
        $af_info = $this->get(array(
            'cti_id' => $cti_id
        ));
        $args    = parse_args($args);
        $this->update('contract_item', array(
            'deleted' => 1
        ), $args);
    }
}