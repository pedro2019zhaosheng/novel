<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use App\Libs\Caches\lib\libredis;
use App\models\BaseModel;
use App\models\CateModel;
use DB;


class NovelSourceModel extends Model
{
    // 数据库'mysql_center'中的nv_novel表
    protected $connection = 'mysql_center';
    protected $table = "nv_novel_source";


    public function getSource($sourceid){
        $result = $this->where('id', $sourceid )
            ->select('id as sourceid','url')
            ->first();

        return $result;
    }

}