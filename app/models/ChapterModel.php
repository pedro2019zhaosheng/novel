<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use App\Libs\Caches\lib\libredis;
use App\models\BaseModel;
use App\models\CateModel;
use DB;


class ChapterModel extends Model
{
    // 数据库'mysql_center'中的nv_novel表
    protected $connection = 'mysql_center';
    protected $table = "nv_novel_chapter";


    public function getChapter($id,$page,$size){


        $result = $this

            ->select('num as chapter','novel_id as novelid','chapter_title as name')
            ->where('novel_id',$id)
            ->groupBy('num')
            ->orderBy('num', 'asc')
            ->skip(($page-1) * $size)
            ->take($size)
            ->get();

        return $result;
    }
    public function getAllChapter($id){


        $result = $this

            ->select('num as chapterId','novel_id as bookid','chapter_title as chapterName')
            ->where('novel_id',$id)
            ->groupBy('num')

            ->get();

        return $result;
    }
    public function getChapterNum($id){
        $result = $this
            ->where('novel_id',$id)
            ->count();

        return $result;
    }



    public function getChapterContent($novelid,$chapter){
        $result = $this

            ->select('chapter_title as title','chapter_url as url','content')
            ->where('novel_id',$novelid)
//            ->where('id',$chapter)
            ->where('num',$chapter)
            ->first();

        return $result;
    }



    public function getMoreChapterContent($novelid,$start,$end){
        $result = $this

            ->select('chapter_title as title','chapter_url as url','content','chapter_num as chapter')
            ->where('novel_id',$novelid)
            ->where('chapter_num','>',$start-1)
            ->where('chapter_num','<',$end+1)
            ->get();

        return $result;
    }


    public  function getLastChapter($novelid){
        $result = $this

            ->select('chapter_title as title','chapter_url as url','content')
            ->where('novel_id',$novelid)
            ->orderBy('num','desc')
            ->first();

        return $result;
    }



    public function getBatchChapter($page,$novelid){
        $result = $this
            ->select('num as chapter','novel_id as novelid','chapter_title as name','content')
            ->where('novel_id',$novelid)
            ->orderBy('num', 'asc')
            ->skip(($page-1) * 5)
            ->take(5)
            ->get();

        return $result ? $result : [];
    }
}