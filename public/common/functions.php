<?php

use App\Libs\Caches\lib\libredis;
use App\models\Syslog;
use Illuminate\Support\Facades\Config;

function p($data)
{
    echo '<pre>';
    print_r($data);
    echo "</pre>";
}

function setRedis($name, $value, $zip = false, $serialize = true, $timeout = 0)
{
    $confredis = Config::get('database.redis.default');
    $redis = new libredis([$confredis['host'], $confredis['port']]);
    $redis->set($name, $value, $zip = false, $serialize = true, $timeout = 0);
    $redis->close();
}

function getRedis($name, $zip = false, $serial = true)
{
    $confredis = Config::get('database.redis.default');
    $redis = new libredis([$confredis['host'], $confredis['port']]);
    $result = $redis->get($name, $zip, $serial);
    $redis->close();
    return $result;
}

function hsetRedis($field, $name, $value, $zip = false, $serial = false, $timeout = 0)
{
    $confredis = Config::get('database.redis.default');
    $redis = new libredis([$confredis['host'], $confredis['port']]);
    $redis->hSet($field, $name, $value, $zip = false, $serial = false, $timeout = 0);
    $redis->close();
}

function hgetRedis($field, $name, $zip = false, $serial = false)
{
    $confredis = Config::get('database.redis.default');
    $redis = new libredis([$confredis['host'], $confredis['port']]);
    $result = $redis->hGet($field, $name, $zip = false, $serial = false);
    $redis->close();
    return $result;
}

function delRedis($key)
{
    $confredis = Config::get('database.redis.default');
    $redis = new libredis([$confredis['host'], $confredis['port']]);
    $result = $redis->delete($key);
    $redis->close();
    return $result;
}

function hdelRedis($field, $name)
{
    $confredis = Config::get('database.redis.default');
    $redis = new libredis([$confredis['host'], $confredis['port']]);
    $result = $redis->hDel($name, $field);
    $redis->close();
    return $result;
}

//生产随机字符串
function generate_password($length = 8)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[mt_rand(0, strlen($chars) - 1)];
    }
    return $password;
}

/**
 * 记录系统操作日志
 * @param string $uid
 * @param string $name
 * @param string $desc
 * @param string $ip
 * @param $ctime
 */
function saveSyslog($uid = '', $name = '', $desc = '', $ip = '', $ctime)
{
    $syslog = new Syslog();
    $res = $syslog->insertGetId([
        'userid' => $uid,
        'name' => $name,
        'desc' => $desc,
        'ip' => $ip,
        'created_at' => $ctime
    ]);
    if ($res) {
        return true;
    } else {
        return false;
    }
}

function getCates()
{
    $values = DB::table('category')->select(['cateid', 'name'])->orderBy('sort', 'ASC')->get();

    $cates = [];
    foreach ($values as $v) {
        $cates[$v->cateid] = $v->name;
    }

    return $cates;
}

function curls($url, $data_string)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'X-AjaxPro-Method:ShowList',
        'User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36'
    ));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}

function fieldAsKey($dataArr, $field = 'id')
{
    if (!$dataArr || !is_array($dataArr)) return $dataArr;
    $newArr = array();
    foreach ($dataArr as $row) {
        $row = (array)$row;
        if (!isset($row[$field])) {
            return $dataArr;
        }
        $fieldval = $row[$field];
        $newArr[$fieldval] = $row;
    }
    return $newArr;
}

function curlGet($url, $ip)
{
    $post_url = 'http://47.112.200.195:8088';
    $params = array(
        'url' => $url,
        'ip' => $ip,
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $post_url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

    if (strpos($url, 'https') !== false) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    }

    // 3. 执行并获取HTML文档内容
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function urlappend($url = null)
{
    if (!function_exists('urlappend_inner')) {
        function urlappend_inner($url, $k = null, $v = null)
        {
            if (!$url) {
                $url = 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
            }
            $uriArr = parse_url($url);
            if ('#' == $k) {
                if (is_null($v))
                    unset($uriArr['fragment']);
                else
                    $uriArr['fragment'] = $v;
            } else {
                $paraArr = array();
                if (isset($uriArr['query'])) {
                    parse_str($uriArr['query'], $paraArr);
                }
                if (is_null($v))
                    unset($paraArr[$k]);
                else
                    $paraArr[$k] = $v;
                $uriArr['query'] = http_build_query($paraArr);
            }
            $new_url = '';
            $new_url .= isset($uriArr['scheme']) ? $uriArr['scheme'] . '://' : '';
            $new_url .= isset($uriArr['host']) ? $uriArr['host'] : '';
            $new_url .= isset($uriArr['port']) ? ':' . $uriArr['port'] : '';
            $new_url .= isset($uriArr['path']) ? $uriArr['path'] : '';
            $new_url .= !empty($uriArr['query']) ? '?' . $uriArr['query'] : '';
            $new_url .= !empty($uriArr['fragment']) ? '#' . $uriArr['fragment'] : '';

            return $new_url;
        }
    }
    $args = array_slice(func_get_args(), 1);
    if ($args) {
        for ($a = 0, $len = count($args); $a < $len; $a += 2) {
            $url = urlappend_inner($url, $args[$a], $args[$a + 1]);
        }
    } else {
        if (!$url) {
            $url = urlappend_inner(null);
        }
    }
    return $url;
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


function unicode_decode($unistr, $encoding = 'GBK', $prefix = '&#', $postfix = ';')
{
    $arruni = explode($prefix, $unistr);
    $unistr = '';
    for ($i = 1, $len = count($arruni); $i < $len; $i++) {
        if (strlen($postfix) > 0) {
            $arruni[$i] = substr($arruni[$i], 0, strlen($arruni[$i]) - strlen($postfix));
        }
        $temp = intval($arruni[$i]);
        $unistr .= ($temp < 256) ? chr(0) . chr($temp) : chr($temp / 256) . chr($temp % 256);
    }
    return iconv('UCS-2', $encoding, $unistr);
}
