<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use App\Libs\Caches\lib\libredis;
use App\models\BaseModel;
use App\models\CateModel;
use DB;


class ChapterInsertModel extends Model
{
    // 数据库'mysql_center'中的nv_novel表
    protected $connection = 'mysql_center';
    protected $table = "nv_novel_chapter1";


    public function insertChapter($novelid,$chapter_list)
    {
        foreach ($chapter_list as $k=>$value){
            $data=array(
                'novel_id'=>$novelid,
                'num'=>$value['num'],
                'chapter_url'=>$value['chapter_url'],
                'chapter_title'=>$value['chapter_title'],
                'is_charge'=>3,
                'chapter_word_nums'=>$value['chapter_word_nums'],
                'content'=>"'".$value['content']."'",
                'chapter_updated_at'=>$value['chapter_updated_at'],
                'day'=>date('Y-m-d',time()),
                'created_at'=>date('Y-m-d H:i:s',time()),
                'updated_at'=>date('Y-m-d H:i:s',time()),

            );
            $this->insert($data);
        }


    }
}