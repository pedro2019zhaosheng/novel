<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use App\Libs\Caches\lib\libredis;
use App\models\BaseModel;
use DB;

class NovelClickRecordModel extends Model
{

    protected $connection = 'mysql';
    protected $table = "novel_click_record";
    public $timestamps = false;


    public function recordMsg($novelid,$name)
    {
        $result = $this->select('id','num')
            ->where('novelid',$novelid)
            ->first();
        if(empty($result)){

            $data=array(
                'novelid'=>$novelid,
                'name'=>$name,
                'num'=>1
            );
            $this->insert($data);
        }else{
            $num = $result->num +1;
            $data=array(
              'num'=>$num
            );
            $result = $this->where('id',$result->id)
                ->update($data);
            return $result;
        }
    }


    public function getTopNovelClick(){

        $result = $this->from($this->table . ' as novel_click_record')
            ->select('novel_click_record.novelid','novel_click_record.num','novel_click_record.name','novel.author','novel.source_id','novel.chapter','novel.novel_url')
            ->join('nv_novel as novel', function($join){
                $join->on('novel_click_record.novelid', '=', 'novel.id');
            })

            ->orderBy('novel_click_record.num', 'desc')
            ->take(100)
            ->get();

        return $result;
    }


}