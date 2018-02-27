<?php 
!defined('IN_SUPU') && exit('Forbidden');
   /*
    选择检验机构
   */
   extract($_GET, EXTR_SKIP);
   $where = "";
   if($code = trim($code)){
	   $where .= " AND code LIKE '%$code%'";
   };
   if($ctc_code = trim($ctc_code)){
	   $where .= " AND ctc_code LIKE '%$ctc_code%'";
   };
   
   if($name = trim($name)){
	   $where .= " AND name LIKE '%$name%'";
   };
   if($cti_id){
		$cti_ids = explode("|",$cti_id);
		$query = $db->query("SELECT * FROM `sp_contract_item` WHERE `cti_id` IN (".join(",",$cti_ids).")");
		$test_ids = array();
		while($rt = $db->fetch_array($query)){
			$s = $db->get_var("SELECT prod_rule_id FROM `sp_settings_prod_xiaolei` WHERE `code` = '$rt[prod_id]' AND `fac_code` = '$rt[fac_code]'");
			$t = $db->get_var("SELECT test_id FROM `sp_settings_prod_test` WHERE `prod_id` = '$s'");
			if($t)$test_ids[] = $t;
			
		}
		if($test_ids)
			$where .= " AND id IN (".join(",",$test_ids).")";
   }
   $where .= " AND ctc_code IS NOT NULL";
   $sql = "SELECT * FROM sp_settings_test_org WHERE 1 $where ";
   $orgers = array();
   $query=$db->query($sql);
    while ($rt = $db->fetch_array($query)) {
        $orgers[] = $rt;
    }
    tpl();
?>