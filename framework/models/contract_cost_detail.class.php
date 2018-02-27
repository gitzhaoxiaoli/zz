<?php
//合同：合同费用登记

class contract_cost_detail{


	function add( $args ){
 
		global $db;
		$id = $db->insert( 'contract_cost_detail', $args );
		$bf_info = $this->get($id);
		$ct_code = $db->get_var("select * from sp_contract_cost_detail where ct_id='$args[ct_id]' ");
		// 日志
		do {
			//log_add($bf_info['eid'], 0, "[说明:合同费用登记]<合同编号:$ct_code>", NULL, serialize($bf_info));
		}while(false);
		return $id;
	}

	function edit( $id, $args ){
		global $db;
		$af_info = $this->get($id);
		$db->update('contract_cost_detail', $args, array('id'=>$id));
		$bf_info = $this->get($id);
		$ct_code = $db->get_var("select * from sp_contract_cost_detail where ct_id='$args[ct_id]' ");
		// 日志
		do {
			//log_add($bf_info['eid'], 0, "[说明:合同费用修改]<合同编号:$ct_code>", serialize($af_info), serialize($bf_info));
		}while(false);
	}

	function get( $id ){
		global $db;
		$row = $db->get_row("SELECT * FROM sp_contract_cost_detail WHERE id = '$id' and deleted='0'");
		return $row;
	}
	
	function del( $id ){
		global $db;
		$row = $db->query("update sp_contract_cost_detail set deleted='1' WHERE id = '$id'");
		return $row;
	}

	function gets( $ids ){
		if( empty( $ids ) ) return false;
		global $db;
		$result = array();
		$query = $db->query("SELECT * FROM sp_contract_cost_detail d  WHERE d.cost_id=$ids and d.deleted='0'");
		while( $rt = $db->fetch_array( $query ) ){
			$rt['is_finance']=$db->get_var("select is_finance from sp_contract_cost where id='$rt[cost_id]' and deleted='0' order by is_finance desc");
			$data2[]=$rt;
		}

		return $data2;
	}


}

?>