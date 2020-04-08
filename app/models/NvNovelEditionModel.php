<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use App\Libs\Caches\lib\libredis;
use App\models\BaseModel;
use App\models\CateModel;
use DB;


class NvNovelEditionModel  extends Model
{
    // 数据库'mysql_center'中的nv_novel表
    protected $connection = 'mysql_center';
    protected $table = "nv_novel";


    public function getPopularity($page,$size){

        $result = $this->from($this->table . ' as novel')
            ->select('novel.name as name','novel.source_id as sourceid','novel.source_id as sourceid','novel.nove_line_cate_id as cateid','novel.id as novelid','novel.click_nums as read','novel.img as img','novel.summary as summary','novel.author as author', 'cate.name as catename')
            ->join('nv_novel_line_category as cate', function($join){
                $join->on('cate.id', '=', 'novel.nove_line_cate_id');
            })

            ->where('novel.status', '=', 1)
            ->where('novel.is_finished', '=', 1)
            ->orderBy('novel.click_nums', 'desc')
            ->skip(($page-1) * $size)
            ->take($size)
            ->get();

        if(!empty($result)){
            foreach ($result as $k=>$value){
                $result[$k]->summary = empty($value->summary)?'':$value->summary;
                $result[$k]->read = empty($value->read)?0:$value->read;
                if($result[$k]->read >10000){
                    $result[$k]->read = (intval($result[$k]->read/1000)/10)."W+";
                }
            }
        }

        return $result ? $result : [];

    }

    public function getNovelIdArray($idArr,$size){
        $result = $this->from($this->table . ' as novel')
            ->select('novel.name as name','novel.source_id as sourceid','novel.source_id as sourceid','novel.nove_line_cate_id as cateid','novel.id as novelid','novel.click_nums as read','novel.img as img','novel.summary as summary','novel.author as author', 'cate.name as catename')
            ->join('nv_novel_line_category as cate', function($join){
                $join->on('cate.id', '=', 'novel.nove_line_cate_id');
            })

            ->whereIn('novel.id', $idArr )
            ->where('novel.status', '=', 1)
            ->where('novel.is_finished', '=', 1)
            ->orderBy('sort', 'desc')
            ->take($size)
            ->get();
        if(!empty($result)){
            foreach ($result as $k=>$value){
                $result[$k]->summary = empty($value->summary)?'':$value->summary;
                $result[$k]->read = empty($value->read)?0:$value->read;
                if($result[$k]->read >10000){
                    $result[$k]->read = (intval($result[$k]->read/1000)/10)."W+";
                }
            }
        }
        return $result ? $result : [];

    }

    public function getRefreshEndNovel($page,$size,$status){
        $result = $this->from($this->table . ' as novel')
            ->select('novel.name as novelname','novel.last_updated_at as refresh_time','novel.source_id as sourceid','novel.nove_line_cate_id as cateid','novel.id as novelid','novel.click_nums as read','novel.img as img','novel.summary as summary','novel.author as author', 'cate.name as catename')
            ->join('nv_novel_line_category as cate', function($join){
                $join->on('cate.id', '=', 'novel.nove_line_cate_id');
            })

            ->where('novel.progress_status', $status )
            ->where('novel.status', '=', 1)
            ->where('novel.is_finished', '=', 1)
            ->orderBy('novel.click_nums', 'desc')
            ->skip(($page-1) * $size)
            ->take($size)
            ->get();

        if(!empty($result)){
            foreach ($result as $k=>$value){
                $result[$k]->summary = empty($value->summary)?'':$value->summary;
                $result[$k]->refresh_time = empty($value->refresh_time)?'':$value->refresh_time;
                $result[$k]->read = empty($value->read)?0:$value->read;
                if($result[$k]->read >10000){
                    $result[$k]->read = (intval($result[$k]->read/1000)/10)."W+";
                }
            }
        }
        return $result ? $result : [];


    }



    public function getCateNovel($novelid,$page){
        $ret = $this->from($this->table . ' as novel')
            ->select('novel.nove_line_cate_id')

            ->where('novel.id',$novelid)

            ->first();
        $cate_id = $ret['nove_line_cate_id'];


        $result = $this->from($this->table . ' as novel')
            ->select('novel.name as name','novel.id as novelid','novel.img as img','novel.author as author')

            ->where('novel.nove_line_cate_id',$cate_id)
            ->where('novel.status', '=', 1)
            ->where('novel.is_finished', '=', 1)
            ->orderBy('novel.click_nums','desc')
            ->skip(($page-1) * 6)
            ->take(6)
            ->get();
        return $result ? $result : [];
    }

    public function getTopClickNovel($page){
        $result = $this->from($this->table . ' as novel')
            ->select('novel.name as name','novel.id as novelid')
            ->join('nv_novel_line_category as cate', function($join){
                $join->on('cate.id', '=', 'novel.nove_line_cate_id');
            })
            ->where('novel.status', '=', 1)
            ->where('novel.is_finished', '=', 1)
            ->orderBy('novel.novel_clicks', 'desc')
            ->skip(($page-1) * 10)
            ->take(10)
            ->get();

        return $result ? $result : [];
    }
}