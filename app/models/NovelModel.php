<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use App\Libs\Caches\lib\libredis;
use App\models\BaseModel;
use DB;

class NovelModel extends Model
{
    private static $_instance = array();
    protected $connection;
    protected $table;
    public $timestamps = false;
    public $source = 4;


    // $start 偏移  $limit 数量
    public function categoryTop($start, $limit, $t)
    {
        $novelid = [];
        $confredis = Config::get('database.redis.default');
        $redis = new libredis([$confredis['host'], $confredis['port']]);

        $categoryTop10 = $redis->sRandMember("categoryTop|{$start}|{$end}");
        if (!$categoryTop10) {
            $cates = BaseModel::factory('category')->select('cateid')->get();
            foreach ($cates as $k => $v) {
                $cateid = $v->cateid;
                $novels = BaseModel::factory('novel')->select('novelid')
                    ->where('cateid', $cateid)
                    ->where('show', '=', 1)
                    ->orderBy('read', 'desc')->offset($start)->limit($limit)->get();
                foreach ($novels as $novel) {
                    $i = $redis->sAdd("categoryTop|{$start}|{$end}", $novel->novelid, false, true, 3600);
                }
            }
        }

        while ($t) {
            $n = $redis->sRandMember("categoryTop|{$start}|{$end}");
            if (!$n) {
                break;
            }
            $novelid[] = $n;
            $t--;
        }

        $novelid = array_unique($novelid);

        return $novelid;
    }

    // 按阅读量获取数据  $top 前多少名   $num  获取前几
    public function read($top, $num, $limit = 0)
    {
        $novelid = [];
        $confredis = Config::get('database.redis.default');
        $redis = new libredis([$confredis['host'], $confredis['port']]);

        $novelid_r = $redis->sRandMember("top|{$top}|{$limit}", false, true, $num);
        if (!$novelid_r) {
            $sql = "SELECT novelid FROM novel where `show` = 1 order by `read` desc limit {$limit},{$top}";
            $novelids = DB::select($sql);
            if ($novelids) {
                foreach ($novelids as $n) {
                    $redis->sAdd("top|{$top}|{$limit}", $n->novelid, false, true, 3600);
                }
            }
            $novelid_r = $redis->sRandMember("top|{$top}|{$limit}", false, true, $num);
        }

        return $novelid_r;
    }

    // 按阅读量获取数据  $day 多少天   $num  获取前几
    public function readByDay($day, $num)
    {
        $novelid = [];
        $confredis = Config::get('database.redis.default');
        $redis = new libredis([$confredis['host'], $confredis['port']]);

        $novelid_r = $redis->get("readByDay|{$day}|$num");
        if (!$novelid_r) {
            $start = date('Y-m-d', strtotime('-{$day} days'));
            $end = date('Y-m-d');
            $sql = "SELECT novelid FROM read_log where date >= '{$start}' and date <= '{$end}' limit 0,{$num}";
            $novelids = DB::connection("novel_web")->select($sql);
            if ($novelids) {
                foreach ($novelids as $n) {
                    $novelid[] = $n->novelid;
                }

                $redis->set("readByDay|{$day}|$num", json_encode($novelid), false, true, 3600);
            }
        } else {
            $novelid = json_decode($novelid_r);
        }


        $novelid = array_unique($novelid);

        return $novelid;
    }

    // 按阅读量获取数据  $channel 1 男频 2 女频   $num  获取前几
    public function novelidByChannel($channel, $num)
    {
        $novelid = [];
        $confredis = Config::get('database.redis.default');
        $redis = new libredis([$confredis['host'], $confredis['port']]);

        $novelid_r = $redis->get("channel|{$channel}|{$num}");
        if (!$novelid_r) {
            $sql = "SELECT novelid FROM novel where `show` = 1 and channel={$channel} order by `read` desc limit 10,{$num}";
            $novelids = DB::select($sql);
            if ($novelids) {
                foreach ($novelids as $n) {
                    $novelid[] = $n->novelid;
                }

                $redis->set("channel|{$channel}|{$num}", json_encode($novelid), false, true, 3600);
            }
        } else {
            $novelid = json_decode($novelid_r);
        }

        return $novelid;
    }

    // 按阅读量,完成状态获取数据  $status "连载中","已完成"   $num  获取前几
    public function novelidByStatus($status, $num)
    {
        $novelid = [];
        $confredis = Config::get('database.redis.default');
        $redis = new libredis([$confredis['host'], $confredis['port']]);

        $novelid_r = $redis->get("status|{$status}|{$num}");
        if (!$novelid_r) {
            $sql = "SELECT novelid FROM novel where `show` = 1 and status='{$status}' order by `read` desc limit 0,{$num}";
            $novelids = DB::select($sql);
            if ($novelids) {
                foreach ($novelids as $n) {
                    $novelid[] = $n->novelid;
                }

                $redis->set("status|{$status}|{$num}", json_encode($novelid), false, true, 3600);
            }
        } else {
            $novelid = json_decode($novelid_r);
        }

        return $novelid;
    }

    // 按搜索量获取数据
    public function novelidBySearch($t)
    {
        $novelid = [];
        $confredis = Config::get('database.redis.default');
        $redis = new libredis([$confredis['host'], $confredis['port']]);

        $novelid = $redis->get("search");
        if (!$novelid) {
            $sql = "SELECT novelid FROM novel where `show` = 1 order by search desc limit 5,50";
            $novelids = DB::select($sql);
            if ($novelids) {
                foreach ($novelids as $n) {
                    $novelid[] = $n->novelid;
                }
                $redis->set("search", json_encode($novelid), false, true, 3600);
            }
        } else {
            $novelid = json_decode($novelid);
        }

        if (count($novelid) > $t) {
            shuffle($novelid);
            $novelid = array_slice($novelid, 0, $t);
        }

        return $novelid;
    }

    public function getNovels($novelids)
    {
        if (!$novelids)
            return [];

        $novels = [];
        $novels = BaseModel::factory('novel')->select('novelid', 'name', 'img', 'summary', 'author', 'channel')
            ->whereIn('novelid', $novelids)->get()->toArray();
        return $novels;
    }
}