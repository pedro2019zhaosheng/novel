<?php
/**
 * Created by PhpStorm.
 * User: PHP-SP
 * Date: 2018/12/10
 * Time: 16:24
 */
require 'simple_html_dom.php';
require 'curl.php';

set_time_limit(0);

$conn = mysqli_connect("127.0.0.1", "root", "", "novel") or die("error connecting"); //连接数据库
mysqli_query($conn, "set names 'utf8'"); //数据库输出编码 应该与你的数据库编码保持一致.南昌网站建设公司百恒网络PHP工程师建议用UTF-8 国际标准编码.

$ip = '';
$file = 'mitu_' . date('Y-m-d') . '.log';
for ($i = 1; $i < 800000; $i++) {
    $time = date("Y-m-d H:i:s");
    if (!$ip || $i % 5 == 0) {
        $sql = "select ip from `ip` order by rand() limit 1"; //SQL语句
        $result = mysqli_query($conn, $sql); //查询ip
        $row = $result->fetch_row();
        $ip = $row[0];
    }

    $url = "http://app.youzibank.com/book/info?bookId=" . $i;
    $data = curlGet($url, $ip);
    $data = json_decode($data);

    if ($data->code == 0) {
        file_put_contents($file, $i . " 小说名：{" . $data->data[0]->name . "} 作者：{" . $data->data[0]->author . "} " . date('Y-m-d H:i:s') . " \r\n", FILE_APPEND);
    }
}

mysqli_close($conn);