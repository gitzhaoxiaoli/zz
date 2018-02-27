<?php
!defined('IN_SUPU') && exit('Forbidden');


		$qualification->del($id);


	$REQUEST_URI='?c=hr_qualification&a=list&status=1';
	showmsg( 'success', 'success', $REQUEST_URI );