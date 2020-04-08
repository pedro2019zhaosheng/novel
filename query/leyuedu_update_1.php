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
mysqli_query($conn, "set names 'utf8'");

$ip = '';
$status = 0;
$max_chapter = 0;
$dir = '/home/wwwlogs/';
$push = true;
$sourceid = 8;
$table_num = 0;

$sql = "SELECT a.novelid,a.sourceid,a.name,a.author,a.chapter,b.url,a.chapter_table FROM novel as a LEFT JOIN novel_status as b ON a.novelid = b.novelid WHERE a.sourceid = {$sourceid}";
$result = mysqli_query($conn, $sql);
$array = $result->fetch_all();

foreach ($array as $i => $value) {
    if (!$value[5] or $value[4] > 0 or $value[0] < 43500) {
        continue;
    }

    if (!$ip || $i % 10 == 0) {
        $sql = "select ip from `ip` order by rand() limit 1"; //SQL语句
        $result = mysqli_query($conn, $sql); //查询ip
        $row = $result->fetch_row();
        $ip = $row[0];
    }

    $novelid = $value[0];
    $sourceid = $value[1];
    $novelname = $value[2];
    $url = $value[5];
    $time = date("Y-m-d H:i:s");

    // 根据小说源处理不同的章节筛选方式
    $file = $dir . 'leyuedu_chapter_update_' . date('Y-m-d') . '.log';

    $txt = curlGet(trim($url), $ip, true);
    $html = new simple_html_dom();
    $html->load($txt);

    // 先解析页面是否存在，不存在即过滤
    if (!$html->find('.title', 0)) continue;
    $refresh_time = $html->find('.synopsisArea_detail p', 3)->plaintext;    // 小说最近更新时间
    $refresh_time = str_replace('更新：', '', $refresh_time);
    $refresh_time = date('Y-m-d', strtotime($refresh_time));

    if (time() - strtotime($refresh_time) > 86400 * 50) {
        $status = 1;
    } else {
        $status = 0;
    }
    $html->clear();

    $chapter_t = $value[6];

    // 章节表示每999本小说的章节存1个表，不存在则创建表
    if ($chapter_t) {
        $sql = "Create Table If Not Exists `{$chapter_t}` (
                  `novelid` int(10) NOT NULL DEFAULT '0' COMMENT '小说id',
                  `chapter` int(10) NOT NULL,
                  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '章节名字',
                  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '章节连接',
                  `add_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '添加时间',
                  PRIMARY KEY (`novelid`,`chapter`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $result = mysqli_query($conn, $sql);
    }

    $sql = "select max(chapter) from {$chapter_t} where novelid ={$novelid}";
    $result = mysqli_query($conn, $sql); //查询
    if ($result) {
        $row = $result->fetch_row();
        $max_chapter = $row ? $row[0] : 0;
    }

    $tmp = explode('/', $url);
    $tmp = str_replace('.html', '', $tmp[4]);
    $url = "https://m.lread.net/read/{$tmp}/";
    $txt = curlGet($url, $ip, true);
    $html = new simple_html_dom();
    $html->load($txt);
    $lis = $html->find('.directoryArea p a');

    if ($max_chapter >= count($lis)) {
        $html->clear();
        continue;
    }

    $lis = array_slice($lis, $max_chapter);
    $update = 0;
    foreach ($lis as $key => $li) {
        if ($key == 0) {
            continue;
        }
        $max_chapter++;

        $name = $li->plaintext;     // 章节名称
        $url = "https://m.lread.net" . $li->href;     // 章节连接

        $sql = "insert into $chapter_t(novelid,chapter,name,url,add_time) VALUES ({$novelid},{$max_chapter},'{$name}','{$url}','{$time}')";   // 添加章节数据
        $result = mysqli_query($conn, $sql); //查询
        $update = 1;
    }

    if ($update) {
        $sql = "update novel set status='{$status}',update_time='{$time}',refresh_time='{$time}',`show`=1,chapter='{$max_chapter}' where novelid={$novelid}";
        $result = mysqli_query($conn, $sql);
        file_put_contents($file, "id:{" . $novelid . "} 小说名：{" . $novelname . "} 更新至章节：{" . $max_chapter . "} 数据库：{" . $chapter_t . "} " . date('Y-m-d H:i:s') . " \r\n", FILE_APPEND);

        if ($push) {
            push($novelid, $novelname);
        }
    }

    $html->clear();
}

mysqli_close($conn);