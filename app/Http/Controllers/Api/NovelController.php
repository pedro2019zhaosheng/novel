<?php

namespace App\Http\Controllers\Api;

use App\models\ChapterModel;
use App\models\ChapterInsertModel;
use App\models\EssayReadModel;
use App\models\NovelSearchLogModel;
use App\models\NovelClickRecordModel;
use App\models\NovelSourceModel;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\models\BaseModel;
use App\models\NovelModel;
use App\models\NvNovelModel;
use Illuminate\Support\Facades\Config;
use App\Libs\Caches\lib\libredis;
use App\Vertify\lib\GeetestLib;

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;



//use Illuminate\Support\Facades\Redis;
use DB;
use PhpParser\Node\Expr\Empty_;

class NovelController extends ApiController
{
    /**
     * 获取首页
     */
    public function postIndex(Request $request)
    {
        $platform = $request->input('platform', '1');

        $data = [];
        $cates = BaseModel::factory('category')->select(['cateid', 'name'])->orderBy('sort', 'ASC')->get();
        foreach ($cates as $v) {
            $novels = BaseModel::factory('novel')->where('cateid', $v->cateid)->select(['novelid', 'name', 'img'])
                ->orderBy('sort', 'ASC')->skip(0)->take(3)->get()->toArray();
            $data['cates'][] = ['cateid' => $v->cateid, 'catename' => $v->name, 'novel' => $novels];
        }

        return json_encode(['status' => 1, 'message' => '成功', 'data' => $data]);
    }

    /**
     * 获取分类数据
     */
    public function postCates(Request $request)
    {
        $confredis = Config::get('database.redis.default');
        $redis = new libredis([$confredis['host'], $confredis['port']]);

        $cates = $redis->get("cates");
        if ($cates) {
            $data = json_decode($cates, true);
        } else {
            $data = BaseModel::factory('category')->select(['cateid', 'name', 'img', 'num'])->orderBy('sort', 'ASC')->get();
            $sql = "select img,cateid from novel group by cateid";
            $img = DB::select($sql);
            foreach ($img as $key => $value) {
                $imgs[$value->cateid] = $value->img;
            }

            foreach ($data as $key => $value) {
                if (empty($value->img) && isset($imgs[$value->cateid])) {
                    $data[$key]->img = $imgs[$value->cateid];
                }
            }
            $redis->set("cates", json_encode($data), false, true, 3600 * 2);
        }

        return json_encode(['status' => 1, 'message' => '成功', 'data' => $data]);
    }

    /**
     * 获取小说排行榜
     */
    public function postRank(Request $request)
    {
        $confredis = Config::get('database.redis.default');
        $redis = new libredis([$confredis['host'], $confredis['port']]);

        $book_type = $request->input('book_type', 1);
        $data = $redis->get("rank|{$book_type}");
        if ($data) {
            $data = json_decode($data, true);
        } else {
            if ($book_type == 1) {
                $a = date('Y-m-d', strtotime('-1 week'));
                $b = date('Y-m-d');
                $sql_man = "select novelid,novel.name,novel.img,author,status,chapter,category.name as catename from novel LEFT JOIN category on novel.cateid=category.cateid where refresh_time > '{$a}' and novel.channel=1 and `show` = 1 order by `read` desc limit 0,10";
                $sql_woman = "select novelid,novel.name,novel.img,author,status,chapter,category.name as catename from novel LEFT JOIN category on novel.cateid=category.cateid where refresh_time > '{$a}' and  novel.channel=2 and `show` = 1 order by `read` desc limit 0,10";
                $novel_man = DB::select($sql_man);
                $novel_woman = DB::select($sql_woman);
            } else {
                $sql_man = "select novelid,novel.name,novel.img,author,status,chapter,category.name as catename from novel LEFT JOIN category on novel.cateid=category.cateid where status=1 and novel.channel=1 and `show` = 1 order by `read` desc limit 0,10";
                $sql_woman = "select novelid,novel.name,novel.img,author,status,chapter,category.name as catename from novel LEFT JOIN category on novel.cateid=category.cateid where status=1 and novel.channel=2 and `show` = 1 order by `read` desc limit 0,10";
                $novel_man = DB::select($sql_man);
                $novel_woman = DB::select($sql_woman);
            }

            foreach ($novel_man as $key => $value) {
                if ($value->status == 1) {
                    $novel_man[$key]->status = '已完本';
                } else {
                    $novel_man[$key]->status = '连载中';
                }
            }

            foreach ($novel_woman as $key => $value) {
                if ($value->status == 1) {
                    $novel_woman[$key]->status = '已完本';
                } else {
                    $novel_woman[$key]->status = '连载中';
                }
            }

            $data = ['男频' => $novel_man, '女频' => $novel_woman];
            $redis->set("rank|{$book_type}", json_encode($data), false, true, 3600 * 2);
        }
        return json_encode(['status' => 1, 'message' => '成功', 'data' => $data]);
    }

    /**
     * 获取小说排行榜更多
     */
    public function postRankMore(Request $request)
    {
        $confredis = Config::get('database.redis.default');
        $redis = new libredis([$confredis['host'], $confredis['port']]);

        $book_type = $request->input('book_type', 1);
        $channel = $request->input('channel', 1);
        $data = $redis->get("rankMore|{$book_type}|{$channel}");
        if ($data) {
            $data = json_decode($data, true);
        } else {
            if ($book_type == 1) {
                $a = date('Y-m-d', strtotime('-1 month'));
                $b = date('Y-m-d');
                $sql = "select novelid,novel.name,novel.img,author,status,chapter,category.name as catename,summary from novel LEFT JOIN category on novel.cateid=category.cateid where refresh_time > '{$a}' and novel.channel={$channel} and `show` = 1 order by num desc limit 0,100";
                $data = DB::select($sql);
            } else {
                $sql = "select novelid,novel.name,novel.img,author,status,chapter,category.name as catename,summary from novel LEFT JOIN category on novel.cateid=category.cateid where status=1 and novel.channel={$channel} and `show` = 1 order by num desc limit 0,100";
                $data = DB::select($sql);
            }

            foreach ($data as $key => $value) {
                if ($value->status == 1) {
                    $data[$key]->status = '已完本';
                } else {
                    $data[$key]->status = '连载中';
                }
            }

            $redis->set("rankMore|{$book_type}|{$channel}", json_encode($data), false, true, 3600 * 2);
        }

        return json_encode(['status' => 1, 'message' => '成功', 'data' => $data]);
    }

    /**
     * 获取广告
     */
    public function postAdvert(Request $request)
    {
        $platform = $request->input('platform', '1');
        $packid = $request->input('packid', '');
        $type = $request->input('type', '0');
        $time = time();
        $data = [];
//        if ($type < 5) {
//            $type = 5;
//            $advert = BaseModel::factory('advert')->select(['title', 'url', 'img', 'type', 'adver_type'])
//                ->where('start', '<=', $time)
//                ->where('end', '>=', $time)
//                ->where('status', 1)
//                ->where('platform', $platform)
//                ->where('packid', $packid)
//                ->where('type', '!=', $type)
//                ->orderBy('sort', 'ASC')->get()->toArray();
//
//            if (!$advert) {
//                $advert = BaseModel::factory('advert')->select(['title', 'url', 'img', 'type', 'adver_type'])
//                    ->where('start', '<=', $time)
//                    ->where('end', '>=', $time)
//                    ->where('status', 1)
//                    ->where('platform', $platform)
//                    ->where('type', '!=', $type)
//                    ->where('packid', '')
//                    ->orderBy('sort', 'ASC')->get()->toArray();
//            }
//        } else {
//            $advert = BaseModel::factory('advert')->select(['title', 'url', 'img', 'type', 'adver_type'])
//                ->where('start', '<=', $time)
//                ->where('end', '>=', $time)
//                ->where('status', 1)
//                ->where('platform', $platform)
//                ->where('type', $type)
//                ->orderBy('sort', 'ASC')->get()->toArray();
//        }
//
//        if ($advert) {
//            foreach ($advert as $v) {
//                $data[$v['type']][] = $v;
//            }
//        }

        return json_encode(['status' => 1, 'message' => '成功', 'data' => $data]);
    }

    /**
     * 获取分类数据
     */
    public function postGetNovelByCate_old(Request $request)
    {
        $cateid = $request->input('cateid', 1);
        $page = intval($request->input('page', 1));
        $size = $request->input('size', 20);

        $data = BaseModel::factory('novel')->select(['novelid', 'cateid','read', 'name', 'status', 'chapter', 'author', 'img', 'summary'])
            ->where('cateid', $cateid)
            ->where('show', '=', 1)
//            ->orderBy('sort', 'ASC')->orderBy('add_time', 'DESC')->paginate($size)->toArray();
            ->orderBy('read', 'desc')->paginate($size)->toArray();

        foreach ($data['data'] as $key => $value) {
            if ($value['status'] == 1) {
                $data['data'][$key]['status'] = '已完本';
            } else {
                $data['data'][$key]['status'] = '连载中';
            }
        }

        self::getCateName($data['data']);
        $p = ['total' => $data['total'], 'curpage' => $page];
        return json_encode(['status' => 1, 'message' => '成功', 'page' => $p, 'data' => $data['data']]);
    }


    /**
     *20190418 新数据源的根据分类去获取小说
     */
    public function postGetNovelByCate(Request $request){
        $cateid = $request->input('cateid', 1);
        $page = intval($request->input('page', 1));
        $size = $request->input('size', 20);
        $ret = DB::table('newcate')->where('id',$cateid)->first();//查询该分类下有哪些小分类
        $data=[];
        $novel=[];
        if(!empty($ret)){
            if(!empty($ret->cate)){
                $id = explode(',',$ret->cate);
                $NvNovel = new NvNovelModel();
                $novel = $NvNovel->getNovel($id,$page,$size);
            }
        }
        if(!empty($novel)){
            foreach ($novel as $k=>$value){
                $data[$k]['catename'] = $ret->name;
                $data[$k]['name'] = $value->name;
                $data[$k]['novelid'] = $value->novelid;
                $data[$k]['author'] = $value->author;
                if($value->progress_status == 1){
                    $progress_status = '连载中';
                }else if($value->progress_status == 2){
                    $progress_status = '已完结';
                }else if($value->progress_status == 3){
                    $progress_status = '未知';
                }
                $data[$k]['status'] = $progress_status;
                if($value->read > 10000){
                    $value->read = ((int)($value->read/1000)/10)."W+";
                }else{
                    $value->read = $value->read;
                }

                $data[$k]['img'] = $value->img;
                $data[$k]['read'] = $value->read;
                $data[$k]['summary'] = $value->summary;
                $data[$k]['chapter'] = $value->chapter;
                $data[$k]['cateid'] = $ret->id;
                $data[$k]['sourceid'] = $value->source_id;
                $data[$k]['novel_word_nums'] = $value->novel_word_nums;

            }
        }
        return json_encode(['status' => 1, 'message' => '成功',   'data' => $data]);

    }
    /**
     * 根据分类id获取随机3个小说
     */
    public function postRandomNovelByCate(Request $request)
    {
        $cateid = $request->input('cateid', 1);

        $sql = "SELECT novelid,name,img from novel where cateid={$cateid} and `show` = 1 order by rand() desc limit 0,3";
        $data = DB::select($sql); //查询

        return json_encode(['status' => 1, 'message' => '成功', 'data' => $data]);
    }

    /**
     * 根据名字获取数据
     */
    public function postGetNovelByName_OLD(Request $request)
    {
        $name = $request->input('name', '');

        $data = BaseModel::factory('novel')->select(['novelid', 'cateid', 'name', 'status', 'chapter', 'author', 'img'])
            ->where('author', 'like', '%' . $name . '%')
            ->where('show', 1)
            ->orwhere('name', 'like', '%' . $name . '%')
            ->where('show', 1)
            ->orderBy('sort', 'ASC')->orderBy('add_time', 'DESC')->skip(0)->take(20)->get()->toArray();

        foreach ($data as $key => $value) {
            if ($value['status'] == 1) {
                $data[$key]['status'] = '已完本';
            } else {
                $data[$key]['status'] = '连载中';
            }
        }

        foreach ($data as $value) {
            $sql = "update novel set search=search+1 where novelid={$value['novelid']}";
            DB::select($sql); //查询
        }

        self::getCateName($data);
        return json_encode(['status' => 1, 'message' => '成功', 'data' => $data]);
    }


    public function postGetNovelByName(Request $request){
        $name = $request->input('name', '');
        $NvNovelModel = new NvNovelModel();
        $data = $NvNovelModel->getNovelByName($name);
    }

    /**
     * 搜索提示
     */
    public function postSearchHint(Request $request)
    {
        $name = trim($request->input('name', ''));

        $data = BaseModel::factory('novel')->select(['name', 'author', 'novelid'])
            ->where('author', 'like', $name . '%')
            ->orwhere('name', 'like', $name . '%')
            ->where('show', 1)
            ->orderBy('add_time', 'DESC')->skip(0)->take(10)->get()->toArray();

        $rtn = [];
        $authors = [];
        foreach ($data as $v) {
            if (strpos($v['author'], $name) !== false && !in_array($v['author'], $authors)) {
                array_unshift($rtn, ['name' => $v['author'], 'type' => '作者']);
                $authors[] = $v['author'];
            }
            if (strpos($v['name'], $name) !== false) {
                array_push($rtn, ['name' => $v['name'], 'type' => '书名','novelid'=>$v['novelid']]);
            }
        }

        // 如果没有搜索到内容则记录搜索的关键词
        if (empty($rtn)) {
            $search = BaseModel::factory('search_log')->select(['num'])->where('keyword', $name)->first();
            if ($search) {
                BaseModel::factory('search_log')->where('keyword', $name)->increment('num');
            } else {
                $data = array(
                    'keyword' => $name,
                    'add_time' => date('Y-m-d H:i:s'),
                );
                BaseModel::factory('search_log')->insert([$data]);
            }
        }

        return json_encode(['status' => 1, 'message' => '成功', 'data' => $rtn]);
    }


    public function postSearchNovel_old(Request $request){
        $name = trim($request->input('name', ''));
        $page = trim($request->input('page', 1));
        $size = trim($request->input('size', 10));

        $data = BaseModel::factory('novel')->select(['name', 'novelid'])
            ->where('name', 'like', $name . '%')
            ->where('show', 1)
            ->orderBy('add_time', 'DESC')->paginate($size)->toArray();
        if(!empty($data)){
            return self::sendData(1,'成功',$data['data']);
        }else{
            return self::sendData(1,'暂无数据');
        }
    }

    public function postSearchNovel(Request $request){
        $name = trim($request->input('name', ''));
        $page = trim($request->input('page', 1));
        $size = trim($request->input('size', 10));
        $NvNovelModel = new NvNovelModel();
        $data = $NvNovelModel->searchNovel($name,$page,$size);
        $novel=$novelname=array();
//        if(!empty($data)){
//            foreach ($data as $k=>$value){
//                $novel[$k]=$value->novelid;
//                $novelname[$k]=$value->name;
//            }
//
//        }
        $NovelSearchLogModel = new NovelSearchLogModel();
        $NovelSearchLogModel->insertNovel($name);

        return json_encode(['status' => 1, 'message' => '成功', 'data' => $data]);

    }
    public function postSearchNovelByName(Request $request){
        $name = trim($request->input('name', ''));
        $page = trim($request->input('page', 1));
        $size = trim($request->input('size', 10));
        $NvNovelModel = new NvNovelModel();
        $data = $NvNovelModel->searchNovel($name,$page,$size);
        $novel=$novelname=array();
        if(!empty($data)){
            foreach ($data as $k=>$value){
                $novel[$k]=$value->novelid;
                $novelname[$k]=$value->name;
            }
            $NovelSearchLogModel = new NovelSearchLogModel();
            $NovelSearchLogModel->insertNovel($name);
        }
        return json_encode(['status' => 1, 'message' => '成功', 'data' => $data]);

    }


    public function postSearchNovelByName_OLD(Request $request){
        $name = trim($request->input('name', ''));
        $page = trim($request->input('page', 1));
        $size = trim($request->input('size', 10));
        $data = BaseModel::factory('novel')
            ->where('name', $name)
            ->orwhere('name', 'like', $name . '%')
            ->where('show', 1)->paginate($size)->toArray();
//        return self::sendData(1,'成功',$data['data']);
        $data = $data['data'];
        if(!empty($data)){
            foreach ($data as $k=>$v){
                $info['search'] = $v['search']+1;
                DB::table('novel')->where('novelid',$v['novelid'])->update($info);

                $query = "select novel.name,novel.novelid,novel.status,novel.read,novel.chapter,novel.cateid,novel.img,author,summary,category.name as catename,sourceid  from novel LEFT JOIN category on novel.cateid=category.cateid where novelid={$v['novelid']} ";
                $novel = DB::selectone($query);
                $data[$k]=$novel;
            }
//            $info['search'] = $data->search+1;
//            DB::table('novel')->where('name',$name)->update($info);
            return self::sendData(1,'成功',$data);
        }else{
            return self::sendData(1,'暂无数据','');
        }
    }
    /**
     * 随机搜索关键字
     */
    public function postSearchHistory(Request $request)
    {
        $novelModel = new NovelModel();
        $novelid = $novelModel->novelidBySearch(10);
        $data['all'] = $novelModel->getNovels($novelid);

        $confredis = Config::get('database.redis.default');
        $redis = new libredis([$confredis['host'], $confredis['port']]);

        $d = $redis->get("searchTop5");
        if ($d) {
            $data['top'] = json_decode($d, true);
        } else {
            $sql = "SELECT novelid,name,img from novel where `show` = 1 ORDER BY search desc limit 0,5";
            $data['top'] = DB::select($sql); //查询
            $redis->set("searchTop5", json_encode($data['top']), false, true, 3600);
        }

        return json_encode(['status' => 1, 'message' => '成功', 'data' => $data]);
    }

    /**
     * 获取目录数据
     */
    public function postGetChapter_OLD(Request $request)
    {
        $novelid = $request->input('novelid', 1);
        $sourceid = $request->input('sourceid', 1);
        $page = intval($request->input('page', 1));
        $size = $request->input('size', 100);

        $source = BaseModel::factory('novel_source')->select(['sourceid', 'chapter_name'])
            ->where('sourceid', $sourceid)
            ->first();

        $novel_array = BaseModel::factory('novel')->select(['chapter_table'])->where('novelid', $novelid)->first();
        $t = $novel_array->chapter_table;
        $data = BaseModel::factory($t)->select(['novelid', 'chapter', 'name'])
            ->where('novelid', $novelid)
            ->orderBy('chapter', 'ASC')->paginate($size)->toArray();

        $p = ['total' => $data['total'], 'curpage' => $page];
        return json_encode(['status' => 1, 'message' => '成功', 'page' => $p, 'data' => $data['data']]);
    }


    public function postGetChapter(Request $request){
        $novelid = $request->input('novelid', 1);
        $sourceid = $request->input('sourceid', 1);
        $page = intval($request->input('page', 1));
        $size = $request->input('size', 100);
        $ChapterModel = new ChapterModel();
        $data = $ChapterModel->getChapter($novelid,$page,$size);
        $total = $ChapterModel->getChapterNum($novelid);
        return json_encode(['status' => 1, 'message' => '成功',  'data' => $data,'total'=>$total]);
    }
    public function postGetAllChapter(Request $request){
        $novelid = $request->input('novelid', 1);
        $sourceid = $request->input('sourceid', 1);
        $ChapterModel = new ChapterModel();
        $data = $ChapterModel->getAllChapter($novelid);
        $total = $ChapterModel->getChapterNum($novelid);
        return json_encode(['status' => 1, 'message' => '成功',  'data' => $data,'total'=>$total]);
    }

    /**
     * 获取小说数据
     */
    public function postGetNovelById_OLD(Request $request)
    {
        $novelid = $request->input('novelid', 1);
        $data = BaseModel::factory('novel')->select(['novelid', 'name', 'summary', 'author', 'img', 'status', 'chapter', 'sourceid', 'update_time', 'cateid', 'chapter_table'])
            ->whereIn('novelid', explode(',', $novelid))
            ->get()->toArray();

        $source = BaseModel::factory('novel_source')->select(['sourceid', 'name', 'url', 'chapter_name'])->get();
        foreach ($source as $key => $value) {
            $s[$value->sourceid] = $value;
        }
        foreach ($data as $key => $value) {
            $sources = explode(',', $value['sourceid']);
            $data[$key]['chapter'] = $data[$key]['chapter_name'] = '';
            $data[$key]['refresh_time'] = date('Y-m-d', strtotime($value['update_time']));
            foreach ($sources as $v) {
                $c = $s[$v];
                if ($c->sourceid == $this->source)
                    continue;

                $t = $value['chapter_table'];
                $chapter = BaseModel::factory($t)->select(['name', 'chapter'])
                    ->where('novelid', $novelid)
                    ->orderBy('chapter', 'desc')
                    ->first();

                $c->chapter_name = $chapter['name'];
                $c->refresh_time = date('Y-m-d', strtotime($value['update_time']));
                $c->chapter = $chapter['chapter'];
                $data[$key]['source'][] = $c;

                if (!$data[$key]['chapter'] || $data[$key]['chapter'] < $c->chapter) {
                    $data[$key]['chapter'] = $c->chapter;
                    $data[$key]['chapter_name'] = $c->chapter_name;
                }
            }
            if ($value['status'] == 1) {
                $data[$key]['status'] = '已完本';
            } else {
                $data[$key]['status'] = '连载中';
            }

            $sql = "SELECT novelid,name,img from novel where `show`=1 and cateid={$value['cateid']} order by rand() desc limit 0,3";
            $data[$key]['random'] = DB::select($sql); //查询
        }

        return json_encode(['status' => 1, 'message' => '成功', 'data' => $data]);
    }


    public function postGetNovelById(Request $request){
        $novelid = $request->input('novelid', 1);
        $NvNovelModel = new NvNovelModel();
        $data = $NvNovelModel->getNovelPartById1($novelid);
        $sourceid = $data->sourceid;
        $NovelSourceModel = new NovelSourceModel();
        $source = $NovelSourceModel->getSource($sourceid);
        $source->chapter = $data->chapter;
        $temp[]=$source;
        $data['source'] = $temp;
        $datas[] = $data;
        return json_encode(['status' => 1, 'message' => '成功', 'data' => $datas]);
    }




