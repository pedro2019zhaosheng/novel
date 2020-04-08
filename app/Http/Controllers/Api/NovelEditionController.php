<?php

namespace App\Http\Controllers\Api;

use App\models\ChapterModel;
use App\models\NovelSourceModel;
use App\models\NvNovelEditionModel;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\models\BaseModel;
use App\models\NovelModel;
use App\models\NvNovelModel;
use Illuminate\Support\Facades\Config;
use App\Libs\Caches\lib\libredis;
//use Illuminate\Support\Facades\Redis;
use DB;
use PhpParser\Node\Expr\Empty_;

class NovelEditionController extends ApiController
{
    /**
     * 获取书城首页的全部信息
     * @return false|string
     */
    public function postBookStore(Request $request){
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);
        $offset = ($page-1)*$size;
        $NvNovelModel = new NvNovelModel();
        //banner
        $banners =  DB::table('novel_banner')->where('status',1)->where('type',1)->orderBy('sort','asc')->get();
        $data['banner'] = $banners;


        //morepart
        $morepart =array();
        $morepart =  DB::table('choiceness')->where('status',1)->where('isdel',1)->whereIn('type',[2,4,5])->orderBy('sort','asc')->get();
        if(!empty($morepart)){
            foreach ($morepart as $key=>$v){
                $choiceid = $v->id;
                if($v->type == 4){//作者
                    $res =  DB::table('novel_banner')->select('id','img','link')->where('status',1)->where('type',2)->where('choiceid',$choiceid)->orderBy('sort','asc')->get();
                    $morepart[$key]->detail = $res;
                }else{
                    $hotonequery1 = "select 
                        novelid,id   from choice_novel 
                         where choiceid=$choiceid and status=1  order by  sort asc limit $offset,$size";
                    $res = DB::select($hotonequery1);

                    foreach ($res as $k=>$val){
                        $novelres = $NvNovelModel->getNovelPartById($val->novelid);
                        if(!empty($novelres)){
                            $res[$k]->novelname=$novelres->novelname;
                            $res[$k]->img=$novelres->img;
                            if($novelres->read >10000){
                                $res[$k]->read = (intval($novelres->read/1000)/10)."W+";
                            }else{
                                $res[$k]->read=$novelres->read;
                            }

                            $res[$k]->summary=$novelres->summary;
                            $res[$k]->author=$novelres->author;
                            $res[$k]->catename=$novelres->catename;
                            $res[$k]->sourceid=$novelres->sourceid;
                        }
//
                    }
                    $morepart[$key]->detail = $res;
                }
            }
        }



        $data['morepart'] = $morepart;

        return json_encode(['status'=>1,'message'=>'成功','data'=>$data]);
    }


    /**
     * 获取排行下的榜单分类
     * @param Request $request
     * @return false|string
     */
    public function postList(Request $request){
        $data = [
            ['id'=>1,'name'=>'人气榜'],
            ['id'=>2,'name'=>'书架榜'],
            ['id'=>3,'name'=>'热搜榜'],
            ['id'=>4,'name'=>'完结榜'],
            ['id'=>5,'name'=>'更新榜'],
        ];
        return json_encode(['status'=>1,'message'=>'成功','data'=>$data]);
    }


    /**
     * 获取排行榜下的小说
     * @param Request $request
     * @return false|string
     */
    public function postGetNovelUnderRank(Request $request){
        $id = $request->input('id', 1);
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);
        $offset = ($page-1)*$size;
        $NvNovelEditionModel = new NvNovelEditionModel();
        $data=array();
        if($id ==1){
            $data = $NvNovelEditionModel->getPopularity($page,$size);//人气榜

        }else if($id ==2){
            $shellquery = "SELECT bookshelf.novelid, COUNT('bookshelf.novelid')as num  FROM bookshelf  where bookshelf.status=1 GROUP BY novelid ORDER BY num desc limit 50;";
            $bookshell = DB::select($shellquery);
            if(!empty($bookshell)){

                foreach ($bookshell as $k=>$value){
                    $novelid[] = $value->novelid;
                }
                $data = $NvNovelEditionModel->getNovelIdArray($novelid,50);
            }
        }else if($id ==3){
            $searchquery = "SELECT novel_search_log.novelid, COUNT('novel_search_log.novelid')as num  FROM novel_search_log   GROUP BY novelid ORDER BY num desc limit 50;";
            $search = DB::select($searchquery);
            if(!empty($search)){
                foreach ($search as $k=>$value){
                    $novelid[] = $value->novelid;
                }
                $data = $NvNovelEditionModel->getNovelIdArray($novelid,50);
            }else{
                $data = $NvNovelEditionModel->getPopularity($page,50);//人气榜
            }

        }else if($id ==4){
            $data = $NvNovelEditionModel->getRefreshEndNovel($page,$size,2);
        }else if($id ==5){
            $data = $NvNovelEditionModel->getRefreshEndNovel($page,$size,1);
        }
        return json_encode(['status'=>1,'message'=>'成功','data'=>$data]);
    }


    /**
     * 编辑用户信息
     * @param Request $request
     * @return false|string
     */
    public function postEditUserData(Request $request){
        $uuid = $request->input('uuid', '');
        $avatar = $request->input('avatar', '');
        $nickname = $request->input('nickname', '');
        $birthday = $request->input('birthday', '');
        $sex = $request->input('sex', '');

        $data = array(
            'avatar' => $avatar,
            'nickname' => $nickname,
            'sex' => $sex,
            'birthday' => $birthday,
        );

        $res = DB::table('novel_user')->where('id',$uuid)->update($data);
        return json_encode(['status'=>1,'message'=>'成功']);

    }


    /**
     * 小说封面换一批接口
     * @param Request $request
     * @return false|string
     */
    public function postGetAnotherBatch(Request $request){
        $novelid = $request->input('novelid', 1);
        $page = $request->input('page', 1);
        //查询该小说属于哪个分类 并推荐6本同类小说
        $NvNovelEditionModel = new NvNovelEditionModel();

        $data  = $NvNovelEditionModel->getCateNovel($novelid,$page);

        return json_encode(['status'=>1,'message'=>'成功','data'=>$data ]);
    }




    public function postGetMySpread(Request $request){
        $uuid = $request->input('uuid', 1);
        $data = DB::table('novel_user')->select('phone','nickname','regtime')->where('userfrom',$uuid)->orderBy('regtime','desc')->get();
        foreach ($data as $k=>$v){
            if(empty($v->phone)){
                $data[$k]->phone='';
            }else{
                $data[$k]->phone= substr_replace($data[$k]->phone,'****',-4);
            }
            if(empty($v->nickname)){
                $data[$k]->nickname='未设置';
            }
            $data[$k]->regtime= date('Y-m-d',strtotime($data[$k]->regtime));
        }
        return json_encode(['status'=>1,'message'=>'成功','data'=>$data ]);
    }

    public function postGetAnotherSearch(Request $request){
        $page = $request->input('page', 2);
        $NvNovelEditionModel = new NvNovelEditionModel();
        $data = $NvNovelEditionModel->getTopClickNovel($page);
        return json_encode(['status'=>1,'message'=>'成功','data'=>$data ]);
    }
}
