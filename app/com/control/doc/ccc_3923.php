<?php
            $certId = $_GET['id'];
            $result = $db->query("select * from sp_certificate cer LEFT JOIN sp_contract_item cti on(cer.cti_id=cti.cti_id) LEFT JOIN sp_settings_prod setp ON(cti.prod_id=setp.code) where cer.id={$certId}");
            $certi = mysql_fetch_assoc($result);
//            P($certi);die;
            $nameof = '';
            
            if ($certi['is_change']) {
                $nameof = "换证";
            }
            
            $file_name = '低压' . $certi['s_date'] . $nameof . '证书';
           
            $data = readover(TEMPLATE_DIR . 'doc/ccc_3923.xml');

            if ($nameof) {
                $data = readover(TEMPLATE_DIR . 'doc/ccc_3923_r.xml'); //要求控制器文件名与模板文件名一致，并且模板格式为XML
            }
            if (empty($certi)) {
                echo '请求证书不存在';
            } else {
                foreach ($certi as $k => $v) {
                    $data = str_replace('{' . $k . '}', $v, $data);
                }
            }
            
            header("Content-type: application/octet-stream");
            header("Accept-Ranges: bytes");
            header("Content-Disposition: attachment; filename=" . iconv('UTF-8', 'gbk', $file_name . '.doc'));
            echo $data;