    /**
     * 获取小说数据
     */
    public function postGetContent_OLD(Request $request)
    {
        $novelid = $request->input('novelid', 1);
        $chapterid = $request->input('chapter', 1);
        $sourceid = $request->input('sourceid', 1);

        $source = BaseModel::factory('novel_source')->select(['sourceid', 'chapter_name', 'start', 'end'])
            ->where('sourceid', $sourceid)
            ->first();

        $novel_array = BaseModel::factory('novel')->select(['chapter_table'])->where('novelid', $novelid)->first();
        $t = $novel_array->chapter_table;

        $chapter = BaseModel::factory($t)->select(['name', 'url'])
            ->where('novelid', $novelid)
            ->where('chapter', $chapterid)
            ->first();

        if ($chapterid == 1) {
            $sql = "update novel set `read`=`read`+1 where novelid={$novelid}";
            DB::query($sql); //查询
        }

        if ($chapter) {
            $path = env('CONTENT_PATH') . $t . '_' . $novelid . '_' . $chapterid . '.txt';
            if (file_exists($path)) {
                $c = file_get_contents($path);
            } else {
                $error = 0;
                $sql = "select ip from `ip` order by rand() limit 1"; //SQL语句
                $ip = DB::select($sql);
                $ip = $ip[0]->ip;

                if ($sourceid == 8) {
                    $c = '';
                    for ($i = 1; $i <= 4; $i++) {
                        if ($i > 1) {
                            $url = str_replace('.html', '-' . $i . '.html', $chapter->url);
                        } else {
                            $url = $chapter->url;
                        }
                        $content = curlGet($url, $ip);
                        if (strpos($content, '502') !== false or strpos($content, '500') !== false) {
                            $error = 1;
                            sleep(1);
                            continue;
                        }
                        $s = strpos($content, $source->start) + strlen($source->start);
                        $e = strrpos($content, $source->end);
                        $txt = substr($content, $s, $e - $s);

                        if (mb_strpos($txt, "您访问太过频繁") || empty($txt)) {
                            $error = 1;
                            sleep(1);
                            continue;
                        }
                        $c .= $txt;
                    }
                } else {
                    for ($i = 0; $i < 5; $i++) {
                        $content = curlGet($chapter->url, $ip);
                        if (strpos($content, 'error-wrapper') !== false) {
                            $error = 1;
                            sleep(1);
                            continue;
                        }
                        $s = strpos($content, $source->start) + strlen($source->start);
                        $e = strrpos($content, $source->end);
                        $c = substr($content, $s, $e - $s);

                        if ($sourceid != 11) {
                            if (!strpos($content, 'charset="utf-8"') and !strpos($content, 'charset=UTF-8') and !strpos($content, 'charset="UTF-8"')) {
                                $c = mb_convert_encoding($c, "UTF-8", "GBK");
                            }
                        }

                        if (mb_strpos($c, "您访问太过频繁") || empty($c)) {
                            $error = 1;
                            sleep(1);
                            continue;
                        }
                    }
                }

                $c = trim($c);
                $c = str_replace('&#12288;', '', $c);
                $c = str_replace('　', '', $c);
                $c = str_replace('&nbsp;', '  ', $c);
                $c = str_replace('<p>', "\r\n", $c);
                $c = str_replace('</p>', "", $c);
                $c = str_replace('<br />', "\r\n", $c);
                $c = str_replace('<br>', "\r\n", $c);
                $c = str_replace('<br/>', "\r\n", $c);
                $c = str_replace("<br\/>", "\r\n", $c);
                $c = str_replace("</br>", "\r\n", $c);
                $c = strip_html_tags(['div', 'script', 'a', 'style'], $c);
                $c = preg_replace("/\r\n *\r\n *\r\n/", "\r\n", $c);

                if ($sourceid == 6 && !(preg_match("/[\x7f-\xff]/", $c))) {
                    $c = str_replace("\r\n", '&#13;&#10;', $c);
                    $c = unicode_decode($c, 'UTF-8');
                }

                if (mb_strpos($c, '章节数据正在同步中') === false && $c) {
                    $error = 0;
                    file_put_contents($path, $c);
                }

                if ($error || empty($c)) {
                    return json_encode(['status' => 0, 'message' => '下载失败']);
                }
            }

            $data['title'] = $chapter->name;
            $data['content'] = $c;
            $data['url'] = $chapter->url;

            return json_encode(['status' => 1, 'message' => '成功', 'data' => $data]);
        }
        return json_encode(['status' => 0, 'message' => '失败']);
    }

    public function postGetContent(Request $request){
        $novelid = $request->input('novelid', 1);
        $chapterid = $request->input('chapter', 1);
        $sourceid = $request->input('sourceid', 1);
        $ChapterModel = new ChapterModel();
        $data = $ChapterModel->getChapterContent($novelid,$chapterid);//获取章节数据

//        $data->content = htmlspecialchars_decode($data->content);

        $data = empty($data)?$this->nullClass:$data;

        return json_encode(['status' => 1, 'message' => '成功',  'data' => $data]);

    }

    /**
     * 同时获取小说多个章节数据
     */
    public function postGetMoreContent_OLD(Request $request)
    {
        $novelid = $request->input('novelid', 1);
        $sourceid = $request->input('sourceid', 1);
        $start = $request->input('start', 1);
        $end = $request->input('end', 1);

        $source = BaseModel::factory('novel_source')->select(['sourceid', 'chapter_name', 'start', 'end'])
            ->where('sourceid', $sourceid)
            ->first();

        $novel_array = BaseModel::factory('novel')->select(['chapter_table'])->where('novelid', $novelid)->first();
        $t = $novel_array->chapter_table;

        $chapters = BaseModel::factory($t)->select(['name', 'url', 'chapter'])
            ->where('novelid', $novelid)
            ->where('chapter', ">", $start - 1)
            ->where('chapter', "<", $end + 1)
            ->get();

        if ($start == 1) {
            $sql = "update novel set `read`=`read`+1 where novelid={$novelid}";
            DB::select($sql); //查询
        }

        if ($chapters) {
            foreach ($chapters as $chapter) {
                $path = env('CONTENT_PATH') . $t . '_' . $novelid . '_' . $chapter->chapter . '.txt';
                if (file_exists($path)) {
                    $c = file_get_contents($path);
                    $data[] = ['title' => $chapter->name, 'chapter' => $chapter->chapter, 'url' => $chapter->url, 'content' => $c];
                    continue;
                } else {
                    $error = 0;
                    $sql = "select ip from `ip` order by rand() limit 1"; //SQL语句
                    $ip = DB::select($sql);
                    $ip = $ip[0]->ip;

                    if ($sourceid == 8) {
                        $c = '';
                        for ($i = 1; $i <= 4; $i++) {
                            if ($i > 1) {
                                $url = str_replace('.html', '-' . $i . '.html', $chapter->url);
                            } else {
                                $url = $chapter->url;
                            }
                            $content = curlGet($url, $ip);
                            if (strpos($content, '502') !== false or strpos($content, '500') !== false) {
                                $error = 1;
                                sleep(1);
                                continue;
                            }
                            $s = strpos($content, $source->start) + strlen($source->start);
                            $e = strrpos($content, $source->end);
                            $txt = substr($content, $s, $e - $s);

                            if (mb_strpos($txt, "您访问太过频繁") || empty($txt)) {
                                $error = 1;
                                sleep(1);
                                continue;
                            }
                            $c .= $txt;
                        }
                    } else {
                        for ($i = 0; $i < 5; $i++) {
                            $content = curlGet($chapter->url, $ip);
                            if (strpos($content, 'error-wrapper') !== false) {
                                $error = 1;
                                sleep(1);
                                continue;
                            }
                            $s = strpos($content, $source->start) + strlen($source->start);
                            $e = strrpos($content, $source->end);
                            $c = substr($content, $s, $e - $s);

                            if ($sourceid != 11) {
                                if (!strpos($content, 'charset="utf-8"') and !strpos($content, 'charset=UTF-8') and !strpos($content, 'charset="UTF-8"')) {
                                    $c = mb_convert_encoding($c, "UTF-8", "GBK");
                                }
                            }

                            if (mb_strpos($c, "您访问太过频繁") || empty($c)) {
                                $error = 1;
                                sleep(1);
                                continue;
                            }
                        }
                    }

                    $c = trim($c);
                    $c = str_replace('&#12288;', '', $c);
                    $c = str_replace('　', '', $c);
                    $c = str_replace('&nbsp;', '  ', $c);
                    $c = str_replace('<p>', "\r\n", $c);
                    $c = str_replace('</p>', "", $c);
                    $c = str_replace('<br />', "\r\n", $c);
                    $c = str_replace('<br>', "\r\n", $c);
                    $c = str_replace('<br/>', "\r\n", $c);
                    $c = str_replace("<br\/>", "\r\n", $c);
                    $c = str_replace("</br>", "\r\n", $c);
                    $c = strip_html_tags(['div', 'script', 'a', 'style'], $c);
                    $c = preg_replace("/\r\n *\r\n *\r\n/", "\r\n", $c);
                    if ($sourceid == 6 && !(preg_match("/[\x7f-\xff]/", $c))) {
                        $c = str_replace("\r\n", '&#13;&#10;', $c);
                        $c = unicode_decode($c, 'UTF-8');
                    }

                    if (mb_strpos($c, '章节数据正在同步中') === false && $c) {
                        $error = 0;
                        file_put_contents($path, $c);
                        $data[] = ['title' => $chapter->name, 'chapter' => $chapter->chapter, 'url' => $chapter->url, 'content' => $c];
                    }

                    if ($error || empty($c)) {
                        return json_encode(['status' => 0, 'message' => '下载失败']);
                    }
                }
            }

            return json_encode(['status' => 1, 'message' => '成功', 'data' => $data]);
        }
        return json_encode(['status' => 0, 'message' => '失败']);
    }

    public function postGetMoreContent(Request $request){
        $novelid = $request->input('novelid', 1);
        $sourceid = $request->input('sourceid', 1);
        $start = $request->input('start', 1);
        $end = $request->input('end', 1);
        $ChapterModel = new ChapterModel();
        $data = $ChapterModel->getMoreChapterContent($novelid,$start,$end);

        return json_encode(['status' => 1, 'message' => '成功',  'data' => $data]);

    }

    /**
     * 安装或卸载时调用
     */
    public function postInstallInfo(Request $request)
    {
        $type = $request->input('type', 1);
        $platform = $data['platform'] = $request->input('platform', 1);
        $uuid = $data['uuid'] = $request->input('uuid', '');
        $request->setTrustedProxies(array('10.32.0.1/16'));
        $ip = $data['ip'] = $request->getClientIp();
        $data['pack_name'] = $request->input('pack_name', '');

        $t = $type == 1 ? 'install' : 'uninstall';

        if (!$uuid) {
            return json_encode(['status' => -1, 'message' => 'uuid不能为空']);
        }

        $data['time'] = time();
        DB::table($t)->insert(array($data));

        return json_encode(['status' => 1, 'message' => '成功']);
    }

    /**
     * 每天第一次打开应用时调用
     */
    public function postFirstOpen(Request $request)
    {
        $platform = $data['platform'] = $request->input('platform', 1);
        $uuid = $data['uuid'] = $request->input('uuid', '');
        $request->setTrustedProxies(array('10.32.0.1/16'));
        $ip = $data['ip'] = $request->getClientIp();
        $data['pack_name'] = $request->input('pack_name', '');

        if (!$uuid) {
            return json_encode(['status' => -1, 'message' => 'uuid不能为空']);
        }

        $t = "login";
        $t .= date('Y') . (intval(date('m') / 5) + 1);
        $data['time'] = time();
        DB::table($t)->insert(array($data));

        return json_encode(['status' => 1, 'message' => '成功']);
    }

    /**
     * 获取小说排行榜更多
     */
    public function postRecommend(Request $request)
    {
        $novelModel = new NovelModel();
        $novelid = $novelModel->read(3, 3, 0);
        $data['精选推荐'] = $novelModel->getNovels($novelid);

        $novelid = $novelModel->read(3, 3, 4);
        $data['大家都在看'] = $novelModel->getNovels($novelid);

        $novelid = $novelModel->novelidByChannel(1, 3);
        $data['男频推荐'] = $novelModel->getNovels($novelid);

        $novelid = $novelModel->novelidByChannel(2, 3);
        $data['女频推荐'] = $novelModel->getNovels($novelid);

        return json_encode(['status' => 1, 'message' => '成功', 'data' => $data]);
    }

    /**
     * 获取推荐更多
     */
    public function postRecommendMore(Request $request)
    {
        $channel = $request->input('channel', 1);
        $page = intval($request->input('page', 1));
        $size = $request->input('size', 20);

        $b = ($page - 1) * $size;
        if ($channel <= 2) {
            $sql = "select a.novelid,a.cateid,a.name,a.img,a.status,a.author,a.chapter,a.summary,b.name as catename from `novel` as a left join `category` as b on a.cateid = b.cateid where a.show = 1 and b.channel = {$channel} order by a.sort asc, a.read desc limit {$b},{$size}"; //SQL语句
        } else {
            if ($channel == 3) {
                $sql = "select a.novelid,a.cateid,a.name,a.img,a.status,a.author,a.chapter,a.summary,b.name as catename from `novel` as a left join `category` as b on a.cateid = b.cateid where a.show = 1 order by a.add_time desc, a.read desc limit {$b},{$size}"; //SQL语句
            } elseif ($channel == 4) {
                $sql = "select a.novelid,a.cateid,a.name,a.img,a.status,a.author,a.chapter,a.summary,b.name as catename from `novel` as a left join `category` as b on a.cateid = b.cateid where a.show = 1 order by a.sort asc, a.read desc limit {$b},{$size}"; //SQL语句
            }
        }
        $data['data'] = DB::select($sql);
        $data['total'] = count($data['data']);

//        self::getCateName($data['data'], true);

        $p = ['total' => $data['total'], 'curpage' => $page];
        return json_encode(['status' => 1, 'message' => '成功', 'page' => $p, 'data' => $data['data']]);
    }

    /**
     * 登录
     */
    public function postLogin(Request $request)
    {
        $weiboid = $data['weiboid'] = $request->input('weiboid', '');
        $qqid = $data['qqid'] = $request->input('qqid', '');
        $weixinid = $data['weixinid'] = $request->input('weixinid', '');
        $nickname = $data['nickname'] = $request->input('nickname', '');

        if (!$weiboid && !$qqid && !$weixinid) {
            return json_encode(['status' => -1, 'message' => '唯一字符串为空']);
        }
        if ($weiboid) {
            $where['weiboid'] = $weiboid;
        }
        if ($qqid) {
            $where['qqid'] = $qqid;
        }
        if ($weixinid) {
            $where['weixinid'] = $weixinid;
        }

        $user = BaseModel::factory('reader')->select(['id', 'nickname'])
            ->where($where)->first();

        if (!$user) {
            $id = DB::table('reader')->insertGetId($data);
        } else {
            $id = $user->id;
        }

        $token = $this->token($id);
        return json_encode(['status' => 1, 'message' => '成功', 'data' => ['token' => $token, 'userid' => $id]]);
    }

    /**
     * 获取书架
     */
    public function postGetBooks(Request $request)
    {
        $token = $request->input('token', '');

        if (!$token) {
            return json_encode(['status' => -1, 'message' => 'token为空']);
        }

        $id = $this->parseToken($token);
        $data = BaseModel::factory('reader_book')->select(['novelid', 'chapter'])
            ->where('userid', $id)->get();

        return json_encode(['status' => 1, 'message' => '成功', 'data' => $data]);
    }

    /**
     * 获取书架
     */
    public function postDelBook(Request $request)
    {
        $token = $request->input('token', '');
        $novelid = $request->input('novelid', '');

        if (!$token) {
            return json_encode(['status' => -1, 'message' => 'token为空']);
        }

        $id = $this->parseToken($token);
        $data = BaseModel::factory('reader_book')->where('userid', $id)->where('novelid', $novelid)->delete();

        return json_encode(['status' => 1, 'message' => '成功']);
    }

    /**
     * 获取书架
     */
    public function postUpdateBook(Request $request)
    {
        $token = $request->input('token', '');
        $novelid = $request->input('novelid', '');
        $chapter = $request->input('chapter', '');

        if (!$token || !$novelid || !$chapter) {
            return json_encode(['status' => -1, 'message' => '参数不能为空']);
        }

        $id = $this->parseToken($token);
        $sql = "INSERT INTO reader_book VALUES($id, $novelid, $chapter,'',{date('Y-m-d H:i:s')}) ON DUPLICATE KEY UPDATE chapter=$chapter;";
        DB::select($sql);

        return json_encode(['status' => 1, 'message' => '成功']);
    }

    /**
     * 根据名字获取数据
     */
    public function postVersion(Request $request)
    {
        $packid = $request->input('packid', '');
        $pack_name = $request->input('pack_name', '');

        $data = BaseModel::factory('android_update')->select(['packid', 'version', 'url', 'update', 'remark'])
            ->where('packid', $packid)->where('pack_name', $pack_name)->first();

        $data = $data ? $data : [];
        return json_encode(['status' => 1, 'message' => '成功', 'data' => $data]);
    }

    /**
     * 获取分享链接
     */
    public function postShare(Request $request)
    {
        return json_encode(['status' => 1, 'message' => '成功', 'data' => ['url' => 'http://www.shuhun.com']]);
    }

    /**
     * 反馈
     */
    public function postFeedback(Request $request)
    {
        $platform = $data['platform'] = $request->input('platform', 1);
        $feedback = $data['feedback'] = $request->input('feedback', '');
        $contact = $data['contact'] = $request->input('contact', '书籍需求反馈');

        if (!$feedback || !$contact) {
            return json_encode(['status' => -1, 'message' => '联系方式和反馈内容不能为空']);
        }

        $data['add_time'] = date('Y-m-d H:i:s');
        DB::table('feedback')->insert(array($data));

        return json_encode(['status' => 1, 'message' => '成功']);
    }

    public function getCateName(&$data, $type = false)
    {
        $sql = "select name,cateid from category";
        $name = DB::select($sql);
        foreach ($name as $key => $value) {
            $names[$value->cateid] = $value->name;
        }

        if ($type) {
            foreach ($data as $key => $value) {
                if (isset($names[$value->cateid])) {
                    $data[$key]->catename = $names[$value->cateid];
                }
            }
        } else {
            foreach ($data as $key => $value) {
                if (isset($names[$value['cateid']])) {
                    $data[$key]['catename'] = $names[$value['cateid']];
                }
            }
        }
    }

    /**
     * 获取广告新接口
     */
    public function postNewAdvert(Request $request)
    {
        $platform = $request->input('platform', '1');
        $packid = $request->input('packid', '');
        $time = time();

        $advert = BaseModel::factory('advert')->select(['title', 'url', 'img', 'type', 'adver_type'])
            ->where('start', '<=', $time)
            ->where('end', '>=', $time)
            ->where('status', 1)
            ->where('platform', $platform)
            ->orderBy('sort', 'ASC')->get()->toArray();

        return json_encode(['status' => 1, 'message' => '成功', 'data' => $advert]);
    }

    /**
     * 获取推荐小说新接口
     */
    public function postNewRecommend()
    {
        $data1 = array(
            'type_name' => '精选推荐',
            'type_id' => 3,
        );

        $data2 = array(
            'type_name' => '大家都在看',
            'type_id' => 4,
        );

        $data3 = array(
            'type_name' => '男频推荐',
            'type_id' => 1,
        );

        $data4 = array(
            'type_name' => '女频推荐',
            'type_id' => 2,
        );

        $data = array($data1, $data3, $data4, $data2);

        return json_encode(['status' => 1, 'message' => '成功', 'data' => $data]);
    }

    /**
     * 根据分类ID随机获取3条数据
     */
    public function postNovelCate(Request $request)
    {
        $cate = $request->input('cate', '0');
        $cate_array = explode('|', $cate);
        $cate_array = array_filter($cate_array);

        if (!empty($cate_array)) {
            $novel = BaseModel::factory('novel')->select(['novelid', 'name', 'summary', 'img', 'author', 'chapter', 'sourceid', 'update_time', 'cateid', 'status'])
                ->whereIn('cateid', $cate_array)
                ->where('show', 1)
                ->orderBy('read', 'desc')
                ->limit(50)
                ->inRandomOrder()
                ->take(6)
                ->get()
                ->toArray();
        } else {
            $novel = BaseModel::factory('novel')->select(['novelid', 'name', 'summary', 'img', 'author', 'chapter', 'sourceid', 'update_time', 'cateid', 'status'])
                ->where('show', 1)
                ->orderBy('read', 'desc')
                ->limit(50)
                ->inRandomOrder()
                ->take(6)
                ->get()
                ->toArray();
        }

        foreach ($novel as $key => $value) {
            if ($value['status'] == 1) {
                $novel[$key]['status'] = '已完本';
            } else {
                $novel[$key]['status'] = '连载中';
            }

            $source = BaseModel::factory('novel_source')->select(['sourceid', 'name', 'url'])->where('sourceid', $value['sourceid'])->first();
            $novel[$key]['source'][0] = $source;
            $novel[$key]['source'][0]['refresh_time'] = date('Y-m-d', strtotime($value['update_time']));
        }

        return json_encode(['status' => 1, 'message' => '成功', 'data' => $novel]);
    }

    /**
     * 根据推广渠道统计下载APP次数
     */
    public function postAppDownload(Request $request)
    {
        $pack_name = $request->input('pack_name', '0');
        $platform = $request->input('platform', '1');
        $data = array(
            'pack_name' => $pack_name,
            'platform' => $platform,
            'ip' => $request->getClientIp(),
            'add_time' => time(),
        );
        DB::table('download_log')->insert(array($data));

        return json_encode(['status' => 1, 'message' => '成功']);
    }

    //--
    public function bookname($id){
//        $novel = DB::table('novel');
//        $name =  DB::table('novel')->where('novelid',$id)->first();
        $query = "select novel.name,novel.img,author,status,chapter,summary,novel.read,category.name as catename from novel LEFT JOIN category on novel.cateid=category.cateid where novelid={$id} ";
        $name = DB::selectone($query);
        return
            [
                'name' => $name->name,
                'summary' => $name->summary,
                'img' => $name->img,
                'author' => $name->author,
                'read' => $name->read,
                'catename' => $name->catename,
                'chapter' => $name->chapter,
                'status' => $name->status,
            ];

    }

