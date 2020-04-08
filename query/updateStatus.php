<?php

$conn=mysqli_connect("127.0.0.1","root","root","novel") or die("error connecting") ; //连接数据库
mysqli_query($conn,"set names 'utf8'"); //数据库输出编码 应该与你的数据库编码保持一致.南昌网站建设公司百恒网络PHP工程师建议用UTF-8 国际标准编码.

$tabs = ['yznn','quanshu', 'boquge','jingcaiyuedu','kanshuzhong'];
for ($i=0; $i <= 50; $i++) {
    foreach ($tabs as $tab) {
        $t = "chapter_" . $tab . "{$i}";
        if(mysqli_num_rows( mysqli_query($conn,"SHOW TABLES LIKE '{$t}'"))) {
            for ($j=0; $j < 50; $j++) {
                $limit = $j * 200;
                $sql = "select novelid from {$t} where name like '%大结局%' group by novelid limit {$limit}, 200;";
                $res = mysqli_query($conn,$sql); //查询
                if (!$res) {
                    break;
                }
                while ($row = $result->fetch_row()) {
                    $noveids[] = $row[0];
                }
                $noveids = implode(',',$noveids);
                $sql = "update novel set status='已完成' where novelid in ({$noveids})";
                mysqli_query($conn,$sql); //查询
                $sql = "update novel_status set status=1 where url like '%{$tab}%' and novelid in ({$noveids})";
                mysqli_query($conn,$sql); //查询
            }
        }
    }
}

mysqli_close($conn);
