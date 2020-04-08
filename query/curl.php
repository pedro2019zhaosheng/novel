<?php
require 'JPush.php';

function push($novelid, $novelname)
{
    $pushObj = new JPush();
    $content = "您书架里的《" . $novelname . "》有新的章节可以看啦。详情>>";
    $m_time = 600;        //离线保留时间

    //调用推送,并处理
    $result = $pushObj->push($novelid, $content, $m_time);
    if ($result) {
        $res_arr = json_decode($result, true);
        if (isset($res_arr['error'])) {                       //如果返回了error则证明失败
            $pushObj->push($content, $m_time);
        } else {
            return true;
        }
    } else {      //接口调用失败或无响应
        $pushObj->push($content, $m_time);
    }
}

function curlGet($url, $ip, $ssl = false)
{
    $ch = curl_init();
    //$user_agent = 'Safari Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.73.11 (KHTML, like Gecko) Version/7.0.1 Safari/5';
    $user_agent = "* Baiduspider+(+http://www.baidu.com/search/spider.htm)";
    curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
    // 2. 设置选项，包括URL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-FORWARDED-FOR:{$ip}", "CLIENT-IP:{$ip}"));  //构造IP
    curl_setopt($ch, CURLOPT_REFERER, "http://www.baidu.com/");   //构造来路
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

    if ($ssl) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    }

    // 3. 执行并获取HTML文档内容
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function strip_html_tags($tags, $str)
{
    $html = array();
    foreach ($tags as $tag) {
        $html[] = '/<' . $tag . '.*?>[\s|\S]*?<\/' . $tag . '>/';
        $html[] = '/<' . $tag . '.*?>/';
        $html[] = '/<\/' . $tag . '.*?>/';
    }
    $data = preg_replace($html, '', $str);
    return $data;
} 