<?php
$doc            = load('data');
$doc->file_name = "有机证书";
 
//证书基本信息
$cert_id        = $_GET['id'];
$cert_info      = load('cert')->get($cert_id);
//合同项目信息
$cti_info=load('cti')->get(array('cti_id'=>$cert_info['cti_id']));
 
//基地信息与基地产品
$ogas           = load('oga')->get_ogas(array(
    'cti_id' => $cert_info['cti_id']
));
foreach ($ogas as $oga) {
    //单一基地
    //获取第一个基地
    $oga_first_pro = load('oga_info')->get_firt_pro($oga['oga_id']);
    $i++;
    //获取其他基地信息
    $oga_other_pro = load('oga_info')->get_oga_other_pro($oga['oga_id']);
    $oga_info .= '<w:tr wsp:rsidR="009D3676" wsp:rsidRPr="000827F3" wsp:rsidTr="00F856BF"><w:trPr><w:jc w:val="center"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="403" w:type="pct"/><w:vmerge w:val="restart"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="009D3676" wsp:rsidRPr="00D4346F" wsp:rsidRDefault="009D3676" wsp:rsidP="004E1EF2"><w:pPr><w:spacing w:line="312" w:line-rule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体"/><wx:font wx:val="宋体"/><w:i-cs/><w:sz-cs w:val="21"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体" w:hint="fareast"/><wx:font wx:val="宋体"/><w:i-cs/><w:sz-cs w:val="21"/></w:rPr><w:t>' . $i . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="897" w:type="pct"/><w:vmerge w:val="restart"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="009D3676" wsp:rsidRPr="00D4346F" wsp:rsidRDefault="009D3676" wsp:rsidP="004E1EF2"><w:pPr><w:spacing w:line="312" w:line-rule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体"/><wx:font wx:val="宋体"/><w:i-cs/><w:sz-cs w:val="21"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体" w:hint="fareast"/><wx:font wx:val="宋体"/><w:i-cs/><w:sz-cs w:val="21"/></w:rPr><w:t>' . $oga['oga_name'] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1337" w:type="pct"/><w:vmerge w:val="restart"/><w:vAlign w:val="center"/></w:tcPr>
	
	<w:p wsp:rsidR="009D3676" wsp:rsidRPr="00D4346F" wsp:rsidRDefault="009D3676" wsp:rsidP="00D5667E"><w:pPr><w:spacing w:line="312" w:line-rule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体"/><wx:font wx:val="宋体"/><w:i-cs/><w:sz-cs w:val="21"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体" w:hint="fareast"/><wx:font wx:val="宋体"/><w:i-cs/><w:sz-cs w:val="21"/></w:rPr><w:t>' . $oga['oga_addr'] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="668" w:type="pct"/><w:vmerge w:val="restart"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="009D3676" wsp:rsidRPr="00E52E81" wsp:rsidRDefault="009D3676" wsp:rsidP="009D3676"><w:pPr><w:spacing w:line="312" w:line-rule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体"/><wx:font wx:val="宋体"/><w:i-cs/><w:sz-cs w:val="21"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体" w:hint="fareast"/><wx:font wx:val="宋体"/><w:i-cs/><w:sz-cs w:val="21"/></w:rPr><w:t>' . $oga['oga_area'] . 
    //第一个基地信息 
        '
 	</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="585" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr>
  	<w:p wsp:rsidR="009D3676" wsp:rsidRPr="00D4346F" wsp:rsidRDefault="009D3676" wsp:rsidP="004E1EF2">
 	<w:pPr><w:spacing w:line="312" w:line-rule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体"/><wx:font wx:val="宋体"/><w:i-cs/><w:sz-cs w:val="21"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体" w:hint="fareast"/><wx:font wx:val="宋体"/><w:i-cs/><w:sz-cs w:val="21"/></w:rPr><w:t>' . $oga_first_pro['prod_id'] . ' </w:t></w:r><w:r><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体" w:hint="fareast"/><wx:font wx:val="宋体"/><w:i-cs/><w:sz-cs w:val="21"/></w:rPr><w:t> </w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="586" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="009D3676" wsp:rsidRPr="00D4346F" wsp:rsidRDefault="00E85E00" wsp:rsidP="00DE46AC"><w:pPr><w:spacing w:line="312" w:line-rule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体"/><wx:font wx:val="宋体"/><w:i-cs/><w:sz-cs w:val="21"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体" w:hint="fareast"/><wx:font wx:val="宋体"/><w:i-cs/><w:sz-cs w:val="21"/></w:rPr><w:t>' . $oga_first_pro['prod_note'] . '</w:t></w:r><w:r><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体" w:hint="fareast"/><wx:font wx:val="宋体"/><w:i-cs/><w:sz-cs w:val="21"/></w:rPr><w:t> </w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="524" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="009D3676" wsp:rsidRPr="00D4346F" wsp:rsidRDefault="004B746C" wsp:rsidP="004E1EF2"><w:pPr><w:spacing w:line="312" w:line-rule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体"/><wx:font wx:val="宋体"/><w:i-cs/><w:sz-cs w:val="21"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体" w:hint="fareast"/><wx:font wx:val="宋体"/><w:i-cs/><w:sz-cs w:val="21"/></w:rPr><w:t>' . $oga_first_pro['prod_area'] . '
   </w:t></w:r></w:p></w:tc></w:tr>
	';
    //其他基地产品信息
    if ($oga_other_pro)
        foreach ($oga_other_pro as $other) {
            $oga_info .= '<w:tr wsp:rsidR="009D3676" wsp:rsidRPr="000827F3" wsp:rsidTr="00F856BF"><w:trPr><w:jc w:val="center"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="403" w:type="pct"/><w:vmerge/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="009D3676" wsp:rsidRPr="00D4346F" wsp:rsidRDefault="009D3676" wsp:rsidP="004E1EF2"><w:pPr><w:spacing w:line="312" w:line-rule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体"/><wx:font wx:val="宋体"/><w:i-cs/><w:sz-cs w:val="21"/></w:rPr></w:pPr></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="897" w:type="pct"/><w:vmerge/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="009D3676" wsp:rsidRDefault="009D3676" wsp:rsidP="004E1EF2"><w:pPr><w:spacing w:line="312" w:line-rule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体"/><wx:font wx:val="宋体"/><w:i-cs/><w:sz-cs w:val="21"/></w:rPr></w:pPr></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1337" w:type="pct"/><w:vmerge/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="009D3676" wsp:rsidRDefault="009D3676" wsp:rsidP="00D5667E"><w:pPr><w:spacing w:line="312" w:line-rule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体"/><wx:font wx:val="宋体"/><w:i-cs/><w:sz-cs w:val="21"/></w:rPr></w:pPr></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="668" w:type="pct"/><w:vmerge/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="009D3676" wsp:rsidRDefault="009D3676" wsp:rsidP="00D5667E"><w:pPr><w:spacing w:line="312" w:line-rule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体"/><wx:font wx:val="宋体"/><w:i-cs/><w:sz-cs w:val="21"/></w:rPr></w:pPr></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="585" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="009D3676" wsp:rsidRDefault="009D3676" wsp:rsidP="004E1EF2"><w:pPr><w:spacing w:line="312" w:line-rule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体"/><wx:font wx:val="宋体"/><w:i-cs/><w:sz-cs w:val="21"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体" w:hint="fareast"/><wx:font wx:val="宋体"/><w:i-cs/><w:sz-cs w:val="21"/></w:rPr>  
	<w:t>' . $other['prod_id'] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="586" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="009D3676" wsp:rsidRDefault="009D3676" wsp:rsidP="00DE46AC"><w:pPr><w:spacing w:line="312" w:line-rule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体"/><wx:font wx:val="宋体"/><w:i-cs/><w:sz-cs w:val="21"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体" w:hint="fareast"/><wx:font wx:val="宋体"/><w:i-cs/><w:sz-cs w:val="21"/></w:rPr><w:t>' . $other['prod_note'] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="524" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="009D3676" wsp:rsidRDefault="009D3676" wsp:rsidP="004E1EF2"><w:pPr><w:spacing w:line="312" w:line-rule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体"/><wx:font wx:val="宋体"/><w:i-cs/><w:sz-cs w:val="21"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体" w:hint="fareast"/><wx:font wx:val="宋体"/><w:i-cs/><w:sz-cs w:val="21"/></w:rPr><w:t>' . $other['prod_area'] . '</w:t></w:r></w:p></w:tc>
	</w:tr>';
        }
}
$data             = $cert_info;
$data['oga_info'] = $oga_info;
$data['cti_code']=$cti_info['cti_code'];
$data['pro_addr']=$cti_info['ep_prod_addr'];
  
//导出word文档
$doc->export_doc($data, 'oga_jiagong');