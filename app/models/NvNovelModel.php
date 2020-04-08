<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use App\Libs\Caches\lib\libredis;
use App\models\BaseModel;
use App\models\CateModel;
use DB;


class NvNovelModel extends Model
{
    // 数据库'mysql_center'中的nv_novel表
    protected $connection = 'mysql_center';
    protected $table = "nv_novel";


    // 获取分类下的小说
    public function getNovel($cateid,$page,$size)
    {

        $result = $this->from($this->table . ' as novel')
            ->select('novel.name as name','novel.progress_status as progress_status','novel.id as novelid','novel.source_id as sourceid','novel.nove_line_cate_id as cateid','novel.click_nums as read','novel.img as img','novel.summary as summary','novel.author as author', 'cate.name as catename')
            ->join('nv_novel_line_category as cate', function($join){
                $join->on('cate.id', '=', 'novel.nove_line_cate_id');
            })

            ->whereIn('cate.id', $cateid )
            ->where('novel.status', '=', 1)
            ->where('novel.is_finished', '=', 1)
            ->orderBy('click_nums', 'desc')
            ->skip(($page-1) * $size)
            ->take($size)
            ->get();

        if(!empty($result)){
            foreach ($result as $k=>$value){
                $result[$k]->summary = empty($value->summary)?'':$value->summary;
                $result[$k]->read = empty($value->read)?0:$value->read;
//                if($result[$k]->read >10000){
//                    $result[$k]->read = (intval($result[$k]->read/1000)/10)."W+";
//                }
            }
        }
        return $result ? $result : [];

    }
    public function getNovelNocate($page,$size)
    {
        $result = $this->from($this->table . ' as novel')
            ->select('novel.name as novelname','novel.source_id as sourceid','novel.nove_line_cate_id as cateid','novel.id as novelid','novel.click_nums as read','novel.img as img','novel.summary as summary','novel.author as author', 'cate.name as catename')
            ->join('nv_novel_line_category as cate', function($join){
                $join->on('cate.id', '=', 'novel.nove_line_cate_id');
            })


            ->where('novel.status', '=', 1)
            ->where('novel.is_finished', '=', 1)
            ->orderBy('click_nums', 'desc')
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

    public function getNovelWithoutSome($cateid,$page,$size,$novelidarr)
    {
        $result = $this->from($this->table . ' as novel')
            ->select('novel.name as novelname','novel.source_id as sourceid','novel.nove_line_cate_id as cateid','novel.id as novelid','novel.click_nums as read','novel.img as img','novel.summary as summary','novel.author as author', 'cate.name as catename')
            ->join('nv_novel_line_category as cate', function($join){
                $join->on('cate.id', '=', 'novel.nove_line_cate_id');
            })

            ->whereIn('novel.nove_line_cate_id', $cateid )
            ->whereNotIn('novel.id',$novelidarr)
            ->where('novel.status', '=', 1)
            ->where('novel.is_finished', '=', 1)
            ->orderBy('click_nums', 'desc')
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
    public function getNovelWithoutSomeNocate($page,$size,$novelidarr)
    {

        $result = $this->from($this->table . ' as novel')
            ->select('novel.name as novelname','novel.source_id as sourceid','novel.source_id as sourceid','novel.nove_line_cate_id as cateid','novel.id as novelid','novel.click_nums as read','novel.img as img','novel.summary as summary','novel.author as author', 'cate.name as catename')
            ->join('nv_novel_line_category as cate', function($join){
                $join->on('cate.id', '=', 'novel.nove_line_cate_id');
            })


            ->whereNotIn('novel.id',$novelidarr)
            ->where('novel.status', '=', 1)
            ->where('novel.is_finished', '=', 1)
            ->orderBy('click_nums', 'desc')
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



    public function getNovelByCondition($cate,$name,$novelid){
        $query = NvNovelModel::select();
        if($cate && !empty($cate)){
            $query = $query->where('nove_line_cate_id',$cate);
        }
        if($name && !empty($name)){
            $query = $query->where('name', 'like', '%' . $name . '%');
        }
        if($novelid && !empty($novelid)){
            $query = $query->where('id', $novelid);
        }
        return $query;
    }


    public function getNovelById($id){

        $result = $this->from($this->table . ' as novel')
            ->select('novel.name as name','novel.shelf_nums as shelf_nums','novel.down_nums as down_nums','novel.source_id as sourceid','novel.progress_status as status','novel.chapter as chapter','novel.source_id as sourceid','novel.nove_line_cate_id as cateid','novel.id as novelid','novel.click_nums as read','novel.img as img','novel.summary as summary','novel.author as author', 'cate.name as catename')
            ->join('nv_novel_line_category as cate', function($join){
                $join->on('cate.id', '=', 'novel.nove_line_cate_id');
            })

            ->where('novel.id',$id)
            ->where('novel.status', '=', 1)
            ->where('novel.is_finished', '=', 1)
            ->first();

        if(!empty($result)){

            $result->summary = empty($result->summary)?'':$result->summary;
            $result->read = empty($result->read)?0:$result->read;
//            $read = $result->read;
//            if($result->read >10000000){
//                $temp = (intval($result->read/100000)/10)."KW+";
//            }
//            if($result->read >10000){
//                $temp = (intval($result->read/1000)/10)."W+";
//            }
//            $result->read = $temp;
        }

        return $result ? $result : [];


    }
    public function getNovelData($id){

        $result = $this->from($this->table . ' as novel')
            ->select('novel.name as name','novel.nove_line_cate_id as cateid', 'cate.name as catename')
            ->join('nv_novel_line_category as cate', function($join){
                $join->on('cate.id', '=', 'novel.nove_line_cate_id');
            })
            ->where('novel.id',$id)
            ->first();

        return $result ? $result : '';


    }

    public function getNovelById1($id){

        $result = $this->from($this->table . ' as novel')
            ->select('novel.name as novelname','novel.source_id as sourceid','novel.progress_status as status','novel.chapter as chapter','novel.source_id as sourceid','novel.nove_line_cate_id as cateid','novel.id as novelid','novel.click_nums as read','novel.img as img','novel.summary as summary','novel.author as author', 'cate.name as catename')
            ->join('nv_novel_line_category as cate', function($join){
                $join->on('cate.id', '=', 'novel.nove_line_cate_id');
            })

            ->where('novel.id',$id)
            ->where('novel.status', '=', 1)
            ->where('novel.is_finished', '=', 1)
            ->first();

        if(!empty($result)){

            $result->summary = empty($result->summary)?'':$result->summary;
            $result->read = empty($result->read)?0:$result->read;
            if($result->read >10000){
                $result->read = (intval($result->read/1000)/10)."W+";
            }
        }

        return $result ? $result : [];

    }
    public function getNovelPartById($id){

        $result = $this->from($this->table . ' as novel')
            ->select('novel.name as novelname','novel.source_id as sourceid','novel.source_id as sourceid','novel.nove_line_cate_id as cateid','novel.id as novelid','novel.click_nums as read','novel.img as img','novel.summary as summary','novel.author as author', 'cate.name as catename')
            ->join('nv_novel_line_category as cate', function($join){
                $join->on('cate.id', '=', 'novel.nove_line_cate_id');
            })

            ->where('novel.id', '=', $id)
            ->where('novel.is_finished', '=', 1)
            ->where('novel.status', '=', 1)

            ->first();

        if(!empty($result)){
            $result->summary = empty($result->summary)?'':$result->summary;
            $result->read = empty($result->read)?0:$result->read;
//            if($result->read >10000){
//                $result->read = (intval($result->read/1000)/10)."W+";
//            }

        }
        return $result ? $result : [];

    }
    public function getNovelPartById1($id){

        $result = $this->from($this->table . ' as novel')
            ->select('novel.name as name','novel.source_id as sourceid','novel.source_id as sourceid','novel.progress_status as status','novel.chapter as chapter','novel.last_updated_at as refresh_time','novel.nove_line_cate_id as cateid','novel.id as novelid','novel.click_nums as read','novel.img as img','novel.summary as summary','novel.author as author', 'cate.name as catename')
            ->join('nv_novel_line_category as cate', function($join){
                $join->on('cate.id', '=', 'novel.nove_line_cate_id');
            })

            ->where('novel.id', '=', $id)
            ->where('novel.status', '=', 1)
            ->where('novel.is_finished', '=', 1)

            ->first();

        if(!empty($result)){
            $result->summary = empty($result->summary)?'':$result->summary;
            $result->read = empty($result->read)?0:$result->read;
            if($result->read >10000){
                $result->read = (intval($result->read/1000)/10)."W+";
            }

        }
        return $result ;

    }


    public function getNovelIdArray($idArr,$size){
//        $ids = implode(",", $idArr);
        $result = $this->from($this->table . ' as novel')
            ->select('novel.name as name','novel.source_id as sourceid','novel.source_id as sourceid','novel.nove_line_cate_id as cateid','novel.id as novelid','novel.click_nums as read','novel.img as img','novel.summary as summary','novel.author as author', 'cate.name as catename')
            ->join('nv_novel_line_category as cate', function($join){
                $join->on('cate.id', '=', 'novel.nove_line_cate_id');
            })

            ->whereIn('novel.id', $idArr )
            ->where('novel.status', '=', 1)
            ->where('novel.is_finished', '=', 1)
            ->orderBy(\DB::raw('FIND_IN_SET(novel.id, "' . implode(",", $idArr) . '"' . ")"))
//            ->orderByRaw(DB::raw('FIELD(id,'.$temp.')'))
//            ->orderBy('sort', 'desc')
            ->take($size)
            ->get();
        if(!empty($result)){
            foreach ($result as $k=>$value){
                $result[$k]->summary = empty($value->summary)?'':$value->summary;
                $result[$k]->read = empty($value->read)?0:$value->read;
//                if($result[$k]->read >10000){
//                    $result[$k]->read = (intval($result[$k]->read/1000)/10)."W+";
//                }

            }
        }
        return $result ? $result : [];

    }
    public function getNovelIdArray1($idArr,$size){
        $result = $this->from($this->table . ' as novel')
            ->select('novel.name as novelname','novel.source_id as sourceid','novel.source_id as sourceid','novel.nove_line_cate_id as cateid','novel.id as novelid','novel.click_nums as read','novel.img as img','novel.summary as summary','novel.author as author', 'cate.name as catename')
            ->join('nv_novel_line_category as cate', function($join){
                $join->on('cate.id', '=', 'novel.nove_line_cate_id');
            })

            ->whereIn('novel.id', $idArr )
            ->where('novel.status', '=', 1)
            ->where('novel.is_finished', '=', 1)
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

    public function getNovelIdArrayNosize($idArr){

        $result = $this->from($this->table . ' as novel')
            ->select('novel.name as name','novel.source_id as sourceid','novel.source_id as sourceid','novel.nove_line_cate_id as cateid','novel.id as novelid','novel.click_nums as read','novel.img as img','novel.summary as summary','novel.author as author', 'cate.name as catename')
            ->join('nv_novel_line_category as cate', function($join){
                $join->on('cate.id', '=', 'novel.nove_line_cate_id');
            })

            ->whereIn('novel.id', $idArr )
            ->where('novel.status', '=', 1)
            ->where('novel.is_finished', '=', 1)
//            ->orderBy('click_nums', 'desc')
            ->orderBy(\DB::raw('FIND_IN_SET(novel.id, "' . implode(",", $idArr) . '"' . ")"))
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
    public function getPopularity(){

        $result = $this->from($this->table . ' as novel')
            ->select('novel.name as name','novel.source_id as sourceid','novel.source_id as sourceid','novel.nove_line_cate_id as cateid','novel.id as novelid','novel.click_nums as read','novel.img as img','novel.summary as summary','novel.author as author', 'cate.name as catename')
            ->join('nv_novel_line_category as cate', function($join){
                $join->on('cate.id', '=', 'novel.nove_line_cate_id');
            })

            ->where('novel.status', '=', 1)
            ->where('novel.is_finished', '=', 1)
            ->orderBy('novel.click_nums', 'desc')
            ->take(50)
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


//        $result = $this
//            ->where('status',1)
//            ->orderBy('click_nums', 'asc')
//            ->take(50)
//            ->get();
//
//        return $result;
    }
    public function getNovelNoCondition(){

        $result = $this->from($this->table . ' as novel')
            ->select('novel.name as novelname','novel.source_id as sourceid','novel.nove_line_cate_id as cateid','novel.id as novelid','novel.click_nums as read','novel.img as img','novel.summary as summary','novel.author as author', 'cate.name as catename')
            ->join('nv_novel_line_category as cate', function($join){
                $join->on('cate.id', '=', 'novel.nove_line_cate_id');
            })

            ->where('novel.status', '=', 1)
            ->where('novel.is_finished', '=', 1)
            ->orderBy('novel.click_nums', 'desc')
            ->take(50)
            ->get();


//        $result = $this
//            ->where('status',1)
//            ->orderBy('click_nums', 'asc')
//            ->take(50)
//            ->get();
//
        if(!empty($result)){
            foreach ($result as $k=>$value){
                $result[$k]->summary = empty($value->summary)?'':$value->summary;
                $result[$k]->read = empty($value->read)?0:$value->read;
                if($result[$k]->read >10000){
                    $result[$k]->read = (intval($result[$k]->read/1000)/10)."W+";
                }
            }
        }
        return $result;
    }





    public function getNovelByAuthor($page,$size,$author){

        $result = $this->from($this->table . ' as novel')
            ->select('novel.name as novelname','novel.source_id as sourceid','novel.nove_line_cate_id as cateid','novel.id as novelid','novel.click_nums as read','novel.img as img','novel.summary as summary','novel.author as author', 'cate.name as catename')
            ->join('nv_novel_line_category as cate', function($join){
                $join->on('cate.id', '=', 'novel.nove_line_cate_id');
            })
            ->where('novel.author',$author)
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


//        $result = $this
//            ->where('author',$author)
//            ->where('status',1)
//            ->orderBy('sort', 'asc')
//            ->skip(($page-1) * $size)
//            ->take($size)
//            ->get();
//
//        return $result;
    }

    public function getNovelByCate($cateid){
        $result = $this->from($this->table . ' as novel')
            ->select('novel.name as name','novel.source_id as sourceid','novel.progress_status as progress_status','novel.source_id as sourceid','novel.nove_line_cate_id as cateid','novel.id as novelid','novel.click_nums as read','novel.img as img','novel.summary as summary','novel.author as author', 'cate.name as catename')
            ->join('nv_novel_line_category as cate', function($join){
                $join->on('cate.id', '=', 'novel.nove_line_cate_id');
            })
            ->whereIn('novel.nove_line_cate_id',$cateid)
            ->where('novel.status', '=', 1)
            ->where('novel.is_finished', '=', 1)
            ->orderBy('novel.click_nums', 'desc')
            ->take(9)
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

    public function getGuessNovel($cateid){
        $result = $this->from($this->table . ' as novel')
            ->select('novel.name as novelname','novel.source_id as sourceid','novel.progress_status as progress_status','novel.source_id as sourceid','novel.nove_line_cate_id as cateid','novel.id as novelid','novel.click_nums as read','novel.img as img','novel.summary as summary','novel.author as author', 'cate.name as catename')
            ->join('nv_novel_line_category as cate', function($join){
                $join->on('cate.id', '=', 'novel.nove_line_cate_id');
            })
            ->whereIn('novel.nove_line_cate_id',$cateid)
            ->where('novel.status', '=', 1)
            ->where('novel.is_finished', '=', 1)
            ->orderBy('novel.click_nums', 'desc')
            ->take(10)
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


    public function getTopClickNovel(){
        $result = $this->from($this->table . ' as novel')
            ->select('novel.name as name','novel.source_id as sourceid','novel.progress_status as progress_status','novel.source_id as sourceid','novel.nove_line_cate_id as cateid','novel.id as novelid','novel.click_nums as read','novel.img as img','novel.summary as summary','novel.author as author', 'cate.name as catename')
            ->join('nv_novel_line_category as cate', function($join){
                $join->on('cate.id', '=', 'novel.nove_line_cate_id');
            })
            ->where('novel.status', '=', 1)
            ->where('novel.is_finished', '=', 1)
            ->orderBy('novel.novel_clicks', 'desc')
            ->take(10)
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


    public function getTenNovel(){
        $result = $this->from($this->table . ' as novel')
            ->select('novel.name as novelname','novel.source_id as sourceid','novel.progress_status as progress_status','novel.source_id as sourceid','novel.nove_line_cate_id as cateid','novel.id as novelid','novel.click_nums as read','novel.img as img','novel.summary as summary','novel.author as author', 'cate.name as catename')
            ->join('nv_novel_line_category as cate', function($join){
                $join->on('cate.id', '=', 'novel.nove_line_cate_id');
            })
            ->where('novel.status', '=', 1)
            ->where('novel.is_finished', '=', 1)
            ->orderBy('novel.click_nums', 'desc')
            ->take(10)
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

    public function getNovelByName($name){

    }


    public function searchNovel($name,$page,$size){
        $result = $this->from($this->table . ' as novel')
            ->select('novel.name as name','novel.source_id as sourceid','novel.source_id as sourceid','novel.nove_line_cate_id as cateid','novel.id as novelid','novel.click_nums as read','novel.img as img','novel.summary as summary','novel.author as author', 'cate.name as catename')
            ->join('nv_novel_line_category as cate', function($join){
                $join->on('cate.id', '=', 'novel.nove_line_cate_id');
            })



            ->where('novel.status', '=', 1)
            ->where('novel.is_finished', '=', 1)
            ->where('novel.name', 'like', '%'.$name . '%')
            ->orwhere('novel.author', 'like', '%'.$name . '%')
            ->orderBy('sort', 'desc')
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



    public function updateRead($novelid){
        $res = $this->where('id', '=', $novelid)->first();
        $click_nums_real = $res->click_nums_real;
        $result = $this->where('id',$novelid)
            ->update(['click_nums_real'=>$click_nums_real+1]);
        return $result;
    }

    public function updateShelfNum($novel){

        for($i=0;$i<count($novel);$i++){
            $res = $this->where('id', '=', $novel[$i])->first();
            $shelf_nums_real = $res->shelf_nums_real;
            $result = $this->where('id',$novel[$i])
                ->update(['shelf_nums_real'=>$shelf_nums_real+1]);
        }
    }

    public function updateDownNum($novelid){
        $res = $this->where('id', '=', $novelid)->first();
        $down_nums_real = $res->down_nums_real;
        $result = $this->where('id',$novelid)
            ->update(['down_nums_real'=>$down_nums_real+1]);
        return $result;
    }

    public function getCollection($novelid){
        $result = $this->from($this->table . ' as novel')
            ->select('novel.name as name','novel.source_id as sourceid','novel.source_id as sourceid','novel.progress_status as status','novel.chapter as chapter','novel.last_updated_at as refresh_time','novel.nove_line_cate_id as cateid','novel.id as novelid','novel.click_nums as read','novel.img as img','novel.summary as summary','novel.author as author', 'cate.name as catename')
            ->join('nv_novel_line_category as cate', function($join){
                $join->on('cate.id', '=', 'novel.nove_line_cate_id');
            })

            ->where('novel.id', '=', $novelid)
            ->where('novel.is_finished', '=', 1)
            ->where('novel.status', '=', 1)

            ->first();

//        if(!empty($result)){
//            $result->summary = empty($result->summary)?'':$result->summary;
//            $result->read = empty($result->read)?0:$result->read;
//
//        }
        return $result ? $result : [];
    }


    public function getAuthor($name){
        $result = $this->where('author', $name )
            ->first();

        return $result ? $result : [];
    }




    public function updateNovelInfo($novelid,$click_nums,$down_nums,$shelf_nums,$cover,$cateid,$summary){
        if(!empty($cover)){
            $data['img'] = $cover;
        }
        $data['click_nums'] = $click_nums;
        $data['down_nums'] = $down_nums;
        $data['shelf_nums'] = $shelf_nums;
        $data['nove_line_cate_id'] = $cateid;
        $data['summary'] = $summary;

        $result = $this->where('id',$novelid)
            ->update($data);
        return $result;
    }

    public function getRecommend(){
        $result = $this->from($this->table . ' as novel')
            ->select('novel.name as name','novel.source_id as sourceid','novel.progress_status as progress_status','novel.source_id as sourceid','novel.nove_line_cate_id as cateid','novel.id as novelid','novel.click_nums as read','novel.img as img','novel.summary as summary','novel.author as author', 'cate.name as catename')
            ->join('nv_novel_line_category as cate', function($join){
                $join->on('cate.id', '=', 'novel.nove_line_cate_id');
            })
            ->where('novel.status', '=', 1)
            ->where('novel.is_finished', '=', 1)
            ->orderBy('novel.click_nums', 'desc')
            ->take(3)
            ->get();

        if(!empty($result)){
            foreach ($result as $k=>$value){
                $result[$k]->summary = empty($value->summary)?'':$value->summary;
                $result[$k]->read = empty($value->read)?0:$value->read;
            }
        }
        return $result ? $result : [];
    }

    public function getBookshelf(){

        $result = $this->from($this->table . ' as novel')
            ->select('novel.name as name','novel.source_id as sourceid','novel.source_id as sourceid','novel.nove_line_cate_id as cateid','novel.id as novelid','novel.click_nums as read','novel.img as img','novel.summary as summary','novel.author as author', 'cate.name as catename')
            ->join('nv_novel_line_category as cate', function($join){
                $join->on('cate.id', '=', 'novel.nove_line_cate_id');
            })

            ->where('novel.status', '=', 1)
            ->where('novel.is_finished', '=', 1)
            ->orderBy('novel.shelf_nums', 'desc')
            ->take(50)
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

    public function getdown(){

        $result = $this->from($this->table . ' as novel')
            ->select('novel.name as name','novel.source_id as sourceid','novel.source_id as sourceid','novel.nove_line_cate_id as cateid','novel.id as novelid','novel.click_nums as read','novel.img as img','novel.summary as summary','novel.author as author', 'cate.name as catename')
            ->join('nv_novel_line_category as cate', function($join){
                $join->on('cate.id', '=', 'novel.nove_line_cate_id');
            })

            ->where('novel.status', '=', 1)
            ->where('novel.is_finished', '=', 1)
            ->orderBy('novel.down_nums', 'desc')
            ->take(50)
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

    //日更或者完本
    public function getRefreshEndNovel($page,$size,$status){
        $result = $this->from($this->table . ' as novel')
            ->select('novel.name as novelname','novel.last_updated_at as refresh_time','novel.source_id as sourceid','novel.nove_line_cate_id as cateid','novel.id as novelid','novel.click_nums as read','novel.img as img','novel.summary as summary','novel.author as author', 'cate.name as catename')
            ->join('nv_novel_line_category as cate', function($join){
                $join->on('cate.id', '=', 'novel.nove_line_cate_id');
            })

            ->where('novel.progress_status', $status )
            ->where('novel.status', '=', 1)
            ->where('novel.is_finished', '=', 1)
            ->orderBy('click_nums', 'desc')
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


    public function operate($id,$status){
        $result = $this->where('id',$id)
            ->update(['status'=>$status]);
        return $result;
    }

    public function updateChapter($novelid,$chapter){
        $result = $this->where('id',$novelid)
            ->update(['chapter' => $chapter]);
        return $result;
    }
}