<?php
require 'curl.php';

$conn=mysqli_connect("127.0.0.1","root","root","novel") or die("error connecting") ; //连接数据库
mysqli_query($conn,"set names 'utf8'"); //数据库输出编码 应该与你的数据库编码保持一致.南昌网站建设公司百恒网络PHP工程师建议用UTF-8 国际标准编码.

$time = date('Y-m-d H:i:s', time()-1800);
$sql = "select novelid,name from novel where `read` != 0 and update_time >= '{$time}'";
$result = mysqli_query($conn,$sql); //查询
if ($result) {
    $novelid = [];
    while (($row = $result->fetch_row())) {
        $novelid[] = $row[0];
        if (count($novelid) >= 18) {    
            push($novelid,'803c15e63435aa2dd262dece','9cba466c330d1ed53e3cd3c2');
            push($novelid,'528bb2d1f7cc9225a2752309','affd8bb2052aac5f01cedd92');
            push($novelid,'b9fcc14c1d8a7c0f3550609f','3548c7312a23a7cdd70a49a0');
            push($novelid,'a2c7c839483cda97af96fb25','ca177cdac1bf80a1b958522d');
            push($novelid,'7f9737551639e5b84d3fa5bd','ff2405ce54f0334ad4f099b7');
            push($novelid,'f6e1ae9ac803387b3a161216','dcfc24b1ba43b3b095082ca6');
            push($novelid,'408083cd088e9b5ec0a61315','8e5c6740450c766ef0035e9f');
            push($novelid,'9d7d73720e88c2e563a2f56e','08fdb892df71a8e77e729be1');
            file_put_contents('/home/wwwroot/novel/query/ts.txt',  json_encode($novelid) . date('Y-m-d H:i:s') . " \r\n", FILE_APPEND);
            sleep(30);
            $novelid = [];
        }
    }
    if ($novelid) {   
        push($novelid,'803c15e63435aa2dd262dece','9cba466c330d1ed53e3cd3c2');
        push($novelid,'528bb2d1f7cc9225a2752309','affd8bb2052aac5f01cedd92');
        push($novelid,'b9fcc14c1d8a7c0f3550609f','3548c7312a23a7cdd70a49a0');
        push($novelid,'a2c7c839483cda97af96fb25','ca177cdac1bf80a1b958522d');
        push($novelid,'7f9737551639e5b84d3fa5bd','ff2405ce54f0334ad4f099b7');
        push($novelid,'f6e1ae9ac803387b3a161216','dcfc24b1ba43b3b095082ca6');
        push($novelid,'408083cd088e9b5ec0a61315','8e5c6740450c766ef0035e9f');
        push($novelid,'9d7d73720e88c2e563a2f56e','08fdb892df71a8e77e729be1');
        file_put_contents('/home/wwwroot/novel/query/ts.txt',  json_encode($novelid) . date('Y-m-d H:i:s') . " \r\n", FILE_APPEND);
    }
}


mysqli_close($conn);