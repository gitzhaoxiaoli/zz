<?php
!defined('IN_SUPU') && exit('Forbidden');
//产品对标准配置
if ($_POST) {
	// exit(p($_POST));
    //添加配置内容
    if ($_POST['new']['prod_ver_id']) {
        $db->insert('settings_prod_ver', $_POST['new']);
    }
	/////p($_POST);
	//exit;
    //更新配置内容
    unset($_POST['new']);
    if ($_POST['old']) {
        foreach ($_POST['old'] as $k => $v) {
            $db->update('settings_prod_ver', $v, array(
                'id' => $k
            ));
        }
    }
    showmsg('success', 'success', "?m=com&c=setting&a=prod_ver_list&type=$_GET[type]");
	
	exit;
}


//加载模型
$set           = load('set');
$set->tbl_name = 'prod_ver'; //初始化操作表


//删除配置
if ($_GET['del']) {
	 
    $set->del_set($_GET['del']);
	 
   // showmsg('success', 'success', "?m=com&c=setting&a=prod_ver_list&type=$_GET[type]");
}

$type=$_GET['type'];
//产品过滤条件
$where = '';
$where .= " AND deleted!='1'"; 
$prod_ids=$db->get_Col(" SELECT code from sp_settings_prod_xiaolei where prod_type='$type' and deleted!=1");

$vers=$db->get_Col(" SELECT code from sp_settings_ver where type='$type' and deleted=0");
 
//读取配置内容  

/* if ($_GET['prod_name']) {
	$search_prod_ids=$db->get_Col(" SELECT code from sp_settings_prod_xiaolei where prod_type='$type' AND  name like '%$_GET[prod_name]%'  AND deleted=0");
	if($search_prod_ids){
		
	$prod_ids=array_intersect($prod_ids,$search_prod_ids);	
	//$where.= " AND ".("prod_id  IN ('" . implode('\',\'', $search_prod_ids)) . '\')';
	
 	}else{
		$where.=" and  id=0";	
		
	} 
} */
//标准编号
if ($_GET['name']) {
	$search_vers=$db->get_Col(" SELECT code from sp_settings_ver where  type='$type' AND code like '%$_GET[name]%' AND deleted=0");
    if($search_vers){ 
	$vers=array_intersect($vers,$search_vers);	
	
		//$where.= " AND ".("prod_ver_id IN ('" . implode('\',\'', $search_vers)) . '\')';
	}else{
		$where.=" and  id=0";	
 	} 
	
}

 
// $where.= " AND ".("prod_id  IN ('" . implode('\',\'', $prod_ids)) . '\')';
 $where.= " AND ".("prod_ver_id  IN ('" . implode('\',\'', $vers)) . '\')';
 
//$where.=" AND prod_ver_id ";
 
//标准过滤条件

$total = $set->count_set("$where", $joins); 
$pages = numfpage($total);
  
$sql   = " SELECT * FROM sp_settings_prod_ver $joins WHERE 1 $where order by vieworder desc,id desc $pages[limit]";
$query=$db->query($sql);
while($row=$db->fetch_array($query)){ 
	$row['prod_ver_name']=load('set')->get_set_name_by_id('ver',$row['prod_ver_id']);//标准名称
 	$row['prod_name']=load('set')->get_set_name_by_id('prod_xiaolei',$row['prod_id']); //产品名称
  	// $row['prod_rule_name'] = load('set')->get_set_name_by_id('prod_rule',$row['prod_id']);
	$resdb[$row[id]]=$row; 
} 
tpl();

 