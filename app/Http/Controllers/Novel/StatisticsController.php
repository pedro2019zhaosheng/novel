<?php

namespace App\Http\Controllers\Novel;

use App\config\rediskeys;
use App\Libs\Caches\lib\libredis;
use App\models\BaseModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Pagination\Paginator;
use Request as Requ;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class StatisticsController extends Controller
{

    public function getDays($beginDate, $endDate)
    {
        $today = date('Y-m-d', time());

        $days[] = $beginDate;
        $d = '';
        for ($i = 1; $i <= 356 && $d != $endDate; $i++) {
            $d = date('Y-m-d', strtotime($beginDate) + 24 * 3600 * $i);
            if ($d > $today || $d > $endDate) {
                break;
            }
            $days[] = $d;
        }

        return $days;
    }
    public function getHours($beginDate, $endDate)
    {
        $today = date('Y-m-d', time());

        $days[] = strtotime($beginDate);
        $d = '';
        for ($i = 1; $i <= 356 && $d != $endDate; $i++) {
            $d = date('Y-m-d H:00:00', strtotime($beginDate) +  3600 * $i);
            if ($d > $today || $d > $endDate) {
                break;
            }
            $days[] = strtotime($d);
        }

        return $days;
    }
    public function getHourstr($beginDate, $endDate)
    {
        $today = date('Y-m-d H:00:00', time());

        $days[] =  date('Y-m-d H:00:00', strtotime($beginDate));
        $d = '';
        for ($i = 1; $i <= 356 && $d != $endDate; $i++) {
            $d = date('Y-m-d H:00:00', strtotime($beginDate) +  3600 * $i);
            if ($d > $today || $d > $endDate) {
                break;
            }
            $days[] = $d;
        }

        return $days;
    }

    public function getHours_0()
    {
        $time = date('H:i');

        $date = date('Y-m-d', time());
        $times[] = date('H:i', strtotime($date));
        $d = '';
        for ($i = 1; $i < 48; $i++) {
            $d = date('H:i', strtotime($date) + 1800 * $i);
            $times[] = $d;
        }

        return $times;
    }

    /*
      注册卸载统计
     */

    public function getStatistics()
    {
        $confredis = Config::get('database.redis.default');
        $redis = new libredis([$confredis['host'], $confredis['port']]);

        $request = Request();
        $where = " 1=1";

        $start = trim($request->input('start', date('Y-m-01', strtotime(date("Y-m-d")))));
        $end = trim($request->input('end', date('Y-m-d')));
        $platform = trim($request->input('platform', 1));
        $pack_name = trim($request->input('pack_name', 0));
        $name = $platform == 1 ? "安卓" : "ios";

        $days = $this->getDays($start, $end);
        foreach ($days as $v) {
            $install[$v] = $activate[$v] = 0;
            $s = strtotime($v);
            $e = strtotime($v) + 86400;
            $c = $redis->hGet("statistics|activate|{$platform}|{$pack_name}", $v);
            if ($c) {
                $activate[$v] = intval($c);
            } else {
                if ($pack_name > 0) {
                    $sql_line = "select count(distinct(uuid)) count from install where platform={$platform} and pack_name={$pack_name} and time >= {$s} and time <= {$e}";
                } else {
                    $sql_line = "select count(distinct(uuid)) count from install where platform={$platform} and time >= {$s} and time <= {$e}";
                }
                $res = DB::select($sql_line);
                $activate[$v] = $res ? $res[0]->count : 0;
                $redis->hSet("statistics|activate|{$platform}|{$pack_name}", $v, $activate[$v]);
            }

            $c = $redis->hGet("statistics|install|{$platform}|{$pack_name}", $v);
            if ($c) {
                $install[$v] = intval($c);
            } else {
                if ($pack_name > 0) {
                    $sql_line = "select count(id) count from download_log where platform={$platform} and pack_name={$pack_name} and add_time >= {$s} and add_time <= {$e}";
                } else {
                    $sql_line = "select count(id) count from download_log where platform={$platform} and add_time >= {$s} and add_time <= {$e}";
                }
                $res = DB::select($sql_line);
                $install[$v] = $res ? $res[0]->count : 0;
                $redis->hSet("statistics|install|{$platform}|{$pack_name}", $v, $install[$v]);
            }
        }

        $yAxisData[] = ['data' => array_values($install), 'name' => "{$name}安装"];
        $yAxisData[] = ['data' => array_values($activate), 'name' => "{$name}激活"];

        $install_total = array_sum($install);
        $activate_total = array_sum($activate);

        $xAxis = json_encode($days);
        $data = json_encode($yAxisData);
        $page_title = "{$name}安装激活统计";

        if ($request->input('btnQuery', "") == "导出") {
            header("Content-type:application/octet-stream");
            header("Accept-Ranges:bytes");
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename={$page_title}.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            $s = "日期\t安装\t激活\n";
            $s = iconv("UTF-8", "GB2312", $s);
            echo $s;

            $s = "总量\t{$install_total}\t{$activate_total}\n";
            $s = iconv("UTF-8", "GB2312", $s);
            echo $s;
            foreach ($install as $k => $v) {
                $s = "{$k}\t{$v}\t{$activate[$k]}\n";
                $s = iconv("UTF-8", "GB2312", $s);
                echo $s;
            }
            return;
        }

        $roleid = BaseModel::factory('role_user')->where('user_id', Auth::user()->id)->value('role_id');

        return view('statistics/statistics', compact('page_title', 'data', 'xAxis', 'start', 'end', 'install', 'activate', 'install_total', 'activate_total', 'platform', 'pack_name', 'roleid'));
    }

    /*
      日活统计
     */

    public function getDaily()
    {
        $confredis = Config::get('database.redis.default');
        $redis = new libredis([$confredis['host'], $confredis['port']]);

        $request = Request();
        $where = " 1=1";

        $start = trim($request->input('start', date('Y-m-01', strtotime(date("Y-m-d")))));
        $end = trim($request->input('end', date('Y-m-d')));
        $platform = trim($request->input('platform', 0));
        $pack_name = trim($request->input('pack_name', 0));

        switch ($platform) {
            case 1:
                $name = '安卓';
                $where .= " and platform=1";
                break;
            case 2:
                $name = 'ios';
                $where .= " and platform=2";
                break;
            default:
                $name = '';
                break;
                break;
        }

        $days = $this->getDays($start, $end);
        foreach ($days as $v) {
            $daily[$v] = 0;
            $c = $redis->hGet("statistics|daily|{$platform}|{$pack_name}", $v);
            if ($c) {
                $daily[$v] = intval($c);
            } else {
                $t = "login";
                $t .= date('Y', strtotime($v)) . (intval(date('m', strtotime($v)) / 5) + 1);
                $s = strtotime($v);
                $e = strtotime($v) + 86400;
                if ($pack_name > 0) {
                    $sql_line = "select count(distinct(uuid)) count from {$t} where {$where} and pack_name = {$pack_name} and time >= {$s} and time <= {$e}";
                } else {
                    $sql_line = "select count(distinct(uuid)) count from {$t} where {$where} and time >= {$s} and time <= {$e}";
                }
                $res = DB::select($sql_line);
                $daily[$v] = $res ? $res[0]->count : 0;
                $redis->hSet("statistics|daily|{$platform}|{$pack_name}", $v, $daily[$v]);
            }
        }

        $yAxisData[] = ['data' => array_values($daily), 'name' => "{$name}日活"];
        $total = array_sum($daily);

        $xAxis = json_encode($days);
        $data = json_encode($yAxisData);
        $page_title = "{$name}日活";

        if ($request->input('btnQuery', "") == "导出") {
            header("Content-type:application/octet-stream");
            header("Accept-Ranges:bytes");
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename={$page_title}.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            $s = "日期\t活跃数\n";
            $s = iconv("UTF-8", "GB2312", $s);
            echo $s;

            $s = "总量\t{$total}\n";
            $s = iconv("UTF-8", "GB2312", $s);
            echo $s;
            foreach ($daily as $k => $v) {
                $s = "{$k}\t{$v}\n";
                $s = iconv("UTF-8", "GB2312", $s);
                echo $s;
            }
            return;
        }

        $roleid = BaseModel::factory('role_user')->where('user_id', Auth::user()->id)->value('role_id');

        return view('statistics/daily', compact('page_title', 'data', 'xAxis', 'start', 'end', 'platform', 'daily', 'total', 'pack_name', 'roleid'));
    }

    /*
      留存统计
     */

    public function getKeep()
    {
        $confredis = Config::get('database.redis.default');
        $redis = new libredis([$confredis['host'], $confredis['port']]);

        $request = Request();
        $where = " 1=1";

        $start = trim($request->input('start', date('Y-m-01', strtotime(date("Y-m-d")))));
        $end = trim($request->input('end', date('Y-m-d')));
        $platform = trim($request->input('platform', 0));
        $pack_name = trim($request->input('pack_name', 0));
        switch ($platform) {
            case 1:
                $name = '安卓';
                $where .= " and platform=1";
                break;
            case 2:
                $name = 'ios';
                $where .= " and platform=2";
                break;
            default:
                $name = '';
                break;
                break;
        }

        $days = $this->getDays($start, $end);

        $t = "login";
        $t .= date('Y', strtotime($start)) . (intval(date('m', strtotime($start)) / 5) + 1);

        // 线性统计图
        foreach ($days as $v) {
            $s1 = strtotime($v);
            $e1 = strtotime($v) + 86400;
            $s2 = strtotime($v) + 86400;
            $e2 = strtotime($v) + 86400 * 2;

            $c = $redis->hGet("statistics|keep|{$platform}|{$pack_name}", $v);
            if ($c) {
                $data[$v] = intval($c);
            } else {
                if ($pack_name > 0) {
                    $sql_line = "select count(distinct uuid) _cnt from install where {$where} and pack_name = {$pack_name} and time between $s1 and $e1";
                } else {
                    $sql_line = "select count(distinct uuid) _cnt from install where {$where} and time between $s1 and $e1";
                }
                $res = DB::select($sql_line);
                if ($res && $res[0]->_cnt != 0) {
                    $t = "login";
                    $t .= date('Y', $s1) . (intval(date('m', $s1) / 5) + 1);

                    if ($pack_name > 0) {
                        $sql_line = "select count(distinct uuid) _cnt from install where {$where} and pack_name = {$pack_name} and time between {$s1} and {$e1} and uuid in(select uuid from {$t} where time between {$s2} and {$e2} group by uuid)";
                    } else {
                        $sql_line = "select count(distinct uuid) _cnt from install where {$where} and time between {$s1} and {$e1} and uuid in(select uuid from {$t} where time between {$s2} and {$e2} group by uuid)";
                    }

                    $r = DB::select($sql_line);
                    $data[$v] = intval(($r[0]->_cnt / $res[0]->_cnt) * 100);
                } else {
                    $data[$v] = 0;
                }
                $redis->hSet("statistics|keep|{$platform}|{$pack_name}", $v, $data[$v]);
            }
        }

        $yAxisData[] = ['data' => array_values($data), 'name' => "{$name}次日留存"];

        $xAxis = json_encode($days);
        $data = json_encode($yAxisData);
        $page_title = "{$name}次日留存";
        $roleid = BaseModel::factory('role_user')->where('user_id', Auth::user()->id)->value('role_id');
        return view('statistics/retained', compact('page_title', 'data', 'xAxis', 'start', 'end', 'platform', 'pack_name', 'roleid'));
    }



//    数据统计相关begin
    public function getNewuser(){
        $request = Request();
        $start = trim($request->input('start', date('Y-m-01', strtotime(date("Y-m-d")))));
        $end = trim($request->input('end', date('Y-m-d',time())));

        $pack_name = trim($request->input('pack_name', 0));

        $days = $this->getDays($start, $end);
//        foreach ($days as $v) {
//
//
//        }

        $data = array();
        $ios = array();
        $android = array();
        $all = array();
        if(!empty($pack_name)){
            for($i=0;$i<count($days);$i++){
                $anquery = "select count(*) as an from user_read_first where type=1  and channel={$pack_name} and date='".$days[$i]."'";
                $anres = DB::selectone($anquery);
                $data[$i]['android'] = $anres->an;
                $android[$i]=$anres->an;
                $ioquery = "select count(*) as io from user_read_first where type=2   and channel={$pack_name} and date='".$days[$i]."'";
                $iores = DB::selectone($ioquery);
                $alquery = "select count(*) as al  from user_read_first where channel={$pack_name}   and date='".$days[$i]."'";
                $alres = DB::selectone($alquery);
                $data[$i]['ios'] = $iores->io;
                $ios[$i]=$iores->io;
                $data[$i]['day'] = $days[$i];
                $data[$i]['al'] = $alres->al;
                $all[$i]=$alres->al;
            }
        }else{
            for($i=0;$i<count($days);$i++){
                $anquery = "select count(*) as an from user_read_first where type=1   and date='".$days[$i]."'";
                $anres = DB::selectone($anquery);
                $data[$i]['android'] = $anres->an;
                $android[$i]=$anres->an;
                $ioquery = "select count(*) as io from user_read_first where type=2    and  date='".$days[$i]."'";
                $iores = DB::selectone($ioquery);
                $alquery = "select count(*) as al  from user_read_first where    date='".$days[$i]."'";
                $alres = DB::selectone($alquery);
                $data[$i]['ios'] = $iores->io;
                $data[$i]['al'] = $alres->al;
                $data[$i]['day'] = $days[$i];
                $all[$i]=$alres->al;
                $ios[$i] = $iores->io;

            }
        }
        $xAxis = json_encode($days);
        $iosyAxis = json_encode($ios);
        $andyAxis = json_encode($android);
        $allyAxis = json_encode($all);






        if ($request->input('btnQuery', "") == "导出") {
            $page_title = '新增用户统计';
            header("Content-type:application/octet-stream");
            header("Accept-Ranges:bytes");
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename={$page_title}.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            $s = "日期\t安卓\tIOS\t新增用户总数\n";
            $s = iconv("UTF-8", "GB2312", $s);
            echo $s;

//            $s = "总量\t{$install_total}\t{$activate_total}\n";
//            $s = iconv("UTF-8", "GB2312", $s);
//            echo $s;
            foreach ($data as $k => $v) {
                $s = "{$v['day']}\t{$v['android']}\t{$v['ios']}\t{$v['al']}\n";
                $s = iconv("UTF-8", "GB2312", $s);
                echo $s;
            }
            return;
        }


        $packArr = DB::table('android_update')->select('id','pack_name')->get();


        $roleid = BaseModel::factory('role_user')->where('user_id', Auth::user()->id)->value('role_id');

        return view(
            'statistics/newuser',
            [
                'page_title' => '新增用户统计',
                'roleid'=>$roleid,
                'pack_name'=>$pack_name,
                'start'=>$start,
                'end'=>$end,
                'datas'=>$data,
                'xAxis'=>$xAxis,
                'iosyAxis'=>$iosyAxis,
                'andyAxis'=>$andyAxis,
                'allyAxis'=>$allyAxis,
                'packArr'=>$packArr
            ]);
    }
    public function getDayactive(){
        $request = Request();
        $start = trim($request->input('start', date('Y-m-01', strtotime(date("Y-m-d")))));
        $end = trim($request->input('end', date('Y-m-d',time())));

        $pack_name = trim($request->input('pack_name', 0));

        $days = $this->getDays($start, $end);
        $data = array();
        $per = array();
        $all = array();
        if(!empty($pack_name)){
            for($i=0;$i<count($days);$i++){

                $alquery = "select count(DISTINCT(uid)) as al  from statistics where channel={$pack_name}  and novelid>0 and addtime='".$days[$i]."'  ";
                $alres = DB::selectone($alquery);
                $data[$i]['day'] = $days[$i];
                $data[$i]['al'] = $alres->al;
                $all[$i]=$alres->al;

                $newuserquery = "select count(DISTINCT(uid)) as newuser  from statistics where channel={$pack_name}  and firstvisit=1 and addtime='".$days[$i]."'  ";
                $newuserres = DB::selectone($newuserquery);
                if(!empty($alres->al)){
                    $percent = round(($newuserres->newuser/$alres->al)*100,2);
                    $data[$i]['per'] = $percent;
                    $per[$i] = round($newuserres->newuser/$alres->al,2);
                }else{
                    $data[$i]['per'] = 0;
                    $per[$i] = 0;
                }
            }
        }else{
            for($i=0;$i<count($days);$i++){

                $alquery = "select count(DISTINCT(uid)) as al  from statistics where   novelid>0 and addtime='".$days[$i]."'";
                $alres = DB::selectone($alquery);
                $data[$i]['day'] = $days[$i];
                $data[$i]['al'] = $alres->al;
                $all[$i]=$alres->al;

                $newuserquery = "select count(DISTINCT(uid)) as newuser  from statistics where  firstvisit=1 and addtime='".$days[$i]."'  ";
                $newuserres = DB::selectone($newuserquery);
                if(!empty($alres->al)){
                    $percent = round(($newuserres->newuser/$alres->al)*100,2);
                    $data[$i]['per'] = $percent;
                    $per[$i] = round($newuserres->newuser/$alres->al,2);
                }else{
                    $data[$i]['per'] = 0;
                    $per[$i] = 0;
                }
            }
        }

        $xAxis = json_encode($days);
        $allyAxis = json_encode($all);
        $peryAxis = json_encode($per);

        $packArr = DB::table('android_update')->select('id','pack_name')->get();
        if ($request->input('btnQuery', "") == "导出") {
            $page_title = '活跃用户统计';
            header("Content-type:application/octet-stream");
            header("Accept-Ranges:bytes");
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename={$page_title}.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            $s = "日期\t活跃用户数\t活跃构成（新增用户占比）%\n";
            $s = iconv("UTF-8", "GB2312", $s);
            echo $s;

            foreach ($data as $k => $v) {
                $s = "{$v['day']}\t{$v['al']}\t{$v['per']}%\n";
                $s = iconv("UTF-8", "GB2312", $s);
                echo $s;
            }
            return;
        }


        $roleid = BaseModel::factory('role_user')->where('user_id', Auth::user()->id)->value('role_id');

        return view(
            'statistics/dayactive',
            [
                'page_title' => '活跃用户统计',
                'roleid'=>$roleid,
                'pack_name'=>$pack_name,
                'start'=>$start,
                'end'=>$end,
                'datas'=>$data,
                'xAxis'=>$xAxis,
                'allyAxis'=>$allyAxis,
                'peryAxis'=>$peryAxis,
                'packArr'=>$packArr
            ]);
    }
    public function getAppstart(){
        $request = Request();
        $start = trim($request->input('start', date('Y-m-01', strtotime(date("Y-m-d")))));
        $end = trim($request->input('end', date('Y-m-d',time())));

        $pack_name = trim($request->input('pack_name', 0));

        $days = $this->getDays($start, $end);
//        foreach ($days as $v) {
//
//
//        }

        $data = array();
        $ios = array();
        $android = array();
        $all = array();
        if(!empty($pack_name)){
            for($i=0;$i<count($days);$i++){
                $anquery = "select count(*) as an from statistics where type=1 and module='0' and novelid=0  and channel={$pack_name} and addtime='".$days[$i]."'";
                $anres = DB::selectone($anquery);
                $data[$i]['android'] = $anres->an;
                $android[$i]=$anres->an;
                $ioquery = "select count(*) as io from statistics where type=2 and module='0' and novelid=0   and channel={$pack_name} and addtime='".$days[$i]."'";
                $iores = DB::selectone($ioquery);
                $alquery = "select count(*) as al  from statistics where channel={$pack_name}  and module='0' and novelid=0  and addtime='".$days[$i]."'";
                $alres = DB::selectone($alquery);
                $data[$i]['ios'] = $iores->io;
                $ios[$i]=$iores->io;
                $data[$i]['day'] = $days[$i];
                $data[$i]['al'] = $alres->al;
                $all[$i]=$alres->al;
            }
        }else{
            for($i=0;$i<count($days);$i++){
                $anquery = "select count(*) as an from statistics where type=1  and module='0' and novelid=0  and addtime='".$days[$i]."'";
                $anres = DB::selectone($anquery);
                $data[$i]['android'] = $anres->an;
                $android[$i]=$anres->an;
                $ioquery = "select count(*) as io from statistics where type=2  and module='0' and novelid=0   and  addtime='".$days[$i]."'";
                $iores = DB::selectone($ioquery);
                $alquery = "select count(*) as al  from statistics where   module='0' and novelid=0  and addtime='".$days[$i]."'";
                $alres = DB::selectone($alquery);
                $data[$i]['ios'] = $iores->io;
                $data[$i]['al'] = $alres->al;
                $data[$i]['day'] = $days[$i];
                $all[$i]=$alres->al;
                $ios[$i] = $iores->io;

            }
        }
        $xAxis = json_encode($days);
        $iosyAxis = json_encode($ios);
        $andyAxis = json_encode($android);
        $allyAxis = json_encode($all);

        $packArr = DB::table('android_update')->select('id','pack_name')->get();

        if ($request->input('btnQuery', "") == "导出") {
            $page_title = 'APP打开次数统计';
            header("Content-type:application/octet-stream");
            header("Accept-Ranges:bytes");
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename={$page_title}.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            $s = "日期\t安卓\tIOS\t打开总数\n";
            $s = iconv("UTF-8", "GB2312", $s);
            echo $s;

//            $s = "总量\t{$install_total}\t{$activate_total}\n";
//            $s = iconv("UTF-8", "GB2312", $s);
//            echo $s;
            foreach ($data as $k => $v) {
                $s = "{$v['day']}\t{$v['android']}\t{$v['ios']}\t{$v['al']}\n";
                $s = iconv("UTF-8", "GB2312", $s);
                echo $s;
            }
            return;
        }


        $roleid = BaseModel::factory('role_user')->where('user_id', Auth::user()->id)->value('role_id');

        return view(
            'statistics/appstart',
            [
                'page_title' => 'APP打开次数统计',
                'roleid'=>$roleid,
                'pack_name'=>$pack_name,
                'start'=>$start,
                'end'=>$end,
                'datas'=>$data,
                'xAxis'=>$xAxis,
                'iosyAxis'=>$iosyAxis,
                'andyAxis'=>$andyAxis,
                'allyAxis'=>$allyAxis,
                'packArr'=>$packArr
            ]);
    }
    public function getUvpv(){
        $request = Request();
        $start = trim($request->input('start', date('Y-m-01', strtotime(date("Y-m-d")))));
        $end = trim($request->input('end', date('Y-m-d',time())));

        $pack_name = trim($request->input('pack_name', 0));
        $days = $this->getDays($start, $end);
        $data = array();

        $all = array();
        $uv = array();
        $pv = array();
        if(!empty($pack_name)){
            for($i=0;$i<count($days);$i++){

                $uvquery = "select count(DISTINCT(uid)) as uv  from statistics where channel={$pack_name}    and addtime='".$days[$i]."'";
                $uvres = DB::selectone($uvquery);

                $pvquery = "select count(*) as pv  from statistics where channel={$pack_name}    and addtime='".$days[$i]."'";
                $pvres = DB::selectone($pvquery);

                $data[$i]['day'] = $days[$i];
                $data[$i]['uv'] = $uvres->uv;
                $data[$i]['pv'] = $pvres->pv;
                $uv[$i] = $uvres->uv;
                $pv[$i] = $pvres->pv;
                if(!empty($uvres->uv)){
                    $avaragepv = $pvres->pv/$uvres->uv;
                    $data[$i]['avaragepv'] = round($avaragepv,2);
                }else{
                    $data[$i]['avaragepv'] = 0;
                }


            }
        }else{
            for($i=0;$i<count($days);$i++){
                $uvquery = "select count(DISTINCT(uid)) as uv  from statistics where  addtime='".$days[$i]."'";
                $uvres = DB::selectone($uvquery);

                $pvquery = "select count(*) as pv  from statistics where   addtime='".$days[$i]."'";
                $pvres = DB::selectone($pvquery);

                $data[$i]['day'] = $days[$i];
                $data[$i]['uv'] = $uvres->uv;
                $data[$i]['pv'] = $pvres->pv;
                $uv[$i] = $uvres->uv;
                $pv[$i] = $pvres->pv;
                if(!empty($uvres->uv)){
                    $avaragepv = $pvres->pv/$uvres->uv;
                    $data[$i]['avaragepv'] = round($avaragepv,2);
                }else{
                    $data[$i]['avaragepv'] = 0;
                }
            }
        }
        $xAxis = json_encode($days);

        $uvyAxis = json_encode($uv);
        $pvyAxis = json_encode($pv);

        $packArr = DB::table('android_update')->select('id','pack_name')->get();

        if ($request->input('btnQuery', "") == "导出") {
            $page_title = 'UV/PV次数统计';
            header("Content-type:application/octet-stream");
            header("Accept-Ranges:bytes");
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename={$page_title}.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            $s = "日期\tUV\tPV\t平均PV\n";
            $s = iconv("UTF-8", "GB2312", $s);
            echo $s;

//            $s = "总量\t{$install_total}\t{$activate_total}\n";
//            $s = iconv("UTF-8", "GB2312", $s);
//            echo $s;
            foreach ($data as $k => $v) {
                $s = "{$v['day']}\t{$v['uv']}\t{$v['pv']}\t{$v['avaragepv']}\n";
                $s = iconv("UTF-8", "GB2312", $s);
                echo $s;
            }
            return;
        }

        $roleid = BaseModel::factory('role_user')->where('user_id', Auth::user()->id)->value('role_id');

        return view(
            'statistics/uvpv',
            [
                'page_title' => 'UV/PV统计',
                'roleid'=>$roleid,
                'pack_name'=>$pack_name,
                'start'=>$start,
                'end'=>$end,
                'datas'=>$data,
                'xAxis'=>$xAxis,

                'uvyAxis'=>$uvyAxis,
                'pvyAxis'=>$pvyAxis,
                'packArr'=>$packArr
            ]);
    }
    public function getNewkeep(){
        $request = Request();
        $start = trim($request->input('start', date('Y-m-01', strtotime(date("Y-m-d")))));
        $end = trim($request->input('end', date('Y-m-d',time())));

        $pack_name = trim($request->input('pack_name', 0));
        $days = $this->getDays($start, $end);
        $data = array();
        if(!empty($pack_name)) {
            for ($i = 0; $i < count($days); $i++) {
                //当天的新增用户数
                $today_query = "select count(*) as num from statistics where firstvisit=1  and channel={$pack_name} and addtime='".$days[$i]."'";
                $today_res = DB::selectone($today_query);
                $today_num = $today_res->num;
                $data[$i]['day'] = $days[$i];
                $data[$i]['today_num'] = $today_num;
                if(empty($today_num)){
                    $data[$i]['today_user'] = 0;

                    $data[$i]['today_num'] = 0;
                    $data[$i]['onelater_num'] = 0;
                    $data[$i]['twolater_num'] = 0;
                    $data[$i]['threelater_num'] = 0;
                    $data[$i]['fourlater_num'] = 0;
                    $data[$i]['fivelater_num'] = 0;
                    $data[$i]['sixlater_num'] = 0;
                    $data[$i]['senvenlater_num'] = 0;
                    $data[$i]['fourteenlater_num'] = 0;
                    $data[$i]['thritylater_num'] = 0;

                    $data[$i]['onelater_per'] = 0;
                    $data[$i]['twolater_per'] = 0;
                    $data[$i]['threelater_per'] = 0;
                    $data[$i]['fourlater_per'] = 0;
                    $data[$i]['fivelater_per'] = 0;
                    $data[$i]['sixlater_per'] = 0;
                    $data[$i]['senvenlater_per'] = 0;
                    $data[$i]['fourteenlater_per'] = 0;
                    $data[$i]['thritylater_per'] = 0;

                }else{
                    //当天的新增具体用户
                    $today_user_query = "select uid,addtime from statistics where firstvisit=1 and channel={$pack_name}  and addtime='".$days[$i]."'";
                    $today_user_res = DB::select($today_user_query);

                    $uidArr=array();
                    $uidArr1=array();
                    array_slice($uidArr1,0,count($uidArr1));
                    foreach ($today_user_res as $k=>$v){
                        $uidArr[$k] = $v->uid;
                        $uidArr1[$k] = "'".$v->uid."'";
                    }
                    $data[$i]['today_user'] = $uidArr;

                    $uid = implode(',',$uidArr1);
                    $onelatertime = $this->getDetailTime($days[$i],1);
                    $twolatertime = $this->getDetailTime($days[$i],2);
                    $threelatertime = $this->getDetailTime($days[$i],3);
                    $fourlatertime = $this->getDetailTime($days[$i],4);
                    $fivelatertime = $this->getDetailTime($days[$i],5);
                    $sixlatertime = $this->getDetailTime($days[$i],6);
                    $senvenlatertime = $this->getDetailTime($days[$i],7);
                    $fourthlatertime = $this->getDetailTime($days[$i],14);
                    $thritylatertime = $this->getDetailTime($days[$i],30);

                    $onelater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and addtime='".$onelatertime."'";
                    $onelater_res = DB::selectone($onelater_query);
                    $data[$i]['onelater_num'] = $onelater_res->num;
                    $data[$i]['onelater_per'] = round($onelater_res->num/$today_num*100,2);

                    $twolater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and addtime='".$twolatertime."'";
                    $twolater_res = DB::selectone($twolater_query);
                    $data[$i]['twolater_num'] = $twolater_res->num;
                    $data[$i]['twolater_per'] = round($twolater_res->num/$today_num*100,2);

                    $threelater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0  and addtime='".$threelatertime."'";
                    $threelater_res = DB::selectone($threelater_query);
                    $data[$i]['threelater_num'] = $threelater_res->num;
                    $data[$i]['threelater_per'] = round($threelater_res->num/$today_num*100,2);

                    $fourlater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0  and addtime='".$fourlatertime."'";
                    $fourlater_res = DB::selectone($fourlater_query);
                    $data[$i]['fourlater_num'] = $fourlater_res->num;
                    $data[$i]['fourlater_per'] = round($fourlater_res->num/$today_num*100,2);

                    $fivelater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and addtime='".$fivelatertime."'";
                    $fivelater_res = DB::selectone($fivelater_query);
                    $data[$i]['fivelater_num'] = $fivelater_res->num;
                    $data[$i]['fivelater_per'] = round($fivelater_res->num/$today_num*100,2);

                    $sixlater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and addtime='".$sixlatertime."'";
                    $sixlater_res = DB::selectone($sixlater_query);
                    $data[$i]['sixlater_num'] = $sixlater_res->num;
                    $data[$i]['sixlater_per'] = round($sixlater_res->num/$today_num*100,2);

                    $senvenlater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and addtime='".$senvenlatertime."'";
                    $senvenlater_res = DB::selectone($senvenlater_query);
                    $data[$i]['senvenlater_num'] = $senvenlater_res->num;
                    $data[$i]['senvenlater_per'] = round($senvenlater_res->num/$today_num*100,2);

                    $fourthlater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and addtime='".$fourthlatertime."'";
                    $fourthlater_res = DB::selectone($fourthlater_query);
                    $data[$i]['fourteenlater_num'] = $fourthlater_res->num;
                    $data[$i]['fourteenlater_per'] = round($fourthlater_res->num/$today_num*100,2);

                    $thrithlater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0  and addtime='".$thritylatertime."'";
                    $thrithlater_res = DB::selectone($thrithlater_query);
                    $data[$i]['thritylater_num'] = $thrithlater_res->num;
                    $data[$i]['thritylater_per'] = round($thrithlater_res->num/$today_num*100,2);


                }


            }
        }else{
            for ($i = 0; $i < count($days); $i++) {
                //当天的新增用户数
                $today_query = "select count(*) as num from statistics where firstvisit=1   and addtime='".$days[$i]."'";
                $today_res = DB::selectone($today_query);
                $today_num = $today_res->num;
                $data[$i]['day'] = $days[$i];
                $data[$i]['today_num'] = $today_num;
                if(empty($today_num)){
                    $data[$i]['today_user'] = 0;

                    $data[$i]['today_num'] = 0;
                    $data[$i]['onelater_num'] = 0;
                    $data[$i]['twolater_num'] = 0;
                    $data[$i]['threelater_num'] = 0;
                    $data[$i]['fourlater_num'] = 0;
                    $data[$i]['fivelater_num'] = 0;
                    $data[$i]['sixlater_num'] = 0;
                    $data[$i]['senvenlater_num'] = 0;
                    $data[$i]['fourteenlater_num'] = 0;
                    $data[$i]['thritylater_num'] = 0;

                    $data[$i]['onelater_per'] = 0;
                    $data[$i]['twolater_per'] = 0;
                    $data[$i]['threelater_per'] = 0;
                    $data[$i]['fourlater_per'] = 0;
                    $data[$i]['fivelater_per'] = 0;
                    $data[$i]['sixlater_per'] = 0;
                    $data[$i]['senvenlater_per'] = 0;
                    $data[$i]['fourteenlater_per'] = 0;
                    $data[$i]['thritylater_per'] = 0;

                }else{
                    //当天的新增具体用户
                    $today_user_query = "select uid,addtime from statistics where firstvisit=1   and addtime='".$days[$i]."'";
                    $today_user_res = DB::select($today_user_query);

                    $uidArr=array();
                    $uidArr1=array();
                    array_slice($uidArr1,0,count($uidArr1));
                    foreach ($today_user_res as $k=>$v){
                        $uidArr[$k] = $v->uid;
                        $uidArr1[$k] = "'".$v->uid."'";

                    }
                    $data[$i]['today_user'] = $uidArr;

                    $uid = implode(',',$uidArr1);
                    $onelatertime = $this->getDetailTime($days[$i],1);
                    $twolatertime = $this->getDetailTime($days[$i],2);
                    $threelatertime = $this->getDetailTime($days[$i],3);
                    $fourlatertime = $this->getDetailTime($days[$i],4);
                    $fivelatertime = $this->getDetailTime($days[$i],5);
                    $sixlatertime = $this->getDetailTime($days[$i],6);
                    $senvenlatertime = $this->getDetailTime($days[$i],7);
                    $fourthlatertime = $this->getDetailTime($days[$i],14);
                    $thritylatertime = $this->getDetailTime($days[$i],30);

                    $onelater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and addtime='".$onelatertime."'";
                    $onelater_res = DB::selectone($onelater_query);
                    $data[$i]['onelater_num'] = $onelater_res->num;
                    $data[$i]['onelater_per'] = round($onelater_res->num/$today_num*100,2);

                    $twolater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and addtime='".$twolatertime."'";
                    $twolater_res = DB::selectone($twolater_query);
                    $data[$i]['twolater_num'] = $twolater_res->num;
                    $data[$i]['twolater_per'] = round($twolater_res->num/$today_num*100,2);

                    $threelater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and addtime='".$threelatertime."'";
                    $threelater_res = DB::selectone($threelater_query);
                    $data[$i]['threelater_num'] = $threelater_res->num;
                    $data[$i]['threelater_per'] = round($threelater_res->num/$today_num*100,2);

                    $fourlater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and addtime='".$fourlatertime."'";
                    $fourlater_res = DB::selectone($fourlater_query);
                    $data[$i]['fourlater_num'] = $fourlater_res->num;
                    $data[$i]['fourlater_per'] = round($fourlater_res->num/$today_num*100,2);

                    $fivelater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid})  and novelid>0 and addtime='".$fivelatertime."'";
                    $fivelater_res = DB::selectone($fivelater_query);
                    $data[$i]['fivelater_num'] = $fivelater_res->num;
                    $data[$i]['fivelater_per'] = round($fivelater_res->num/$today_num*100,2);

                    $sixlater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and addtime='".$sixlatertime."'";
                    $sixlater_res = DB::selectone($sixlater_query);
                    $data[$i]['sixlater_num'] = $sixlater_res->num;
                    $data[$i]['sixlater_per'] = round($sixlater_res->num/$today_num*100,2);

                    $senvenlater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and addtime='".$senvenlatertime."'";
                    $senvenlater_res = DB::selectone($senvenlater_query);
                    $data[$i]['senvenlater_num'] = $senvenlater_res->num;
                    $data[$i]['senvenlater_per'] = round($senvenlater_res->num/$today_num*100,2);

                    $fourthlater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and addtime='".$fourthlatertime."'";
                    $fourthlater_res = DB::selectone($fourthlater_query);
                    $data[$i]['fourteenlater_num'] = $fourthlater_res->num;
                    $data[$i]['fourteenlater_per'] = round($fourthlater_res->num/$today_num*100,2);

                    $thrithlater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and addtime='".$thritylatertime."'";
                    $thrithlater_res = DB::selectone($thrithlater_query);
                    $data[$i]['thritylater_num'] = $thrithlater_res->num;
                    $data[$i]['thritylater_per'] = round($thrithlater_res->num/$today_num*100,2);


                }


            }
        }

        $onelater_numArr = array();
        $onelater_perArr = array();
        $threelater_numArr = array();
        $threelater_perArr = array();
        $senvenlater_numArr = array();
        $senvenlater_perArr = array();
        $thritylater_numArr = array();
        $thritylater_perArr = array();

        foreach ($data as $k=>$v){

            $onelater_numArr[$k] = $v['onelater_num'];
            $onelater_perArr[$k] = $v['onelater_per'];
            $threelater_numArr[$k] = $v['threelater_num'];
            $threelater_perArr[$k] = $v['threelater_per'];
            $senvenlater_numArr[$k] = $v['senvenlater_num'];
            $senvenlater_perArr[$k] = $v['senvenlater_per'];
            $thritylater_numArr[$k] = $v['thritylater_num'];
            $thritylater_perArr[$k] = $v['thritylater_per'];
        }
        $xAxis = json_encode($days);
        $onelaternumyAxis = json_encode($onelater_numArr);
        $onelaterperyAxis = json_encode($onelater_perArr);
        $threelaternumyAxis = json_encode($threelater_numArr);
        $threelaterperyAxis = json_encode($threelater_perArr);
        $senvenlaternumyAxis = json_encode($senvenlater_numArr);
        $senvenlaterperyAxis = json_encode($senvenlater_perArr);
        $thritylaternumyAxis = json_encode($thritylater_numArr);
        $thritylaterperyAxis = json_encode($thritylater_perArr);

        $packArr = DB::table('android_update')->select('id','pack_name')->get();
        if ($request->input('btnQuery', "") == "导出") {
            $page_title = '留存信息统计';
            header("Content-type:application/octet-stream");
            header("Accept-Ranges:bytes");
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename={$page_title}.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            $s = "日期\t新增用户\t1天后\t2天后\t3天后\t4天后\t5天后\t6天后\t7天后\t14天后\t30天后\n";
            $s = iconv("UTF-8", "GB2312", $s);
            echo $s;

//            $s = "总量\t{$install_total}\t{$activate_total}\n";
//            $s = iconv("UTF-8", "GB2312", $s);
//            echo $s;
            foreach ($data as $k => $v) {
                $s = "{$v['day']}\t{$v['today_num']}\t{$v['onelater_num']}\t{$v['twolater_num']}\t{$v['threelater_num']}\t{$v['fourlater_num']}\t{$v['fivelater_num']}\t{$v['sixlater_num']}\t{$v['senvenlater_num']}\t{$v['fourteenlater_num']}\t{$v['thritylater_num']}\n";
                $s = iconv("UTF-8", "GB2312", $s);
                echo $s;
            }
            return;
        }
        $roleid = BaseModel::factory('role_user')->where('user_id', Auth::user()->id)->value('role_id');
        return view(
            'statistics/newkeep',
            [
                'page_title' => '留存信息统计',
                'roleid'=>$roleid,
                'pack_name'=>$pack_name,
                'start'=>$start,
                'end'=>$end,
                'datas'=>$data,
                'xAxis'=>$xAxis,

                'onelaternumyAxis'=>$onelaternumyAxis,
                'onelaterperyAxis'=>$onelaterperyAxis,
                'threelaternumyAxis'=>$threelaternumyAxis,
                'threelaterperyAxis'=>$threelaterperyAxis,
                'senvenlaternumyAxis'=>$senvenlaternumyAxis,
                'senvenlaterperyAxis'=>$senvenlaterperyAxis,
                'thritylaternumyAxis'=>$thritylaternumyAxis,
                'thritylaterperyAxis'=>$thritylaterperyAxis,

                'packArr'=>$packArr

//                'uvyAxis'=>$uvyAxis,
//                'pvyAxis'=>$pvyAxis,
            ]);


    }
    public function getModulepv(){
        $request = Request();
        $start = trim($request->input('start', date('Y-m-01', strtotime(date("Y-m-d")))));
        $end = trim($request->input('end', date('Y-m-d',time())));

        $pack_name = trim($request->input('pack_name', 0));
        $days = $this->getDays($start, $end);
        $data = array();

        $all = array();
        $uv = array();
        $pv = array();
        $today = date('Y-m-d',time());
        if(!empty($pack_name)) {
            $allmodulequery = "select distinct(module) as module from statistics where  channel={$pack_name} ";

        }else{
            $allmodulequery = "select distinct(module) as module from statistics";

        }
        $allres = DB::select($allmodulequery);
        $xAxis = json_encode($days);

        $uvyAxis = json_encode($uv);
        $pvyAxis = json_encode($pv);
        $datas=array();
        if(!empty($allres)){
            foreach ($allres as $k=>$v){
                if(!empty($v->module)) {
                    $datas[$k]['module'] = $v->module;
                    if (!empty($pack_name)) {
                        $modulepvquery = "select count(*)  as num from statistics where addtime='" . $today . "' and channel={$pack_name} and   module='" . $v->module . "'";
                    } else {
                        $modulepvquery = "select count(*) as num from statistics where addtime='" . $today . "' and  module='" . $v->module . "'";
                    }
                    $modulepvres = DB::selectone($modulepvquery);
                    $datas[$k]['todaypv'] = $modulepvres->num;
                }
            }
        }


        $roleid = BaseModel::factory('role_user')->where('user_id', Auth::user()->id)->value('role_id');
        $packArr = DB::table('android_update')->select('id','pack_name')->get();

        if ($request->input('btnQuery', "") == "导出") {
            $page_title = '模块PV统计';
            header("Content-type:application/octet-stream");
            header("Accept-Ranges:bytes");
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename={$page_title}.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            $s = "模块\t今日点击量（pv）\n";
            $s = iconv("UTF-8", "GB2312", $s);
            echo $s;

//            $s = "总量\t{$install_total}\t{$activate_total}\n";
//            $s = iconv("UTF-8", "GB2312", $s);
//            echo $s;
            foreach ($datas as $k => $v) {
                $s = "{$v['module']}\t{$v['todaypv']}\n";
                $s = iconv("UTF-8", "GB2312", $s);
                echo $s;
            }
            return;
        }

        return view(
            'statistics/modulepv',
            [
                'page_title' => '模块PV统计',
                'roleid'=>$roleid,
                'pack_name'=>$pack_name,
                'start'=>$start,
                'end'=>$end,
                'datas'=>$datas,
                'xAxis'=>$xAxis,

                'uvyAxis'=>$uvyAxis,
                'pvyAxis'=>$pvyAxis,
                'packArr'=>$packArr
            ]);
    }
    public function getHistory(){
        $request = Request();
        $start = trim($request->input('start', date('Y-m-01', strtotime(date("Y-m-d")))));
        $end = trim($request->input('end', date('Y-m-d',time())));

        $pack_name = trim($request->input('pack_name', 0));
        $module = trim($request->input('module', ''));

        $days = $this->getDays($start, $end);
//        foreach ($days as $v) {
//
//
//        }

        $data = array();
        $ios = array();
        $android = array();
        $all = array();
        if(!empty($pack_name)){
            for($i=0;$i<count($days);$i++){
                $anquery = "select count(*) as an from statistics where type=1  and channel={$pack_name} and module='{$module}' and addtime='".$days[$i]."'";
                $anres = DB::selectone($anquery);
                $data[$i]['android'] = $anres->an;
                $android[$i]=$anres->an;
                $ioquery = "select count(*) as io from statistics where type=2   and channel={$pack_name} and module='{$module}' and addtime='".$days[$i]."'";
                $iores = DB::selectone($ioquery);
                $alquery = "select count(DISTINCT(uid)) as al  from statistics where channel={$pack_name}  and module='{$module}'  and addtime='".$days[$i]."'";
                $alres = DB::selectone($alquery);
                $data[$i]['ios'] = $iores->io;
                $ios[$i]=$iores->io;
                $data[$i]['day'] = $days[$i];
                $data[$i]['al'] = $alres->al;
                $all[$i]=$alres->al;
            }
        }else{
            for($i=0;$i<count($days);$i++){
                $anquery = "select count(*) as an from statistics where type=1 and module='{$module}'   and addtime='".$days[$i]."'";
                $anres = DB::selectone($anquery);
                $data[$i]['android'] = $anres->an;
                $android[$i]=$anres->an;
                $ioquery = "select count(*) as io from statistics where type=2   and module='{$module}'  and  addtime='".$days[$i]."'";
                $iores = DB::selectone($ioquery);
                $alquery = "select count(*) as al  from statistics where module='{$module}'  and addtime='".$days[$i]."'";
                $alres = DB::selectone($alquery);
                $data[$i]['ios'] = $iores->io;
                $data[$i]['al'] = $alres->al;
                $data[$i]['day'] = $days[$i];
                $all[$i]=$alres->al;
                $ios[$i] = $iores->io;

            }
        }
        $xAxis = json_encode($days);
        $iosyAxis = json_encode($ios);
        $andyAxis = json_encode($android);
        $allyAxis = json_encode($all);
        $packArr = DB::table('android_update')->select('id','pack_name')->get();

        if ($request->input('btnQuery', "") == "导出") {
            $page_title = "{$module}模块PV统计";
            header("Content-type:application/octet-stream");
            header("Accept-Ranges:bytes");
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename={$page_title}.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            $s = "日期\t安卓\tIOS\t点击总数\n";
            $s = iconv("UTF-8", "GB2312", $s);
            echo $s;

//            $s = "总量\t{$install_total}\t{$activate_total}\n";
//            $s = iconv("UTF-8", "GB2312", $s);
//            echo $s;
            foreach ($data as $k => $v) {
                $s = "{$v['day']}\t{$v['android']}\t{$v['ios']}\t{$v['al']}\n";
                $s = iconv("UTF-8", "GB2312", $s);
                echo $s;
            }
            return;
        }


        $roleid = BaseModel::factory('role_user')->where('user_id', Auth::user()->id)->value('role_id');

        return view(
            'statistics/history',
            [
                'page_title' => '模块历史详情',
                'roleid'=>$roleid,
                'pack_name'=>$pack_name,
                'start'=>$start,
                'end'=>$end,
                'datas'=>$data,
                'xAxis'=>$xAxis,
                'iosyAxis'=>$iosyAxis,
                'andyAxis'=>$andyAxis,
                'allyAxis'=>$allyAxis,
                'module'=>$module,
                'packArr'=>$packArr
            ]);
    }
    public function getAndroid(){
        $request = Request();
        $start = trim($request->input('start', date('Y-m-01', strtotime(date("Y-m-d")))));
        $end = trim($request->input('end', date('Y-m-d',time())));

        $pack_name = trim($request->input('pack_name', 0));

        $days = $this->getDays($start, $end);
//        foreach ($days as $v) {
//
//
//        }

        $data = array();
        $ios = array();
        $android = array();
        $all = array();
        if(!empty($pack_name)){
            for($i=0;$i<count($days);$i++){
                $anquery = "select count(*) as an from statistics where type=1 and firstvisit=2 and channel={$pack_name} and addtime='".$days[$i]."'";
                $anres = DB::selectone($anquery);
                $data[$i]['android'] = $anres->an;
                $android[$i]=$anres->an;
                $ioquery = "select count(*) as io from statistics where type=2 and firstvisit=2 and channel={$pack_name} and addtime='".$days[$i]."'";
                $iores = DB::selectone($ioquery);
                $alquery = "select count(*) as al  from statistics where channel={$pack_name} and firstvisit=2 and addtime='".$days[$i]."'";
                $alres = DB::selectone($alquery);
                $data[$i]['ios'] = $iores->io;
                $ios[$i]=$iores->io;
                $data[$i]['day'] = $days[$i];
                $data[$i]['al'] = $alres->al;
                $all[$i]=$alres->al;
            }
        }else{
            for($i=0;$i<count($days);$i++){
                $anquery = "select count(*) as an from statistics where type=1 and firstvisit=2 and addtime='".$days[$i]."'";
                $anres = DB::selectone($anquery);
                $data[$i]['android'] = $anres->an;
                $android[$i]=$anres->an;
                $ioquery = "select count(*) as io from statistics where type=2  and firstvisit=2 and  addtime='".$days[$i]."'";
                $iores = DB::selectone($ioquery);
                $alquery = "select count(*) as al  from statistics where  firstvisit=2 and addtime='".$days[$i]."'";
                $alres = DB::selectone($alquery);
                $data[$i]['ios'] = $iores->io;
                $data[$i]['al'] = $alres->al;
                $data[$i]['day'] = $days[$i];
                $all[$i]=$alres->al;
                $ios[$i] = $iores->io;

            }
        }
        $xAxis = json_encode($days);
        $iosyAxis = json_encode($ios);
        $andyAxis = json_encode($android);
        $allyAxis = json_encode($all);



        $packArr = DB::table('android_update')->select('id','pack_name')->get();
        if ($request->input('btnQuery', "") == "导出") {
            $page_title = '安卓/IOS下载激活统计';
            header("Content-type:application/octet-stream");
            header("Accept-Ranges:bytes");
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename={$page_title}.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            $s = "日期\t安卓\tIOS\t安卓/IOS下载激活总数\n";
            $s = iconv("UTF-8", "GB2312", $s);
            echo $s;

//            $s = "总量\t{$install_total}\t{$activate_total}\n";
//            $s = iconv("UTF-8", "GB2312", $s);
//            echo $s;
            foreach ($data as $k => $v) {
                $s = "{$v['day']}\t{$v['android']}\t{$v['ios']}\t{$v['al']}\n";
                $s = iconv("UTF-8", "GB2312", $s);
                echo $s;
            }
            return;
        }



        $roleid = BaseModel::factory('role_user')->where('user_id', Auth::user()->id)->value('role_id');

        return view(
            'statistics/android',
            [
                'page_title' => '安卓/IOS下载激活统计',
                'roleid'=>$roleid,
                'pack_name'=>$pack_name,
                'start'=>$start,
                'end'=>$end,
                'datas'=>$data,
                'xAxis'=>$xAxis,
                'iosyAxis'=>$iosyAxis,
                'andyAxis'=>$andyAxis,
                'allyAxis'=>$allyAxis,
                'packArr'=>$packArr
            ]);
    }
    public function getDetailTime($time,$day){
        $detailtime = strtotime($time)+$day*86400;
        return date('Y-m-d',$detailtime);
    }

    public function getActivekeep(){
        $request = Request();
        $start = trim($request->input('start', date('Y-m-01', strtotime(date("Y-m-d")))));
        $end = trim($request->input('end', date('Y-m-d',time())));

        $pack_name = trim($request->input('pack_name', 0));
        $days = $this->getDays($start, $end);
        $data = array();
        if(!empty($pack_name)) {
            for ($i = 0; $i < count($days); $i++) {
                //当天的新增用户数
                $today_query = "select count(DISTINCT(uid)) as num  from statistics where   novelid>0 and channel=$pack_name and addtime='".$days[$i]."'";//活跃 打开了小说的用户数
//                $today_query = "select count(*) as num from statistics where firstvisit=1  and channel={$pack_name} and addtime='".$days[$i]."'";
                $today_res = DB::selectone($today_query);
                $today_num = $today_res->num;
                $data[$i]['day'] = $days[$i];
                $data[$i]['today_num'] = $today_num;
                if(empty($today_num)){
                    $data[$i]['today_user'] = 0;

                    $data[$i]['today_num'] = 0;
                    $data[$i]['onelater_num'] = 0;
                    $data[$i]['twolater_num'] = 0;
                    $data[$i]['threelater_num'] = 0;
                    $data[$i]['fourlater_num'] = 0;
                    $data[$i]['fivelater_num'] = 0;
                    $data[$i]['sixlater_num'] = 0;
                    $data[$i]['senvenlater_num'] = 0;
                    $data[$i]['fourteenlater_num'] = 0;
                    $data[$i]['thritylater_num'] = 0;

                    $data[$i]['onelater_per'] = 0;
                    $data[$i]['twolater_per'] = 0;
                    $data[$i]['threelater_per'] = 0;
                    $data[$i]['fourlater_per'] = 0;
                    $data[$i]['fivelater_per'] = 0;
                    $data[$i]['sixlater_per'] = 0;
                    $data[$i]['senvenlater_per'] = 0;
                    $data[$i]['fourteenlater_per'] = 0;
                    $data[$i]['thritylater_per'] = 0;

                }else{
                    //当天的新增具体用户
//                    $today_user_query = "select uid,addtime from statistics where firstvisit=1 and channel={$pack_name}  and addtime='".$days[$i]."'";
                    $today_user_query = "select DISTINCT(uid) as uid  from statistics where  novelid>0 and channel=$pack_name and addtime='".$days[$i]."'";
                    $today_user_res = DB::select($today_user_query);

                    $uidArr=array();
                    $uidArr1=array();
                    array_slice($uidArr1,0,count($uidArr1));
                    foreach ($today_user_res as $k=>$v){
                        $uidArr[$k] = $v->uid;
                        $uidArr1[$k] = "'".$v->uid."'";
                    }
                    $data[$i]['today_user'] = $uidArr;

                    $uid = implode(',',$uidArr1);
                    $onelatertime = $this->getDetailTime($days[$i],1);
                    $twolatertime = $this->getDetailTime($days[$i],2);
                    $threelatertime = $this->getDetailTime($days[$i],3);
                    $fourlatertime = $this->getDetailTime($days[$i],4);
                    $fivelatertime = $this->getDetailTime($days[$i],5);
                    $sixlatertime = $this->getDetailTime($days[$i],6);
                    $senvenlatertime = $this->getDetailTime($days[$i],7);
                    $fourthlatertime = $this->getDetailTime($days[$i],14);
                    $thritylatertime = $this->getDetailTime($days[$i],30);

                    $onelater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and channel=$pack_name and addtime='".$onelatertime."'";
                    $onelater_res = DB::selectone($onelater_query);
                    $data[$i]['onelater_num'] = $onelater_res->num;
                    $data[$i]['onelater_per'] = round($onelater_res->num/$today_num*100,2);

                    $twolater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and channel=$pack_name and addtime='".$twolatertime."'";
                    $twolater_res = DB::selectone($twolater_query);
                    $data[$i]['twolater_num'] = $twolater_res->num;
                    $data[$i]['twolater_per'] = round($twolater_res->num/$today_num*100,2);

                    $threelater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and channel=$pack_name  and addtime='".$threelatertime."'";
                    $threelater_res = DB::selectone($threelater_query);
                    $data[$i]['threelater_num'] = $threelater_res->num;
                    $data[$i]['threelater_per'] = round($threelater_res->num/$today_num*100,2);

                    $fourlater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0  and channel=$pack_name and addtime='".$fourlatertime."'";
                    $fourlater_res = DB::selectone($fourlater_query);
                    $data[$i]['fourlater_num'] = $fourlater_res->num;
                    $data[$i]['fourlater_per'] = round($fourlater_res->num/$today_num*100,2);

                    $fivelater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and channel=$pack_name and addtime='".$fivelatertime."'";
                    $fivelater_res = DB::selectone($fivelater_query);
                    $data[$i]['fivelater_num'] = $fivelater_res->num;
                    $data[$i]['fivelater_per'] = round($fivelater_res->num/$today_num*100,2);

                    $sixlater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0  and channel=$pack_name and addtime='".$sixlatertime."'";
                    $sixlater_res = DB::selectone($sixlater_query);
                    $data[$i]['sixlater_num'] = $sixlater_res->num;
                    $data[$i]['sixlater_per'] = round($sixlater_res->num/$today_num*100,2);

                    $senvenlater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and channel=$pack_name and addtime='".$senvenlatertime."'";
                    $senvenlater_res = DB::selectone($senvenlater_query);
                    $data[$i]['senvenlater_num'] = $senvenlater_res->num;
                    $data[$i]['senvenlater_per'] = round($senvenlater_res->num/$today_num*100,2);

                    $fourthlater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and channel=$pack_name and addtime='".$fourthlatertime."'";
                    $fourthlater_res = DB::selectone($fourthlater_query);
                    $data[$i]['fourteenlater_num'] = $fourthlater_res->num;
                    $data[$i]['fourteenlater_per'] = round($fourthlater_res->num/$today_num*100,2);

                    $thrithlater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and channel=$pack_name and addtime='".$thritylatertime."'";
                    $thrithlater_res = DB::selectone($thrithlater_query);
                    $data[$i]['thritylater_num'] = $thrithlater_res->num;
                    $data[$i]['thritylater_per'] = round($thrithlater_res->num/$today_num*100,2);


                }


            }
        }else{
            for ($i = 0; $i < count($days); $i++) {
                //当天的新增用户数
                $today_query =  "select count(DISTINCT(uid)) as num  from statistics where   novelid>0  and addtime='".$days[$i]."'";
                $today_res = DB::selectone($today_query);
                $today_num = $today_res->num;
                $data[$i]['day'] = $days[$i];
                $data[$i]['today_num'] = $today_num;
                if(empty($today_num)){
                    $data[$i]['today_user'] = 0;

                    $data[$i]['today_num'] = 0;
                    $data[$i]['onelater_num'] = 0;
                    $data[$i]['twolater_num'] = 0;
                    $data[$i]['threelater_num'] = 0;
                    $data[$i]['fourlater_num'] = 0;
                    $data[$i]['fivelater_num'] = 0;
                    $data[$i]['sixlater_num'] = 0;
                    $data[$i]['senvenlater_num'] = 0;
                    $data[$i]['fourteenlater_num'] = 0;
                    $data[$i]['thritylater_num'] = 0;

                    $data[$i]['onelater_per'] = 0;
                    $data[$i]['twolater_per'] = 0;
                    $data[$i]['threelater_per'] = 0;
                    $data[$i]['fourlater_per'] = 0;
                    $data[$i]['fivelater_per'] = 0;
                    $data[$i]['sixlater_per'] = 0;
                    $data[$i]['senvenlater_per'] = 0;
                    $data[$i]['fourteenlater_per'] = 0;
                    $data[$i]['thritylater_per'] = 0;

                }else{
                    //当天的新增具体用户
                    $today_user_query = "select DISTINCT(uid) as uid  from statistics where  novelid>0  and addtime='".$days[$i]."'";

                    $today_user_res = DB::select($today_user_query);

                    $uidArr=array();
                    $uidArr1=array();
                    array_slice($uidArr1,0,count($uidArr1));
                    foreach ($today_user_res as $k=>$v){
                        $uidArr[$k] = $v->uid;
                        $uidArr1[$k] = "'".$v->uid."'";

                    }
                    $data[$i]['today_user'] = $uidArr;

                    $uid = implode(',',$uidArr1);
                    $onelatertime = $this->getDetailTime($days[$i],1);
                    $twolatertime = $this->getDetailTime($days[$i],2);
                    $threelatertime = $this->getDetailTime($days[$i],3);
                    $fourlatertime = $this->getDetailTime($days[$i],4);
                    $fivelatertime = $this->getDetailTime($days[$i],5);
                    $sixlatertime = $this->getDetailTime($days[$i],6);
                    $senvenlatertime = $this->getDetailTime($days[$i],7);
                    $fourthlatertime = $this->getDetailTime($days[$i],14);
                    $thritylatertime = $this->getDetailTime($days[$i],30);

                    $onelater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and addtime='".$onelatertime."'";
                    $onelater_res = DB::selectone($onelater_query);
                    $data[$i]['onelater_num'] = $onelater_res->num;
                    $data[$i]['onelater_per'] = round($onelater_res->num/$today_num*100,2);

                    $twolater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and addtime='".$twolatertime."'";
                    $twolater_res = DB::selectone($twolater_query);
                    $data[$i]['twolater_num'] = $twolater_res->num;
                    $data[$i]['twolater_per'] = round($twolater_res->num/$today_num*100,2);

                    $threelater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and addtime='".$threelatertime."'";
                    $threelater_res = DB::selectone($threelater_query);
                    $data[$i]['threelater_num'] = $threelater_res->num;
                    $data[$i]['threelater_per'] = round($threelater_res->num/$today_num*100,2);

                    $fourlater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and addtime='".$fourlatertime."'";
                    $fourlater_res = DB::selectone($fourlater_query);
                    $data[$i]['fourlater_num'] = $fourlater_res->num;
                    $data[$i]['fourlater_per'] = round($fourlater_res->num/$today_num*100,2);

                    $fivelater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid})  and novelid>0 and addtime='".$fivelatertime."'";
                    $fivelater_res = DB::selectone($fivelater_query);
                    $data[$i]['fivelater_num'] = $fivelater_res->num;
                    $data[$i]['fivelater_per'] = round($fivelater_res->num/$today_num*100,2);

                    $sixlater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and addtime='".$sixlatertime."'";
                    $sixlater_res = DB::selectone($sixlater_query);
                    $data[$i]['sixlater_num'] = $sixlater_res->num;
                    $data[$i]['sixlater_per'] = round($sixlater_res->num/$today_num*100,2);

                    $senvenlater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and addtime='".$senvenlatertime."'";
                    $senvenlater_res = DB::selectone($senvenlater_query);
                    $data[$i]['senvenlater_num'] = $senvenlater_res->num;
                    $data[$i]['senvenlater_per'] = round($senvenlater_res->num/$today_num*100,2);

                    $fourthlater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and addtime='".$fourthlatertime."'";
                    $fourthlater_res = DB::selectone($fourthlater_query);
                    $data[$i]['fourteenlater_num'] = $fourthlater_res->num;
                    $data[$i]['fourteenlater_per'] = round($fourthlater_res->num/$today_num*100,2);

                    $thrithlater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and addtime='".$thritylatertime."'";
                    $thrithlater_res = DB::selectone($thrithlater_query);
                    $data[$i]['thritylater_num'] = $thrithlater_res->num;
                    $data[$i]['thritylater_per'] = round($thrithlater_res->num/$today_num*100,2);


                }


            }
        }

        $onelater_numArr = array();
        $onelater_perArr = array();
        $threelater_numArr = array();
        $threelater_perArr = array();
        $senvenlater_numArr = array();
        $senvenlater_perArr = array();
        $thritylater_numArr = array();
        $thritylater_perArr = array();

        foreach ($data as $k=>$v){

            $onelater_numArr[$k] = $v['onelater_num'];
            $onelater_perArr[$k] = $v['onelater_per'];
            $threelater_numArr[$k] = $v['threelater_num'];
            $threelater_perArr[$k] = $v['threelater_per'];
            $senvenlater_numArr[$k] = $v['senvenlater_num'];
            $senvenlater_perArr[$k] = $v['senvenlater_per'];
            $thritylater_numArr[$k] = $v['thritylater_num'];
            $thritylater_perArr[$k] = $v['thritylater_per'];
        }
        $xAxis = json_encode($days);
        $onelaternumyAxis = json_encode($onelater_numArr);
        $onelaterperyAxis = json_encode($onelater_perArr);
        $threelaternumyAxis = json_encode($threelater_numArr);
        $threelaterperyAxis = json_encode($threelater_perArr);
        $senvenlaternumyAxis = json_encode($senvenlater_numArr);
        $senvenlaterperyAxis = json_encode($senvenlater_perArr);
        $thritylaternumyAxis = json_encode($thritylater_numArr);
        $thritylaterperyAxis = json_encode($thritylater_perArr);

        $packArr = DB::table('android_update')->select('id','pack_name')->get();
        if ($request->input('btnQuery', "") == "导出") {
            $page_title = '留存信息统计';
            header("Content-type:application/octet-stream");
            header("Accept-Ranges:bytes");
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename={$page_title}.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            $s = "日期\t活跃用户\t1天后\t2天后\t3天后\t4天后\t5天后\t6天后\t7天后\t14天后\t30天后\n";
            $s = iconv("UTF-8", "GB2312", $s);
            echo $s;

//            $s = "总量\t{$install_total}\t{$activate_total}\n";
//            $s = iconv("UTF-8", "GB2312", $s);
//            echo $s;
            foreach ($data as $k => $v) {
                $s = "{$v['day']}\t{$v['today_num']}\t{$v['onelater_num']}\t{$v['twolater_num']}\t{$v['threelater_num']}\t{$v['fourlater_num']}\t{$v['fivelater_num']}\t{$v['sixlater_num']}\t{$v['senvenlater_num']}\t{$v['fourteenlater_num']}\t{$v['thritylater_num']}\n";
                $s = iconv("UTF-8", "GB2312", $s);
                echo $s;
            }
            return;
        }
        $roleid = BaseModel::factory('role_user')->where('user_id', Auth::user()->id)->value('role_id');
        return view(
            'statistics/activekeep',
            [
                'page_title' => '每日活跃用户留存信息统计',
                'roleid'=>$roleid,
                'pack_name'=>$pack_name,
                'start'=>$start,
                'end'=>$end,
                'datas'=>$data,
                'xAxis'=>$xAxis,

                'onelaternumyAxis'=>$onelaternumyAxis,
                'onelaterperyAxis'=>$onelaterperyAxis,
                'threelaternumyAxis'=>$threelaternumyAxis,
                'threelaterperyAxis'=>$threelaterperyAxis,
                'senvenlaternumyAxis'=>$senvenlaternumyAxis,
                'senvenlaterperyAxis'=>$senvenlaterperyAxis,
                'thritylaternumyAxis'=>$thritylaternumyAxis,
                'thritylaterperyAxis'=>$thritylaterperyAxis,

                'packArr'=>$packArr

//                'uvyAxis'=>$uvyAxis,
//                'pvyAxis'=>$pvyAxis,
            ]);


    }

    public function getLbuserkeep(){
        $request = Request();
        $start = trim($request->input('start', date('Y-m-01', strtotime(date("Y-m-d")))));
        $end = trim($request->input('end', date('Y-m-d',time())));

        $pack_name = trim($request->input('pack_name', 0));
        $days = $this->getDays($start, $end);
        $data = array();
        if(!empty($pack_name)) {
            for ($i = 0; $i < count($days); $i++) {
                //当天的新增裂变用户数
                $today_query = "select count(*) as num  from novel_user where   userfrom>1 and channel=$pack_name and regtime>'".$days[$i]."' and regtime<'".($days[$i]." 23:59:59")."'";
//                $today_query = "select count(*) as num from statistics where firstvisit=1  and channel={$pack_name} and addtime='".$days[$i]."'";
                $today_res = DB::selectone($today_query);
                $today_num = $today_res->num;
                $data[$i]['day'] = $days[$i];
                $data[$i]['today_num'] = $today_num;
                if(empty($today_num)){
                    $data[$i]['today_user'] = 0;

                    $data[$i]['today_num'] = 0;
                    $data[$i]['onelater_num'] = 0;
                    $data[$i]['twolater_num'] = 0;
                    $data[$i]['threelater_num'] = 0;
                    $data[$i]['fourlater_num'] = 0;
                    $data[$i]['fivelater_num'] = 0;
                    $data[$i]['sixlater_num'] = 0;
                    $data[$i]['senvenlater_num'] = 0;
                    $data[$i]['fourteenlater_num'] = 0;
                    $data[$i]['thritylater_num'] = 0;

                    $data[$i]['onelater_per'] = 0;
                    $data[$i]['twolater_per'] = 0;
                    $data[$i]['threelater_per'] = 0;
                    $data[$i]['fourlater_per'] = 0;
                    $data[$i]['fivelater_per'] = 0;
                    $data[$i]['sixlater_per'] = 0;
                    $data[$i]['senvenlater_per'] = 0;
                    $data[$i]['fourteenlater_per'] = 0;
                    $data[$i]['thritylater_per'] = 0;

                }else{
                    //当天的新增具体用户
//                    $today_user_query = "select uid,addtime from statistics where firstvisit=1 and channel={$pack_name}  and addtime='".$days[$i]."'";
                    $today_user_query = "select id as uid  from novel_user where  userfrom>1 and channel=$pack_name and regtime>'".$days[$i]."' and regtime<'".($days[$i]." 23:59:59")."'";
                    $today_user_res = DB::select($today_user_query);

                    $uidArr=array();
                    $uidArr1=array();
                    array_slice($uidArr1,0,count($uidArr1));
                    foreach ($today_user_res as $k=>$v){
                        $uidArr[$k] = $v->uid;
                        $uidArr1[$k] = "'".$v->uid."'";
                    }
                    $data[$i]['today_user'] = $uidArr;

                    $uid = implode(',',$uidArr1);
                    $onelatertime = $this->getDetailTime($days[$i],1);
                    $twolatertime = $this->getDetailTime($days[$i],2);
                    $threelatertime = $this->getDetailTime($days[$i],3);
                    $fourlatertime = $this->getDetailTime($days[$i],4);
                    $fivelatertime = $this->getDetailTime($days[$i],5);
                    $sixlatertime = $this->getDetailTime($days[$i],6);
                    $senvenlatertime = $this->getDetailTime($days[$i],7);
                    $fourthlatertime = $this->getDetailTime($days[$i],14);
                    $thritylatertime = $this->getDetailTime($days[$i],30);

                    $onelater_query = "select count(*) as num from novel_user where id in ({$uid}) and userfrom>1 and channel=$pack_name and regtime>'".$onelatertime."' and regtime<'".($onelatertime." 23:59:59")."'";
                    $onelater_res = DB::selectone($onelater_query);
                    $data[$i]['onelater_num'] = $onelater_res->num;
                    $data[$i]['onelater_per'] = round($onelater_res->num/$today_num*100,2);

                    $twolater_query = "select count(*) as num from novel_user where id in ({$uid}) and userfrom>1 and channel=$pack_name and regtime>'".$twolatertime."' and regtime<'".($twolatertime." 23:59:59")."'";
                    $twolater_res = DB::selectone($twolater_query);
                    $data[$i]['twolater_num'] = $twolater_res->num;
                    $data[$i]['twolater_per'] = round($twolater_res->num/$today_num*100,2);

                    $threelater_query = "select count(*) as num from novel_user where id in ({$uid}) and userfrom>1 and channel=$pack_name and regtime>'".$threelatertime."' and regtime<'".($threelatertime." 23:59:59")."'";
                    $threelater_res = DB::selectone($threelater_query);
                    $data[$i]['threelater_num'] = $threelater_res->num;
                    $data[$i]['threelater_per'] = round($threelater_res->num/$today_num*100,2);

                    $fourlater_query = "select count(*) as num from novel_user where id in ({$uid}) and userfrom>1 and channel=$pack_name and regtime>'".$fourlatertime."' and regtime<'".($fourlatertime." 23:59:59")."'";
                    $fourlater_res = DB::selectone($fourlater_query);
                    $data[$i]['fourlater_num'] = $fourlater_res->num;
                    $data[$i]['fourlater_per'] = round($fourlater_res->num/$today_num*100,2);

                    $fivelater_query = "select count(*) as num from novel_user where id in ({$uid}) and userfrom>1 and channel=$pack_name and regtime>'".$fivelatertime."' and regtime<'".($fivelatertime." 23:59:59")."'";
                    $fivelater_res = DB::selectone($fivelater_query);
                    $data[$i]['fivelater_num'] = $fivelater_res->num;
                    $data[$i]['fivelater_per'] = round($fivelater_res->num/$today_num*100,2);

                    $sixlater_query = "select count(*) as num from novel_user where id in ({$uid}) and userfrom>1 and channel=$pack_name and regtime>'".$sixlatertime."' and regtime<'".($sixlatertime." 23:59:59")."'";
                    $sixlater_res = DB::selectone($sixlater_query);
                    $data[$i]['sixlater_num'] = $sixlater_res->num;
                    $data[$i]['sixlater_per'] = round($sixlater_res->num/$today_num*100,2);

                    $senvenlater_query = "select count(*) as num from novel_user where id in ({$uid}) and userfrom>1 and channel=$pack_name and regtime>'".$senvenlatertime."' and regtime<'".($senvenlatertime." 23:59:59")."'";
                    $senvenlater_res = DB::selectone($senvenlater_query);
                    $data[$i]['senvenlater_num'] = $senvenlater_res->num;
                    $data[$i]['senvenlater_per'] = round($senvenlater_res->num/$today_num*100,2);

                    $fourthlater_query = "select count(*) as num from novel_user where id in ({$uid}) and userfrom>1 and channel=$pack_name and regtime>'".$fourthlatertime."' and regtime<'".($fourthlatertime." 23:59:59")."'";
                    $fourthlater_res = DB::selectone($fourthlater_query);
                    $data[$i]['fourteenlater_num'] = $fourthlater_res->num;
                    $data[$i]['fourteenlater_per'] = round($fourthlater_res->num/$today_num*100,2);

                    $thrithlater_query = "select count(*) as num from novel_user where id in ({$uid}) and userfrom>1 and channel=$pack_name and regtime>'".$thritylatertime."' and regtime<'".($thritylatertime." 23:59:59")."'";
                    $thrithlater_res = DB::selectone($thrithlater_query);
                    $data[$i]['thritylater_num'] = $thrithlater_res->num;
                    $data[$i]['thritylater_per'] = round($thrithlater_res->num/$today_num*100,2);


                }


            }
        }else{
            for ($i = 0; $i < count($days); $i++) {

                $today_query =  "select count(*) as num  from novel_user where   userfrom>1   and regtime>'".$days[$i]."' and regtime<'".($days[$i]." 23:59:59")."'";
                $today_res = DB::selectone($today_query);
                $today_num = $today_res->num;
                $data[$i]['day'] = $days[$i];
                $data[$i]['today_num'] = $today_num;
                if(empty($today_num)){
                    $data[$i]['today_user'] = 0;

                    $data[$i]['today_num'] = 0;
                    $data[$i]['onelater_num'] = 0;
                    $data[$i]['twolater_num'] = 0;
                    $data[$i]['threelater_num'] = 0;
                    $data[$i]['fourlater_num'] = 0;
                    $data[$i]['fivelater_num'] = 0;
                    $data[$i]['sixlater_num'] = 0;
                    $data[$i]['senvenlater_num'] = 0;
                    $data[$i]['fourteenlater_num'] = 0;
                    $data[$i]['thritylater_num'] = 0;

                    $data[$i]['onelater_per'] = 0;
                    $data[$i]['twolater_per'] = 0;
                    $data[$i]['threelater_per'] = 0;
                    $data[$i]['fourlater_per'] = 0;
                    $data[$i]['fivelater_per'] = 0;
                    $data[$i]['sixlater_per'] = 0;
                    $data[$i]['senvenlater_per'] = 0;
                    $data[$i]['fourteenlater_per'] = 0;
                    $data[$i]['thritylater_per'] = 0;

                }else{

                    $today_user_query = "select id as uid  from novel_user where  userfrom>1   and regtime>'".$days[$i]."' and regtime<'".($days[$i]." 23:59:59")."'";

                    $today_user_res = DB::select($today_user_query);

                    $uidArr=array();
                    $uidArr1=array();
                    array_slice($uidArr1,0,count($uidArr1));
                    foreach ($today_user_res as $k=>$v){
                        $uidArr[$k] = $v->uid;
                        $uidArr1[$k] = "'".$v->uid."'";

                    }
                    $data[$i]['today_user'] = $uidArr;

                    $uid = implode(',',$uidArr1);
                    $onelatertime = $this->getDetailTime($days[$i],1);
                    $twolatertime = $this->getDetailTime($days[$i],2);
                    $threelatertime = $this->getDetailTime($days[$i],3);
                    $fourlatertime = $this->getDetailTime($days[$i],4);
                    $fivelatertime = $this->getDetailTime($days[$i],5);
                    $sixlatertime = $this->getDetailTime($days[$i],6);
                    $senvenlatertime = $this->getDetailTime($days[$i],7);
                    $fourthlatertime = $this->getDetailTime($days[$i],14);
                    $thritylatertime = $this->getDetailTime($days[$i],30);

                    $onelater_query = "select count(*) as num from novel_user where id in ({$uid}) and userfrom>1   and regtime>'".$onelatertime."' and regtime<'".($onelatertime." 23:59:59")."'";
                    $onelater_res = DB::selectone($onelater_query);
                    $data[$i]['onelater_num'] = $onelater_res->num;
                    $data[$i]['onelater_per'] = round($onelater_res->num/$today_num*100,2);

                    $twolater_query = "select count(*) as num from novel_user where id in ({$uid}) and userfrom>1   and regtime>'".$twolatertime."' and regtime<'".($twolatertime." 23:59:59")."'";
                    $twolater_res = DB::selectone($twolater_query);
                    $data[$i]['twolater_num'] = $twolater_res->num;
                    $data[$i]['twolater_per'] = round($twolater_res->num/$today_num*100,2);

                    $threelater_query = "select count(*) as num from novel_user where id in ({$uid}) and userfrom>1   and regtime>'".$threelatertime."' and regtime<'".($threelatertime." 23:59:59")."'";
                    $threelater_res = DB::selectone($threelater_query);
                    $data[$i]['threelater_num'] = $threelater_res->num;
                    $data[$i]['threelater_per'] = round($threelater_res->num/$today_num*100,2);

                    $fourlater_query = "select count(*) as num from novel_user where id in ({$uid}) and userfrom>1   and regtime>'".$fourlatertime."' and regtime<'".($fourlatertime." 23:59:59")."'";
                    $fourlater_res = DB::selectone($fourlater_query);
                    $data[$i]['fourlater_num'] = $fourlater_res->num;
                    $data[$i]['fourlater_per'] = round($fourlater_res->num/$today_num*100,2);

                    $fivelater_query = "select count(*) as num from novel_user where id in ({$uid}) and userfrom>1   and regtime>'".$fivelatertime."' and regtime<'".($fivelatertime." 23:59:59")."'";
                    $fivelater_res = DB::selectone($fivelater_query);
                    $data[$i]['fivelater_num'] = $fivelater_res->num;
                    $data[$i]['fivelater_per'] = round($fivelater_res->num/$today_num*100,2);

                    $sixlater_query = "select count(*) as num from novel_user where id in ({$uid}) and userfrom>1   and regtime>'".$sixlatertime."' and regtime<'".($sixlatertime." 23:59:59")."'";
                    $sixlater_res = DB::selectone($sixlater_query);
                    $data[$i]['sixlater_num'] = $sixlater_res->num;
                    $data[$i]['sixlater_per'] = round($sixlater_res->num/$today_num*100,2);

                    $senvenlater_query = "select count(*) as num from novel_user where id in ({$uid}) and userfrom>1   and regtime>'".$senvenlatertime."' and regtime<'".($senvenlatertime." 23:59:59")."'";
                    $senvenlater_res = DB::selectone($senvenlater_query);
                    $data[$i]['senvenlater_num'] = $senvenlater_res->num;
                    $data[$i]['senvenlater_per'] = round($senvenlater_res->num/$today_num*100,2);

                    $fourthlater_query = "select count(*) as num from novel_user where id in ({$uid}) and userfrom>1   and regtime>'".$fourthlatertime."' and regtime<'".($fourthlatertime." 23:59:59")."'";
                    $fourthlater_res = DB::selectone($fourthlater_query);
                    $data[$i]['fourteenlater_num'] = $fourthlater_res->num;
                    $data[$i]['fourteenlater_per'] = round($fourthlater_res->num/$today_num*100,2);

                    $thrithlater_query = "select count(*) as num from novel_user where id in ({$uid}) and userfrom>1   and regtime>'".$thritylatertime."' and regtime<'".($thritylatertime." 23:59:59")."'";
                    $thrithlater_res = DB::selectone($thrithlater_query);
                    $data[$i]['thritylater_num'] = $thrithlater_res->num;
                    $data[$i]['thritylater_per'] = round($thrithlater_res->num/$today_num*100,2);


                }


            }
        }

        $onelater_numArr = array();
        $onelater_perArr = array();
        $threelater_numArr = array();
        $threelater_perArr = array();
        $senvenlater_numArr = array();
        $senvenlater_perArr = array();
        $thritylater_numArr = array();
        $thritylater_perArr = array();

        foreach ($data as $k=>$v){

            $onelater_numArr[$k] = $v['onelater_num'];
            $onelater_perArr[$k] = $v['onelater_per'];
            $threelater_numArr[$k] = $v['threelater_num'];
            $threelater_perArr[$k] = $v['threelater_per'];
            $senvenlater_numArr[$k] = $v['senvenlater_num'];
            $senvenlater_perArr[$k] = $v['senvenlater_per'];
            $thritylater_numArr[$k] = $v['thritylater_num'];
            $thritylater_perArr[$k] = $v['thritylater_per'];
        }
        $xAxis = json_encode($days);
        $onelaternumyAxis = json_encode($onelater_numArr);
        $onelaterperyAxis = json_encode($onelater_perArr);
        $threelaternumyAxis = json_encode($threelater_numArr);
        $threelaterperyAxis = json_encode($threelater_perArr);
        $senvenlaternumyAxis = json_encode($senvenlater_numArr);
        $senvenlaterperyAxis = json_encode($senvenlater_perArr);
        $thritylaternumyAxis = json_encode($thritylater_numArr);
        $thritylaterperyAxis = json_encode($thritylater_perArr);

        $packArr = DB::table('android_update')->select('id','pack_name')->get();
        if ($request->input('btnQuery', "") == "导出") {
            $page_title = '裂变留存信息统计';
            header("Content-type:application/octet-stream");
            header("Accept-Ranges:bytes");
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename={$page_title}.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            $s = "日期\t裂变用户\t1天后\t2天后\t3天后\t4天后\t5天后\t6天后\t7天后\t14天后\t30天后\n";
            $s = iconv("UTF-8", "GB2312", $s);
            echo $s;

//            $s = "总量\t{$install_total}\t{$activate_total}\n";
//            $s = iconv("UTF-8", "GB2312", $s);
//            echo $s;
            foreach ($data as $k => $v) {
                $s = "{$v['day']}\t{$v['today_num']}\t{$v['onelater_num']}\t{$v['twolater_num']}\t{$v['threelater_num']}\t{$v['fourlater_num']}\t{$v['fivelater_num']}\t{$v['sixlater_num']}\t{$v['senvenlater_num']}\t{$v['fourteenlater_num']}\t{$v['thritylater_num']}\n";
                $s = iconv("UTF-8", "GB2312", $s);
                echo $s;
            }
            return;
        }
        $roleid = BaseModel::factory('role_user')->where('user_id', Auth::user()->id)->value('role_id');
        return view(
            'statistics/lbuserkeep',
            [
                'page_title' => '裂变留存信息统计',
                'roleid'=>$roleid,
                'pack_name'=>$pack_name,
                'start'=>$start,
                'end'=>$end,
                'datas'=>$data,
                'xAxis'=>$xAxis,

                'onelaternumyAxis'=>$onelaternumyAxis,
                'onelaterperyAxis'=>$onelaterperyAxis,
                'threelaternumyAxis'=>$threelaternumyAxis,
                'threelaterperyAxis'=>$threelaterperyAxis,
                'senvenlaternumyAxis'=>$senvenlaternumyAxis,
                'senvenlaterperyAxis'=>$senvenlaterperyAxis,
                'thritylaternumyAxis'=>$thritylaternumyAxis,
                'thritylaterperyAxis'=>$thritylaterperyAxis,

                'packArr'=>$packArr

//                'uvyAxis'=>$uvyAxis,
//                'pvyAxis'=>$pvyAxis,
            ]);


    }

    public function getHouractive(){
        $request = Request();
        $start = trim($request->input('start', date('Y-m-01', strtotime(date("Y-m-d")))));
        $end = trim($request->input('end', date('Y-m-02',time())));

        $pack_name = trim($request->input('pack_name', 0));

//        $days = $this->getDays($start, $end);
        $hours = $this->getHours($start, $end);
        $hourstr = $this->getHourstr($start, $end);
        $data = array();
        $per = array();
        $all = array();
        if(!empty($pack_name)){
            for($i=0;$i<count($hours);$i++){

                $alquery = "select count(DISTINCT(uid)) as al  from statistics where channel={$pack_name}  and novelid>0 and timestamp>='".$hours[$i]."' and timestamp < '".($hours[$i]+3600)."'";
                $alres = DB::selectone($alquery);
                $data[$i]['day'] = $hourstr[$i];
                $data[$i]['al'] = $alres->al;
                $all[$i]=$alres->al;

                $newuserquery = "select count(DISTINCT(uid)) as newuser  from statistics where channel={$pack_name}  and firstvisit=1 and timestamp>='".$hours[$i]."' and timestamp < '".($hours[$i]+3600)."'";
                $newuserres = DB::selectone($newuserquery);
                if(!empty($alres->al)){
                    $percent = round(($newuserres->newuser/$alres->al)*100,2);
                    $data[$i]['per'] = $percent;
                    $per[$i] = round($newuserres->newuser/$alres->al,2);
                }else{
                    $data[$i]['per'] = 0;
                    $per[$i] = 0;
                }
            }
        }else{
            for($i=0;$i<count($hours);$i++){

                $alquery = "select count(DISTINCT(uid)) as al  from statistics where   novelid>0 and timestamp>='".$hours[$i]."' and timestamp < '".($hours[$i]+3600)."'";
                $alres = DB::selectone($alquery);
                $data[$i]['day'] = $hourstr[$i];
                $data[$i]['al'] = $alres->al;
                $all[$i]=$alres->al;

                $newuserquery = "select count(DISTINCT(uid)) as newuser  from statistics where  firstvisit=1 and timestamp>='".$hours[$i]."' and timestamp < '".($hours[$i]+3600)."'";
                $newuserres = DB::selectone($newuserquery);
                if(!empty($alres->al)){
                    $percent = round(($newuserres->newuser/$alres->al)*100,2);
                    $data[$i]['per'] = $percent;
                    $per[$i] = round($newuserres->newuser/$alres->al,2);
                }else{
                    $data[$i]['per'] = 0;
                    $per[$i] = 0;
                }
            }
        }

        $xAxis = json_encode($hourstr);
        $allyAxis = json_encode($all);
        $peryAxis = json_encode($per);

        $packArr = DB::table('android_update')->select('id','pack_name')->get();
        if ($request->input('btnQuery', "") == "导出") {
            $page_title = '用户活跃时段统计';
            header("Content-type:application/octet-stream");
            header("Accept-Ranges:bytes");
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename={$page_title}.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            $s = "日期\t活跃用户数\t活跃构成（新增用户占比）%\n";
            $s = iconv("UTF-8", "GB2312", $s);
            echo $s;

            foreach ($data as $k => $v) {
                $s = "{$v['day']}\t{$v['al']}\t{$v['per']}%\n";
                $s = iconv("UTF-8", "GB2312", $s);
                echo $s;
            }
            return;
        }


        $roleid = BaseModel::factory('role_user')->where('user_id', Auth::user()->id)->value('role_id');

        return view(
            'statistics/houractive',
            [
                'page_title' => '用户活跃时段统计',
                'roleid'=>$roleid,
                'pack_name'=>$pack_name,
                'start'=>$start,
                'end'=>$end,
                'datas'=>$data,
                'xAxis'=>$xAxis,
                'allyAxis'=>$allyAxis,
                'peryAxis'=>$peryAxis,
                'packArr'=>$packArr,
                'hour'=>$hours
            ]);
    }
//    数据统计相关end
}
