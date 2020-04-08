<?php
require 'simple_html_dom.php';
require 'curl.php';

$conn=mysqli_connect("127.0.0.1","root","root","novel") or die("error connecting") ; //连接数据库
mysqli_query($conn,"set names 'utf8'"); //数据库输出编码 应该与你的数据库编码保持一致.南昌网站建设公司百恒网络PHP工程师建议用UTF-8 国际标准编码.

$sources = [ 1=>['start'=>'</center>',
                 'end'=>'<div class="button_con">',
                 'name'=>'chapter_yznn'],
             2=>['start'=>'<script type="text/javascript">style5();</script>',
                 'end'=>'<script type="text/javascript">style6();</script>',
                 'name'=>'chapter_quanshu'],
             3=>['start'=>'<div id="txtContent">',
                 'end'=>'<script type="text/javascript">try {applySetting();} catch(ex){}</script>',
                 'name'=>'chapter_boquge'],
             4=>['start'=>'<script>a1();</script>',
                 'end'=>'<script>a2();</script>',
                 'name'=>'chapter_jingcaiyuedu'],
             6=>['start'=>'<script>read_01();</script>',
                 'end'=>'<script>read_02();</script>',
                 'name'=>'chapter_kanshuzhong'],];
$ip ='';
$max_chapter_t = 0;
$basepath = '/home/wwwroot/content/';
foreach ($sources as $key => $source) {
    for ($i=0; $i < 50; $i++) {
        $t = $source['name'] . $i;
        $sql = "SHOW TABLES LIKE '{$t}'";
        $result = mysqli_query($conn,$sql); //判断表是否存在
        $row = $result->fetch_row();
        if (!$row || !$row[0]) {
            continue;
        }

        for ($j=0;$j < 1000; $j++) {
            $limit = $j*1000;
            $sql = "select * from {$t} order by novelid asc limit {$limit},1000";
            $result = mysqli_query($conn,$sql); //获取数据
            $k=0;
            while (($chapter = $result->fetch_row())) {
                $path = $basepath .$t.'_'.$chapter[0].'_'.$chapter[1].'.txt';
                if (file_exists($path))
                    continue;

                if (!$ip || $k % 10 == 0) {
                    $sql ="select ip from `ip` order by rand() limit 1"; //SQL语句
                    $result_ip = mysqli_query($conn,$sql); //查询ip
                    $row = $result_ip->fetch_row();
                    $ip = $row[0];
                    $k=0;
                    sleep(2);
                }
                $content = curlGet($chapter[3], $ip);
                if (strpos($content, 'error-wrapper') !== false) {
                    $error=1;
                    sleep(1);
                    continue;
                }

                if (strpos($content, $source['start']) === false) {
                    sleep(1);
                    continue;
                }
                
                $s = strpos($content, $source['start']) + strlen($source['start']);
                $e = strrpos($content, $source['end']);
                $c = substr($content, $s, $e-$s);
                if (!strpos($content, '<meta charset="utf-8">')) {
                    $c=mb_convert_encoding($c, "UTF-8", "GB2312"); 
                }

                if (mb_strpos($c, "您访问太过频繁") || empty($c)) {
                    $error = 1;
                    sleep(1);
                    continue;
                }

                $c=str_replace('&nbsp;',' ', $c);
                $c=str_replace('<br />',"\r\n", $c);
                $c=str_replace('<br/>',"\r\n", $c);
                $c=str_replace("<br\/>","\r\n", $c);
                $c=str_replace("</br>","\r\n", $c);
                $c = strip_html_tags(['div','script','a','style'],$c);
                $c = preg_replace("/\r\n *\r\n *\r\n/", "\r\n", $c);
                $c = preg_replace("/\r\n *\r\n *\r\n/", "\r\n", $c);
                if ($key == 6 && !(preg_match("/[\x7f-\xff]/", $c))) {
                        //print_r($c);
                        $c = str_replace("\r\n",'&#13;&#10;',$c);
                        $c = unicode_decode($c, 'UTF-8'); 
                        //print_r($c);
                    }
                if ($key != 2)
                    $c = "\r\n" . $chapter[2] . "\r\n" . $c;

                if (mb_strpos($c,'章节数据正在同步中') === false && $c) {
                    $error = 0;
                    file_put_contents($path,$c);
                }
                $k = $k + 1;
            }
            if ($chapter) {
                file_put_contents('/home/wwwroot/novel/query/content.txt',  "{$t} {$chapter[0]} " . date('Y-m-d H:i:s') . " \r\n", FILE_APPEND);
            }
        }
    }
}

mysqli_close($conn);
