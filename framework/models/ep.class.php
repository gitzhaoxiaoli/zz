
<?php
/*
企业基础类
*/
class ep extends model
{
    private $tbl = 'enterprises';
    private $short_tbl = 'ep';
    //统计数据
    public function total($field_name = '', $where = '', $joins = '')
    {
        if (is_array($where)) {
            foreach ($where as $key => $val) {
                $wheresql[] = " AND {$key}='{$val}'";
            }
            $where = implode(' ', $wheresql);
        }
        $query = $this->query("SELECT {$this->short_tbl}.{$field_name},COUNT(*) total FROM sp_{$this->tbl} {$this->short_tbl} {$joins} WHERE 1  {$where} GROUP BY {$this->short_tbl}.{$field_name}");
        while ($rt = $this->fetch_array($query)) {
            $total[$rt[$field_name]] = $rt['total'];
        }
        return $total;
    }
    //企业名称模糊搜索: $type 类型： 委托人 生产者 生产企业: 默认主表为合同项目表 类型为生产企业
    public function search_ep_name($ep_name = '', $type = 'ep_prod_id', $tbl_key = 'cti.cti_id')
    {
        $ep_name = trim($ep_name); //清楚两端空格
        $_eids   = $this->get_Col("SELECT eid FROM sp_{$this->tbl} WHERE ep_name LIKE '%" . str_replace('%', '\%', $ep_name) . "%'");
        if ($_eids) {
            $where = " AND cti.$type IN (" . implode(',', $_eids) . ")";
        } else {
            $where = " AND $tbl_key =0";
        }
        return $where;
    }
    //企业列表查询--分页-简单-链表查询
    public function gets($where, $fields = '*', $joins = '', $pages = '', $order = '')
    {
        $result = array();
        if (is_array($where)) {
            foreach ($where as $key => $val) {
                $wheresql[] = " AND {$key}='{$val}'";
            }
            $where = implode(' ', $wheresql);
        }
        //默认条件 
        $where .= " AND deleted=0";
        $query = $this->query("SELECT {$fields} FROM sp_{$this->tbl} {$this->short_tbl} {$joins}  WHERE 1 {$where} {$order} {$pages}");
        while ($rt = $this->fetch_array($query)) {
            $metas              = array();
            $rt['province']     = f_region_province($rt['areacode']); //省份
            $metas              = $this->getMeta('ep', $rt['eid']); //附表信息
            $rt                 = array_merge($metas, $rt);
            $result[$rt['eid']] = $rt;
        }
        return $result;
    }
    //通过企业ID获取企业表中的某个字段信息
    public function getEpFieldById($eid, $field = 'ep_name')
    {
        $fieldVal = $this->getField($this->tbl, $field, array(
            'eid' => $eid
        ));
        return $fieldVal;
    }
    //获取一条企业信息
    function get($args, $meta = true, $fields = '*') //是否读取附表信息
    {
        if (empty($args) || !is_array($args))
            return false;
        $args   = parse_args($args);
        $where  = $this->sqls($args, 'AND');
        $result    = $this->get_row("SELECT {$fields} FROM sp_{$this->tbl} WHERE $where");
		 
		//追加附表信息
		if($meta){
			    $metas  = ($meta) ? $this->getMeta($this->short_tbl, $result['eid']) : array();
				
			 
				 if($metas)
     		   $result = @array_merge($metas, $result); //链接附表
			
		}
    
        return $result;
    }
    //新增企业,添加内容， 是否附表信息，是否自动生成编码：导数据使用
    function add($args, $meta = true, $is_code = true)
    {
		//默认参数
        $default = array(
            'create_uid' => nowUsr('uid'), //创建人
            'create_date' => nowTime('mysql') //创建时间
        );
        $args    = parse_args($args, $default);
		//格式化
		$args['ep_name']= trim($args['ep_name']);//清空企业名称两边的空格
		
        if ($args['work_code']) { //清空-对组织机构代码的影响
            $args['work_code'] = str_replace('-', '', trim($args['work_code']));
        }
        //过滤组织机构代码或者企业名称  
		//组织机构代码已存在，则返回原eid
		if($args['work_code']!=='111111111'){//不是境外：港澳台
 				$old_work_code = $this->get(array(
					'work_code' => $args['work_code']
				,'deleted'=>'0'));
				if ($old_work_code) {
					return $old_work_code['eid'];
				} 
		}
		 
		//过滤已经存在的企业: 已删除企业
		//企业名称已存在，则返回原eid
        $old_ep_name=$this->get(array(
            'ep_name' => trim($args['ep_name']),'deleted'=>'0'
        ));
        if ($old_ep_name) {
            return $old_ep_name['eid'];
        }
        //================= 
        $eid  = $this->insert('enterprises', $args);
        //系统根据企业表主键生成企业编码 
        $code = load('flow_code')->ep_code($eid);
        $this->edit($eid, array(
            'code' => $code
        ), false); 
		 
        //系统处理附加信息
        if ($meta)
            $this->setMeta($this->short_tbl, $eid);
        return $eid;
    }
    //保存企业信息
    function edit($eid, $args, $meta = true)
    {
        if (empty($eid))
            return false;
        $args['update_date'] = nowTime('mysql');
        $args['update_uid']  = nowUsr('uid');
        $this->update('enterprises', $args, array(
            'eid' => $eid
        ));
        if ($args['work_code']) { //清空-对组织机构代码的影响
            $args['work_code'] = str_replace('-', '', $args['work_code']);
        }
        if ($meta)
            $this->setMeta($this->short_tbl, $eid);
    }
    //删除企业
    function del($args)
    {
        if (empty($args) || !is_array($args))
            return false;
        $args = parse_args($args);
        $eid  = $args['eid'];
        $this->update('enterprises', array(
            'deleted' => 1
        ), $args);
    }
}
?>