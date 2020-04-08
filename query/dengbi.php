<?php
require 'simple_html_dom.php';
require 'curl.php';

set_time_limit(0);

$conn = mysqli_connect("127.0.0.1", "root", "", "novel") or die("error connecting"); //连接数据库
mysqli_query($conn, "set names 'utf8'"); //数据库输出编码 应该与你的数据库编码保持一致.南昌网站建设公司百恒网络PHP工程师建议用UTF-8 国际标准编码.

$ip = '';
$file = '/data/wwwlogs/dengbi_' . date('Y-m-d') . '.log';
for ($i = 1; $i < 100; $i++) {
    $time = date("Y-m-d H:i:s");
    if (!$ip || $i % 20 == 0) {
        $sql = "select ip from `ip` order by rand() limit 1"; //SQL语句
        $result = mysqli_query($conn, $sql); //查询ip
        $row = $result->fetch_row();
        $ip = $row[0];
    }

    sleep(7);

    $url = "https://www.dengbi.cc/shu/{$i}/";

    // 根据链接查询小说id，如果存在则表示该小说已插入过小说表
    $sql = "select novelid,name from novel_status where url='{$url}'"; //SQL语句
    $result = mysqli_query($conn, $sql); //查询
    $row = $result->fetch_row();
    if (!$row) {
        $txt = curlGet($url, $ip, true);
        $txt = mb_convert_encoding($txt, "UTF-8", "gbk");

        // 解析页面不存在页面
        if (strpos($txt, '非常抱歉')) {
            continue;
        }

        $html = new simple_html_dom();
        $html->load($txt);

        $error = $html->find('.error-wrapper', 0);  // 错误数据
        if ($error) {
            continue;
        } else {
            $novelname = trim($html->find('#info h1', 0)->plaintext);  // 小说名
            $img = "http://www.dengbi.cc/files/article/image/" . intval($i / 1000) . "/" . $i . "/" . $i . "s.jpg";             // 小说封面
            $summary = trim($html->find('#intro p', 0)->plaintext);       // 小说简介
            $summary = str_replace('<br />', '', $summary);
            $summary = str_replace('&nbsp;', '', $summary);
            $summary = str_replace('&#12288;', '', $summary);
            $summary = str_replace('【【', '【', $summary);
            $summary = str_replace('】】', '】', $summary);
            $summary = str_replace(' ', '', $summary);

            $author = trim($html->find('#info p', 0)->plaintext);
            $author = str_replace('作&nbsp;&nbsp;&nbsp;&nbsp;者：', '', $author);;      // 小说作者

            $refresh_time = trim($html->find('#info p', 3)->plaintext);    // 小说最近更新时间
            $refresh_time = str_replace('最后更新：', '', $refresh_time);

            $status = trim($html->find('#info p', 1)->plaintext);    // 小说最近更新时间
            $status = str_replace('状&nbsp;&nbsp;&nbsp;&nbsp;态：', '', $refresh_time);

            if ($status == "完结" or time() - strtotime($refresh_time) > 86400 * 100) {
                $status = 1;
            } else {
                $status = 0;
            }

            $cate = 11;      // 小说分类

            if (!$novelname || !$img || !$summary || !$refresh_time || !$author) {
                continue;
            }


            var_dump($i . '--' . $novelname);

            $sql = "select novelid,sourceid from novel where name='{$novelname}' and author='{$author}'";
            $result = mysqli_query($conn, $sql);
            $s_row = $result->fetch_row();

            if (!$s_row) {
//                $sql = "select cateid,channel from category where keyword like '%$cate%' limit 0,1";
//                $result = mysqli_query($conn, $sql); //根据页面的分类名对比数据库分类关键字,获取分类id
//                $cateid = 11;
//                $channel = 1;
//                if ($result) {
//                    $row = $result->fetch_row();
//                    $cateid = $row ? $row[0] : 1;
//                    $channel = $row ? $row[1] : 1;
//                }
//
//                $sql = "INSERT INTO novel VALUES (0, {$cateid},{$channel},'3','{$novelname}','{$author}','{$summary}','{$img}',999,'{$status}',0,'{$refresh_time}',0,0,0,'{$time}','{$time}')"; //SQL语句
//                $result = mysqli_query($conn, $sql);
//                $novelid = mysqli_insert_id($conn);   // 插入小说记录W
//                file_put_contents($file, $sql . date('Y-m-d H:i:s') . " \r\n", FILE_APPEND);
//
//                sleep(3);
//
//                if ($novelid > 0) {
//                    $sql = "INSERT INTO novel_status VALUES ($novelid, '{$url}','{$novelname}',{$status})"; //插入小说状态语句
//                    mysqli_query($conn, $sql); //执行
//                    file_put_contents($file, $sql . " \r\n", FILE_APPEND);
//
//                    $sql = "update category set num = num + 1 where cateid = {$cateid}";
//                    mysqli_query($conn, $sql);
//                }
            }
            $html->clear();
        }
    }
}

mysqli_close($conn);