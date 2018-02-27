<?php
//@cyf 2016-03-01
!defined('IN_SUPU') && exit('Forbidden');

extract($_GET, EXTR_SKIP);
    //合同费用添加
	$id = getgp('id');

	$ct_id = getgp('ct_id');
    if($id){//得到修改信息
     $data= $ctc->get($id);
     extract($data, EXTR_SKIP);
    }
    //合同收费记录删除
    if($cz=='del'){
      $row=$ctc->del($id);
      if($row){
         showmsg('success', 'success', "?c=cost&a=list");
      }
     
      
    }
  //登记过的信息
  $data1=$ctc->gets($ct_id);
  
  //通过读取缓存生成下拉菜单 返回收费类型
	$cost_type_select=f_select('cost_type',$cost_type);
	tpl( 'contract/cost_edit' );