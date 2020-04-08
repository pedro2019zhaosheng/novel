<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use App\Libs\Caches\lib\libredis;
use App\models\BaseModel;
use App\models\CateModel;
use DB;


class EssayReadModel extends Model
{
    // 数据库'mysql_center'中的nv_novel表
    protected $connection = 'mysql';
    protected $table = "essay_read_times";


    public function getList($id,$page,$size){

        $result = $this

            ->select('type','frequency','addtime')
            ->where('uuid',$id)

            ->orderBy('addtime', 'desc')
            ->skip(($page-1) * $size)
            ->take($size)
            ->get();

        return $result;
    }

}