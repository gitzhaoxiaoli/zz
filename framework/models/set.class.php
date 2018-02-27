<?php
//配置类：多个表
//附件类
class set extends db_mysql
{
    private $set_pre = 'settings_'; //配置表前缀
    public $tbl_name = ''; //配置表名
    public $joins = '';
    public $fields = '';
    //获取配置表中某个字段的值 ，用于修改，列表等功能
    //变量说明： $table 表名  $code 配置编码  $field 字段 默认为name
    function get_set_name_by_id($table, $code, $field = 'name',$prod_type='')
    {
		global $db;
        $this->tbl_name = $table;
		$where="code='$code'";
		//认证领域
		if($prod_type){
			$where.=" AND prod_type='$prod_type'";	
 		}
		$sql="SELECT $field from sp_$this->set_pre$this->tbl_name WHERE $where order by id desc";
        $name           = $db->get_var($sql);
         //echo $this->sql;
        return $name;
    }
	
    //区县转化问完整行政区划==导数据使用
    function get_region_by_country($code)
    {
		global $db;
        //区县
        $country  = $db->getField('settings_region', 'name', array(
            'code' => $code
        ));
        //市
        $city     = $db->getField('settings_region', 'name', array(
            'code' => substr($code, 0, 4) . '00'
        ));
        $province = $db->getField('settings_region', 'name', array(
            'code' => substr($code, 0, 2) . '0000'
        )); 
        return $province . $city . $country;
    }
    //添加配置
    //修改配置
    //计算配置数量
    function count_set($where = '', $joins = '')
    {
		global $db;
        $short_tbl = explode(" ", $this->tbl_name);
        if ($short_tbl[1]) { //判断是否是多表查询
            $short_tbl = $short_tbl[1] . '.';
        } else {
            $short_tbl = '';
        }
        return $db->get_var("SELECT COUNT(*) FROM sp_$this->set_pre$this->tbl_name $joins WHERE 1 $where AND $short_tbl deleted='0'");
    }
    //删除配置
    function del_set($id)
    {
		global $db;
        $db->update($this->set_pre . $this->tbl_name, array(
            'deleted' => '1'
        ), array(
            'id' => $id
        ));
	 
    }
	
	//根据项目编号计算行业代码
	function get_industry_by_prod_id($prod_id){
		$temp='';
	    $use_code = load('set')->get_set_name_by_id('prod', $prod_id, 'use_code');
        //根据人员代码到配置里面查询国民经济代码
        $temp   = load('set')->get_set_name_by_id('audit_code', $use_code, 'industry');
		return $temp;
	}
	
	
	
    //获取列表  过滤deleted  要求数据表中必须有deleted字段， 获取全局内容
    //应用范围： 对数据库表中信息基本没有处理。或者基本没有外键。最粗糙的处理方式
    public function get_set_datas($table, $where = '', $fields = '*', $order = '', $pages = '', $output = 'ARRAY')
    {
		global $db;
        if (is_array($where)) {
            foreach ($where as $key => $val) {
                $wheresql[] = " AND $key='$val'";
            }
            ;
            $where = implode(' ', $wheresql);
        }
        $sql = "select $fields from sp_$table WHERE 1 AND deleted='0' $where ORDER BY $order id DESC $pages ";
        $query     = $db->query($sql);
        while ($data = $db->fetch_array($query)) {
            $arr[$data['code']] = $data;
        }
        return $arr;
    }
}
	