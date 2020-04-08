<?php
/**
 * Created by PhpStorm.
 * User: PHP-SP
 * Date: 2018/12/11
 * Time: 14:38
 */
require 'simple_html_dom.php';
require 'curl.php';

set_time_limit(0);

$conn = mysqli_connect("127.0.0.1", "root", "", "novel") or die("error connecting"); //连接数据库
mysqli_query($conn, "set names 'utf8'"); //数据库输出编码 应该与你的数据库编码保持一致.南昌网站建设公司百恒网络PHP工程师建议用UTF-8 国际标准编码.

$ip = '';
$file = '/data/wwwlogs/leyuedu_' . date('Y-m-d') . '.log';
for ($i = 1; $i < 138835; $i++) {
    $time = date("Y-m-d H:i:s");
    if (!$ip || $i % 5 == 0) {
        $sql = "select ip from `ip` order by rand() limit 1"; //SQL语句
        $result = mysqli_query($conn, $sql); //查询ip
        $row = $result->fetch_row();
        $ip = $row[0];
    }

    $url = "https://m.lread.net/book/{$i}.html";

    // 根据链接查询小说id，如果存在则表示该小说已插入过小说表
    $sql = "select novelid,name from novel_status where url='{$url}'"; //SQL语句
    $result = mysqli_query($conn, $sql); //查询
    $row = $result->fetch_row();
    if (!$row) {
        $txt = curlGet($url, $ip, true);
        $txt = mb_convert_encoding($txt, "UTF-8", "GBK");
        $html = new simple_html_dom();
        $html->load($txt);

        // 解析html字段
        if (!$html->find('.title', 0)) continue;
        $novelname = trim($html->find('.title', 0)->plaintext);  // 小说名

        $img = $html->find('.synopsisArea_detail img', 0)->src;             // 小说封面

        $summary = trim($html->find('.review', 0)->plaintext);       // 小说简介
        $summary = str_replace('&nbsp;', '', $summary);            // 小说简介
        $summary = str_replace('<br />', '', $summary);
        $summary = str_replace('&#12288;', '', $summary);
        $summary = str_replace('【【', '【', $summary);
        $summary = str_replace('】】', '】', $summary);

        $refresh_time = $html->find('.synopsisArea_detail p', 3)->plaintext;    // 小说最近更新时间
        $refresh_time = str_replace('更新：', '', $refresh_time);
        $refresh_time = date('Y-m-d', strtotime($refresh_time));

        if (time() - strtotime($refresh_time) > 86400 * 50) {
            $status = 1;
        } else {
            $status = 0;
        }

        $author = trim($html->find('.synopsisArea_detail p', 0)->plaintext);      // 小说作者
        $author = str_replace('作者：', '', $author);

        $cate = trim($html->find('.synopsisArea_detail p', 1)->plaintext);                // 小说分类
        $cate = str_replace('类别：', '', $cate);

        if (!$novelname || !$img || !$summary || !$refresh_time || !$author) {
            continue;
        }

        $sql = "select novelid,sourceid from novel where name='{$novelname}' and author='{$author}'";
        $result = mysqli_query($conn, $sql);
        $s_row = $result->fetch_row();

        if (!$s_row) {
            $sql = "select cateid,channel from category where keyword like '%$cate%' limit 0,1";
            $result = mysqli_query($conn, $sql); //根据页面的分类名对比数据库分类关键字,获取分类id
            $cateid = 11;
            $channel = 1;
            if ($result) {
                $row = $result->fetch_row();
                $cateid = $row ? $row[0] : 1;
                $channel = $row ? $row[1] : 1;
            }

            $sql = "INSERT INTO novel VALUES (0, {$cateid},{$channel},'8','{$novelname}','{$author}','{$summary}','{$img}',999,'{$status}',0,'{$refresh_time}',0,0,0,'{$time}','{$time}')"; //SQL语句
            $result = mysqli_query($conn, $sql);
            $novelid = mysqli_insert_id($conn);   // 插入小说记录W
            file_put_contents($file, $sql . date('Y-m-d H:i:s') . " \r\n", FILE_APPEND);

            if ($novelid > 0) {
                $sql = "INSERT INTO novel_status VALUES ($novelid, '{$url}','{$novelname}',{$status})"; //插入小说状态语句
                mysqli_query($conn, $sql); //执行
                file_put_contents($file, $sql . " \r\n", FILE_APPEND);

                $sql = "update category set num = num + 1 where cateid = {$cateid}";
                mysqli_query($conn, $sql);
            }
        }
        $html->clear();
    }
}

mysqli_close($conn);