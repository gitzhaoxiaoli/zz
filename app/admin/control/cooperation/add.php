<?php
!defined('IN_SUPU') && exit('Forbidden'); 
   /**
   *合作单位登记/修改
   */
   //数据接收
   $coo_id = getgp('coo_id');
   $coo_from_select = f_ctfrom_select();
   $coo_baddr_code = f_province_select();
  if($_POST)
   {
	  unset($_POST['step']);
	  if($coo_id){
		  unset($_POST['coo_id']);
		  $tmp = $db->update('cooperation',$_POST,array('coo_id'=>$coo_id));
	  }else{
	  $tmp = $db->insert('cooperation',$_POST);
	  }
	  if($tmp){
		    showmsg('success', 'success', "?c=cooperation&a=list_attach");
	  }
   };
    if('edit' == $a){
		$res = $db->find_one('cooperation'," AND coo_id = '$coo_id'");
		$coo_leibie = $res['coo_leibie'];
		$coo_code = $res['coo_code'];
		$coo_name = $res['coo_name'];
		$coo_baddr = $res['coo_baddr'];
		$coo_addrcode = $res['coo_addrcode'];
		$coo_fzperson = $res['coo_fzperson'];
		$coo_addr = $res['coo_addr'];
		$coo_person = $res['coo_person'];
		$coo_mphone = $res['coo_mphone'];
		$coo_tphone = $res['coo_tphone'];
		$coo_fax = $res['coo_fax'];
		$coo_mail = $res['coo_mail'];
		$coo_fanwei = $res['coo_fanwei'];
		$coo_status = $res['coo_status'];
		$coo_web = $res['coo_web'];
		$coo_note = $res['coo_note'];
		$coo_from = $res['coo_from'];
		$coo_from_select = str_replace("value=\"$coo_from\"", "value='$coo_from' selected " , $coo_from_select );
		$coo_baddr_code = str_replace("value=\"$coo_baddr\"", "value='$coo_baddr' selected " , $coo_baddr_code );
		tpl('cooperation/add');
	}else
    tpl();

?>