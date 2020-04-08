<?php
/**
 * Created by PhpStorm.
 * User: PHP-SP
 * Date: 2018/12/20
 * Time: 11:08
 */
set_time_limit(0);

$conn = mysqli_connect("localhost", "root", "", "novel") or die("error connecting"); //连接数据库
mysqli_query($conn, "set names 'utf8'"); //数据库输出编码 应该与你的数据库编码保持一致.南昌网站建设公司百恒网络PHP工程师建议用UTF-8 国际标准编码.

$tmp1 = 0;
$tmp2 = 0;
$tmp3 = 0;
$tmp4 = 0;


$sql = "SELECT novelid,sourceid FROM novel";
$result = mysqli_query($conn, $sql);
$array = $result->fetch_all();

foreach ($array as $key => $value) {
    if ($sourceid == 3) {
        $tmp1++;
        $table_name = 'chapter_boquge' . intval($tmp1 / 1000);
    } elseif ($sourceid == 2) {
        $tmp2++;
        $table_name = 'chapter_quanshu' . intval($tmp2 / 1000);
    } elseif ($sourceid == 7) {
        $tmp3++;
        $table_name = 'chapter_dingdian' . intval($tmp3 / 1000);
    } elseif ($sourceid == 8) {
        $tmp4++;
        $table_name = 'chapter_leyuedu' . intval($tmp4 / 1000);
    }

    $sql = "update novel set chapter_table='{$table_name}' where novelid={$value[0]} and chapter_table = ''";
    $result = mysqli_query($conn, $sql);
}

mysqli_close($conn);