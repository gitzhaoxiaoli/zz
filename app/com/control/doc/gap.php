<?php
$doc            = load('data');
$doc->file_name = "有机证书";
//证书基本信息
$cert_id        = $_GET['id'];
$cert_info      = load('cert')->get($cert_id);
//基地信息与基地产品
$ogas           = load('oga')->get_ogas(array(
    'cti_id' => $cert_info['cti_id']
));
 
$gap_info='<w:tr wsp:rsidR="00063E62" wsp:rsidRPr="00C22A7E" wsp:rsidTr="00E2415B"><w:trPr><w:trHeight w:val="807"/></w:trPr><w:tc><w:tcPr><w:tcW w:w="1147" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="00063E62" wsp:rsidRPr="00C22A7E" wsp:rsidRDefault="009B72A7" wsp:rsidP="00234619"><w:pPr><w:spacing w:line="280" w:line-rule="exact"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体" w:cs="方正楷体_GBK"/><wx:font wx:val="宋体"/><w:sz-cs w:val="21"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体" w:cs="方正楷体_GBK" w:hint="fareast"/><wx:font wx:val="宋体"/><w:sz-cs w:val="21"/></w:rPr><w:t>枣</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="932" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="00063E62" wsp:rsidRPr="00176FD8" wsp:rsidRDefault="009B72A7" wsp:rsidP="004B788A"><w:pPr><w:spacing w:line="280" w:line-rule="exact"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体"/><wx:font wx:val="宋体"/><w:sz-cs w:val="21"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体" w:hint="fareast"/><wx:font wx:val="宋体"/><w:sz-cs w:val="21"/></w:rPr><w:t>阿拉尔市塔克拉玛果业有限公司</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1204" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="00063E62" wsp:rsidRPr="005E676C" wsp:rsidRDefault="009B72A7" wsp:rsidP="00420872"><w:pPr><w:spacing w:line="280" w:line-rule="exact"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体"/><wx:font wx:val="宋体"/><w:color w:val="000000"/><w:sz-cs w:val="21"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体" w:hint="fareast"/><wx:font wx:val="宋体"/><w:sz-cs w:val="21"/></w:rPr><w:t>新疆生产建设兵团农一师十一团17连</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="799" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="00063E62" wsp:rsidRPr="00C22A7E" wsp:rsidRDefault="009B72A7" wsp:rsidP="00234619"><w:pPr><w:spacing w:line="280" w:line-rule="exact"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体"/><wx:font wx:val="宋体"/><w:sz-cs w:val="21"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体" w:hint="fareast"/><wx:font wx:val="宋体"/><w:sz-cs w:val="21"/></w:rPr><w:t>72.07</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="918" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p wsp:rsidR="00063E62" wsp:rsidRPr="00C22A7E" wsp:rsidRDefault="009B72A7" wsp:rsidP="00234619"><w:pPr><w:spacing w:line="280" w:line-rule="exact"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体"/><wx:font wx:val="宋体"/><w:sz-cs w:val="21"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="宋体" w:h-ansi="宋体" w:hint="fareast"/><wx:font wx:val="宋体"/><w:sz-cs w:val="21"/></w:rPr><w:t>330.24</w:t></w:r></w:p></w:tc></w:tr>';
 $gap_info='';
$data             = $cert_info;
$data['gap_info'] = $gap_info;
//导出word文档
$doc->export_doc($data, 'gap');