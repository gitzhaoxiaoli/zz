<?php
/** 
 * Respose A Http Request 
 * 
 * @param string $url 
 * @param array $post 
 * @param string $method 
 * @param bool $returnHeader 
 * @param string $cookie 
 * @param bool $bysocket 
 * @param string $ip 
 * @param integer $timeout 
 * @param bool $block 
 * @return string Response 
 */
function httpRequest($url, $post = '', $method = 'GET', $limit = 0, $returnHeader = FALSE, $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 15, $block = TRUE)
{
    $return  = '';
    $matches = parse_url($url);
    !isset($matches['host']) && $matches['host'] = '';
    !isset($matches['path']) && $matches['path'] = '';
    !isset($matches['query']) && $matches['query'] = '';
    !isset($matches['port']) && $matches['port'] = '';
    $host = $matches['host'];
    $path = $matches['path'] ? $matches['path'] . ($matches['query'] ? '?' . $matches['query'] : '') : '/';
    $port = !empty($matches['port']) ? $matches['port'] : 80;
    if (strtolower($method) == 'post') {
        $post = (is_array($post) and !empty($post)) ? http_build_query($post) : $post;
        $out  = "POST $path HTTP/1.0\r\n";
        $out .= "Accept: */*\r\n";
        //$out .= "Referer: $boardurl\r\n";  
        $out .= "Accept-Language: zh-cn\r\n";
        $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
        $out .= "Host: $host\r\n";
        $out .= 'Content-Length: ' . strlen($post) . "\r\n";
        $out .= "Connection: Close\r\n";
        $out .= "Cache-Control: no-cache\r\n";
        $out .= "Cookie: $cookie\r\n\r\n";
        $out .= $post;
    } else {
        $out = "GET $path HTTP/1.0\r\n";
        $out .= "Accept: */*\r\n";
        //$out .= "Referer: $boardurl\r\n";  
        $out .= "Accept-Language: zh-cn\r\n";
        $out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
        $out .= "Host: $host\r\n";
        $out .= "Connection: Close\r\n";
        $out .= "Cookie: $cookie\r\n\r\n";
    }
    $fp = fsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
    if (!$fp)
        return '';
    else {
        $header = $content = '';
        stream_set_blocking($fp, $block);
        stream_set_timeout($fp, $timeout);
        fwrite($fp, $out);
        $status = stream_get_meta_data($fp);
        if (!$status['timed_out']) { //未超时  
            while (!feof($fp)) {
                $header .= $h = fgets($fp);
                if ($h && ($h == "\r\n" || $h == "\n"))
                    break;
            }
            
            $stop = false;
            while (!feof($fp) && !$stop) {
                $data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
                $content .= $data;
                if ($limit) {
                    $limit -= strlen($data);
                    $stop = $limit <= 0;
                }
            }
        }
        fclose($fp);
        return $returnHeader ? array(
            $header,
            $content
        ) : $content;
    }
}
/*
1、域名查询 接口采用HTTP，POST，GET协议： 
调用URL：http://panda.www.net.cn/cgi-bin/check.cgi 
参数名称：area_domain 值为标准域名，例：hichina.com 
调用举例： http://panda.www.net.cn/cgi-bin/check.cgi?area_domain=qxue8.com   
返回XML： <?xml version="1.0" encoding="gb2312"?>  <property>  <returncode>200</returncode>  <key>qxue8.com</key>  <original>211 : Domain name is not available</original>  </property> 
返回 XML 结果说明： 
returncode=200 表示接口返回成功 
key=***.com表示当前check的域名 
original=210 : Domain name is available     表示域名可以注册 
original=211 : Domain name is not available 表示域名已经注册 
original=212 : Domain name is invalid   表示域名参数传输错误 
original=213 : Time out 查询超时 
2、域名信息whois a) 接口采用HTTP，POST，GET协议： 
调用URL：http://whois.www.net.cn/ 
参数名称：domain 值为标准域名，
例：qxue8.com 调用举例：http://whois.www.net.cn/whois/domain/qxue8.com 
返回文本：其中在字符《pre》与字符《/pre》之间即为域名信息内容。 b) http://whois.chinaz.com/domain.com   原文来自http://www.cnblogs.com/cocobiz/archive/2012/03/29/2423335.html
 */

/**
 * [checkDomain description]
 * @param  [type] $domain [description]
 * @return [type]         [description]
 */
function checkDomain($domain){
    $url = "http://panda.www.net.cn/cgi-bin/check.cgi";
    $param = "area_domain=";
    $param .= $domain;
    $url .= "?";
    $url .= $param;
    echo $url;
    exit();
    $res = httpRequest($url);
    $res = simplexml_load_string($res);
    $res = json_encode($res);
    $res = json_decode($res,TRUE);
    return $res;

}
$res = checkDomain("fn.com");
echo "<pre>";
var_dump($res);
echo "</pre>";
exit();
// aaaa
set_time_limit(0);
for ($i='a'; $i != 'z'; $i++) { 
    for ($j='a'; $j != 'z'; $j++) { 
        for ($k='a'; $k != 'z'; $k++) { 
            for ($l='a'; $l != 'z'; $l++) { 
                $domain = $i.$j.$k.$l . ".com";
                file_put_contents("./domain.log", $domain . "\r\n",FILE_APPEND);
                $res = checkDomain($domain);
                if ($res['200'] and strpos($res['original'], '210') !== false) {
                    file_put_contents("./domain.log", $domain . "\r\n",FILE_APPEND);
                    ECHO $domain . "<BR/>";

                }

            }
        }

    }
}
?>