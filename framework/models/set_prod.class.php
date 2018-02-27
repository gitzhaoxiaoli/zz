<?php
//产品小类配置
class set_prod extends model{
	
	private $short_tbl='settings_prod';
	
	//新增产品小类
	public function add($data){
		//检验编码是否重复
		if(!$code=$this->_check_code($data['code']))continue; 
		$this->db->insert('settings_prod',$data); 
		
	}
	 //检验有效编码是否重复
	 function _check_code($code){
		 
		 
		 
	}
	//通过编码获取配置信息
	function get_by_code($code){
		
		
		
	}
	
	
	
	
	
}
