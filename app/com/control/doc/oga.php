<?php
$doc            = load('data');
$doc->file_name = "有机证书";
 $doc->doc_left=$doc->doc_right='';
//证书基本信息
$cert_id        = $_GET['id'];
$cert_info      = load('cert')->get($cert_id);
//合同项目信息
$cti_info=load('cti')->get(array('cti_id'=>$cert_info['cti_id']));
 //转换日期格式：将数据库数据转化为年-月-日
function date_db2view($str) {
    $temp = '';
    $date = explode('-', $str);
    $y = $date[0];
    $m = $date[1];
    $d = $date[2];
    $temp = $y . '年' . $m . '月' . $d . '日';
    return $temp;
}

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
    $oga_info .= '<w:tr w:rsidR="00AF722C" w:rsidRPr="00B211E7" w:rsidTr="00D763DD"><w:tc><w:tcPr><w:tcW w:w="369" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00AF722C" w:rsidRPr="00B211E7" w:rsidRDefault="00AF722C" w:rsidP="0079115B"><w:pPr><w:spacing w:line="312" w:lineRule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:hAnsi="宋体"/><w:iCs/><w:szCs w:val="21"/></w:rPr></w:pPr><w:r w:rsidRPr="00B211E7"><w:rPr><w:rFonts w:ascii="宋体" w:hAnsi="宋体" w:hint="eastAsia"/><w:iCs/><w:szCs w:val="21"/></w:rPr><w:t>' . $i . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1011" w:type="pct"/><w:vMerge w:val="restart"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00AF722C" w:rsidRPr="00B211E7" w:rsidRDefault="00AF722C" w:rsidP="00D763DD"><w:pPr><w:spacing w:line="312" w:lineRule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:hAnsi="宋体"/><w:iCs/><w:szCs w:val="21"/></w:rPr></w:pPr><w:r w:rsidRPr="00B211E7"><w:rPr><w:rFonts w:ascii="宋体" w:hAnsi="宋体" w:hint="eastAsia"/><w:szCs w:val="21"/></w:rPr><w:t>' . $oga['oga_name'] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1087" w:type="pct"/><w:vMerge w:val="restart"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00AF722C" w:rsidRPr="00B211E7" w:rsidRDefault="00AF722C" w:rsidP="00567D53"><w:pPr><w:spacing w:line="312" w:lineRule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:hAnsi="宋体"/><w:iCs/><w:color w:val="000000"/><w:szCs w:val="21"/></w:rPr></w:pPr><w:r w:rsidRPr="00B211E7"><w:rPr><w:rFonts w:ascii="宋体" w:hAnsi="宋体" w:hint="eastAsia"/><w:iCs/><w:color w:val="000000"/><w:szCs w:val="21"/></w:rPr><w:t>' . $oga['oga_addr'] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="621" w:type="pct"/><w:vMerge w:val="restart"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00AF722C" w:rsidRPr="00B211E7" w:rsidRDefault="00AF722C" w:rsidP="005702E0"><w:pPr><w:spacing w:line="312" w:lineRule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:hAnsi="宋体"/><w:iCs/><w:szCs w:val="21"/></w:rPr></w:pPr><w:r><w:rPr><w:rFonts w:ascii="宋体" w:hAnsi="宋体"/><w:iCs/><w:szCs w:val="21"/></w:rPr><w:t>   ' . $oga['oga_area'] . 
    //第一个基地信息 
        '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="544" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00AF722C" w:rsidRPr="00B211E7" w:rsidRDefault="00AF722C" w:rsidP="0079115B"><w:pPr><w:spacing w:line="312" w:lineRule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:hAnsi="宋体"/><w:iCs/><w:szCs w:val="21"/></w:rPr></w:pPr><w:r w:rsidRPr="00B211E7"><w:rPr><w:rFonts w:ascii="宋体" w:hAnsi="宋体" w:hint="eastAsia"/><w:iCs/><w:szCs w:val="21"/></w:rPr><w:t>' . $oga_first_pro['prod_area'] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="466" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00AF722C" w:rsidRPr="00B211E7" w:rsidRDefault="00AF722C" w:rsidP="0079115B"><w:pPr><w:spacing w:line="312" w:lineRule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:hAnsi="宋体"/><w:iCs/><w:szCs w:val="21"/></w:rPr></w:pPr><w:r w:rsidRPr="00B211E7"><w:rPr><w:rFonts w:ascii="宋体" w:hAnsi="宋体" w:hint="eastAsia"/><w:iCs/><w:szCs w:val="21"/></w:rPr><w:t>' . $oga_first_pro['prod_id'] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="438" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00AF722C" w:rsidRPr="00B211E7" w:rsidRDefault="00AF722C" w:rsidP="00FC22C8"><w:pPr><w:spacing w:line="312" w:lineRule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:hAnsi="宋体"/><w:iCs/><w:szCs w:val="21"/></w:rPr></w:pPr><w:r w:rsidRPr="00B211E7"><w:rPr><w:rFonts w:ascii="宋体" w:hAnsi="宋体" w:hint="eastAsia"/><w:iCs/><w:szCs w:val="21"/></w:rPr><w:t>' . $oga_first_pro['prod_note'] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="464" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00AF722C" w:rsidRPr="00B211E7" w:rsidRDefault="00AF722C" w:rsidP="0079115B"><w:pPr><w:spacing w:line="312" w:lineRule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:hAnsi="宋体"/><w:iCs/><w:szCs w:val="21"/></w:rPr></w:pPr><w:r w:rsidRPr="00B211E7"><w:rPr><w:rFonts w:ascii="宋体" w:hAnsi="宋体" w:hint="eastAsia"/><w:iCs/><w:szCs w:val="21"/></w:rPr><w:t>' . $oga_first_pro['prod_output_num'] . '</w:t></w:r></w:p></w:tc></w:tr>';
    //其他基地产品信息
    if ($oga_other_pro)
        foreach ($oga_other_pro as $other) {
            $oga_info .= '<w:tr w:rsidR="00AF722C" w:rsidRPr="00B211E7" w:rsidTr="00D763DD"><w:tc><w:tcPr><w:tcW w:w="369" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00AF722C" w:rsidRPr="00B211E7" w:rsidRDefault="00AF722C" w:rsidP="0079115B"><w:pPr><w:spacing w:line="312" w:lineRule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:hAnsi="宋体"/><w:iCs/><w:szCs w:val="21"/></w:rPr></w:pPr><w:r w:rsidRPr="00B211E7"><w:rPr><w:rFonts w:ascii="宋体" w:hAnsi="宋体" w:hint="eastAsia"/><w:iCs/><w:szCs w:val="21"/></w:rPr><w:t>2</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1011" w:type="pct"/><w:vMerge/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00AF722C" w:rsidRPr="00B211E7" w:rsidRDefault="00AF722C" w:rsidP="00D763DD"><w:pPr><w:spacing w:line="312" w:lineRule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:hAnsi="宋体"/><w:szCs w:val="21"/></w:rPr></w:pPr></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="1087" w:type="pct"/><w:vMerge/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00AF722C" w:rsidRPr="00B211E7" w:rsidRDefault="00AF722C" w:rsidP="00567D53"><w:pPr><w:spacing w:line="312" w:lineRule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:hAnsi="宋体"/><w:iCs/><w:color w:val="000000"/><w:szCs w:val="21"/></w:rPr></w:pPr></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="621" w:type="pct"/><w:vMerge/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00AF722C" w:rsidRPr="00B211E7" w:rsidRDefault="00AF722C" w:rsidP="005702E0"><w:pPr><w:spacing w:line="312" w:lineRule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:hAnsi="宋体"/><w:iCs/><w:szCs w:val="21"/></w:rPr></w:pPr></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="544" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00AF722C" w:rsidRPr="00B211E7" w:rsidRDefault="00AF722C" w:rsidP="0079115B"><w:pPr><w:spacing w:line="312" w:lineRule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:hAnsi="宋体"/><w:iCs/><w:szCs w:val="21"/></w:rPr></w:pPr><w:r w:rsidRPr="00B211E7"><w:rPr><w:rFonts w:ascii="宋体" w:hAnsi="宋体" w:hint="eastAsia"/><w:iCs/><w:szCs w:val="21"/></w:rPr><w:t>' . $oga_first_pro['prod_area'] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="466" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00AF722C" w:rsidRPr="00B211E7" w:rsidRDefault="00AF722C" w:rsidP="0079115B"><w:pPr><w:spacing w:line="312" w:lineRule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:hAnsi="宋体"/><w:iCs/><w:szCs w:val="21"/></w:rPr></w:pPr><w:r w:rsidRPr="00B211E7"><w:rPr><w:rFonts w:ascii="宋体" w:hAnsi="宋体" w:hint="eastAsia"/><w:iCs/><w:szCs w:val="21"/></w:rPr><w:t>' . $oga_first_pro['prod_id'] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="438" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00AF722C" w:rsidRPr="00B211E7" w:rsidRDefault="00AF722C" w:rsidP="00FC22C8"><w:pPr><w:spacing w:line="312" w:lineRule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:hAnsi="宋体"/><w:iCs/><w:szCs w:val="21"/></w:rPr></w:pPr><w:r w:rsidRPr="00B211E7"><w:rPr><w:rFonts w:ascii="宋体" w:hAnsi="宋体" w:hint="eastAsia"/><w:iCs/><w:szCs w:val="21"/></w:rPr><w:t>' . $oga_first_pro['prod_note'] . '</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="464" w:type="pct"/><w:vAlign w:val="center"/></w:tcPr><w:p w:rsidR="00AF722C" w:rsidRPr="00B211E7" w:rsidRDefault="00AF722C" w:rsidP="0079115B"><w:pPr><w:spacing w:line="312" w:lineRule="auto"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="宋体" w:hAnsi="宋体"/><w:iCs/><w:szCs w:val="21"/></w:rPr></w:pPr><w:r w:rsidRPr="00B211E7"><w:rPr><w:rFonts w:ascii="宋体" w:hAnsi="宋体" w:hint="eastAsia"/><w:iCs/><w:szCs w:val="21"/></w:rPr><w:t>' . $oga_first_pro['prod_output_num'] . '</w:t></w:r></w:p></w:tc></w:tr>';
        }
}
 
$data['cert_name']=$cert_info['cert_name'];
$data['cert_addr']=$cert_info['cert_addr'];
$data['pro_name']=$cert_info['pro_name'];
$data['pro_addr']=$cert_info['pro_addr'];
$data['s_date']=date_db2view($cert_info['s_date']);
$data['certno']=$cert_info['certno'];
$data['e_date']=date_db2view($cert_info['e_date']); 

$data['oga_info'] = $oga_info;
$data['cti_code']=$cti_info['cti_code'];
$data['pro_addr']=$cti_info['ep_prod_addr'];
 
//导出word文档 :生产
$doc->export_doc($data, 'oga');