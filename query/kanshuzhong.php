<?php
require 'simple_html_dom.php';
require 'curl.php';

$conn=mysqli_connect("127.0.0.1","root","vc&fy^rMi!ult7auSV","novel") or die("error connecting") ; //连接数据库
mysqli_query($conn,"set names 'utf8'"); //数据库输出编码 应该与你的数据库编码保持一致.南昌网站建设公司百恒网络PHP工程师建议用UTF-8 国际标准编码.
 
$ip ='';
$max_chapter_t = 0;
//for ($i=1; $i < 70000; $i++) {
for ($i=1; $i < 120000; $i++) {
    $time = date("Y-m-d H:i:s");
    if (!$ip || $i % 20 == 0) {
        $sql ="select ip from `ip` order by rand() limit 1"; //SQL语句
        $result = mysqli_query($conn,$sql); //查询ip
        $row = $result->fetch_row();
        $ip = $row[0];
    }

    $novelid = 0;
    $url = "http://www.kanshuzhong.com/book/{$i}/";

    // 根据链接查询小说id，如果存在则表示该小说已插入过小说表
    $sql ="select novelid,name,status from novel_status where url='{$url}'"; //SQL语句
    $result = mysqli_query($conn,$sql); //查询
    $row = $result->fetch_row();
    if ($row)  {
        if ($row[2] == 1 && date("w") != 1) {
            continue;
        }
        $novelid = $row[0];
        $novelname = $row[1];
    }

    //$txt = file_get_contents($url);
    $txt = curlGet($url,$ip);
    $txt=mb_convert_encoding($txt, "UTF-8", "GB2312");
    // 解析页面不存在页面
    if (strpos($txt,'页面不存在')) {
        continue;
    }

    $html = new simple_html_dom();
    $html->load($txt);

    // 解析html字段
    if (!$html->find('.book_title h1', 0))  continue;
    $name = $novelname = trim($html->find('.book_title h1', 0)->plaintext);  // 小说名

    if (!$html->find('.readtip img', 0)) continue;
    $img = trim($html->find('.readtip img', 0)->src);             // 小说封面

    $summary = trim($html->find('.readtip',0)->plaintext);       // 小说简介
    $refresh_time = mb_substr($summary,mb_strpos($summary,'(')+1,mb_strpos($summary,')')-mb_strpos($summary,'(')-1);    // 小说最近更新时间
    $summary = mb_substr($summary,0,mb_strpos($summary,'最新章节'));
    $status_str = trim($html->find('.top_left',0)->plaintext);
    $status = '连载中';
    if (mb_strpos($status_str,'连载中') === false) {
        $status = '已完成';
    }

    if (!$html->find('.top_left a',3)) continue;
    $author = trim($html->find('.top_left a',3)->plaintext);      // 小说作者
    $cate = trim($html->find('.top_left a', 1)->plaintext);      // 小说分类
    $new_url = "http://www.kanshuzhong.com/book/{$i}/".trim($html->find('.readtip a',1)->href);
    if (!$name || !$img || !$summary || !$status || !$refresh_time || !$author) {
        continue;
    }

    $summary=str_replace('<br />','', $summary);
    $summary=str_replace('&nbsp;','', $summary);

    $sql = "select novelid,sourceid from novel where name='{$name}' and author='{$author}' limit 0,1";
    $result = mysqli_query($conn,$sql); //查询小说是否已经存在，不存在则写数据库
    $row = $result->fetch_row();
    if (!$row) {
        $sql = "select cateid,channel from category where keyword like '%$cate%' limit 0,1";
        $result = mysqli_query($conn,$sql); //根据页面的分类名对比数据库分类关键字,获取分类id
        $cateid = 11;
        $channel = 1;
        if ($result) {  
            $row = $result->fetch_row();
            $cateid = $row ? $row[0] : 11;
            $channel = $row ? $row[1] : 1;
        }
        $sql ="INSERT INTO novel VALUES (0, {$cateid},{$channel},'6','{$name}','{$author}','{$summary}','{$img}',999,'{$status}',0,'{$refresh_time}',0,0,'{$time}','{$time}')"; //SQL语句
        $result = mysqli_query($conn,$sql);
        $novelid=mysqli_insert_id($conn);   // 插入小说记录

        $status = strpos($status,"已完成") === false ? 0 : 1;
        $sql ="INSERT INTO novel_status VALUES ($novelid, '{$url}','{$name}',{$status})"; //插入小说状态语句
        mysqli_query($conn,$sql); //查询 
    } else {
        $novelid = $row[0];
        $status = strpos($status,"已完成") === false ? 0 : 1;

        $sourceid = strpos($row[1],"6") === false ? 0 : 1;
        if (!$sourceid) {
            $sourceid = $row[1]. ",6";
            $sql ="update novel set sourceid='{$sourceid}' where novelid={$novelid}"; //更新小说状态
            $result = mysqli_query($conn,$sql); //查询 

            $sql ="INSERT INTO novel_status VALUES ($novelid, '{$url}','{$name}',{$status})"; //插入小说状态语句
            mysqli_query($conn,$sql); //查询 
        }
        if ($status) {
            $sql ="update novel_status set status={$status} where url='{$url}'"; //更新小说状态
            $result = mysqli_query($conn,$sql); //查询 
        }
    }
    
    if (!$novelid)
        continue;

    //print_r($i . ' ' . $novelid . "\r\n");
    // 章节表示每10000本存1个数据库，不存在则创建数据库
    if ($max_chapter_t <= $novelid) {
        $chapter_t = 'chapter_kanshuzhong' . intval($novelid/10000);
        $sql = "Create Table If Not Exists `{$chapter_t}` (
                  `novelid` int(10) NOT NULL DEFAULT '0' COMMENT '小说id',
                  `chapter` int(10) NOT NULL,
                  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '章节名字',
                  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '章节连接',
                  `add_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '添加时间',
                  PRIMARY KEY (`novelid`,`chapter`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $result = mysqli_query($conn,$sql);
        $max_chapter_t = (intval($novelid/10000)+1) * 1000;
    }

    $chapter_t = 'chapter_kanshuzhong' . intval($novelid/10000);
    $sql = "select chapter,url from {$chapter_t} where novelid={$novelid} order by chapter desc limit 0,1"; // 获取最大章节数
    $result = mysqli_query($conn,$sql); //查询
    $max_chapter = 0;
    $max_url = "";
    if ($result) {
        $row = $result->fetch_row();
        $max_chapter = $row ? $row[0] : 0;
        $max_url = $row ? $row[1] : '';
    }

    if ($new_url == $max_url) {     // 最新的链接和数据库一样则不更新
        continue;
    }
    $lis=$html->find('.bookcontent a');
    if ($max_chapter >= count($lis)) {   
        $html->clear();
        continue;
    }

    $lis = array_slice($lis , $max_chapter);
    $update=0;
    foreach ($lis as $li) {
        $max_chapter++;

        $name = $li->plaintext;     // 章节名称
        $url = "http://www.kanshuzhong.com/book/{$i}/". $li->href;     // 章节连接
        $sql="insert into $chapter_t(novelid,chapter,name,url,add_time) VALUES ({$novelid},{$max_chapter},'{$name}','{$url}','{$time}')";   // 添加章节数据
        $result = mysqli_query($conn,$sql); //查询
        $update=1;
    }

    if ($update) {
        if (isset($status)) {
            $status = $status == 1 ? "已完成" : "连载中";
        } else {
            $status = "连载中";
        }

        $sql = "update novel set status='{$status}',update_time='{$time}',refresh_time='{$time}',chapter=(select max(chapter) from {$chapter_t} where novelid={$novelid}) where novelid={$novelid}";
        $result = mysqli_query($conn,$sql);
        file_put_contents('kanshuzhong.txt',  "{$i} {$novelid} {$novelname} " . date('Y-m-d H:i:s') . " \r\n", FILE_APPEND);
    }
    $html->clear();
}

$sql = "update category,(SELECT cateid,count(novelid) cnt from novel group by cateid) t set num=cnt where category.cateid=t.cateid";
mysqli_query($conn,$sql);

mysqli_close($conn);