    /**
     * 获取书城的全部信息gt
     * @return false|string
     *
     */
    public function postBookStore_old(){
        //banner
        $banners =  DB::table('selection')->where('status',1)->where('type',1)->orderBy('sort','ASC')->get();
        $banner = array();
        if(count($banners)>0){
            foreach ($banners as $k=>$vv){
                if($vv->extra ==1){//广告banner
                    $name =  DB::table('advert')->where('advertid',intval($vv->content))->first();
                    $banner[$k]['title']=$name->title;
                    $banner[$k]['img']=$name->img;
                    $banner[$k]['id']=$vv->content;
                    $banner[$k]['type']=$vv->extra;
                }else{//
                    $name =  DB::table('novel')->where('novelid',intval($vv->content))->first();
                    $banner[$k]['title']=$name->name;
                    $banner[$k]['img']=$name->img;
                    $banner[$k]['id']=$vv->content;
                    $banner[$k]['type']=$vv->extra;
                }

            }

        }

        //hot one begin
        $hotone =  DB::table('selection')->where('status',1)->where('type',2)->first();
        $onehot = array();
        if(!empty($hotone)){
            //根据书id去查询书名
            $bookinfo = $this->bookname($hotone->content);
            $onehot['title'] = $bookinfo['name'];
            $onehot['summary'] = $bookinfo['summary'];
            $onehot['img'] = $bookinfo['img'];
            $onehot['author'] = $bookinfo['author'];
            $onehot['id'] = $hotone->content;
            $onehot['catename']=$bookinfo['catename'];
        }
        //hot one end


        //hot more begin
        $hotmore = DB::table('selection')->where('status',1)->where('type',3)->orderBy('sort','ASC')->get();

        $morehot = array();
        if(count($hotmore)>0){
            foreach ($hotmore as $k=>$value){
                $bookinfo = $this->bookname($value->content);
                $morehot[$k]['title'] = $bookinfo['name'];
                $morehot[$k]['summary'] = $bookinfo['summary'];
                $morehot[$k]['img'] = $bookinfo['img'];
                $morehot[$k]['author'] = $bookinfo['author'];
                $morehot[$k]['id'] = $value->content;
                $morehot[$k]['catename']=$bookinfo['catename'];
            }
        }
        //hot more end


        $morepart= DB::table('selection')->where('status',1)->whereIn('type',[4,5])->orderBy('index','desc')->groupBy("name")->get();
        $partmore = array();
        if(count($morepart)>0){
            foreach ($morepart as $k=>$v){
                if($v->type==5){
                    $inclu = DB::table('selection')->where('status',1)->where('type',5)->orderBy('sort','ASC')->get();
                    $partmore[$k] = $inclu;
                }else{
                    $includes = DB::table('selection')->where('name',$v->name)->where('type',4)->orderBy('sort','ASC')->get();
                    foreach ($includes as $key=>$val){

                        $bookinfo = $this->bookname($val->content);
                        $include[$key]['title'] = $bookinfo['name'];
                        $include[$key]['summary'] = $bookinfo['summary'];
                        $include[$key]['img'] = $bookinfo['img'];
                        $include[$key]['author'] = $bookinfo['author'];
                        $include[$key]['id'] = $val->content;
                        $include[$key]['catename']=$bookinfo['catename'];
                        $include[$key]['showtype']=$val->showtype;


//                    $include[$key]->title = $this->bookname($val->content);
                    }
                    $partmore[$k] = $include;
                }

            }
        }


//        $art = DB::table('selection')->where('status',1)->where('type',5)->orderBy('sort','ASC')->get();

        return json_encode(
            [
                'status'=>1,
                'message' => "成功",
                'banner'=>$banner,
                'hotone'=>$onehot,
                'hotmore'=>$morehot,
//                'art'=>$art,
                'morepart'=>$partmore,

            ]
        );
    }

    /**
     * 获取书城首页的全部信息
     * @return false|string
     */
    public function postBookStore(Request $request){
        $uuid = $request->input('uuid', '');
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);
        $offset = ($page-1)*$size;
        $NvNovelModel = new NvNovelModel();
        //banner
        $banners =  DB::table('novel_banner')->where('status',1)->where('type',1)->orderBy('sort','asc')->get();
        $data['banner'] = $banners;

        //hot
        $novel = array();
        $hotonequery = "select 
                        choice_novel.novelid as novelid,choiceness.id as cid,choiceness.name as cname from choiceness 
                        left join choice_novel on choiceness.id=choice_novel.choiceid where choiceness.type=2 and choiceness.status=1 and choice_novel.status=1 order by choice_novel.sort asc limit $offset,$size";
        $novel = DB::select($hotonequery);
        $id=0;
        $name='';

        if(!empty($novel)){
            foreach ($novel as $k=>$value){
//                $novelquery = "select novel.novelid,novel.img,novel.read,novel.name as novelname,novel.summary,novel.author,category.name as catename from novel left join category on novel.cateid=category.cateid where novelid={$value->novelid}";
//                $novelres = DB::selectone($novelquery);

                $novelres = $NvNovelModel->getNovelPartById($value->novelid);
                if(!empty($novelres)){
                    if($novelres->read >10000){
                        $novelres->read = (intval($novelres->read/1000)/10)."W+";
                    }
                    $novel[$k]=$novelres;
                }
                $name = $value->cname;
                $id=$value->cid;
            }
        }else{
            $novel=[];
        }
        $data['hot']['detail'] = $novel;
        $data['hot']['id'] = $id;
        $data['hot']['name'] = $name;


        //morepart
        $morepart =array();
        $morepart =  DB::table('choiceness')->where('status',1)->where('isdel',1)->whereIn('type',[4,5])->orderBy('sort','asc')->get();
        if(!empty($morepart)){
            foreach ($morepart as $key=>$v){
                $choiceid = $v->id;
                if($v->type == 4){//作者
                    $res =  DB::table('novel_banner')->select('id','img','link')->where('status',1)->where('type',2)->where('choiceid',$choiceid)->orderBy('sort','asc')->get();
                    $morepart[$key]->detail = $res;
                }else{
//                    $res =  DB::table('choice_novel')->select('id','novelid')->where('status',1)->where('choiceid',$choiceid)->orderBy('sort','asc')->paginate($size);
//                    return json_encode(['status'=>1,'message'=>'成功','data'=>$res1['last_page']]);
                    $hotonequery1 = "select 
                        novelid,id   from choice_novel 
                         where choiceid=$choiceid and status=1  order by  sort asc limit $offset,$size";
                    $res = DB::select($hotonequery1);
                    $res1 = array();
                    foreach ($res as $k=>$val){
//                        $novelquery = "select novel.novelid,novel.img,novel.read,novel.name as novelname,novel.summary,novel.author,category.name as catename from novel left join category on novel.cateid=category.cateid where novelid={$val->novelid}";
//                        $novelres = DB::selectone($novelquery);

                        $novelres = $NvNovelModel->getNovelPartById($val->novelid);

//                        $res1[$k]['a'] = $novelres;
                        if(!empty($novelres)){
//                            $res1[$k]['novelname']=$novelres->novelname;
//                            $res1[$k]['img']=$novelres->img;
                            if($novelres->read >10000){
                                $novelres->read = (intval($novelres->read/1000)/10)."W+";
                            }else{
                                $novelres->read=$novelres->read;
                            }

//                            $res1[$k]['summary']=$novelres->summary;
//                            $res1[$k]['author']=$novelres->author;
//                            $res1[$k]['catename']=$novelres->catename;
//                            $res1[$k]['sourceid']=$novelres->sourceid;
//                            $res1[$k]['novelid']=$novelres->novelid;
                            $res1[] = $novelres;
                        }



                    }
                    $morepart[$key]->detail = $res1;
//                    $morepart[$key]->detail1 = $a;
                }
            }
        }



