<?php
!defined('IN_SUPU') && exit('Forbidden');
//2016-03-16 @cyf 
//收费信息详细信息查看
$detail = load('contract_cost_detail');
$ctc	= load( 'cost' );
$ct_id  = (int) getgp('ct_id');
$eid    = (int) getgp('eid');
$where.="and c.deleted='0'";
$where.="and c.ct_id='$ct_id'";
//应收费用
    $sql = "SELECT * FROM sp_contract_cost c  WHERE 1 $where ORDER BY c.id DESC";
    $res = $db->query($sql);
    while( $rt = $db->fetch_array( $res ) ){
    	$rt[cost_type]=read_cache('cost_type',$rt[cost_type]);
        
        $rt[dk_cost]=$db->get_var("select sum(dk_cost) from sp_contract_cost_detail where ct_id='$rt[ct_id]' and cost_id=$rt[id] and  deleted=0");
        $rt[ct_code]=$db->get_var("select ct_code from sp_project where ct_id='$rt[ct_id]' and  deleted=0");
        $rt[cti_code]=$db->get_var("select cti_code from sp_project where ct_id='$rt[ct_id]' and  deleted=0");
        $rt[checked]="checked";
        $data1[] = $rt;
    }

    // 收费明细
    //p($ct_id);die;
    $sqls = "SELECT * FROM sp_contract_cost_detail c  WHERE 1 $where ORDER BY c.id DESC";
    //echo $db->sql;die;
    $res = $db->query($sqls);
    while( $rt = $db->fetch_array( $res ) ){
    	$rt[cost_type]=$db->getField('contract_cost','cost_type',array('id'=>$rt['cost_id']));
    	$rt[cost_type]=read_cache('cost_type',$rt[cost_type]);
    	$rt[dk_cost]= $rt['dk_cost'];
    	$rt[dk_date]= $rt['dk_date'];
        // $rt[checked]="checked";
        $data2[] = $rt;
    }

	//项目信息
	$sql="select * from sp_project where ct_id=$ct_id and deleted=0";
	$res=$db->query($sql);
	while($rt=mysql_fetch_array($res)){
	    $rt[iso]=f_iso($rt[iso]);
	    $rt[audit_type]=f_audit_type($rt[audit_type]);
	    $data3[]=$rt;
	}

	foreach ($_POST[id2] as $k => $v) {
	   $re=$db->update('project',array('is_finance'=>$_POST[id1]),array('id'=>$k));
	}
	if($re){
	    showmsg("success","success","?c=finance&a=project");
	}
	tpl();
