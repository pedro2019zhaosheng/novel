<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use App\Libs\Caches\lib\libredis;
use App\models\BaseModel;
use DB;

class NovelSearchLogModel extends Model
{

    protected $connection = 'mysql';
    protected $table = "name_search_record";
    public $timestamps = false;

    public function insertNovel($name){

        $data = array(
            'name'=>$name,
            'addtime'=>time()
        );
        $this->insert($data);

    }


    public function getTopNovelSearch(){
        $result = $this->select(DB::raw('count(name) as num'),'name')
            ->groupBy('name')
            ->orderBy('num','desc')
            ->take(100)
            ->get();
        return $result;
    }


}