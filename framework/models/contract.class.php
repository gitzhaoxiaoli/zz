<?php 
//合同模型
 class contract{

	function add( $args ){
 
		global $db; 
 
		$id = $db->insert( 'contract', $args );
		
		$bf_str = $this->get(array( 'ct_id' => $id ));
		return $id;
	}

	function edit( $ct_id, $args ){
		global $db; 
		$db->update( 'contract', $args, array( 'ct_id' => $ct_id ) ); 
	}
//读取单挑合同信息
	function get( $args ){ 
		global $db; 
		$where = $db->sqls( $args, 'AND' );
		$result = $db->get_row("SELECT * FROM sp_contract WHERE  $where AND deleted=0");
	 
		return $result;
	}


	function last( $eid ){
		global $db;
		$row = $db->get_row("SELECT * FROM sp_contract WHERE eid = '$eid' AND AND deleted=0 ORDER BY create_date DESC LIMIT 1");
		return $row;
	}

	//删除合同
	function del( $args ){ 
		global $db; 
		$db->update( 'contract', array( 'deleted' => 1 ), $args );

		// 日志
		do {
			$deleteds = $this->gets($args);
			foreach($deleteds as $deleted) {
				log_add($deleted['eid'], 0, "[说明:合同删除]", NULL, serialize($deleted));
			}
		}while(false);
	}

	function gets( $args ){
		if( empty( $args ) || !is_array( $args ) ) return false;
		global $db;
		$result = array();
		$where = $db->sqls( $args, 'AND' );
		$query = $db->query("SELECT * FROM sp_contract WHERE $where AND deleted=0");
		while( $rt = $db->fetch_array( $query ) ){
			$result[$rt['id']] = $rt;
		}
		return $result;
	}


}

?>