<?php
!defined('IN_SUPU') && exit('Forbidden');
$pd_types=array("未评定","通过","待定","不通过");
$t_status=array("","待派人","待审批","已审批");
$test_res=array("未检验","合格","需整改","不合格");
$cti_id = (int)getgp( 'cti_id' );
	if( $cti_id ){
		$cti_code = $db->getField("contract_item","cti_code",array("cti_id"=>$cti_id));
		$progress = $db->get_row("SELECT * FROM sp_progress WHERE cti_id = '$cti_id'");
		
		extract($progress);
		for($i=1;$i<12;$i++){
			$j = $i+1;
			${"t".$i} = time2Units(${"step".$i},${"step".$j});
			
		}
	}
	tpl();