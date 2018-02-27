<?php
!defined('IN_SUPU') && exit('Forbidden');
$eid=getgp("eid");
$e_info=load( 'enterprise' )->get(array("eid"=>$eid));


tpl();