<?php
!defined('IN_SUPU') && exit('Forbidden');
//2016-03-13 @cyf 
//收费信息登记  和修改
$detail = load('contract_cost_detail');
$ctc	= load( 'cost' );
$pid = getgp("pid");
$ct_id  = (int) getgp('ct_id');
$eid    = (int) getgp('eid');
$cost_id = getgp("cost_id");
//收费明细
$data2=$detail->gets($pid);
extract($_GET);

$enterprise = load( 'enterprise' );
$rows=$enterprise->meta($eid);//企业附表 开票资料信息

$finance_require = $db->getField('contract','finance_require',array('ct_id'=>$ct_id));


//得到修改信息
if($id){

		$row=$detail->get($id);
		extract($row);
		$is_finance = $db->getField('contract_cost' , 'is_finance' , array("id" => $cost_id));
		
		    
  }
if($_POST){
	$is_finance = $_POST['is_finance'];
	$id = $_POST['id'];
	unset($_POST['is_finance'] , $_POST['id']);
	$cost = $db->find_one('contract_cost' , array("id" => $cost_id) , 'ct_id , eid');
	$_POST = array_merge($_POST , $cost);	    
    if($id){//修改收费信息
        $db->update('contract_cost_detail' , $_POST , array("id" => $id));
		
    }else{  //添加收费信息

		$db->insert('contract_cost_detail' , $_POST);
			
			    	

    }
    $db->update('contract_cost' , array('is_finance' => $is_finance) , array("id" => $_POST['cost_id']));
    	
    	    	
	showmsg("success","success","?c=finance&a=dlist");

}

tpl();