        $data['morepart'] = $morepart;

//        $ret = DB::table('bookshelf')->where('user_id',$uuid)->where('status',1)->first();
//
//        $perfer='';
//        if(!empty($ret)){
//            $perfer = $ret->prefer;
//        }
//
//        if(empty($perfer)){
////            $guess = DB::table('novel')->where('show',1)->orderBy('read','desc')->take(10)->get();
//            $novelquery = "select novel.novelid,novel.img,novel.read,novel.name as novelname,novel.summary,novel.author,category.name as catename from novel left join category on novel.cateid=category.cateid order by novel.read desc limit 10";
//            $guess = DB::select($novelquery);
//            foreach ($guess as $k=>$v){
//                if($v->read >10000){
//                    $guess[$k]->read = (intval($v->read/1000)/10)."W+";
//                }
//            }
////            $guess[$i]=$novelres;
//        }else{
//            $perferArr = json_decode($perfer,1);
////            $guess = DB::table('novel')->where('show',1)->whereIn('cateid',$perferArr)->orderBy('read','desc')->take(5)->get();
//            for($i=0;$i<count($perferArr);$i++){
//                $novelquery = "select novel.novelid,novel.img,novel.read,novel.name as novelname,novel.summary,novel.author,category.name as catename from novel left join category on novel.cateid=category.cateid where novelid={$perferArr[$i]} order by novel.read desc limit 10";
//                $novelres = DB::selectone($novelquery);
//                if($novelres->read >10000){
//                    $novelres->read = (intval($novelres->read/1000)/10)."W+";
//                }
//                $guess[$i]=$novelres;
//            }
//
//
//
//        }
//        $data['guess'] = $guess;
        return json_encode(['status'=>1,'message'=>'成功','data'=>$data]);
    }


    /**
     * 获取更多小说
     * @param Request $request
     * @return false|string
     */
    public function postGetMoreNovel(Request $request){

        $id = $request->input('id', 1);

        $page = intval($request->input('page', 1));
        $size = $request->input('size', 20);

        $NvNovelModel = new NvNovelModel();

//        $morequery = "select novel.novelid,novel.img,novel.read,novel.name as novelname,novel.summary,novel.author,novel.cateid from novel left join choice_novel on novel.novelid=choice_novel.novelid where choice_novel.choiceid={$id} and choice_novel.status=1 limit {$pageno},{$size}";
//
//        $moreres = DB::select($morequery);

        $data = DB::table('choice_novel')->where('choiceid',$id)->where('status',1)->orderBy('sort','asc')->skip(($page-1) * $size)->take($size)->get();
        $moreres=[];
        $novelid=[];
        foreach ($data as $k=>$v){
            $novelid[$k] = $v->novelid;
        }
        $moreres = $NvNovelModel->getNovelIdArray($novelid,$size);
        foreach ($moreres as $k=>$v){
            if($v->read >10000){
                $moreres[$k]->read = (intval($v->read/1000)/10)."W+";
            }else{
                $moreres[$k]->read = $v->read;
            }
            $moreres[$k]->novelname = $v->name;

        }
//        $moreres[$k] =$novel;


//        if(!empty($moreres)){
//            foreach ($moreres as $k=>$v){
//                $ret = DB::table('category')->where('cateid',$v->cateid)->first();
//                $moreres[$k]->catename =$ret->name;
//                if($v->read >10000){
//                    $moreres[$k]->read = (intval($v->read/1000)/10)."W+";
//                }
//            }
//        }

        return json_encode(['status'=>1,'message'=>'成功','data'=>$moreres]);
    }



    /**
     * 获取榜单、日更、完本、男频、女频数据gt
     * @param Request $request
     */
    public function postList(Request $request){

        $type = $request->input('type', 1);//1:榜单；2：日更；3完本；4：男频；5：女频
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);
        $offset = ($page-1)*$size;
        $NvNovelModel = new NvNovelModel();
        if($type == 1){


            $recommend = $NvNovelModel->getRecommend();//点击率最高的三本


            $popularity = $NvNovelModel->getPopularity();//人气榜

            $shell = $NvNovelModel->getBookshelf();//书架榜



            $manbook = $womanbook=[];
            $mancate = DB::table('channel')->select('kind')->where('name','男频')->first();
            $man=array();$cataArrayman=array();$cataArraywoman=array();
            if(!empty($mancate)){

                $cataArraynam = explode(",",$mancate->kind);
                $retman = DB::table('newcate')->select('cate')->whereIn('id',$cataArraynam)->get();
                if(!empty($retman)){

                    foreach ($retman as $k=>$val){
                        $cata = $val->cate;
                        $cataArr = explode(',',$cata);
                        $cataArrayman = array_merge($cataArrayman,$cataArr);
                    }
                    $cataArrayman = array_unique($cataArrayman);

//                    $cataArrman = explode(",",$retman->cate);
                    $man = $NvNovelModel->getNovel($cataArrayman,1,50);
                }

            }



            $womancate = DB::table('channel')->select('kind')->where('name','女频')->first();
            $woman=array();
            if(!empty($womancate)){
                $cataArraywo = explode(",",$womancate->kind);
                $retwoman = DB::table('newcate')->select('cate')->whereIn('id',$cataArraywo)->get();

                if(!empty($retwoman)){
                    foreach ($retwoman as $k=>$val){
                        $cata = $val->cate;
                        $cataArr = explode(',',$cata);
                        $cataArraywoman = array_merge($cataArraywoman,$cataArr);
                    }
                    $cataArraywoman = array_unique($cataArraywoman);

//                    $cataArrman = explode(",",$retman->cate);
                    $woman = $NvNovelModel->getNovel($cataArraywoman,1,50);
                }
            }



            $download = $NvNovelModel->getdown();//下载榜

            $data['recommend'] = $recommend;
            $data['popularity'] = $popularity;
            $data['bookshell'] = $shell;
            $data['manbook'] = $man;
            $data['womanbook'] = $woman;
            $data['download'] = $download;


        }elseif ($type ==2){

            $data['refresh'] = $NvNovelModel->getRefreshEndNovel($page,$size,1);

        }elseif ($type ==3){

            $data['end'] = $NvNovelModel->getRefreshEndNovel($page,$size,2);

        }elseif ($type ==4){
            $mancate = DB::table('channel')->select('kind','kindname')->where('status',1)->where('name','男频')->first();
            $cata=array();
            if(!empty($mancate)){
                $cataArr = explode(",",$mancate->kind);
                $catanameArr = explode(",",$mancate->kindname);
                for($i=0;$i<count($cataArr);$i++){
                    $cata[$i]['id'] = $cataArr[$i];
                    $cata[$i]['name'] = $catanameArr[$i];
                    //获取该分类下的书籍
                }
            }

            $data['man'] = $cata;


        }else{

            $mancate = DB::table('channel')->select('kind','kindname')->where('status',1)->where('name','女频')->first();
            $cata=array();
            if(!empty($mancate)){
                $cataArr = explode(",",$mancate->kind);
                $catanameArr = explode(",",$mancate->kindname);


                for($i=0;$i<count($cataArr);$i++){
                    $cata[$i]['id'] = $cataArr[$i];
                    $cata[$i]['name'] = $catanameArr[$i];
                    //获取该分类下的书籍
//                $cata[$i]['novel'] = $this->acquirenovelbycate($cataArr[$i]);
                }
            }


            $data['woman'] = $cata;
        }
        return json_encode(
            [
                'status' =>1,
                'message'  =>'成功',
                'data' => $data
            ]
        );
    }


    /**
     * 根据分类id获取该分类下的部分书籍
     * @param Request $request
     * @return false|string
     */
    public function postAcquireCateByCname(Request $request){
        $type = $request->input('type', 1);

//        $query = "select novel.novelid,novel.img,novel.read,novel.name as novelname,novel.refresh_time,novel.summary,novel.author,category.name as catename from novel left join category on novel.cateid=category.cateid where novel.status=0 and novel.show=1 and novel.cateid={$cateid} order by novel.refresh_time desc,novel.read asc limit 50";
//        $result = DB::select($query);
//
////        $novel = DB::table('novel')->where('show',1)->where('cateid',$cateid)->orderBy('read','desc')->take(30)->get();
//        return json_encode(['status'=>1,'message'=>'成功','data'=>$result]);
        if($type ==1){//男频
            $channel = DB::table('channel')->where('status',1)->where('name','男频')->first();
        }else{//女频
            $channel = DB::table('channel')->where('status',1)->where('name','女频')->first();
        }

        $cataArr = explode(",",$channel->kind);
        $cate = array();
        for($i=0;$i<count($cataArr);$i++){
            $res = DB::table('newcate')->select('id as cateid','name','img')->where('id',$cataArr[$i])->where('status',1)->first();
            $cate[] = $res;

        }
        $channel->category=$cate;

        return json_encode(
            [
                'status' =>1,
                'message'  =>'成功',
                'data' => $channel
            ]
        );
    }

    /**
     * 获取有哪些分类gt
     * @param Request $request
     *返回有哪些频道
     */
    public function postClassify(){

        $channels = DB::table('channel')->select('name','id')->where('status',1)->orderBy('id','asc')->get();

        if(empty($channels)){
            return self::sendData(1,'暂无数据',[]);
        }else{
            return self::sendData(1,'成功', $channels);
        }

    }


    /**
     * 获取有哪些分类gt
     * @param Request $request
     *返回频道下的分类数据
     */
    public function postUnderClassify(Request $request){
        $channelid = $request->input('channelid', 1);
        $channel = DB::table('channel')->where('status',1)->where('id',$channelid)->first();

        if(!empty($channel)){
            if(empty($channel->kind)){
                return self::sendData(1,'暂无数据',$this->nullClass);
            }else{
                $cataArr = explode(",",$channel->kind);
                $cate = array();
                for($i=0;$i<count($cataArr);$i++){
//                    $res = DB::table('category')->select('cateid','keyword','name','img')->where('cateid',$cataArr[$i])->first();
                    $res = DB::table('newcate')->select('id','name','img')->where('id',$cataArr[$i])->where('status',1)->first();
                    if(!empty($res)){
                        $res->cateid=$res->id;
                        $cate[] = $res;
                    }


                }
                $channel->category=$cate;
                return self::sendData(1,'成功',$channel);
            }
        }else{
            return self::sendData(0,'暂无数据',$this->nullClass);
        }

    }



    /**
     * 获取每个用户的书架数据 gt
     * @param Request $request
     *
     */
    public function postBookshelf(Request $request){
        $uuid = $request->input('uuid', "");
        $NvNovelModel = new NvNovelModel();
//        $query = "select name,img,author,cateid,novel.novelid,readnow,sourceid,sourceid,summary from novel left join bookshelf on bookshelf.novelid=novel.novelid where user_id={$uuid} and bookshelf.status=1 order by bookshelf.addtime desc";
//        $result = DB::select($query);
        $result = DB::table('bookshelf')->where(['user_id'=>$uuid,'status'=>1])->orderBy('time','desc')->get();
        $results=[];
        if(!empty($result)){

            foreach ($result as $k=>$value){
//                $result[$k] = $NvNovelModel->getNovelById1($value->novelid,50);
                $res = $NvNovelModel->getNovelById1($value->novelid,50);
                if(!empty($res)){
                    $ret = DB::table('user_read_record')->where('uuid',$uuid)->where('status',1)->where('novelid',$value->novelid)->first();
                    if(!empty($ret)){
                        $res->chapternow = $ret->chapter;
                    }else{
                        $res->chapternow = 1;
                    }
                    $results[] = $res;
                }

            }

        }


        $data['bookshelf'] = $results;

        $read = DB::table('user_read_record')->where('uuid',$uuid)->where('kind',1)->orderBy('addtime','desc')->first();

        if(!empty($read)){
            $novelres = $NvNovelModel->getNovelById($read->novelid);
            $data['readnow']=$novelres;
            $data['readnow']->chapter=$read->chapter;
        }else{
            $data['readnow']=$this->nullClass;
        }

        return json_encode(
            [
                'status' =>1,
                'message'  =>'成功',
                'data' => $data
            ]
        );
    }


    /**
     * 编辑每个用户的书架信息
     * @param Request $request
     */
    public function postEditBookshelf(Request $request){
        $uuid = $request->input('uuid', "");
        $type = $request->input('type', "");//1:添加书籍；2：删除书籍
        $novel = $request->input('novel', "");//书籍id

        if($type == 1){
            $NvNovelModel = new NvNovelModel();
            $data['user_id'] = $uuid;
            $data['status'] = 1;
            $data['readnow'] = 0;
            $data['chapter'] = '';
            $data['prefer'] = '';
            $data['addtime'] = date('Y-m-d H:i:s',time());
            $data['time'] = date('Y-m-d H:i:s',time());
            for($i=0;$i<count($novel);$i++){
                $data['novelid'] = $novel[$i];
                $res = DB::table('bookshelf')->where('status',1)->where('user_id',$uuid)->where('novelid',$novel[$i])->first();
                if(empty($res)){
                    BaseModel::factory('bookshelf')->insert([$data]);       //入库bookshelf
                }

            }
            //更新小说加入书架的次数
            $NvNovelModel->updateShelfNum($novel);
        }else{
            $data['status']=0;
            DB::table('bookshelf')->where('user_id',$uuid)->whereIn('novelid',$novel)->update($data);

        }
        return json_encode(
            [
                'status' =>1,
                'message'  =>'成功',

            ]
        );

    }

    /**
     * 用户在选择偏好的时候设置用户的书架内容
     * @param Request $request
     */

    public function postSetUserShelf(Request $request){
        $type = $request->input('type', 1);//用户初始是选择了跳过：1；或者是选择了某些特定的书籍：2
        $cateArr = $request->input('cate', "");
        $uuid = $request->input('uuid', "");
        $sex = $request->input('sex', 3);//性别；1：男；2：女；3：未知
        //查询该用户在书架里有多少书
        $ret = DB::table('bookshelf')->where(['user_id'=>$uuid,'status'=>1])->get();
        $num = DB::table('bookshelf')->where(['user_id'=>$uuid,'status'=>1])->count();

        $NvNovelModel = new NvNovelModel();
        if(!empty($ret)){
            foreach ($ret as $k=>$value){
                $novelidarr[$k] = $value->novelid;
            }
            $booknum = 5-$num;
        }else{
            $booknum=5;
            $novelidarr=[];
        }
        if($booknum >0){
            if($type == 2){
                if(empty($cateArr)){
                    return json_encode(['status'=>0,'message'=>'未选择偏好书籍']);
                }else{//根据用户选择的种类，推荐所有种类下阅读量最高的五本书
                    $return = DB::table('newcate')->whereIn('id',$cateArr)->where('status',1)->get();
                    $cataArray = array();
                    foreach ($return as $k=>$val){
                        $cata = $val->cate;
                        $cataArr = explode(',',$cata);
                        $cataArray = array_merge($cataArray,$cataArr);
                    }
                    $cataArray = array_unique($cataArray);

                    if(!empty($novelidarr)){
                        $novel = $NvNovelModel->getNovelWithoutSome($cataArray,1,$booknum,$novelidarr);

//                        $novel = DB::table('novel')->where('show',1)->whereIn('cateid',$cateArr)->whereNotIn('novelid',$novelidarr)->orderBy('read','desc')->take($booknum)->get();
                    }else{
                        $novel = $NvNovelModel->getNovel($cataArray,1,$booknum);
//                        $novel = DB::table('novel')->where('show',1)->whereIn('cateid',$cateArr)->orderBy('read','desc')->take($booknum)->get();
                    }

                }
                $data['prefer']=json_encode($cataArray);

            }elseif($type ==1 ){//给用户随机推选五本阅读量最高的书籍
                if(!empty($novelidarr)){
//                    $novel = DB::table('novel')->where('show',1)->whereIn('cateid',$cateArr)->whereNotIn('novelid',$novelidarr)->orderBy('read','desc')->take($booknum)->get();
                    $novel = $NvNovelModel->getNovelWithoutSomeNocate(1,$booknum,$novelidarr);

                }else{
                    $novel = $NvNovelModel->getNovelNocate(1,$booknum);
//                    $novel = DB::table('novel')->where('show',1)->orderBy('read','desc')->take($booknum)->get();
                }

                $data['prefer']=0;
            }

            $data['user_id'] = $uuid;
            $data['status'] = 1;
            $data['readnow'] = 0;
            $data['chapter'] = '';
            $data['addtime'] = date('Y-m-d H:i:s',time());
            foreach ($novel as $k=>$value){

                if(!empty($value->novelid)){
                    $data['novelid'] = $value->novelid;
                }


                BaseModel::factory('bookshelf')->insert([$data]);       //入库bookshelf
            }
        }

        if($type == 2 && !empty($cataArray)){
            $update = array(
                'prefer' =>json_encode($cataArray)
            );
            DB::table('bookshelf')->where('user_id',$uuid)->update($update);
        }


        $info = DB::table('novel_user')->where('id',$uuid)->first();
        if(empty($info->sex)){
            $mess = array(
                'sex'=>$sex
            );
            DB::table('novel_user')->where('id',$uuid)->update($mess);
        }

        return json_encode(['status'=>1,'message'=>'成功']);

    }

    /**
     * 搜索页面初始信息展示
     */
    public function postSearchPage_old(Request $request){
        $uuid = $request->input('uuid', "");
        //查询搜索最高的十本小说

        $novel = DB::table('novel')->select('novelid','name')->orderBy('search','desc')->take(10)->get();
        //猜你喜欢 十本
        $ret = DB::table('bookshelf')->where('user_id',$uuid)->where('status',1)->first();

        $data['topsearch'] = $novel;
        $perfer='';
        if(!empty($ret)){
            $perfer = $ret->prefer;
        }

        if(empty($perfer)){
//            $guess = DB::table('novel')->where('show',1)->orderBy('read','desc')->take(10)->get();
            $novelquery = "select novel.novelid,novel.img,novel.read,novel.name as novelname,novel.summary,novel.author,category.name as catename from novel left join category on novel.cateid=category.cateid order by novel.read desc limit 10";
            $guess = DB::select($novelquery);
//            $guess[$i]=$novelres;
        }else{
            $perferArr = json_decode($perfer,1);
//            $guess = DB::table('novel')->where('show',1)->whereIn('cateid',$perferArr)->orderBy('read','desc')->take(5)->get();
            for($i=0;$i<count($perferArr);$i++){
                $novelquery = "select novel.novelid,novel.img,novel.read,novel.name as novelname,novel.summary,novel.author,category.name as catename from novel left join category on novel.cateid=category.cateid where novelid={$perferArr[$i]} order by novel.read desc limit 10";
                $novelres = DB::selectone($novelquery);
                $guess[$i]=$novelres;
            }



        }
        $data['guess']= $guess;
        return json_encode(
            [
                'status' =>1,
                'message'  =>'成功',
                'data' => $data
            ]
        );
    }



    public function postSearchPage(Request $request){
        $uuid = $request->input('uuid', "");
        //查询小说总点击数 最高的十本小说
        $NvNovelModel = new NvNovelModel();
        $novel = $NvNovelModel->getTopClickNovel();
        $data['topsearch'] = $novel;

        //猜你喜欢 十本
        $ret = DB::table('bookshelf')->where('user_id',$uuid)->where('status',1)->first();
        $perfer='';
        if(!empty($ret)){
            $perfer = $ret->prefer;
        }
        if(empty($perfer)){

            $guess = $NvNovelModel->getTenNovel();
        }else{
            $perferArr = json_decode($perfer,1);

            $guess = $NvNovelModel->getGuessNovel($perferArr);
        }
        $data['guess']= $guess;

        return json_encode(
            [
                'status' =>1,
                'message'  =>'成功',
                'data' => $data
            ]
        );
    }

    /**
     * 获取小说封面信息
     * @param Request $request
     * @return false|string
     *
     */
    public function postGetNovelPageById(Request $request)
    {

        $novelid = $request->input('novelid', 1);
        $uuid = $request->input('uuid', 1);
        $NvNovelModel = new NvNovelModel();
        $ChapterModel = new ChapterModel();


//        $downquery = "select count(*) as num from user_behavior_record where novelid={$novelid} and type=1 and kind=1";
//        $downnum = DB::selectone($downquery);//小说的下载次数
//        $shelfquery = "select count(*) as num from bookshelf where novelid={$novelid} and status=1";
//        $shelfnum = DB::selectone($shelfquery);//小说的加入书架的次数


        $join = DB::table('bookshelf')->where('user_id',$uuid)->where('novelid',$novelid)->where('status',1)->first();
        $isjoin = 0;
        if(!empty($join)){
            $isjoin = 1;
        }

        $novel = $NvNovelModel->getNovelById($novelid);
        $chapter = $ChapterModel->getLastChapter($novelid);
        $novel['chapter_title']=$chapter['title'];
        $cateid=0;
        if($novel->status ==1){//只统计连载中的小说
            $NovelClickRecordModel = new NovelClickRecordModel();
            $NovelClickRecordModel->recordMsg($novelid,$novel->name);
        }
        if(!empty($novel)){
            $cateid = $novel->cateid;
            $temp=0;
            if($novel->read >10000){
                $temp = (intval($novel->read/1000)/10)."W+";
            }
            if($novel->read >10000000){
                $temp = (intval($novel->read/1000000)/10)."KW+";
            }
            $novel->read = $temp;
            if ($novel->status == 1) {
                $novel->status = 0;
            } else {
                $novel->status = 1;
            }
        }

        $res = $NvNovelModel->getNovelByCate([$cateid]);
        $iscollect =0;
        $res1 = DB::table('user_behavior_record')->where('uuid',$uuid)->where('novelid',$novelid)->where('status',1)->where('kind',1)->where('type',2)->first();
        if(!empty($res1)){
            $iscollect =1;
        }

        foreach ($res as $key=>$value){
            if ($value->progress_status == 1) {
                $res[$key]->end = '连载中';
            } else {
                $res[$key]->end = '已完本';
            }

        }
        if($novel->down_nums >10000){
            $novel->down_nums = (intval($novel->down_nums/1000)/10)."W+";
        }
        if($novel->shelf_nums >10000){
            $novel->shelf_nums = (intval($novel->shelf_nums/1000)/10)."W+";
        }
        if(empty($novel->down_nums)){
            $novel->down_nums=0;
        }
        if(empty($novel->shelf_nums)){
            $novel->shelf_nums=0;
        }
        $data['downnum'] = $novel->down_nums;
        $data['shelfnum'] = $novel->shelf_nums;
        $data['novel'] = $novel;
        $data['guess'] = $res;
        $data['isjoin'] = $isjoin;
        $data['iscollect'] = $iscollect;


        return json_encode(['status' => 1, 'message' => '成功', 'data' => $data]);
    }

    public function postRecordNovelClick(Request $request){//给数据里的书点击使用
        $novelid = $request->input('novelid', 1);
        $NvNovelModel = new NvNovelModel();
        $novel = $NvNovelModel->getNovelById($novelid);
        if($novel->status ==1){//只统计连载中的小说
            $NovelClickRecordModel = new NovelClickRecordModel();
            $NovelClickRecordModel->recordMsg($novelid,$novel->name);
        }
        return json_encode(['status' => 1, 'message' => '成功']);
    }

    /**
     * 获取短文分类
     * @return false|string
     */
    public function postGetEssayList(){
        $shortessay = DB::table('shortessay')->where('status',1)->orderBy('id','asc')->get();
        return json_encode(
            [
                'status' =>1,
                'message' => '成功',
                'data' => $shortessay
            ]
        );
    }

    /**
     * 获取短文分类内容
     * @param Request $request
     * @return false|string
     */
    public function postGetEssayContent(Request $request){

        $eid = $request->input('eid', 1);
        $uuid = $request->input('uuid', 1);
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);
        $essaycontent = DB::table('essaycontent')->where('status',1)->where('eid',$eid)->orderBy('sort','asc')->skip(($page-1) * $size)
            ->take($size)->get();
        $essay = DB::table('shortessay')->where('id',$eid)->first();
        $data['essay'] =$essay;
        if(!empty($essaycontent)){
            foreach ($essaycontent as $k=>$value){
                $ret = DB::table('user_read_record')->where('novelid',$value->id)->where('kind',2)->where('status',1)->count();
                $essaycontent[$k]->readman = $ret;
                $return = DB::table('user_behavior_record')->where('novelid',$value->id)->where('kind',2)->where('type',2)->where('uuid',$uuid)->where('status',1)->first();
                if(!empty($return)){
                    $iscollection=1;
                }else{
                    $iscollection=0;
                }
                $essaycontent[$k]->iscollection = $iscollection;
            }


        }

        $data['content'] = $essaycontent;
        return json_encode(
            [
                'status' =>1,
                'message' => '成功',
                'data' => $data
            ]
        );

    }


    /**
     * 用户缓存，收藏时调用的接口
     * @param Request $request
     * @return false|string
     *
     */
    public function postSaveUserBehavior(Request $request){
        $uuid= $request->input('uuid', 1);
        $novelid= $request->input('novelid', 1);
        $kind = $request->input('kind', 1);//1:小说；2：短文
        $name = $request->input('name', '');
        $type= $request->input('type', 1);//1：下载；2：收藏；
        $data = array(
            'uuid' => $uuid,
            'novelid' => $novelid,
            'type' => $type,
            'kind' => $kind,
            'name' => $name,
            'status' => 1,
            'addtime' => date('Y-m-d H:i:s',time()),
        );
        $res = BaseModel::factory('user_behavior_record')->insert([$data]);
        if($kind == 1 && $type ==1 ){
            $NvNovelModel = new NvNovelModel();
            $NvNovelModel->updateDownNum($novelid);
        }
        if(!empty($res)){
            return json_encode(['status'=>1,'message'=>'成功']);
        }else{
            return json_encode(['status'=>0,'message'=>'失败']);
        }
    }



    /**
     * 用户登录
     */
    public function postUserLogin(Request $request){
        $phone = $request->input('phone', '');
        $pass = $request->input('pass', '');
        $imei = $request->input('imei', '');
        $judgepass = md5('abc'.$pass);
        $res = DB::table('novel_user')->where('phone',$phone)->where('password',$judgepass)->first();
        $datas['logintime']=time();
        if(!empty($res)){
            if(empty($res->imei)){
                $datas['imei']=$imei;
            }
            $uid = $res->id;
            if(!empty($res->logintime)){
                if(date('Ymd',$res->logintime) != date('Ymd',time())){
                    self::addReadTimes($uid);
                }
            }else{
                self::addReadTimes($uid);
            }
            $token = $this->token($uid);
            $data['uuid']=intval($uid);
            $data['token']=$token;
            $data['isvisit']=2;

            DB::table('novel_user')->where('phone',$phone)->update($datas);

            return json_encode(['status'=>1,'message'=>'成功','data'=>$data]);
        }else{
            return json_encode(['status'=>0,'message'=>'账号或密码错误']);
        }
    }

    /**
     * 忘记密码,修改密码
     */
    public function postForgetPass(Request $request){
        $phone = $request->input('phone', '');
        $pass = $request->input('pass', '');
        $forpass = md5('abc'.$pass);
        $data = array(
            'password' => $forpass
        );
        DB::table('novel_user')->where('phone',$phone)->update($data);
        return json_encode(['status'=>1,'message'=>'成功']);
    }

    /**
     * 注册,注册结束之后需要调用给书架添加数据的接口postSetUserShelf
     */
    public function postRegister(Request $request){
        $phone = $request->input('phone', '');
        $pass = $request->input('pass', '');
        $channel = $request->input('channel', '');
        $imei = $request->input('imei', '');
        $userfrom = $request->input('userfrom', 1);

        $insertpass = md5('abc'.$pass);
        $res = DB::table('novel_user')->where('phone',$phone)->get();
        if(!empty($res)){
            return json_encode(['status'=>0,'message'=>'该手机号已注册，请直接登录']);
        }else{
            $mes = DB::table('novel_user')->where('imei',$imei)->first();
            if(!empty($mes)){
                if(empty($mes->phone)){
                    $update = array(
                        'password' =>  "$insertpass",
                        'phone' =>"$phone",
                        'userfrom'=>$userfrom,
                    );
                    DB::table('novel_user')->where('id',$mes->id)->update($update);
                    $uid = $mes->id;
                }else{
                    return json_encode(['status'=>-3,'message'=>'该设备已经注册过['.$mes->phone.']，请直接登录']);
                }
            }else{
                $spreadcode = $this->createSpreadCode();
                $ret = DB::table('novel_user')->where('spreadcode',$spreadcode)->first();
                if(!empty($ret)){
                    $spreadcode = $this->createSpreadCode();

                    $data = array(
                        'regtime' => date('Y-m-d H:i:s',time()),
                        'password' =>  "$insertpass",
                        'phone' =>"$phone",
                        'imei' =>"$imei",
                        'userfrom'=>$userfrom,
                        'spreadcode'=>$spreadcode,
                        'channel'=>$channel
                    );
                    $uid = DB::table('novel_user')->insertGetId($data);
                }else{
                    $data = array(
                        'regtime' => date('Y-m-d H:i:s',time()),
                        'password' =>  "$insertpass",
                        'phone' =>"$phone",
                        'imei' =>"$imei",
                        'userfrom'=>$userfrom,
                        'spreadcode'=>$spreadcode,
                        'channel'=>$channel
                    );
                    $uid = DB::table('novel_user')->insertGetId($data);
                }


            }


            if(!empty($uid)){
                $token = $this->token($uid);
                return json_encode(['status' => 1, 'message' => '成功', 'data' => ['token' => $token, 'uuid' => $uid,'isvisit'=>2]]);
            }else{
                return json_encode(['status'=>0,'message'=>'注册失败，请重试']);
            }
        }
    }


    public function postVisitRegister(Request $request){
        $imei = $request->input('imei', '');
        $channel = $request->input('channel', '');
        $from = $request->input('from', 1);//1:app;2:web
        $res = DB::table('novel_user')->where('imei',$imei)->first();
        if(!empty($res)){
            if(!empty($res->phone)){
                $isvisit = 2;
            }else{
                $isvisit = 1;
            }
            $uid = $res->id;
            $hasExist = 1;

            if(!empty($res->logintime)){
                if(date('Ymd',$res->logintime) != date('Ymd',time())){
                    self::addReadTimes($uid);
                }
            }else{
                self::addReadTimes($uid);
            }

            $time = array(
                'logintime' =>  time()
            );
            DB::table('novel_user')->where('imei',$imei)->update($time);

        }else{
            $isvisit = 1;
            $spreadcode = $this->createSpreadCode();
            $data = array(
                'regtime' => date('Y-m-d H:i:s',time()),
                'imei' =>"$imei",
                'sex'=>3,
                'spreadcode'=>$spreadcode,
                'from'=>$from,
                'channel'=>$channel,
                'logintime'=>time()
            );
            $uid = DB::table('novel_user')->insertGetId($data);
            $hasExist = 0;
            self::addReadTimes($uid);
        }
        $token = $this->token(intval($uid));

        return json_encode(['status' => 1, 'message' => '成功', 'data' => ['token' => $token, 'uuid' => $uid,'isvisit'=>$isvisit,'hasExist'=>$hasExist]]);

    }


    /**
     * 首次登录加阅读次数
     * @param $uid
     */
    public static function addReadTimes($uid){

        $data = DB::table('read_times_set')->where('type',1)->first();
        $times = $data->times;
        $insert = array(
            'uuid' => $uid,
            'addtime' => time(),
            'frequency' => $times,
            'type' => 1,
            'kind' => 1,
        );
        DB::table('essay_read_times')->insert($insert);
    }

    /**
     * 判断手机码是否正确
     */
    public function postJudgeCode(Request $request){
        $phone = $request->input('phone', '');
        $code = $request->input('code', '');
        $type = $request->input('type', '');
        $res = DB::table('sendcode_record')->where('phone',$phone)->where('type',$type)->orderBy('addtime','desc')->first();
        if(!empty($res) && $code == $res->code){
            return json_encode(['status'=>1,'message'=>'成功']);
        }else{
            return json_encode(['status'=>0,'message'=>'验证码错误']);
        }
    }


    public function postGetGeetest(){
        $GtSdk = new GeetestLib('6c5a4e95dd4d9bb7400062f3d5109a17','977cd515e10c583015ab82095067f564');
        $data = array(
            "user_id" => "test", # 网站用户id
            "client_type" => "h5", #web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
            "ip_address" => "127.0.0.1" # 请在此处传输用户请求验证时所携带的IP
        );
        $status = $GtSdk->pre_process($data, 1);
        $data =  $GtSdk->get_response_str();
        return json_encode(['status'=>1,'data'=>json_decode($data,1),'code'=>$status]);
    }




    /**
     * 发送验证码
     * @param Request $request
     */
    public function postSendCode(Request $request){
        $phone = $request->input('phone', '');
        $type = $request->input('type', '');

        $time = time();
        $sig = md5('sc%7*g'.$time.'@!$%');
        $code = rand(1000,9999);

        $geetest_challenge = $request->input('geetest_challenge', '');
        $geetest_validate = $request->input('geetest_validate', '');
        $geetest_seccode = $request->input('geetest_seccode', '');
        $code1 = $request->input('code', '');
        $GtSdk = new GeetestLib('6c5a4e95dd4d9bb7400062f3d5109a17','977cd515e10c583015ab82095067f564');
        $data = array(
            "user_id" => 'test', # 网站用户id
            "client_type" => "h5", #web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
            "ip_address" => "127.0.0.1" # 请在此处传输用户请求验证时所携带的IP
        );

        if($type ==1){
            if ($code1 == 1) {   //服务器正常
                $result = $GtSdk->success_validate($geetest_challenge, $geetest_validate, $geetest_seccode, $data);
                if (empty($result)) {
                    return json_encode(['status'=>0,'message'=>'图形码验证失败']);
                }
            }else{  //服务器宕机,走failback模式
                $result = $GtSdk->fail_validate($geetest_challenge, $geetest_validate, $geetest_seccode);
                if (empty($result)) {
                    return json_encode(['status'=>0,'message'=>'图形码验证失败']);
                }
            }
        }
        if($type ==3){
            $userInfo = DB::table('novel_user')->where('phone',$phone)->first();
            if(empty($userInfo)){
                return json_encode(['status'=>0,'message'=>'该手机号还未注册']);
            }
        }
        $post_data = array(
            'mobile' => $phone,
            'accesskey' => '7ORyudRBfRgoQzKm',
            'secret' => 'SiSXdDi7pFWthTYOfFfMwcMVpGQzuEp5',
            'sign' => '27551',
            'templateId' => '37031',
            'content' => $code,
        );
        $data = DB::table('message')->where('status',1)->first();
        if(!empty($data)){

            $domain = $data->domain;

            //查询用户发送的验证码信息，10分钟之内只能发送三次，24小时只能发送5次
            $wheretime = time()-600;
            $wheretime24 = time()-86400;
            $res = DB::table('sendcode_record')->where('phone',$phone)->where('type',$type)->where('addtime','>',$wheretime)->get();
            if(count($res) <3){
                //判断24小时是否大于5次
                $res24 = DB::table('sendcode_record')->where('phone',$phone)->where('type',$type)->where('addtime','>',$wheretime24)->get();
                if(count($res24) <5){//可以发送
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $domain);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                    // 设置请求为post类型
                    curl_setopt($ch, CURLOPT_POST, 1);
                    // 添加post数据到请求中
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

                    // 执行post请求，获得回复
                    $response= curl_exec($ch);
                    curl_close($ch);
                    if(!empty($response)){
                        $mess=array(
                            'phone' =>$phone,
                            'code' =>$code,
                            'type'=>$type,
                            'addtime' =>time(),
                        );
                        DB::table('sendcode_record')->insert($mess);
                        return json_encode(['status'=>1,'message'=>'成功','data'=>$response]);
                    }else{
                        return json_encode(['status'=>0,'message'=>'curl失败','data'=>$response]);
                    }




                }else{//不能发送
                    return json_encode(['status'=>0,'message'=>'发送次数过多']);
                }
            }else{
                return json_encode(['status'=>0,'message'=>'发送次数过多']);
            }



            $response='';



//            $url = $data->domain;
//            $ch = curl_init();
//            curl_setopt($ch, CURLOPT_URL, $url);
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//
//            // 设置请求为post类型
//            curl_setopt($ch, CURLOPT_POST, 1);
//            // 添加post数据到请求中
//            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
//
//            // 执行post请求，获得回复
//            $response= curl_exec($ch);
//            curl_close($ch);
//            if(!empty($response)){
//                return json_encode(['status'=>1,'message'=>'成功','data'=>$response]);
//            }else{
//                return json_encode(['status'=>0,'message'=>'curl失败','data'=>$response]);
//            }
            return json_encode(['status'=>1,'message'=>'成功','data'=>$response,'code'=>$code]);
        }else{
            return json_encode(['status'=>0,'message'=>'未配置有效服务商']);
        }


    }




    /**
     * 编辑用户头像，性别，昵称等信息
     * @param Request $request
     * @return false|string
     */
    public function postEditUserInfo(Request $request){
        $uuid = $request->input('uuid', '');
        $type = $request->input('type', '');//1：修改头像；2：修改昵称；3：修改性别；4：更改手机号
        $content = $request->input('content', '');

        if($type == 1){
            $data['avatar'] = $content;
        }elseif($type ==2){
            $data['nickname'] = $content;
        }elseif($type==3){
            $data['sex'] = $content;
        }elseif($type==4){
            $res = DB::table('novel_user')->where('phone',$content)->first();
            if(!empty($res)){
                return json_encode(['status'=>0,'message'=>'该手机号已绑定其他用户']);
            }
            $data['phone'] = $content;
        }

        DB::table('novel_user')->where('id',$uuid)->update($data);

//        DB::selectone($sql);

        return json_encode(['status'=>1,'message'=>'成功']);

    }

    /**
     * 记录用户每天的阅读时间及读到的章节
     * @param Request $request
     * @return false|string
     */
    public function postRecordReadTime(Request $request){
        $uuid = $request->input('uuid', 1);
        $novelid = $request->input('novelid', '');
        $name = $request->input('name', '');
        $kind = $request->input('kind', '');//1:小说；2：短文
        $long = $request->input('long', '');
        $chapter = $request->input('chapter', '');

        $res = DB::table('user_read_record')->where('uuid',$uuid)->where('novelid',$novelid)->where('kind',$kind)->first();

        if(!empty($res)){

            $long1 = $res->long;
            $time = $long1+$long;
            $update = array(

                'chapter' => $chapter,
                'name' => $name,
                'status' => 1,
                'long' => $time,
                'addtime'=>time()
            );
            BaseModel::factory('user_read_record')->where('kind',$kind)->where('uuid',$uuid)->where('novelid',$novelid)->update($update);

        }else{
            $data = array(
                'uuid' => $uuid,
                'novelid' => $novelid,
                'name' => $name,
                'long' => $long,
                'kind' => $kind,
                'chapter' => $chapter,
                'addtime'=>time(),
                'status'=>1
            );
            $res = BaseModel::factory('user_read_record')->insert([$data]);
        }

        if($kind == 1){
            $NvNovelModel = new NvNovelModel();
            //更新小说的阅读次数
            $res = $NvNovelModel->updateRead($novelid);
            $updatedata = array(
                'time' => date('Y-m-d H:i:s',time())
            );
            DB::table('bookshelf')->where('novelid',$novelid)->where('user_id',$uuid)->update($updatedata);
        }else{//更新短文
            $ret = DB::table('essaycontent')->where('id',$novelid)->first();
            $read = $ret->read;
            if(empty($read)){
                $read = 0;
            }
            $data['read'] = $read+1;
            $res = DB::table('essaycontent')->where('id',$novelid)->update($data);
        }


        if(!empty($res)){
            return json_encode(['status'=>1,'message'=>'成功']);
        }else{
            return json_encode(['status'=>0,'message'=>'失败']);
        }
    }


    /**
     * 获取我的收藏下载列表
     * @param Request $request
     */
    public function postMyCollectionList(Request $request){
        $uuid= $request->input('uuid', 1);
        $kind = $request->input('kind', 1);//1:小说；2：短文
        $type = $request->input('type', 1);//1：下载缓存；2：收藏
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);
        $offset = ($page-1)*$size;
        $data=[];
        $NvNovelModel = new NvNovelModel();
        if($type == 1){
            if($kind == 1){
                $novelquery = "select novelid,addtime from  user_behavior_record where uuid={$uuid} and type=1 and kind=1 and status=1 order by addtime desc limit $offset,$size";
                $datas = DB::select($novelquery);
                if(!empty($datas)){
//                    foreach ($data as $k=>$value){
////                        $query = "select novel.name,novel.img,author,summary,category.name as catename from novel LEFT JOIN category on novel.cateid=category.cateid where novelid={$value->novelid} ";
////                        $name = DB::selectone($query);
////                        $data[$k]->name = $name->name;
////                        $data[$k]->img = $name->img;
////                        $data[$k]->author = $name->author;
////                        $data[$k]->summary = $name->summary;
////                        $data[$k]->catename = $name->catename;
//
//                    }
                    foreach ($datas as $k=>$v){
                        $novelid[$k] = $v->novelid;

                    }

                    $data = $NvNovelModel->getNovelIdArrayNosize($novelid);
                }
            }
        }else{
            if($kind == 1){
                $novelquery = "select novelid,addtime from  user_behavior_record where uuid={$uuid} and type=2 and kind=1 and status=1 order by addtime desc limit $offset,$size";
                $datas = DB::select($novelquery);
                if(!empty($datas)){
//                    foreach ($data as $k=>$value){
//                        $query = "select novel.name,novel.img,author,summary,category.name as catename from novel LEFT JOIN category on novel.cateid=category.cateid where novelid={$value->novelid} ";
//                        $name = DB::selectone($query);
//                        $data[$k]->name = $name->name;
//                        $data[$k]->img = $name->img;
//                        $data[$k]->author = $name->author;
//                        $data[$k]->summary = $name->summary;
//                        $data[$k]->catename = $name->catename;
//                        $data[$k]->addtime=date("Y-m-d",strtotime($value->addtime));
//                    }
                    $data = [];
                    foreach ($datas as $k=>$v){
                        $novelid[$k] = $v->novelid;
                        $name = $NvNovelModel->getCollection($v->novelid);
                        $data[$k]['novelid'] = $v->novelid;
                        $data[$k]['name'] = $name->name;
                        $data[$k]['img'] = $name->img;
                        $data[$k]['author'] = $name->author;
                        $data[$k]['summary'] = $name->summary;
                        $data[$k]['catename'] = $name->catename;
                        $data[$k]['addtime']=date("Y-m-d",strtotime($v->addtime));

                    }
//                    return json_encode(['status'=>1,'message'=>'成功','data'=>$name]);
//                    $data = $NvNovelModel->getNovelIdArrayNosize($novelid);

                }


            }else{
                $novelquery = "select novelid,addtime from  user_behavior_record where uuid={$uuid} and type=2 and kind=2 and status=1 order by addtime desc limit $offset,$size";
                $data = DB::select($novelquery);
                if(!empty($data)){
                    foreach ($data as $k=>$v){
                        $query = "select shortessay.name as catename,essaycontent.name,essaycontent.content from essaycontent left join shortessay on essaycontent.eid=shortessay.id  where essaycontent.id={$v->novelid} ";
                        $name = DB::selectone($query);
                        $data[$k]->name=$name->name;
                        $data[$k]->content=$name->content;
                        $data[$k]->addtime=date("Y-m-d",strtotime($v->addtime));
                        $data[$k]->catename=$name->catename;
                    }
                }

            }
        }
        return json_encode(['status'=>1,'message'=>'成功','data'=>$data]);
    }

    /**
     * 批量删除我的收藏/下载操作
     * @param Request $request
     */
    public function postDelMyCollection(Request $request){
        $uuid= $request->input('uuid', 1);
        $novelArr= $request->input('novelArr', 1);
        $kind = $request->input('kind', 1);//1:小说；2：短文
        $type = $request->input('type', 1);//1：下载缓存；2：收藏
        $data=array(
            'status'=>2
        );
//        $novel = implode(",",$novelArr);
        DB::table('user_behavior_record')->where('uuid',$uuid)->where('kind',$kind)->where('type',$type)->whereIn('novelid',$novelArr)->update($data);
        return json_encode(['status'=>1,'message'=>'成功']);
    }


    /**
     * 获取我的阅读记录
     * @param Request $request
     */
    public function postMyReadList(Request $request){
        $uuid= $request->input('uuid', 1);
        $page= $request->input('page', 1);
        $size= $request->input('size', 10);
        $kind = $request->input('kind', '');//1:小说；2：短文
        $NvNovelModel = new NvNovelModel();
        if($kind == 1){
            $ret = DB::table('user_read_record')->select('addtime','novelid')->where('kind',1)->where('status',1)->where('uuid',$uuid)->orderBy('addtime','desc')->paginate($size)->toArray();
            $res = $ret['data'];
            if(!empty($res)){
                foreach ($res as $k=>$v){
                    //判断是否加入书架
                    $return = DB::table('bookshelf')->where('novelid',$v->novelid)->where('user_id',$uuid)->where('status',1)->orderBy('addtime','desc')->first();
                    if(!empty($return)){
                        $res[$k]->joinshelf=1;
                    }else{
                        $res[$k]->joinshelf=0;
                    }
                    //获取小说的信息
//                    $query = "select novel.name,novel.img,author,category.name as catename from novel LEFT JOIN category on novel.cateid=category.cateid where novelid={$v->novelid} ";
//                    $name = DB::selectone($query);
//                    $res[$k]->name = $name->name;
//                    $res[$k]->img = $name->img;
//                    $res[$k]->author = $name->author;
//                    $res[$k]->catename = $name->catename;
//                    $res[$k]->addtime = date("Y-m-d",$v->addtime);

                    $name = $NvNovelModel->getNovelById($v->novelid);
                    if(!empty($name)){
                        $res[$k]->name = $name->name;
                        $res[$k]->img = $name->img;
                        $res[$k]->author = $name->author;
                        $res[$k]->catename = $name->catename;
                        $res[$k]->addtime = date("Y-m-d",$v->addtime);
                        $res[$k]->progress_status = $name->status;
                    }

                }



            }

        }else{
            $ret = DB::table('user_read_record')->select('addtime','novelid')->where('kind',2)->where('status',1)->where('uuid',$uuid)->orderBy('addtime','desc')->paginate($size)->toArray();
            $res = $ret['data'];
            if(!empty($res)){
                foreach ($res as $k=>$v){
//                    $name = DB::table('essaycontent')->where('id',$v->novelid)->first();
                    $query = "select essaycontent.name as name,essaycontent.content,shortessay.name as sname from essaycontent left join shortessay on essaycontent.eid=shortessay.id where essaycontent.id={$v->novelid}";
                    $name = DB::selectone($query);
                    $res[$k]->name = $name->name;
                    $res[$k]->content = $name->content;
                    $res[$k]->type = $name->sname;
                    $res[$k]->addtime = date("Y-m-d",$v->addtime);
                }
            }
        }
        return json_encode(['status'=>1,'message'=>'成功','data'=>$res]);
    }

    /**
     * 清空用户阅读记录
     * @param Request $request
     */
    public function postDelMyRead(Request $request){
        $uuid= $request->input('uuid', 1);
        $kind = $request->input('kind', '');//1:小说；2：短文
        $data=array(
            'status'=>2
        );

        DB::table('user_read_record')->where('uuid',$uuid)->where('kind',$kind)->update($data);
        return json_encode(['status'=>1,'message'=>'成功']);

    }



    /**
     * 用户在打开app或者打开小说首页的时候调用  用于统计pv，uv
     * @param Request $request
     * @return false|string
     */
    public function postRecordUserAction(Request $request){
        $uuid = $request->input('uuid', 1);//设备号
        $novelid = $request->input('novelid', 0);
        $name = $request->input('name', '');
        $channel = $request->input('channel', '');//渠道
        $module = $request->input('module', 0);//模块名；用于统计pv
        $type = $request->input('type', '');//1:android;2:ios
        if(empty($novelid)){
            $users = DB::table('statistics')->where('uid',$uuid)->get();
            if(!empty($users)){
                $data['firstvisit'] = 0;
            }else{
                $data['firstvisit'] = 2;
            }
        }else{//打开小说首页时
            $users = DB::table('statistics')->where('uid',$uuid)->where('novelid','>',0)->get();
            if(!empty($users)){
                $data['firstvisit'] = 0;
            }else{
                $data['firstvisit'] = 1;
            }
        }
        $data['novelid'] = $novelid;
        $data['name'] = $name;
        $data['uid'] = $uuid;
        $data['type'] = $type;
        $data['module'] = $module;
        $data['channel'] = $channel;
        $data['timestamp'] = time();
        $data['addtime'] = date('Y-m-d',time());

        BaseModel::factory('statistics')->insert([$data]);
        return json_encode(['status'=>1,'message' => '成功']);
    }


    /**
     * 增加短文阅读次数
     * @param Request $request
     * @return false|string
     */
    public function postAddRead(Request $request){
        $id = $request->input('id', 1);
        $query = "update essaycontent set `read`=`read`+1 where id = {$id}";
        $res = DB::selectone($query);
        return json_encode(['status'=>1,'message'=>'成功']);
    }


    /**
     * APP端一键反馈
     */
    public function postRecordSuggestion(Request $request){
        $type = $request->input('type', 1);//反馈类型；1：阅读功能；2：产品建议；3：书籍内容；4：其他
        $content = $request->input('content', '');
        $img = $request->input('img', '');
        $uuid = $request->input('uuid', '');
        $uname = $request->input('uname', '');
        $data = array(
            'uuid' => $uuid,
            'addtime' =>date('Y-m-d H:i:s',time()),
            'content' => $content,
            'type' => $type,
            'uname' => $uname,
        );

        $sid = DB::table('suggestion')->insertGetId($data);
        if(!empty($img)){
            for($i=0;$i<count($img);$i++){
                $insert = array(
                    'img' => $img[$i],
                    'sid' => $sid,
                    'addtime' =>date('Y-m-d H:i:s',time())
                );
                DB::table('suggestion_img')->insert($insert);
            }
        }

        return json_encode(['status'=>1,'message'=>'成功']);
    }


    /**
     * 获取我的公告列表
     * @param Request $request
     */
    public function postGetAnnounce(Request $request){
        $uuid = $request->input('uuid', 1);
        $announce = DB::table('announce')->where('pushstatus',1)->where('status',1)->orderBy('pushtime','desc')->get();
        if(!empty($announce)){
            foreach ($announce as $k=>$value){
                $res = DB::table('announce_read')->where('uuid',$uuid)->where('announceid',$value->id)->get();
                if(!empty($res)){
                    $announce[$k]->read = 1;
                }else{
                    $announce[$k]->read = 0;
                }
                $announce[$k]->pushtime = date('Y-m-d H:i:s',$value->pushtime);
            }
        }
        return json_encode(['status'=>1,'message'=>'成功','data'=>$announce]);



    }

    /**
     * 公告标记为已读
     * @param Request $request
     */
    public function postMarkRead(Request $request){
        $id = $request->input('id', '');
        if(empty($id)){
            return json_encode(['status'=>1,'message'=>'成功']);
        }
        $uuid = $request->input('uuid', 1);
        for($i=0;$i<count($id);$i++){
            $data = array(
                'uuid' => $uuid,
                'announceid' => $id[$i],
                'addtime' =>time()
            );
            $ret = DB::table('announce_read')->where(['uuid'=>$uuid,'announceid'=>$id[$i]])->first();

            if(empty($ret)){
                DB::table('announce_read')->insert($data);
            }

        }

        return json_encode(['status'=>1,'message'=>'成功']);
    }


    /**
     * app反馈下载图片
     * @return false|string
     */
    public function postUploadImg(){
        $request = Request();

        $file = $request->file('imgFile');
        if ($file && $file->isValid()) {


//            require dirname(dirname(dirname(dirname(__FILE__))))."\Qiniu\phpsdk\autoload.php";
//
//            $accessKey = 'zj0Y_E4ppmM3MwES4iuFbYk_DssjnKuyfMQhKJiy';
//            $secretKey = 'aFhsKB5gOWoSyKossrUkxkUeq-GxOP0sCDBstWkd';
//            $auth = new Auth($accessKey, $secretKey);
//            $bucket = 'novel_php';
//            // 生成上传Token
//            $token = $auth->uploadToken($bucket);
//            // 构建 UploadManager 对象
//            $uploadMgr = new UploadManager();
//            $filePath = $file->getRealPath();   //要上传文件的本地路径 //临时文件的绝对路径
//            $key = $file->getClientOriginalName(); // 上传到七牛后保存的文件名 // 文件原名
//
//
//            list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
//            return json_encode(['status'=>1,'message'=>'成功','data'=>$ret,'a'=>$err ]);
//
//
//            // 获取文件相关信息
//            $originalName = $file->getClientOriginalName(); // 文件原名
//            $ext = $file->getClientOriginalExtension();     // 扩展名
//            $realPath = $file->getRealPath();   //临时文件的绝对路径
//            $type = $file->getClientMimeType();     // image/jpeg
//            // 上传文件
//            $filename = time() . uniqid() . '.' . $ext;
//            // 使用我们新建的uploads本地存储空间（目录）
//            file_put_contents('images/' . $filename, file_get_contents($realPath));
//            $data['img'] = url('images/' . $filename);

            $filePath = $file->getRealPath();   //临时文件的绝对路径

            require dirname(dirname(dirname(dirname(__FILE__))))."\Qiniu\phpsdk\autoload.php";
            $accessKey = 'zj0Y_E4ppmM3MwES4iuFbYk_DssjnKuyfMQhKJiy';
            $secretKey = 'aFhsKB5gOWoSyKossrUkxkUeq-GxOP0sCDBstWkd';
            $auth = new Auths($accessKey, $secretKey);
            $bucket = 'novel_php';

            // 生成上传Token

            $expires = 3600;
            $returnBody = '{"key":"$(key)","hash":"$(etag)","fsize":$(fsize),"name":"$(x:name)"}';
            $policy = array(
                'returnBody' => $returnBody
            );
            $token = $auth->uploadToken($bucket, null, $expires, $policy, true);

            // 构建 UploadManager 对象
            $uploadMgr = new UploadManager();

            // 上传到七牛后保存的文件名
            $originalName = $file->getClientOriginalName(); // 文件原名
            $key     = uniqid() . $originalName;
            // 初始化 UploadManager 对象并进行文件的上传。
            $uploadMgr = new UploadManager();
            // 调用 UploadManager 的 putFile 方法进行文件的上传。
            list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
            $data['img'] = "http://novelpic.fjhwsp.com/".$ret['key'];
            return json_encode(['status'=>1,'message'=>'成功','data'=>$data ]);
        }else{
            return json_encode(['status'=>0,'message'=>'失败' ]);
        }
    }


    /**
     * 获取所有分类
     * @return false|string
     */
    public function postGetCate(){
        $data = DB::table('newcate')->select('id as cateid','name')->where('status',1)->orderBy('sort','asc')->get();
        return json_encode(['status'=>1,'message'=>'成功','data'=>$data]);
    }

    /**
     * 首页浮层信息
     * @param Request $request
     * @return false|string
     */
    public function postGetFloorData(Request $request){
        $platform = $request->input('platform', 1);//1:安卓；2：ios
        $channel = $request->input('channel', 1);//渠道
        $data = DB::table('publish')->where('platform',$platform)->where('channel',$channel)->where('status',1)->first();
        if(empty($data)){
            $data = DB::table('publish')->where('platform',$platform)->where('channel',-1)->where('status',1)->first();
            if(empty($data)){
                $data = DB::table('publish')->where('platform',3)->where('channel',$channel)->where('status',1)->first();
                if(empty($data)){
                    $data = DB::table('publish')->where('platform',3)->where('channel',-1)->where('status',1)->first();
                }

            }

        }
        if(empty($data)){
            return json_encode(['status'=>1,'message'=>'浮层信息未配置']);
        }
        return json_encode(['status'=>1,'message'=>'成功','data'=>$data]);
    }


    /**
     * 获取用户的今日阅读时长，及公告未读条数,及用户所有信息
     * @param Request $request
     */
    public function postMyHome(Request $request){
        $uuid = $request->input('uuid', 1);
        //查询全部公告条数
        $all = DB::table('announce')->where('status',1)->where('pushstatus',1)->count();
        //查询已读公告条数
        $read = DB::table('announce_read')->where('uuid',$uuid)->count();
        $data['notread'] = $all-$read;
        if($data['notread']<0){
            $data['notread']=0;
        }

//        $begintime = strtotime(date('Y-m-d',time()));
//        $endtime = strtotime(date('Y-m-d',time()))+24*3600;


        $query = "select sum(`long`) as time from user_read_record where uuid={$uuid}";
        $time = DB::selectone($query);
        if(empty($time->time)){
            $long = 0;
        }else{
            $long = $time ->time;
        }
        $data['readtime'] = $long;
        $userInfo = DB::table('novel_user')->where('id',$uuid)->first();

        if(!empty($userInfo->phone)){
            $isvisit=0;
        }else{
            $isvisit=1;
        }
        if(!empty($userInfo->avatar)){
            $data['avatar'] = $userInfo->avatar;
        }
        if(!empty($userInfo->nickname)){
            $data['nickname'] = $userInfo->nickname;
        }else{
            if(!empty($userInfo->phone)){
                $data['nickname'] = $userInfo->phone;
            }

        }
        if(!empty($userInfo->sex)){
            $data['sex'] = $userInfo->sex;
        }
        if(!empty($userInfo->birthday)){
            $data['birthday'] = $userInfo->birthday;
        }
        if(!empty($userInfo->phone)){
            $data['phone'] = $userInfo->phone;
        }

        if(empty($userInfo->spreadcode)){
            $userInfo['spreadcode'] = '';
        }

        $data['isvisit'] = $isvisit;
        if(!empty($userInfo->spreadcode)){
            $data['spreadcode'] = $userInfo->spreadcode;
        }

        $times = DB::table('essay_read_times')->where('uuid',$uuid)->sum('frequency');
        $data['frequency']=$times;

        $ret1 = DB::table('read_times_set')->where('type',1)->first();
        $data['openapp']=$ret1->times;
        $ret2 = DB::table('read_times_set')->where('type',2)->first();
        $data['shareapp']=$ret2->times;
        $data['shareappmax']=$ret2->extra;
        $ret3 = DB::table('read_times_set')->where('type',3)->first();
        $data['shareuser']=$ret3->times;
        return json_encode(['status'=>1,'message'=>'成功','data'=>$data]);
    }


    public function postGetTimes(Request $request){
        $ret1 = DB::table('read_times_set')->where('type',1)->first();
        $data['openapp']=$ret1->times;
        $ret2 = DB::table('read_times_set')->where('type',2)->first();
        $data['shareapp']=$ret2->times;
        $data['shareappmax']=$ret2->extra;
        $ret3 = DB::table('read_times_set')->where('type',3)->first();
        $data['shareuser']=$ret3->times;
        return json_encode(['status'=>1,'message'=>'成功','data'=>$data]);
    }
    /**
     * 记录用户每次的阅读时长
     * @param Request $request
     */
    public function postNovelReadRecord(Request $request)
    {
        $uuid = $request->input('uuid', '');//用户id
        $imei = $request->input('imei', '');//设备号
        $begin = $request->input('begin', 0);
        $end = $request->input('end', 0);
        $channel = $request->input('channel', 0);
        $type = $request->input('type', 1);//1:android；2：ios
        $time = $end - $begin;

        //代码逻辑
        //只根据设备第一次安装的渠道包 当成新增用户

        //修改 只有注册过的用户才能入库
        $user = DB::table('novel_user')->where('id', $uuid)->first();
        if (!empty($user->phone)) {

            $first = DB::table('user_read_first')->where('uuid', $uuid)->first();//判断是否是新用户
            $new = 2;
            if (empty($first)) {
                $firstData = array(
                    'uuid' => $uuid,
                    'imei' => $imei,
                    'channel' => $channel,
                    'addtime' => time(),
                    'date' => date('Y-m-d',time()),
                    'type' => $type,
                );
                DB::table('user_read_first')->insert($firstData);
                $new = 1;
            } else {
                $nowchannel = $first->channel;
                if ($nowchannel == $channel) {
                    $new = 1;
                }
            }

            $res = DB::table('novel_read_statistics')->where('uuid', $uuid)->where('channel', $channel)->orderBy('lastupdatetime', 'desc')->first();

            if (empty($res)) {
                $statistics = array(
                    'uuid' => $uuid,
                    'imei' => $imei,
                    'day' => 1,
                    'time' => $time,
                    'channel' => $channel,
                    'new' => $new,
                    'lastupdatetime' => time(),
                    'type' => $type
                );
                DB::table('novel_read_statistics')->insert($statistics);
            } else {
                $lastupdatetime = date('Y-m-d', $res->lastupdatetime);
                if ($lastupdatetime == date('Y-m-d', time())) {
                    $update = array(
                        'lastupdatetime' => time(),
                        'time' => $res->time + $time,
                    );
                    DB::table('novel_read_statistics')->where('id', $res->id)->update($update);
                } else {
                    $statistics = array(
                        'uuid' => $uuid,
                        'imei' => $imei,
                        'day' => 1,
                        'time' => $time,
                        'channel' => $channel,
                        'new' => $new,
                        'lastupdatetime' => time(),
                        'type' => $type
                    );
                    DB::table('novel_read_statistics')->insert($statistics);
                }

            }
        }

//        $data = array(
//            'uuid' => $uuid,
//            'imei' => $imei,
//            'time' => $time,
//            'addtime' => time(),
//            'channel' => $channel,
//            'new'=>$new,
//            'day' => date("Y-m-d",time()),
//        );

        //DB::table('novel_read_record')->insert($data);
        return json_encode(['status'=>1,'message'=>'成功']);
    }


    /**
     * 给免费小说使用
     * @param Request $request
     * @return false|string
     */
    public function postFreeNovelReadRecord(Request $request)
    {
        $uuid = $request->input('uuid', '');//用户id
        $imei = $request->input('imei', '');//设备号imei
        $begin = $request->input('begin', 0);
        $end = $request->input('end', 0);
        $channel = $request->input('channel', 0);
        $type = $request->input('type', 1);//1:android；2：ios
        $time = $end - $begin;

        //代码逻辑
        //只根据设备第一次安装的渠道包 当成新增用户

        //修改 只有注册过的用户才能入库
//        $user = DB::table('novel_user')->where('id', $uuid)->first();
//        if (!empty($user->phone)) {

            $first = DB::table('user_read_first')->where('uuid', $uuid)->first();//判断是否是新用户
            $new = 2;
            if (empty($first)) {
                $firstData = array(
                    'uuid' => $uuid,
                    'imei' => $imei,
                    'channel' => $channel,
                    'addtime' => time(),
                    'date' => date('Y-m-d',time()),
                    'type' => $type,
                );
                DB::table('user_read_first')->insert($firstData);
                $new = 1;
            } else {
                $nowchannel = $first->channel;
                if ($nowchannel == $channel) {
                    $new = 1;
                }
            }

            $res = DB::table('novel_read_statistics')->where('uuid', $uuid)->where('channel', $channel)->orderBy('lastupdatetime', 'desc')->first();

            if (empty($res)) {
                $statistics = array(
                    'uuid' => $uuid,
                    'imei' => $imei,
                    'day' => 1,
                    'time' => $time,
                    'channel' => $channel,
                    'new' => $new,
                    'lastupdatetime' => time(),
                    'type' => $type
                );
                DB::table('novel_read_statistics')->insert($statistics);
            } else {
                $lastupdatetime = date('Y-m-d', $res->lastupdatetime);
                if ($lastupdatetime == date('Y-m-d', time())) {
                    $update = array(
                        'lastupdatetime' => time(),
                        'time' => $res->time + $time,
                    );
                    DB::table('novel_read_statistics')->where('id', $res->id)->update($update);
                } else {
                    $statistics = array(
                        'uuid' => $uuid,
                        'imei' => $imei,
                        'day' => 1,
                        'time' => $time,
                        'channel' => $channel,
                        'new' => $new,
                        'lastupdatetime' => time(),
                        'type' => $type
                    );
                    DB::table('novel_read_statistics')->insert($statistics);
                }

            }
//        }

//        $data = array(
//            'uuid' => $uuid,
//            'imei' => $imei,
//            'time' => $time,
//            'addtime' => time(),
//            'channel' => $channel,
//            'new'=>$new,
//            'day' => date("Y-m-d",time()),
//        );

        //DB::table('novel_read_record')->insert($data);
        return json_encode(['status'=>1,'message'=>'成功']);
    }
    /**
     * 获取作者的小说
     * @param Request $request
     */
    public function postGetAuthorNovel(Request $request){
        $author = $request->input('author', '');
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);
//        $data = DB::table('novel')->where('show',1)->where('author',$author)->take(20)->get();

        $NvNovelModel = new NvNovelModel();
        $data = $NvNovelModel->getNovelByAuthor($page,$size,$author);
