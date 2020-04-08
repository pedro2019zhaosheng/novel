<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use App\Libs\Caches\lib\libredis;
use App\models\BaseModel;
use DB;

class CateModel extends Model
{
    // 数据库'dadtabase_center'中的users表
    protected $connection = 'mysql_center';
    protected $table = "nv_novel_line_category";


    // $start 偏移  $limit 数量
    public function getCate()
    {
        $result = $this->select('id','name')
            ->orderBy('id','asc')
            ->get();

        return $result;
    }


    public function getCateName($nove_line_cate_id){
        $result = $this->select('id','name')
            ->where('id',$nove_line_cate_id)
            ->first();

        return $result;
    }


}