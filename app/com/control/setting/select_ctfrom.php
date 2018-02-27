<?php
!defined('IN_SUPU') && exit('Forbidden');
//选择合同来源
$ctfrom_infos = $db->get_results(" SELECT * FROM sp_settings_ctfrom where $where deleted='0' and is_stop='0' order by code asc");
tpl();
 