//        $query = "select novel.name as novelname,novel.img,novel.sourceid,novelid,novel.summary,author,novel.read,category.name as catename from novel LEFT JOIN category on novel.cateid=category.cateid where author='{$author}' ";
//        $data = DB::select($query);
        return json_encode(['status'=>1,'message'=>'成功','data'=> $data]);
    }

    /**
     * 小说加入/删除 收藏
     * @param Request $request
     */
    public function postNovelAddDelCollection(Request $request){
        $novelid = $request->input('novelid', '');
        $uuid = $request->input('uuid', '');
        $name = $request->input('name', '');
        $type = $request->input('type', 1);//1:加入收藏；2：删除收藏
        $kind = $request->input('kind', 1);//1:小说；2：短文

        if($kind == 1){
            if($type == 1){
                $res = DB::table('user_behavior_record')->where('novelid',$novelid)->where('kind',1)->where('type',2)->where('uuid',$uuid)->first();
                if(!empty($res)){
                    $data = array(
                        'status'=>1
                    );
                    DB::table('user_behavior_record')->where('id',$res->id)->update($data);
                }else{
                    $data = array(
                        'uuid'=>$uuid,
                        'novelid'=>$novelid,
                        'addtime'=>date('Y-m-d H:i:s',time()),
                        'type'=>2,
                        'kind'=>1,
                        'name'=>$name,
                        'status'=>1,
                    );
                    DB::table('user_behavior_record')->insert($data);
                }
            }else{
                $res = DB::table('user_behavior_record')->where('novelid',$novelid)->where('kind',1)->where('type',2)->where('uuid',$uuid)->first();
                if(!empty($res)) {
                    $data = array(
                        'status' => 2
                    );
                    DB::table('user_behavior_record')->where('id', $res->id)->update($data);
                }
            }
        }else{
            if($type == 1){
                $res = DB::table('user_behavior_record')->where('novelid',$novelid)->where('kind',2)->where('type',2)->where('uuid',$uuid)->first();
                if(!empty($res)){
                    $data = array(
                        'status'=>1
                    );
                    DB::table('user_behavior_record')->where('id',$res->id)->update($data);
                }else{
                    $data = array(
                        'uuid'=>$uuid,
                        'novelid'=>$novelid,
                        'addtime'=>date('Y-m-d H:i:s',time()),
                        'type'=>2,
                        'kind'=>2,
                        'name'=>$name,
                        'status'=>1,
                    );
                    DB::table('user_behavior_record')->insert($data);
                }
            }else{
                $res = DB::table('user_behavior_record')->where('novelid',$novelid)->where('kind',2)->where('type',2)->where('uuid',$uuid)->first();
                if(!empty($res)) {
                    $data = array(
                        'status' => 2
                    );
                    DB::table('user_behavior_record')->where('id', $res->id)->update($data);
                }
            }
        }

        return json_encode(['status'=>1,'message'=>'成功']);

    }


    /**
     * 获取符合特定阅读条件的用户
     * @param Request $request
     * @return false|string
     */
    public function postGetReadRecord1(Request $request)
    {
        $imei = $request->input('imei', '');
        $channel = $request->input('channel', '');
        $data['fifth_target'] = $data['forth_target'] = $data['third_target'] = $data['second_target'] = $data['first_target'] = false;
        $data['uuid']='';
        $res = DB::table('novel_read_statistics')->select('uuid','imei')->where('imei',$imei)->where('channel',$channel)->first();
        if(!empty($res)){
            $data['uuid'] = $res->uuid;
            $data['first_target'] = true;

        }
        $res = DB::table('novel_read_statistics')->select('uuid','imei')->where('imei',$imei)->where('day','<=',1)->where('time','>',1200000)->where('channel',$channel)->first();
        if(!empty($res)){
            $data['uuid'] = $res->uuid;
            $data['second_target'] = true;
        }
        $res = DB::table('novel_read_statistics')->select('uuid','imei')->where('imei',$imei)->where('day','<=',2)->where('time','>',1200000)->where('channel',$channel)->first();
        if(!empty($res)){
            $data['uuid'] = $res->uuid;
            $data['third_target'] = true;
        }
        $res = DB::table('novel_read_statistics')->select('uuid','imei')->where('imei',$imei)->where('day','<=',4)->where('time','>',1200000)->where('channel',$channel)->first();
        if(!empty($res)){
            $data['uuid'] = $res->uuid;
            $data['forth_target'] = true;
        }
        $res = DB::table('novel_read_statistics')->select('uuid','imei')->where('imei',$imei)->where('day','<=',7)->where('time','>',1200000)->where('channel',$channel)->first();
        if(!empty($res)){
            $data['uuid'] = $res->uuid;
            $data['fifth_target'] = true;
        }


        return json_encode(['status'=>1,'message'=>'成功','data'=>$data]);
    }


    public function postGetReadRecord(Request $request){
//        $imei = $request->input('imei', '');
        $phone = $request->input('phone', '');
        $channel = $request->input('channel', '');
        if(empty($phone) ||  empty($channel)){
            return json_encode(['status'=>0,'message'=>'参数不全']);
        }
        $userInfo = DB::table('novel_user')->where('phone',$phone)->first();

        if(empty($userInfo)){
            return json_encode(['status'=>0,'message'=>'该手机号还未注册']);
        }
        $uuid = $userInfo->id;


        $res = DB::table('novel_read_statistics')->select('uuid','imei','new')->where('uuid',$uuid)->where('channel',$channel)->get();
        if(empty($res)){

            return json_encode(
                [
                    'status'=>0,
                    'message'=>'该用户还未阅读小说',
                ]
            );

        }else{

            if($res[0]->new == 2){
                return json_encode(
                    [
                        'status'=>-1,
                        'message'=>'该设备已经安装过其他渠道包',

                    ]
                );
            }
            $first_target = true;
            $first_target_num = 1;
            $s_num = DB::table('novel_read_statistics')->where('uuid',$uuid)->where('channel',$channel)->where('time','>',1200000)->where('new',1)->count();
            if($s_num>=7){
                $second_target = true;
                $thrid_target = true;
                $forth_target = true;
                $oneday_target = true;

                $second_target_num = 1;
                $thrid_target_num = 1;
                $forth_target_num = 1;
                $oneday_target_num = 1;
            }elseif ($s_num>=4 && $s_num<7){
                $second_target = true;
                $thrid_target = true;
                $forth_target = false;
                $oneday_target = true;

                $second_target_num = 1;
                $thrid_target_num = 1;
                $forth_target_num = 0;
                $oneday_target_num = 1;

            }elseif ($s_num>=2 && $s_num<4){
                $second_target_num = 1;
                $thrid_target_num = 0;
                $forth_target_num = 0;
                $oneday_target_num = 1;

                $second_target = true;
                $thrid_target = false;
                $forth_target = false;
                $oneday_target = true;
            }elseif ($s_num<2 && $s_num>=1){
                $second_target = false;
                $thrid_target = false;
                $forth_target = false;
                $oneday_target = true;

                $second_target_num = 0;
                $thrid_target_num = 0;
                $forth_target_num = 0;
                $oneday_target_num = 1;
            }elseif ($s_num<1){
                $second_target = false;
                $thrid_target = false;
                $forth_target = false;
                $oneday_target = false;

                $second_target_num = 0;
                $thrid_target_num = 0;
                $forth_target_num = 0;
                $oneday_target_num = 0;
            }

            $totalread = $s_num;
            $todaytime = strtotime(date("Y-m-d"),time());
            $end = strtotime(date("Y-m-d"),time())*86400;
            $time = DB::table('novel_read_statistics')->where('uuid',$uuid)->where('channel',$channel)->where('lastupdatetime','>',$todaytime)->where('lastupdatetime','<',$end)->first();
            $data['first_target'] = $first_target;
            $data['second_target'] = $second_target;
            $data['thrid_target'] = $thrid_target;
            $data['oneday_target'] = $oneday_target;
            $data['forth_target'] = $forth_target;

            $data['first_target_num'] = $first_target_num;
            $data['second_target_num'] = $second_target_num;
            $data['thrid_target_num'] = $thrid_target_num;
            $data['oneday_target_num'] = $oneday_target_num;
            $data['forth_target_num'] = $forth_target_num;


            $data['totalread'] = $totalread;
            if(!empty($time)){
                $todayreadtime = intval($time->time/60000);
            }else{
                $todayreadtime=0;
            }
            $data['todayreadtime'] = $todayreadtime;
            $data['uuid'] = $res[0]->uuid;
            return json_encode(['status'=>1,'message'=>'成功','data'=>$data]);

        }


    }
    public function postGetReadRecordByImei(Request $request){
        $imei = $request->input('imei', '');
        $channel = $request->input('channel', '');
        if(empty($imei) ||  empty($channel)){
            return json_encode(['status'=>0,'message'=>'参数不全']);
        }
        $userInfo = DB::table('novel_user')->where('imei',$imei)->first();

        if(empty($userInfo)){
            return json_encode(['status'=>0,'message'=>'该用户还未注册']);
        }

        $res = DB::table('novel_read_statistics')->select('uuid','imei','new')->where('imei',$imei)->where('channel',$channel)->get();
        if(empty($res)){

            return json_encode(
                [
                    'status'=>0,
                    'message'=>'该用户还未阅读小说',
                ]
            );

        }else{

            if($res[0]->new == 2){
                return json_encode(
                    [
                        'status'=>-1,
                        'message'=>'该设备已经安装过其他渠道包',

                    ]
                );
            }
            $first_target = true;
            $first_target_num = 1;
            $s_num = DB::table('novel_read_statistics')->where('imei',$imei)->where('channel',$channel)->where('time','>',1200000)->where('new',1)->count();
            if($s_num>=7){
                $second_target = true;
                $thrid_target = true;
                $forth_target = true;
                $oneday_target = true;

                $second_target_num = 1;
                $thrid_target_num = 1;
                $forth_target_num = 1;
                $oneday_target_num = 1;
            }elseif ($s_num>=4 && $s_num<7){
                $second_target = true;
                $thrid_target = true;
                $forth_target = false;
                $oneday_target = true;

                $second_target_num = 1;
                $thrid_target_num = 1;
                $forth_target_num = 0;
                $oneday_target_num = 1;

            }elseif ($s_num>=2 && $s_num<4){
                $second_target_num = 1;
                $thrid_target_num = 0;
                $forth_target_num = 0;
                $oneday_target_num = 1;

                $second_target = true;
                $thrid_target = false;
                $forth_target = false;
                $oneday_target = true;
            }elseif ($s_num<2 && $s_num>=1){
                $second_target = false;
                $thrid_target = false;
                $forth_target = false;
                $oneday_target = true;

                $second_target_num = 0;
                $thrid_target_num = 0;
                $forth_target_num = 0;
                $oneday_target_num = 1;
            }elseif ($s_num<1){
                $second_target = false;
                $thrid_target = false;
                $forth_target = false;
                $oneday_target = false;

                $second_target_num = 0;
                $thrid_target_num = 0;
                $forth_target_num = 0;
                $oneday_target_num = 0;
            }

            $totalread = $s_num;
            $todaytime = strtotime(date("Y-m-d"),time());
            $end = strtotime(date("Y-m-d"),time())*86400;
            $time = DB::table('novel_read_statistics')->where('imei',$imei)->where('channel',$channel)->where('lastupdatetime','>',$todaytime)->where('lastupdatetime','<',$end)->first();
            $data['first_target'] = $first_target;
            $data['second_target'] = $second_target;
            $data['thrid_target'] = $thrid_target;
            $data['oneday_target'] = $oneday_target;
            $data['forth_target'] = $forth_target;

            $data['first_target_num'] = $first_target_num;
            $data['second_target_num'] = $second_target_num;
            $data['thrid_target_num'] = $thrid_target_num;
            $data['oneday_target_num'] = $oneday_target_num;
            $data['forth_target_num'] = $forth_target_num;


            $data['totalread'] = $totalread;
            if(!empty($time)){
                $todayreadtime = intval($time->time/60000);
            }else{
                $todayreadtime=0;
            }
            $data['todayreadtime'] = $todayreadtime;
            $data['uuid'] = $res[0]->uuid;
            return json_encode(['status'=>1,'message'=>'成功','data'=>$data]);

        }


    }
    public function getGetReadRecordByImei(Request $request){//get请求
        $imei = $request->input('imei', '');
        $channel = $request->input('channel', '');
        if(empty($imei) ||  empty($channel)){
            return json_encode(['status'=>0,'message'=>'参数不全']);
        }

        $userInfo = DB::table('novel_user')->where('imei',$imei)->first();

        if(empty($userInfo)){
            return json_encode(['status'=>0,'message'=>'该用户还未注册']);
        }

        $res = DB::table('novel_read_statistics')->select('uuid','imei','new')->where('imei',$imei)->where('channel',$channel)->get();
        if(empty($res)){

            return json_encode(
                [
                    'status'=>0,
                    'message'=>'该用户还未阅读小说',
                ]
            );

        }else{

            if($res[0]->new == 2){
                return json_encode(
                    [
                        'status'=>-1,
                        'message'=>'该设备已经安装过其他渠道包',

                    ]
                );
            }
            $first_target = true;
            $first_target_num = 1;
            $s_num = DB::table('novel_read_statistics')->where('imei',$imei)->where('channel',$channel)->where('time','>',1200000)->where('new',1)->count();
            if($s_num>=7){
                $second_target = true;
                $thrid_target = true;
                $forth_target = true;
                $oneday_target = true;

                $second_target_num = 1;
                $thrid_target_num = 1;
                $forth_target_num = 1;
                $oneday_target_num = 1;
            }elseif ($s_num>=4 && $s_num<7){
                $second_target = true;
                $thrid_target = true;
                $forth_target = false;
                $oneday_target = true;

                $second_target_num = 1;
                $thrid_target_num = 1;
                $forth_target_num = 0;
                $oneday_target_num = 1;

            }elseif ($s_num>=2 && $s_num<4){
                $second_target_num = 1;
                $thrid_target_num = 0;
                $forth_target_num = 0;
                $oneday_target_num = 1;

                $second_target = true;
                $thrid_target = false;
                $forth_target = false;
                $oneday_target = true;
            }elseif ($s_num<2 && $s_num>=1){
                $second_target = false;
                $thrid_target = false;
                $forth_target = false;
                $oneday_target = true;

                $second_target_num = 0;
                $thrid_target_num = 0;
                $forth_target_num = 0;
                $oneday_target_num = 1;
            }elseif ($s_num<1){
                $second_target = false;
                $thrid_target = false;
                $forth_target = false;
                $oneday_target = false;

                $second_target_num = 0;
                $thrid_target_num = 0;
                $forth_target_num = 0;
                $oneday_target_num = 0;
            }

            $totalread = $s_num;
            $todaytime = strtotime(date("Y-m-d"),time());
            $end = strtotime(date("Y-m-d"),time())*86400;
            $time = DB::table('novel_read_statistics')->where('imei',$imei)->where('channel',$channel)->where('lastupdatetime','>',$todaytime)->where('lastupdatetime','<',$end)->first();
            $data['first_target'] = $first_target;
            $data['second_target'] = $second_target;
            $data['thrid_target'] = $thrid_target;
            $data['oneday_target'] = $oneday_target;
            $data['forth_target'] = $forth_target;

            $data['first_target_num'] = $first_target_num;
            $data['second_target_num'] = $second_target_num;
            $data['thrid_target_num'] = $thrid_target_num;
            $data['oneday_target_num'] = $oneday_target_num;
            $data['forth_target_num'] = $forth_target_num;


            $data['totalread'] = $totalread;
            if(!empty($time)){
                $todayreadtime = intval($time->time/60000);
            }else{
                $todayreadtime=0;
            }
            $data['todayreadtime'] = $todayreadtime;
            $data['uuid'] = $res[0]->uuid;
            return json_encode(['status'=>1,'message'=>'成功','data'=>$data]);

        }


    }




    public function postGetReadRecord_NOPHONE0425(Request $request){
        $imei = $request->input('imei', '');
        $channel = $request->input('channel', '');
        if(empty($imei) ||  empty($channel)){
            return json_encode(['status'=>0,'message'=>'参数不全']);
        }

        $res = DB::table('novel_read_statistics')->select('uuid','imei','new')->where('imei',$imei)->where('channel',$channel)->get();
        if(empty($res)){

            return json_encode(
                [
                    'status'=>0,
                    'message'=>'未查询到该用户信息',
                ]
            );

        }else{

            if($res[0]->new == 2){
                return json_encode(
                    [
                        'status'=>-1,
                        'message'=>'该设备已经安装过其他渠道包',

                    ]
                );
            }
            $first_target = true;
            $s_num = DB::table('novel_read_statistics')->where('imei',$imei)->where('channel',$channel)->where('time','>',1200000)->where('new',1)->count();
            if($s_num>=7){
                $second_target = true;
                $thrid_target = true;
                $forth_target = true;
                $oneday_target = true;
            }elseif ($s_num>=4 && $s_num<7){
                $second_target = true;
                $thrid_target = true;
                $forth_target = false;
                $oneday_target = true;
            }elseif ($s_num>=2 && $s_num<4){
                $second_target = true;
                $thrid_target = false;
                $forth_target = false;
                $oneday_target = true;
            }elseif ($s_num<2 && $s_num>=1){
                $second_target = false;
                $thrid_target = false;
                $forth_target = false;
                $oneday_target = true;
            }elseif ($s_num<1){
                $second_target = false;
                $thrid_target = false;
                $forth_target = false;
                $oneday_target = false;
            }

            $totalread = $s_num;
            $todaytime = strtotime(date("Y-m-d"),time());
            $end = strtotime(date("Y-m-d"),time())*86400;
            $time = DB::table('novel_read_statistics')->where('imei',$imei)->where('channel',$channel)->where('lastupdatetime','>',$todaytime)->where('lastupdatetime','<',$end)->first();
            $data['first_target'] = $first_target;
            $data['second_target'] = $second_target;
            $data['thrid_target'] = $thrid_target;
            $data['oneday_target'] = $oneday_target;
            $data['forth_target'] = $forth_target;
            $data['totalread'] = $totalread;
            if(!empty($time)){
                $todayreadtime = intval($time->time/60000);
            }else{
                $todayreadtime=0;
            }
            $data['todayreadtime'] = $todayreadtime;
            $data['uuid'] = $res[0]->uuid;
            return json_encode(['status'=>1,'message'=>'成功','data'=>$data]);

        }


    }

    /**
     * 批量获取小说章节
     * @param Request $request
     * @return false|string
     */
    public function postGetBatchChapter(Request $request){
        $page = $request->input('page', 1);
        $novelid = $request->input('novelid', 1);
        $ChapterModel = new ChapterModel();
        $data = $ChapterModel->getBatchChapter($page,$novelid);
        return json_encode(['status'=>1,'message'=>'成功','data'=>$data]);
    }

    public function postGetUserAgreement(Request $request){
        $data = DB::table('user_agreement')->where('id',1)->first();
        return json_encode(['status'=>1,'message'=>'成功','data'=>$data]);
    }


    /**
     * 获取搜索词最多的前100
     * @param Request $request
     * @return false|string
     */
    public function postGetTopNovelSearch(Request $request){

        $NovelSearchLogModel = new NovelSearchLogModel();
        $data = $NovelSearchLogModel->getTopNovelSearch();
        return json_encode(['status'=>1,'message'=>'成功','data'=>$data]);
    }

    public function postGetTopNovelClick(Request $request){

//        $NovelClickRecordModel = new NovelClickRecordModel();
//        $data = $NovelClickRecordModel->getTopNovelClick();
//        return json_encode(['status'=>1,'message'=>'成功','data'=>$data]);

        $confredis = Config::get('database.redis.default');
        $redis = new libredis([$confredis['host'], $confredis['port']]);
        $data = $redis->get("novel_click");
        if ($data) {
            $data = json_decode($data, true);
        }else{
            $NovelClickRecordModel = new NovelClickRecordModel();
            $data = $NovelClickRecordModel->getTopNovelClick();
            $redis->set("novel_click", json_encode($data), false, true, 7*24*3600);
        }

        return json_encode(['status'=>1,'message'=>'成功','data'=>$data]);
    }

    /**
     * 获取app的留存 新增等信息
     * @return false|string
     */
    public function postGetAppUserData(Request $request){
        $daytime = $request->input('daytime', '');
        if(empty($daytime)){
            return json_encode(['status'=>0,'message'=>'参数不全']);
        }
        $today = date('Y-m-d',$daytime);
        $temp = strtotime($today)-1;
        $yesterday = date("Y-m-d",$temp);



        $todaydown = DB::table('statistics')->where(['firstvisit'=>2,'addtime'=>$today])->count();
        $yesdown = DB::table('statistics')->where(['firstvisit'=>2,'addtime'=>$yesterday])->count();
        $data['todaydown'] = $todaydown;
        $data['downcompare'] = $todaydown-$yesdown;

        $todaydownadd = DB::table('statistics')->where(['firstvisit'=>1,'addtime'=>$today])->count();
        $yesadd = DB::table('statistics')->where(['firstvisit'=>1,'addtime'=>$yesterday])->count();
        $data['todaynewuser'] = $todaydownadd;
        $data['newusercompare'] = $todaydownadd-$yesadd;

        $todayquery = "select count(DISTINCT(uid)) as num  from statistics where   novelid>0 and addtime='".$today."'";//活跃 打开了小说的用户数
        $todayactive = DB::selectone($todayquery);
        $yesquery = "select count(DISTINCT(uid)) as num  from statistics where   novelid>0 and addtime='".$yesterday."'";
        $yesactive = DB::selectone($yesquery);
        $data['todayactive'] = $todayactive->num;
        $data['activecompare'] = $todayactive->num-$yesactive->num;

        $yesactiveuserquery = "select DISTINCT(uid) as uuid  from statistics where  novelid>0 and addtime='".$yesterday."'";
        $yesactiveuser = DB::select($yesactiveuserquery);
        if(!empty($yesactiveuser)){
            $uidArr2=array();

            foreach ($yesactiveuser as $k=>$v){
                $uidArr2[$k] = $v->uuid;
            }

            $uuid = implode(',',$uidArr2);

            $todayactiveuserquery = "select count(DISTINCT(uid)) as num  from statistics where  novelid>0 and uid in ($uuid) and addtime='".$today."'";
            $todayactiveuser = DB::selectone($todayactiveuserquery);
            $data['activeonedaykeep'] = round($todayactiveuser->num/$yesactive->num*100,2).'%';;
        }else{
            $data['activeonedaykeep'] = 0;
        }

        $appstartquery = "select count(*) as num from statistics where module='0' and novelid=0  and addtime='".$today."'";
        $todayappstart = DB::selectone($appstartquery);
        $yesappstartquery = "select count(*) as num from statistics where module='0' and novelid=0  and addtime='".$yesterday."'";
        $yesappstart = DB::selectone($yesappstartquery);
        $data['todayappstart'] = $todayappstart->num;
        $data['appstartcompare'] = $todayappstart->num-$yesappstart->num;

        $yesaddDetail = DB::table('statistics')->where(['firstvisit'=>1,'addtime'=>$yesterday])->get();
        $data['yesterdaynewuser'] = $yesadd;
        $uidArr1=array();

        foreach ($yesaddDetail as $k=>$v){
            $uidArr1[$k] = "'".$v->uid."'";
        }

        $uid = implode(',',$uidArr1);

        $onelater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and addtime='".$today."'";
        $onelater_res = DB::selectone($onelater_query);
        if(empty($yesadd)){
            $data['onedaykeep'] = 0;
        }else{
            $data['onedaykeep'] = round($onelater_res->num/$yesadd*100,2).'%';
        }

        return json_encode(['status'=>1,'message'=>'成功','data'=>$data]);

    }

    public function postGetEachHourData(Request $request){

        $daytime = $request->input('daytime', '');

        $ret = DB::table('android_update')->where('ifsend',1)->get();
        $str = "";
        if(!empty($ret)){

            $str .="#项目：【智搜小说】数据统计".PHP_EOL;
            $str .="#统计时间：".date('Y-m-d H:i:s',$daytime)."【实时】".PHP_EOL;
            foreach ($ret as $k=>$value){
                $channel = $value->pack_name;
                $channelname = $value->channel;
                $today = date('Y-m-d',$daytime);
                $temp = strtotime($today)-1;
                $yesterday = date("Y-m-d",$temp);


                $todaydown = DB::table('statistics')->where(['firstvisit'=>2,'addtime'=>$today,'channel'=>$channel])->count();
                $yesdown = DB::table('statistics')->where(['firstvisit'=>2,'addtime'=>$yesterday,'channel'=>$channel])->count();
                $data[$channel]['todaydown'] = $todaydown;
                $data[$channel]['downcompare'] = $todaydown-$yesdown;
                $downtemp = $todaydown-$yesdown;


                $todaydownadd = DB::table('statistics')->where(['firstvisit'=>1,'addtime'=>$today,'channel'=>$channel])->count();
                $yesadd = DB::table('statistics')->where(['firstvisit'=>1,'addtime'=>$yesterday,'channel'=>$channel])->count();
                $data[$channel]['todaynewuser'] = $todaydownadd;
                $data[$channel]['newusercompare'] = $todaydownadd-$yesadd;
                $downaddtemp = $todaydownadd-$yesadd;


                $todayquery = "select count(DISTINCT(uid)) as num  from statistics where   novelid>0 and channel=$channel and addtime='".$today."'";//活跃 打开了小说的用户数
                $todayactive = DB::selectone($todayquery);
                $yesquery = "select count(DISTINCT(uid)) as num  from statistics where   novelid>0 and channel=$channel and addtime='".$yesterday."'";
                $yesactive = DB::selectone($yesquery);
                $data[$channel]['todayactive'] = $todayactive->num;
                $data[$channel]['activecompare'] = $todayactive->num-$yesactive->num;
                $activetemp = $todayactive->num-$yesactive->num;






                $hour = strtotime(date("Y-m-d H:00:00", $daytime));
                $hourend =strtotime(date("Y-m-d H:00:00", $daytime))+3600-1;
                $hourtime = strtotime(date("Y-m-d H:00:00", $daytime))-3600;
                $hourtimeend = strtotime(date("Y-m-d H:00:00", $daytime))-1;
                $todayhourquery = "select count(DISTINCT(uid)) as num  from statistics where   novelid>0 and channel=$channel and timestamp>='".$hour."' and timestamp <= '".$hourend."'";//活跃 打开了小说的用户数
                $todayhouractive = DB::selectone($todayhourquery);
                $yeshourquery = "select count(DISTINCT(uid)) as num  from statistics where   novelid>0 and channel=$channel and timestamp>='".$hourtime."' and timestamp <='".$hourtimeend."' ";
                $yeshouractive = DB::selectone($yeshourquery);
                $activehourtemp = $todayhouractive->num-$yeshouractive->num;





                $appstartquery = "select count(*) as num from statistics where module='0' and novelid=0 and channel=$channel  and addtime='".$today."'";
                $todayappstart = DB::selectone($appstartquery);
                $yesappstartquery = "select count(*) as num from statistics where module='0' and novelid=0 and channel=$channel and addtime='".$yesterday."'";
                $yesappstart = DB::selectone($yesappstartquery);
                $data[$channel]['todayappstart'] = $todayappstart->num;
                $data[$channel]['appstartcompare'] = $todayappstart->num-$yesappstart->num;
                $appstarttemp = $todayappstart->num-$yesappstart->num;


                $yesaddDetail = DB::table('statistics')->where(['firstvisit'=>1,'addtime'=>$yesterday,'channel'=>$channel])->get();
                $data[$channel]['yesterdaynewuser'] = $yesadd;
                $uidArr1=array();


                $num_today = DB::table('novel_user')->where('userfrom','>',1)->where('channel',$channel)->where('regtime','>=',$today.' 00:00:00')->where('regtime','<=',$today.' 23:59:59')->count();
                $num_yes = DB::table('novel_user')->where('userfrom','>',1)->where('channel',$channel)->where('regtime','>=',$yesterday.' 00:00:00')->where('regtime','<=',$yesterday.' 23:59:59')->count();


                $yesactiveuserquery = "select DISTINCT(uid) as uuid  from statistics where  novelid>0 and channel=$channel and addtime='".$yesterday."'";
                $yesactiveuser = DB::select($yesactiveuserquery);



                if(!empty($downtemp) || !empty($todaydownadd)|| !empty($todayactive->num)|| !empty($todayhouractive->num)|| !empty($todayappstart->num)|| !empty($yesaddDetail)|| !empty($yesactiveuser)){
                    $str .="#渠道名称：".$channelname."（渠道".$channel."）".PHP_EOL;
                    if($downtemp >0){
                        $str .= "*DL：".$todaydown."（上升 ↑ ".$downtemp."）".PHP_EOL;
                    }else{
                        $str .= "*DL：".$todaydown."（下降 ↓ ".abs($downtemp)."）".PHP_EOL;
                    }

                    if($downaddtemp >0){
                        $str .= "*XZ：".$todaydownadd."（上升 ↑ ".$downaddtemp."）".PHP_EOL;
                    }else{
                        $str .= "*XZ：".$todaydownadd."（下降 ↓ ".abs($downaddtemp)."）".PHP_EOL;
                    }

                    if($activetemp >0){
                        $str .= "*DAU：".$todayactive->num."（上升 ↑ ".$activetemp."）".PHP_EOL;
                    }else{
                        $str .= "*DAU：".$todayactive->num."（下降 ↓ ".abs($activetemp)."）".PHP_EOL;
                    }

                    if($activehourtemp >0){
                        $str .= "*HAU：".$todayhouractive->num."（上升 ↑ ".$activehourtemp."）".PHP_EOL;
                    }else{
                        $str .= "*HAU：".$todayhouractive->num."（下降 ↓ ".abs($activehourtemp)."）".PHP_EOL;
                    }

                    if($appstarttemp >0){
                        $str .= "*QD：".$todayappstart->num."（上升 ↑ ".$appstarttemp."）".PHP_EOL;
                    }else{
                        $str .= "*QD：".$todayappstart->num."（下降 ↓ ".abs($appstarttemp)."）".PHP_EOL;
                    }

                    if(!empty($yesaddDetail)){
                        foreach ($yesaddDetail as $k=>$v){
                            $uidArr1[$k] = "'".$v->uid."'";
                        }

                        $uid = implode(',',$uidArr1);

                        $onelater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and channel=$channel and addtime='".$today."'";
                        $onelater_res = DB::selectone($onelater_query);
                        if(empty($yesadd)){
                            $data[$channel]['onedaykeep'] = 0;
                            $str .= "*XZLC：0（0%）".PHP_EOL;
                        }else{
                            $data[$channel]['onedaykeep'] = round($onelater_res->num/$yesadd*100,2).'%';
                            $str .= "*XZLC：".$onelater_res->num."（".round($onelater_res->num/$yesadd*100,2).'%'."）".PHP_EOL;
                        }

                    }else{
                        $str .= "*XZLC：0（0%）".PHP_EOL;
                    }

                    if($num_today-$num_yes >0){
                        $str .= "*LB：".$num_today."（上升 ↑ ".$num_today-$num_yes."）".PHP_EOL;
                    }else{
                        $str .= "*LB：".$num_today."（下降 ↓ ".abs($num_today-$num_yes)."）".PHP_EOL;
                    }

                    if(!empty($yesactiveuser)){
                        $uidArr2=array();
                        foreach ($yesactiveuser as $k=>$v){
                            $uidArr2[$k] = $v->uuid;
                        }
                        $uuid = implode(',',$uidArr2);
                        $todayactiveuserquery = "select count(DISTINCT(uid)) as num  from statistics where  novelid>0 and uid in ($uuid) and channel=$channel and addtime='".$today."'";
                        $todayactiveuser = DB::selectone($todayactiveuserquery);
                        $str .= "*DAULC：".$todayactiveuser->num."（".round($todayactiveuser->num/$yesactive->num*100,2).'%'."）".PHP_EOL;
                    }else{
                        $str .= "*DAULC：0（0%）".PHP_EOL;
                    }

                }

            }


            $str .= "#数据汇总".PHP_EOL;
            $todaydown = DB::table('statistics')->where(['firstvisit'=>2,'addtime'=>$today])->count();
            $yesdown = DB::table('statistics')->where(['firstvisit'=>2,'addtime'=>$yesterday])->count();
            $downcom = $todaydown-$yesdown;
            if($downcom > 0){
                $str .= "*DL：".$todaydown."（上升 ↑ ".$downcom."）".PHP_EOL;
            }else{
                $str .= "*DL：".$todaydown."（下降 ↓ ".abs($downcom)."）".PHP_EOL;
            }

            $todaydownadd = DB::table('statistics')->where(['firstvisit'=>1,'addtime'=>$today])->count();
            $yesadd = DB::table('statistics')->where(['firstvisit'=>1,'addtime'=>$yesterday])->count();
            $addcom = $todaydownadd-$yesadd;
            if($addcom > 0){
                $str .= "*XZ：".$todaydownadd."（上升 ↑ ".$addcom."）".PHP_EOL;
            }else{
                $str .= "*XZ：".$todaydownadd."（下降 ↓ ".abs($addcom)."）".PHP_EOL;
            }

            $num_today = DB::table('novel_user')->where('userfrom','>',1)->where('regtime','>=',$today.' 00:00:00')->where('regtime','<=',$today.' 23:59:59')->count();
            $num_yes = DB::table('novel_user')->where('userfrom','>',1)->where('regtime','>=',$yesterday.' 00:00:00')->where('regtime','<=',$yesterday.' 23:59:59')->count();
            $numcom = $num_today-$num_yes;
            if($numcom >0){
                $str .= "*LB：".$num_today."（上升 ↑ ".$numcom."）".PHP_EOL;
            }else{
                $str .= "*LB：".$num_today."（下降 ↓ ".abs($numcom)."）".PHP_EOL;
            }
            $yesaddDetail1 = DB::table('statistics')->where(['firstvisit'=>1,'addtime'=>$yesterday])->get();
            $yesadd1 = DB::table('statistics')->where(['firstvisit'=>1,'addtime'=>$yesterday])->count();
            $uidArr3 = array();
            if(!empty($yesaddDetail1)){
                foreach ($yesaddDetail1 as $k=>$v){
                    $uidArr3[$k] = "'".$v->uid."'";
                }

                $uid1 = implode(',',$uidArr3);

                $onelater_query1 = "select count(distinct(uid)) as num from statistics where uid in ({$uid1}) and novelid>0  and addtime='".$today."'";
                $onelater_res1 = DB::selectone($onelater_query1);
                if(empty($yesadd)){
                    $data[$channel]['onedaykeep'] = 0;
                    $str .= "*XZLC：0（0%）".PHP_EOL;
                }else{
                    $data[$channel]['onedaykeep'] = round($onelater_res1->num/$yesadd1*100,2).'%';
                    $str .= "*XZLC：".$onelater_res1->num."（".round($onelater_res1->num/$yesadd1*100,2).'%'."）".PHP_EOL;
                }

            }else{
                $str .= "*XZLC：0（0%）".PHP_EOL;
            }
            $todayquery1 = "select count(DISTINCT(uid)) as num  from statistics where   novelid>0   and addtime='".$today."'";//活跃 打开了小说的用户数
            $todayactive1 = DB::selectone($todayquery1);
            $yesquery1 = "select count(DISTINCT(uid)) as num  from statistics where   novelid>0   and addtime='".$yesterday."'";
            $yesactive1 = DB::selectone($yesquery1);

            $activetemp1 = $todayactive1->num-$yesactive1->num;
            if($activetemp1 >0){
                $str .= "*DAU：".$todayactive1->num."（上升 ↑ ".$activetemp1."）".PHP_EOL;
            }else{
                $str .= "*DAU：".$todayactive1->num."（下降 ↓ ".abs($activetemp1)."）".PHP_EOL;
            }

            $yesactiveuserquery2 = "select DISTINCT(uid) as uuid  from statistics where  novelid>0   and addtime='".$yesterday."'";
            $yesactiveuser2 = DB::select($yesactiveuserquery2);
            if(!empty($yesactiveuser2)){
                $uidArr4=array();
                foreach ($yesactiveuser2 as $k=>$v){
                    $uidArr4[$k] = $v->uuid;
                }
                $uuid3 = implode(',',$uidArr4);
                $todayactiveuserquery3 = "select count(DISTINCT(uid)) as num  from statistics where  novelid>0 and uid in ($uuid3)  and addtime='".$today."'";
                $todayactiveuser3 = DB::selectone($todayactiveuserquery3);
                $str .= "*DAULC：".$todayactiveuser3->num."（".round($todayactiveuser3->num/$yesactive1->num*100,2).'%'."）".PHP_EOL;
            }else{
                $str .= "*DAULC：0（0%）".PHP_EOL;
            }

            $appstartquery1 = "select count(*) as num from statistics where module='0' and novelid=0    and addtime='".$today."'";
            $todayappstart1 = DB::selectone($appstartquery1);
            $yesappstartquery1 = "select count(*) as num from statistics where module='0' and novelid=0   and addtime='".$yesterday."'";
            $yesappstart1 = DB::selectone($yesappstartquery1);
            $appstarttemp1 = $todayappstart1->num-$yesappstart1->num;
            if($appstarttemp1 >0){
                $str .= "*QD：".$todayappstart1->num."（上升 ↑ ".$appstarttemp1."）".PHP_EOL;
            }else{
                $str .= "*QD：".$todayappstart1->num."（下降 ↓ ".abs($appstarttemp1)."）".PHP_EOL;
            }
        }


//        return json_encode(['status'=>1,'msg'=>'成功！！！','data'=>['message'=>$str,'a'=>$today,'b'=>$yesterday,'c'=>$hour,'d'=>$hourend,'e'=>$hourtime,'f'=>$hourtimeend]]);
        return json_encode(['status'=>1,'msg'=>'成功！！！','data'=>['message'=>$str]]);

    }

    public function postGetEachHourDataOLD(Request $request){

        $daytime = $request->input('daytime', '');

        $ret = DB::table('android_update')->where('ifsend',1)->get();
        $str = "";
        if(!empty($ret)){

            $str .="#项目：【智搜小说】数据统计".PHP_EOL;
            $str .="#统计时间：".date('Y-m-d H:i:s',$daytime)."【实时】".PHP_EOL;
            foreach ($ret as $k=>$value){
                $channel = $value->pack_name;
                $channelname = $value->channel;
                $today = date('Y-m-d',$daytime);
                $temp = strtotime($today)-1;
                $yesterday = date("Y-m-d",$temp);



                $str .="#渠道名称：".$channelname."（渠道".$channel."）".PHP_EOL;


                $todaydown = DB::table('statistics')->where(['firstvisit'=>2,'addtime'=>$today,'channel'=>$channel])->count();
                $yesdown = DB::table('statistics')->where(['firstvisit'=>2,'addtime'=>$yesterday,'channel'=>$channel])->count();
                $data[$channel]['todaydown'] = $todaydown;
                $data[$channel]['downcompare'] = $todaydown-$yesdown;

                $downtemp = $todaydown-$yesdown;

                if($downtemp >0){
                    $str .= "*DL：".$todaydown."（上升 ↑ ".$downtemp."）".PHP_EOL;
                }else{
                    $str .= "*DL：".$todaydown."（下降 ↓ ".abs($downtemp)."）".PHP_EOL;
                }



                $todaydownadd = DB::table('statistics')->where(['firstvisit'=>1,'addtime'=>$today,'channel'=>$channel])->count();
                $yesadd = DB::table('statistics')->where(['firstvisit'=>1,'addtime'=>$yesterday,'channel'=>$channel])->count();
                $data[$channel]['todaynewuser'] = $todaydownadd;
                $data[$channel]['newusercompare'] = $todaydownadd-$yesadd;
//                if($todaydownadd-$yesadd >0){
//                    $str .= "*XZ：".$todaydownadd."（上升 ↑ ".$todaydownadd-$yesadd."）".PHP_EOL;
//                }else{
//                    $str .= "*XZ：".$todaydownadd."（下降 ↓ ".abs($todaydownadd-$yesadd)."）".PHP_EOL;
//                }
                $downaddtemp = $todaydownadd-$yesadd;

                if($downaddtemp >0){
                    $str .= "*XZ：".$todaydownadd."（上升 ↑ ".$downaddtemp."）".PHP_EOL;
                }else{
                    $str .= "*XZ：".$todaydownadd."（下降 ↓ ".abs($downaddtemp)."）".PHP_EOL;
                }

//
                $todayquery = "select count(DISTINCT(uid)) as num  from statistics where   novelid>0 and channel=$channel and addtime='".$today."'";//活跃 打开了小说的用户数
                $todayactive = DB::selectone($todayquery);
                $yesquery = "select count(DISTINCT(uid)) as num  from statistics where   novelid>0 and channel=$channel and addtime='".$yesterday."'";
                $yesactive = DB::selectone($yesquery);
                $data[$channel]['todayactive'] = $todayactive->num;
                $data[$channel]['activecompare'] = $todayactive->num-$yesactive->num;
                $activetemp = $todayactive->num-$yesactive->num;
                if($activetemp >0){
                    $str .= "*DAU：".$todayactive->num."（上升 ↑ ".$activetemp."）".PHP_EOL;
                }else{
                    $str .= "*DAU：".$todayactive->num."（下降 ↓ ".abs($activetemp)."）".PHP_EOL;
                }

                $hour = strtotime(date("Y-m-d H:00:00", $daytime));
                $hourend =strtotime(date("Y-m-d H:00:00", $daytime))+3600-1;

                $hourtime = strtotime(date("Y-m-d H:00:00", $daytime))-3600;
                $hourtimeend = strtotime(date("Y-m-d H:00:00", $daytime))-1;
                $todayhourquery = "select count(DISTINCT(uid)) as num  from statistics where   novelid>0 and channel=$channel and timestamp>='".$hour."' and timestamp <= '".$hourend."'";//活跃 打开了小说的用户数
                $todayhouractive = DB::selectone($todayhourquery);
                $yeshourquery = "select count(DISTINCT(uid)) as num  from statistics where   novelid>0 and channel=$channel and timestamp>='".$hourtime."' and timestamp <='".$hourtimeend."' ";
                $yeshouractive = DB::selectone($yeshourquery);

                $activehourtemp = $todayhouractive->num-$yeshouractive->num;
                if($activehourtemp >0){
                    $str .= "*HAU：".$todayhouractive->num."（上升 ↑ ".$activehourtemp."）".PHP_EOL;
                }else{
                    $str .= "*HAU：".$todayhouractive->num."（下降 ↓ ".abs($activehourtemp)."）".PHP_EOL;
                }


//
//
                $appstartquery = "select count(*) as num from statistics where module='0' and novelid=0 and channel=$channel  and addtime='".$today."'";
                $todayappstart = DB::selectone($appstartquery);
                $yesappstartquery = "select count(*) as num from statistics where module='0' and novelid=0 and channel=$channel and addtime='".$yesterday."'";
                $yesappstart = DB::selectone($yesappstartquery);
                $data[$channel]['todayappstart'] = $todayappstart->num;
                $data[$channel]['appstartcompare'] = $todayappstart->num-$yesappstart->num;
                $appstarttemp = $todayactive->num-$yesappstart->num;
                if($appstarttemp >0){
                    $str .= "*QD：".$todayappstart->num."（上升 ↑ ".$appstarttemp."）".PHP_EOL;
                }else{
                    $str .= "*QD：".$todayappstart->num."（下降 ↓ ".abs($appstarttemp)."）".PHP_EOL;
                }
//
                $yesaddDetail = DB::table('statistics')->where(['firstvisit'=>1,'addtime'=>$yesterday,'channel'=>$channel])->get();
                $data[$channel]['yesterdaynewuser'] = $yesadd;
                $uidArr1=array();

                if(!empty($yesaddDetail)){
                    foreach ($yesaddDetail as $k=>$v){
                        $uidArr1[$k] = "'".$v->uid."'";
                    }

                    $uid = implode(',',$uidArr1);

                    $onelater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0 and channel=$channel and addtime='".$today."'";
                    $onelater_res = DB::selectone($onelater_query);
                    if(empty($yesadd)){
                        $data[$channel]['onedaykeep'] = 0;
                        $str .= "*LC：0（0%）".PHP_EOL;
                    }else{
                        $data[$channel]['onedaykeep'] = round($onelater_res->num/$yesadd*100,2).'%';
                        $str .= "*LC：".$onelater_res->num."（".round($onelater_res->num/$yesadd*100,2).'%'."）".PHP_EOL;
                    }

                }else{
                    $str .= "*LC：0（0%）".PHP_EOL;
                }


                $num_today = DB::table('novel_user')->where('userfrom','>',1)->where('channel',$channel)->where('regtime','>=',$today.' 00:00:00')->where('regtime','<=',$today.' 23:59:59')->count();
                $num_yes = DB::table('novel_user')->where('userfrom','>',1)->where('channel',$channel)->where('regtime','>=',$yesterday.' 00:00:00')->where('regtime','<=',$yesterday.' 23:59:59')->count();

                if($num_today-$num_yes >0){
                    $str .= "*LB：".$num_today."（上升 ↑ ".$num_today-$num_yes."）".PHP_EOL;
                }else{
                    $str .= "*LB：".$num_today."（下降 ↓ ".abs($num_today-$num_yes)."）".PHP_EOL;
                }

            }


        }else{

            $data = [];

        }


//        return json_encode(['status'=>1,'msg'=>'成功！！！','data'=>['message'=>$str,'a'=>$today,'b'=>$yesterday,'c'=>$hour,'d'=>$hourend,'e'=>$hourtime,'f'=>$hourtimeend]]);
        return json_encode(['status'=>1,'msg'=>'成功！！！','data'=>['message'=>$str]]);

    }


    public function postGetEachWeekData(Request $request){
        $daytime = $request->input('daytime', '');
        if(empty($daytime)){
            return json_encode(['status'=>0,'message'=>'参数不全']);
        }
        $today = date('Y-m-d',$daytime);
        $temp = strtotime($today)+6*24*3600;
        $todayend = date("Y-m-d",$temp);

        $yestempbegin = strtotime($today)-7*24*3600;
        $yesterday = date("Y-m-d",$yestempbegin);

        $yestempend = strtotime($today)-24*3600;
        $yesterdayend = date("Y-m-d",$yestempend);

//        return json_encode(['status'=>1,'message'=>'成功','today'=>$today,'todayend'=>$todayend,'yesterday'=>$yesterday,'yesterdayend'=>$yesterdayend]);

        $str = "";
        $str .="#项目：【智搜小说】数据统计".PHP_EOL;
        $str .="#统计时间：".$today."~".$todayend."【周】".PHP_EOL;
        $str .="#全部渠道".PHP_EOL;
        $todaydown = DB::table('statistics')->where('firstvisit',2)->where('addtime','>=',$today)->where('addtime','<=',$todayend)->count();
        $yesdown = DB::table('statistics')->where('firstvisit',2)->where('addtime','>=',$yesterday)->where('addtime','<=',$yesterdayend)->count();

        $downtemp = $todaydown-$yesdown;

        if($downtemp >0){
            $str .= "*DL：".$todaydown."（上升 ↑ ".$downtemp."）".PHP_EOL;
        }else{
            $str .= "*DL：".$todaydown."（下降 ↓ ".abs($downtemp)."）".PHP_EOL;
        }


        $data['todaydown'] = $todaydown;
        $data['downcompare'] = $todaydown-$yesdown;

//        $todaydownadd = DB::table('statistics')->where(['firstvisit'=>1,'addtime'=>$today])->count();
        $todaydownadd = DB::table('statistics')->where('firstvisit',1)->where('addtime','>=',$today)->where('addtime','<=',$todayend)->count();
//        $yesadd = DB::table('statistics')->where(['firstvisit'=>1,'addtime'=>$yesterday])->count();
        $yesadd = DB::table('statistics')->where('firstvisit',1)->where('addtime','>=',$yesterday)->where('addtime','<=',$yesterdayend)->count();
        $data['todaynewuser'] = $todaydownadd;
        $data['newusercompare'] = $todaydownadd-$yesadd;

        $downaddtemp = $todaydownadd-$yesadd;

        if($downaddtemp >0){
            $str .= "*XZ：".$todaydownadd."（上升 ↑ ".$downaddtemp."）".PHP_EOL;
        }else{
            $str .= "*XZ：".$todaydownadd."（下降 ↓ ".abs($downaddtemp)."）".PHP_EOL;
        }



        $todayquery = "select count(DISTINCT(uid)) as num  from statistics where   novelid>0 and addtime>='".$today."' and addtime <= '".$todayend."'";//活跃 打开了小说的用户数
        $todayactive = DB::selectone($todayquery);
        $yesquery = "select count(DISTINCT(uid)) as num  from statistics where   novelid>0 and addtime>='".$yesterday."' and addtime <= '".$yesterdayend."'";
        $yesactive = DB::selectone($yesquery);
        $data['todayactive'] = $todayactive->num;
        $data['activecompare'] = $todayactive->num-$yesactive->num;


        $activetemp = $todayactive->num-$yesactive->num;

        if($activetemp >0){

            $str .= "*WAU：".$todayactive->num."（上升 ↑ ".$activetemp."）".PHP_EOL;

        }else{
            $str .= "*WAU：".$todayactive->num."（下降 ↓ ".abs($activetemp)."）".PHP_EOL;
        }


        $yesactiveuserquery = "select DISTINCT(uid) as uuid  from statistics where  novelid>0 and addtime>='".$yesterday."' and addtime <= '".$yesterdayend."'";
        $yesactiveuser = DB::select($yesactiveuserquery);
        if(!empty($yesactiveuser)){
            $uidArr2=array();
            foreach ($yesactiveuser as $k=>$v){
                $uidArr2[$k] = $v->uuid;
            }
            $uuid = implode(',',$uidArr2);
            $todayactiveuserquery = "select count(DISTINCT(uid)) as num  from statistics where  novelid>0 and uid in ($uuid)  and addtime>='".$today."' and addtime <= '".$todayend."'";
            $todayactiveuser = DB::selectone($todayactiveuserquery);
            $str .= "*WAULC：".$todayactiveuser->num."（".round($todayactiveuser->num/$yesactive->num*100,2).'%'."）".PHP_EOL;
        }else{
            $str .= "*WAULC：0（0%）".PHP_EOL;
        }
        
        


        $appstartquery = "select count(*) as num from statistics where module='0' and novelid=0  and addtime>='".$today."' and addtime <= '".$todayend."'";
        $todayappstart = DB::selectone($appstartquery);
        $yesappstartquery = "select count(*) as num from statistics where module='0' and novelid=0  and addtime>='".$yesterday."' and addtime <= '".$yesterdayend."'";
        $yesappstart = DB::selectone($yesappstartquery);

        $data['todayappstart'] = $todayappstart->num;
        $data['appstartcompare'] = $todayactive->num-$yesappstart->num;

        $appstartemp = $todayactive->num-$yesappstart->num;
        if($appstartemp >0){
            $str .= "*QD：".$todayappstart->num."（上升 ↑ ".$appstartemp."）".PHP_EOL;
        }else{
            $str .= "*QD：".$todayappstart->num."（下降 ↓ ".abs($appstartemp)."）".PHP_EOL;
        }


//        $yesaddDetail = DB::table('statistics')->where(['firstvisit'=>1,'addtime'=>$yesterday])->get();
        $yesaddDetail = DB::table('statistics')->where('firstvisit',1)->where('addtime','>=',$yesterday)->where('addtime','<=',$yesterdayend)->get();

        if(!empty($yesaddDetail)){
            foreach ($yesaddDetail as $k=>$v){
                $uidArr1[$k] = "'".$v->uid."'";
            }

            $uid = implode(',',$uidArr1);

            $onelater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0   and addtime='".$today."'";
            $onelater_res = DB::selectone($onelater_query);
            if(empty($yesadd)){

                $str .= "*XZLC：0（0%）".PHP_EOL;
            }else{

                $str .= "*XZLC：".$onelater_res->num."（".round($onelater_res->num/$yesadd*100,2).'%'."）".PHP_EOL;
            }

        }else{
            $str .= "*XZLC：0（0%）".PHP_EOL;
        }


        $num_today = DB::table('novel_user')->where('userfrom','>',1)->where('regtime','>=',$today.' 00:00:00')->where('regtime','<=',$todayend.' 23:59:59')->count();
        $num_yes = DB::table('novel_user')->where('userfrom','>',1)->where('regtime','>=',$yesterday.' 00:00:00')->where('regtime','<=',$yesterdayend.' 23:59:59')->count();

        $lbtemp = $num_today-$num_yes;
        if($lbtemp >0){
            $str .= "*LB：".$num_today."（上升 ↑ ".$lbtemp."）".PHP_EOL;
        }else{
            $str .= "*LB：".$num_today."（下降 ↓ ".abs($lbtemp)."）".PHP_EOL;
        }


        return json_encode(['status'=>1,'msg'=>'成功！！！','data'=>['message'=>$str]]);

    }

    public function postGetEachMonthData(Request $request){
        $daytime = $request->input('daytime', '');
        if(empty($daytime)){
            return json_encode(['status'=>0,'message'=>'参数不全']);
        }
        $today = date('Y-m-01',$daytime);
//        $temp = strtotime($today)+6*24*3600;
        $todayend = date("Y-m-t",time());


        $yesterday = date('Y-m-01',strtotime('-1 month'));
//        $yesterday = date("Y-m-d",$yestempbegin);

//        $yestempend = strtotime($today)-24*3600;
        $yesterdayend = date("Y-m-t",strtotime('-1 month'));

//        return json_encode(['status'=>1,'message'=>'成功','today'=>$today,'todayend'=>$todayend,'yesterday'=>$yesterday,'yesterdayend'=>$yesterdayend]);
//        return json_encode(['status'=>1,'msg'=>'成功！！！','data'=>['message'=>$yesterday,'a'=>$yesterdayend]]);

        $str = "";
        $str .="#项目：【智搜小说】数据统计".PHP_EOL;
        $str .="#统计时间：".date('Y-m',time())."【月】".PHP_EOL;
        $str .="#全部渠道".PHP_EOL;
        $todaydown = DB::table('statistics')->where('firstvisit',2)->where('addtime','>=',$today)->where('addtime','<=',$todayend)->count();
        $yesdown = DB::table('statistics')->where('firstvisit',2)->where('addtime','>=',$yesterday)->where('addtime','<=',$yesterdayend)->count();

        $downtemp = $todaydown-$yesdown;

        if($downtemp >0){
            $str .= "*DL：".$todaydown."（上升 ↑ ".$downtemp."）".PHP_EOL;
        }else{
            $str .= "*DL：".$todaydown."（下降 ↓ ".abs($downtemp)."）".PHP_EOL;
        }


        $data['todaydown'] = $todaydown;
        $data['downcompare'] = $todaydown-$yesdown;

//        $todaydownadd = DB::table('statistics')->where(['firstvisit'=>1,'addtime'=>$today])->count();
        $todaydownadd = DB::table('statistics')->where('firstvisit',1)->where('addtime','>=',$today)->where('addtime','<=',$todayend)->count();
//        $yesadd = DB::table('statistics')->where(['firstvisit'=>1,'addtime'=>$yesterday])->count();
        $yesadd = DB::table('statistics')->where('firstvisit',1)->where('addtime','>=',$yesterday)->where('addtime','<=',$yesterdayend)->count();
        $data['todaynewuser'] = $todaydownadd;
        $data['newusercompare'] = $todaydownadd-$yesadd;

        $addtemp = $todaydownadd-$yesadd;
        if($addtemp >0){
            $str .= "*XZ：".$todaydownadd."（上升 ↑ ".$addtemp."）".PHP_EOL;
        }else{
            $str .= "*XZ：".$todaydownadd."（下降 ↓ ".abs($addtemp)."）".PHP_EOL;
        }



        $todayquery = "select count(DISTINCT(uid)) as num  from statistics where   novelid>0 and addtime>='".$today."' and addtime <= '".$todayend."'";//活跃 打开了小说的用户数
        $todayactive = DB::selectone($todayquery);
        $yesquery = "select count(DISTINCT(uid)) as num  from statistics where   novelid>0 and addtime>='".$yesterday."' and addtime <= '".$yesterdayend."'";
        $yesactive = DB::selectone($yesquery);
        $data['todayactive'] = $todayactive->num;
        $data['activecompare'] = $todayactive->num-$yesactive->num;


        $activetemp = $todayactive->num-$yesactive->num;

        if($activetemp >0){

            $str .= "*MAU：".$todayactive->num."（上升 ↑ ".$activetemp."）".PHP_EOL;

        }else{
            $str .= "*MAU：".$todayactive->num."（下降 ↓ ".abs($activetemp)."）".PHP_EOL;
        }



        $appstartquery = "select count(*) as num from statistics where module='0' and novelid=0  and addtime>='".$today."' and addtime <= '".$todayend."'";
        $todayappstart = DB::selectone($appstartquery);
        $yesappstartquery = "select count(*) as num from statistics where module='0' and novelid=0  and addtime>='".$yesterday."' and addtime <= '".$yesterdayend."'";
        $yesappstart = DB::selectone($yesappstartquery);
        $data['todayappstart'] = $todayappstart->num;
        $data['appstartcompare'] = $todayactive->num-$yesappstart->num;

        $appstartemp = $todayactive->num-$yesappstart->num;
        if($appstartemp >0){
            $str .= "*QD：".$todayappstart->num."（上升 ↑ ".$appstartemp."）".PHP_EOL;
        }else{
            $str .= "*QD：".$todayappstart->num."（下降 ↓ ".abs($appstartemp)."）".PHP_EOL;
        }


//        $yesaddDetail = DB::table('statistics')->where(['firstvisit'=>1,'addtime'=>$yesterday])->get();
        $yesaddDetail = DB::table('statistics')->where('firstvisit',1)->where('addtime','>=',$yesterday)->where('addtime','<=',$yesterdayend)->get();

        if(!empty($yesaddDetail)){
            foreach ($yesaddDetail as $k=>$v){
                $uidArr1[$k] = "'".$v->uid."'";
            }

            $uid = implode(',',$uidArr1);

            $onelater_query = "select count(distinct(uid)) as num from statistics where uid in ({$uid}) and novelid>0   and addtime='".$today."'";
            $onelater_res = DB::selectone($onelater_query);
            if(empty($yesadd)){

                $str .= "*XZLC：0（0%）".PHP_EOL;
            }else{

                $str .= "*XZLC：".$onelater_res->num."（".round($onelater_res->num/$yesadd*100,2).'%'."）".PHP_EOL;
            }

        }else{
            $str .= "*XZLC：0（0%）".PHP_EOL;
        }


        $yesactiveuserquery = "select DISTINCT(uid) as uuid  from statistics where  novelid>0 and addtime>='".$yesterday."' and addtime <= '".$yesterdayend."'";
        $yesactiveuser = DB::select($yesactiveuserquery);
        if(!empty($yesactiveuser)){
            $uidArr2=array();
            foreach ($yesactiveuser as $k=>$v){
                $uidArr2[$k] = $v->uuid;
            }
            $uuid = implode(',',$uidArr2);
            $todayactiveuserquery = "select count(DISTINCT(uid)) as num  from statistics where  novelid>0 and uid in ($uuid) and addtime>='".$today."' and addtime <= '".$todayend."'";
            $todayactiveuser = DB::selectone($todayactiveuserquery);
            $str .= "*MAULC：".$todayactiveuser->num."（".round($todayactiveuser->num/$yesactive->num*100,2).'%'."）".PHP_EOL;
        }else{
            $str .= "*MAULC：0（0%）".PHP_EOL;
        }



        $num_today = DB::table('novel_user')->where('userfrom','>',1)->where('regtime','>=',$today.' 00:00:00')->where('regtime','<=',$todayend.' 23:59:59')->count();
        $num_yes = DB::table('novel_user')->where('userfrom','>',1)->where('regtime','>=',$yesterday.' 00:00:00')->where('regtime','<=',$yesterdayend.' 23:59:59')->count();

        $lbtemp = $num_today-$num_yes;
        if($lbtemp >0){
            $str .= "*LB：".$num_today."（上升 ↑ ".$lbtemp."）".PHP_EOL;
        }else{
            $str .= "*LB：".$num_today."（下降 ↓ ".abs($lbtemp)."）".PHP_EOL;
        }


        return json_encode(['status'=>1,'msg'=>'成功！！！','data'=>['message'=>$str]]);

    }





    /**
     * 用户在登录和分享app时调用
     * @param Request $request
     * @return false|string
     */
    public function postAddEssayTimes(Request $request){
        $uuid = $request->input('uuid', '');
        $type = $request->input('type', '');//1:登录；2：分享app

        $login = DB::table('read_times_set')->where('type',1)->first();
        $shareApp = DB::table('read_times_set')->where('type',2)->first();



        if($type == 1){
            $loginTimes = $login->times;
            $data = array(
                'uuid'=>$uuid,
                'addtime'=>time(),
                'frequency'=>$loginTimes,
                'type'=>$type,
                'kind'=>1,
            );
            DB::table('essay_read_times')->insert($data);
        }elseif ($type == 2){
            $shareAppTimes = $shareApp->times;
            $extra = $shareApp->extra;
            $end = strtotime(date('Ymd'))+ 86400;
            $begin = strtotime(date('Ymd'));
            $ret = DB::table('essay_read_times')->where('uuid',$uuid)->where('type',2)->where('addtime','>',$begin)->where('addtime','<',$end)->count();
            if($ret < $extra){
                $data = array(
                    'uuid'=>$uuid,
                    'addtime'=>time(),
                    'frequency'=>$shareAppTimes,
                    'type'=>$type,
                    'kind'=>1,
                );
                DB::table('essay_read_times')->insert($data);
            }
        }
        return json_encode(['status'=>1,'message'=>'成功']);
    }

    /**
     * 用户阅读短文时调用
     * @param Request $request
     * @return false|string
     */
    public function postEssayClick(Request $request){
        $uuid = $request->input('uuid', '');
        $data = DB::table('essay_read_times')->where('uuid',$uuid)->sum('frequency');

        if($data > 0){
            $data = array(
                'uuid'=>$uuid,
                'addtime'=>time(),
                'frequency'=>-1,
                'type'=>'',
                'kind'=>2,
            );
            DB::table('essay_read_times')->insert($data);
            return json_encode(['status'=>1,'message'=>'成功','data'=>array('read'=>true)]);
        }

        return json_encode(['status'=>1,'message'=>'可用阅读次数不足','data'=>array('read'=>false)]);
    }


    /**
     * 获取短文的消耗和收获次数列表
     * @param Request $request
     * @return false|string
     */
    public function postGetExpensesList(Request $request){
        $uuid = $request->input('uuid', '');
        $page = intval($request->input('page', 1));
        $size = $request->input('size', 20);
        $EssayReadModel = new EssayReadModel();
        $data = $EssayReadModel->getList($uuid,$page,$size);
        if(!empty($data)){
            foreach ($data as $k=>$v){
                if($v->type == 0){
                    $data[$k]->name = '阅读短文';
                }elseif ($v->type == 1){
                    $data[$k]->name = '登录';
                }elseif ($v->type == 2){
                    $data[$k]->name = '分享app';
                }elseif ($v->type == 3){
                    $data[$k]->name = '分享用户注册成功';
                }
                if($v->frequency >0){
                    $data[$k]->frequency = "+".$v->frequency;
                }else{
                    $data[$k]->frequency = "-".abs($v->frequency);
                }
                $data[$k]->addtime = date('Y-m-d H:i:s',$v->addtime);
            }
        }
        return json_encode(['status'=>1,'message'=>'成功','data'=>$data]);

    }
    public function createSpreadCode()
    {
        $arr = array_merge(range(0, 9), range('A', 'Z'));
        $invitecode = '';$arr_len = count($arr);
        for ($i = 0; $i < 6; $i++) {
            $rand = mt_rand(0, $arr_len - 1);
            $invitecode .= $arr[$rand];
        }
        return $invitecode ;

    }


    public function postGetUpdateInfo(Request $request){

        $con = $request->input('content', '');

        $content = json_decode($con,1);
        $novelid = $content['novel_id'];
        $chapter = $content['chapter'];

        $chapter_list = $content['chapter_list'];

        $NvNovelModel = new NvNovelModel();
        $NvNovelModel->updateChapter($novelid,$chapter);


        $ChapterModel = new ChapterInsertModel();

        $ChapterModel->insertChapter($novelid,$chapter_list);
        return json_encode(['status'=>1,'message'=>'成功']);
    }


    public function postSendToken(){
        require dirname(dirname(dirname(dirname(__FILE__))))."/Qiniu/phpsdk/autoload.php";

        $accessKey = 'zj0Y_E4ppmM3MwES4iuFbYk_DssjnKuyfMQhKJiy';
        $secretKey = 'aFhsKB5gOWoSyKossrUkxkUeq-GxOP0sCDBstWkd';
        $auth = new Auth($accessKey, $secretKey);
        $bucket = 'novel_php';


        // 生成上传Token

        $expires = 3600;
        $returnBody = '{"key":"$(key)","hash":"$(etag)","fsize":$(fsize),"name":"$(x:name)"}';
        $policy = array(
            'returnBody' => $returnBody
        );
        $token = $auth->uploadToken($bucket, null, $expires, $policy, true);
        $data['token'] = $token;
        return json_encode(['status'=>1,'message'=>'成功','data'=>$data]);
    }





}
