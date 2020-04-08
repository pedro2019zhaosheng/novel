<?php
require 'simple_html_dom.php';
require 'curl.php';

$conn=mysqli_connect("127.0.0.1","root","root","novel") or die("error connecting") ; //连接数据库
mysqli_query($conn,"set names 'utf8'"); //数据库输出编码 应该与你的数据库编码保持一致.

$sql ="delete from ip where time < " . (time()-30*86400); //删除1个月前的ip
mysqli_query($conn,$sql); //查询 

$url = "http://www.youdaili.net/Daili/http/";

//$txt = file_get_contents($url);
$txt = curlGet($url,'27.192.144.179');
//$txt=mb_convert_encoding($txt, "UTF-8", "GB2312");
$html = new simple_html_dom();
$html->load($txt);

// 更新时间
$time = $html->find('.chunlist span', 0)->plaintext;  //
if (date('Y-m-d',strtotime($time)) != date('Y-m-d')) exit();

// 解析html字段
$url = $html->find('.chunlist a', 0);  //
$url = $url->href;  // 小说名
$s=1;
for ($i=1; $i <= 5; $i++) {
    $_url = $url;
    if ($i != 1)
        $_url = str_replace('.html', "_{$i}.html", $_url);
    $txt = curlGet($_url,'27.192.144.179');
    $html->load($txt);

    $divs = $html->find('.arc .content div');  //
    if (!$divs) {
        $divs = $html->find('.arc .content p');  //
    }
    if (!$divs)
        break;

    $time = strtotime(date('Y-m-d'));
    foreach ($divs as $div) {
        $div = $div->plaintext;
        $ip_i = mb_strpos($div,':');
        $ip = trim(mb_substr($div, 0, $ip_i));
        print_r($ip);
        if (!filter_var($ip, FILTER_VALIDATE_IP)) 
            continue;

        $sql ="INSERT INTO ip VALUES ('{$ip}', '{$time}')"; //插入小说状态语句
        mysqli_query($conn,$sql); //查询 
    }
}

mysqli_close($conn);