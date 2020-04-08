<?php
/**
 * Created by PhpStorm.
 * User: PHP-SP
 * Date: 2018/12/3
 * Time: 14:39
 */
require 'simple_html_dom.php';
require 'curl.php';

set_time_limit(0);

$conn = mysqli_connect("localhost", "root", "", "novel") or die("error connecting"); //连接数据库
mysqli_query($conn, "set names 'utf8'"); //数据库输出编码 应该与你的数据库编码保持一致.南昌网站建设公司百恒网络PHP工程师建议用UTF-8 国际标准编码.

$sql = "SELECT a.novelid,b.url FROM novel as a LEFT JOIN novel_status as b ON a.novelid = b.novelid WHERE a.sourceid=3 limit 15,5";
$result = mysqli_query($conn, $sql);
$array = $result->fetch_all();

$ip = '';
foreach ($array as $i => $value) {
    if (!$value[1]) {
        continue;
    }

    if (!$ip || $i % 10 == 0) {
        $sql = "select ip from `ip` order by rand() limit 1"; //SQL语句
        $result = mysqli_query($conn, $sql); //查询ip
        $row = $result->fetch_row();
        $ip = $row[0];
    }

    sleep(2);
    $txt = curlGet(trim($value[1]), $ip, true);
    $txt = mb_convert_encoding($txt, "UTF-8", "GBK");
    $html = new simple_html_dom();
    $html->load($txt);

    // 先解析页面是否存在，不存在即过滤
    $error = $html->find('.error-wrapper', 0);  // 错误数据
    if ($error) {
        continue;
    } else {
        if ($html->find('#all', 0)) {
            $summary = trim($html->find('#all', 0)->plaintext);       // 小说简介
            $summary = str_replace('[收起]', '', $summary);
        } else {
            $summary = trim($html->find('#shot', 0)->plaintext);       // 小说简介
        }

        $summary = str_replace('&#12288;', '', $summary);
        $summary = str_replace('【【', '【', $summary);
        $summary = str_replace('】】', '】', $summary);
    }

    $novelid = $value[0];
    $sql = "update novel set summary='{$summary}' where novelid={$novelid}"; //更新小说状态
    $result = mysqli_query($conn, $sql); //查询
}

mysqli_close($conn);