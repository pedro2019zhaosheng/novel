<?php
/**
 * Created by PhpStorm.
 * User: PHP-SP
 * Date: 2018/12/11
 * Time: 9:49
 */
require 'simple_html_dom.php';
require 'curl.php';

set_time_limit(0);

$conn = mysqli_connect("localhost", "root", "", "novel") or die("error connecting"); //连接数据库
mysqli_query($conn, "set names 'utf8'"); //数据库输出编码 应该与你的数据库编码保持一致.南昌网站建设公司百恒网络PHP工程师建议用UTF-8 国际标准编码.

$ip = '';
$file = '/data/wwwlogs/dingdian_' . date('Y-m-d') . '.log';
for ($i = 1; $i < 50; $i++) {
//foreach ($novel_array as $i => $url) {
    $time = date("Y-m-d H:i:s");
    if (!$ip || $i % 10 == 0) {
        $sql = "select ip from `ip` order by rand() limit 1"; //SQL语句
        $result = mysqli_query($conn, $sql); //查询ip
        $row = $result->fetch_row();
        $ip = $row[0];
    }

    $url = "https://www.dingdiann.com/ddk" . $i;
    $txt = curlGet(trim($url), $ip, true);
    $html = new simple_html_dom();
    $html->load($txt);

    // 先解析页面是否存在，不存在即过滤
    $error = $html->find('.error-wrapper', 0);  // 错误数据
    if ($error) {
        continue;
    } else {
        // 解析html字段
        if (!$html->find('#info h1', 0)) continue;
        $novelname = trim($html->find('#info h1', 0)->plaintext);  // 小说名

        if (!$html->find('#fmimg img', 0)) continue;
        $img = trim($html->find('#fmimg img', 0)->src);             // 小说封面
        $img = 'https://www.dingdiann.com' . $img;

        if ($html->find('#intro', 0)) {
            $summary = trim($html->find('#intro', 0)->plaintext);       // 小说简介
        }
        $summary = str_replace('<br />', '', $summary);
        $summary = str_replace('&nbsp;', '', $summary);
        $summary = str_replace('&#12288;', '', $summary);
        $summary = str_replace('【【', '【', $summary);
        $summary = str_replace('】】', '】', $summary);
        $summary = str_replace('　', '', $summary);

        $refresh_time = trim($html->find('#info p', 2)->plaintext);    // 小说最近更新时间
        $refresh_time = str_replace('最后更新：', '', $refresh_time);

        if (!$html->find('#info p', 0)) continue;
        $author = trim($html->find('#info p', 0)->plaintext);      // 小说作者
        $author = str_replace('作&nbsp;&nbsp;者：', '', $author);

        $status = 0;
        if (time() - strtotime($refresh_time) > 86400 * 100) {
            $status = 1;
        }

        if (!$novelname || !$img || !$summary || !$refresh_time || !$author) {
            continue;
        }

        //查询小说是否已经存在

        $sql = "select novelid,sourceid from novel where name='{$novelname}' and author='{$author}'";
        $result = mysqli_query($conn, $sql);
        $s_row = $result->fetch_row();

        if (!$s_row) {
            $cateid = 11;
            $channel = 1;

            $sql = "INSERT INTO novel VALUES (0, {$cateid},{$channel},'7','{$novelname}','{$author}','{$summary}','{$img}',999,'{$status}',0,'{$refresh_time}',0,0,0,'{$time}','{$time}')"; //SQL语句
            $result = mysqli_query($conn, $sql);
            $novelid = mysqli_insert_id($conn);   // 插入小说记录W
            file_put_contents($file, $sql . date('Y-m-d H:i:s') . " \r\n", FILE_APPEND);

            sleep(3);

            if ($novelid > 0) {
                $sql = "INSERT INTO novel_status VALUES ($novelid, '{$url}','{$novelname}',{$status})"; //插入小说状态语句
                mysqli_query($conn, $sql); //执行
                file_put_contents($file, $sql . " \r\n", FILE_APPEND);

                $sql = "update category set num = num + 1 where cateid = {$cateid}";
                mysqli_query($conn, $sql);
            }
        } else {
            $novelid = $s_row[0];
            $sql = "update novel set status='{$status}' where novelid={$novelid}"; //更新小说状态
            $result = mysqli_query($conn, $sql); //查询

            // 根据链接查询本地数据库是否已下载过小说
            $sql = "select novelid,name,status from novel_status where novelid='{$novelid}'"; //SQL语句
            $result = mysqli_query($conn, $sql); //查询
            $row = $result->fetch_row();
            if (!$row) {
                $sql = "INSERT INTO novel_status VALUES ($novelid, '{$url}','{$novelname}',{$status})"; //插入小说状态语句
                file_put_contents($file, $sql . " \r\n", FILE_APPEND);
            } else {
                $sql = "update novel_status set status='{$status}' where novelid={$novelid}"; //更新小说状态
            }
            mysqli_query($conn, $sql); //查询

        }
    }
    $html->clear();
}

mysqli_close($conn);