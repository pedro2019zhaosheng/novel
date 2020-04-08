<?php
require 'simple_html_dom.php';
require 'curl.php';

$conn=mysqli_connect("127.0.0.1","root","root","novel") or die("error connecting") ; //连接数据库
mysqli_query($conn,"set names 'utf8'"); //数据库输出编码 应该与你的数据库编码保持一致.南昌网站建设公司百恒网络PHP工程师建议用UTF-8 国际标准编码.

for ($i=1; $i < 50; $i++) {
    $novelid = 0;
    $url = "http://www.66ip.cn/{$i}.html";

    //$txt = file_get_contents('http://www.66ip.cn/3.html');
    $txt = curlGet($url,'134.35.184.165');
    $txt=mb_convert_encoding($txt, "UTF-8", "GB2312");
    $html = new simple_html_dom();
    $html->load($txt);

    // 解析页面不存在页面
    $error = $html->find('.blocktitle', 0);  // 错误数据
    if ($error) break;

    // 解析html字段
    $trs = $html->find('.containerbox tr');  // 小说名
    $time=0;
    if (!$trs)
        break;
    
    foreach ($trs as $tr) {
        $ip = $tr->find('td',0)->plaintext;
        if (!filter_var($ip, FILTER_VALIDATE_IP)) 
            continue;

        $time_s = $tr->find('td',4)->plaintext;
        $i_y = mb_strpos($time_s, '年');
        $i_m = mb_strpos($time_s, '月');
        $i_d = strpos($time_s, '日');
        $y = intval(mb_substr($time_s, 0, $i_y));
        $m = intval(mb_substr($time_s, $i_y+1, 2));
        $d = intval(mb_substr($time_s, $i_m+1, 2));

        $time = mktime(0, 0, 0, $m, $d ,$y);
        
        $sql ="INSERT INTO ip VALUES ('{$ip}', '{$time}')"; //插入小说状态语句
        mysqli_query($conn,$sql); //查询 
    }

    if (strtotime("-1 month") > $time) {
        break;
    }
}

mysqli_close($conn);