<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\models\BaseModel;
use App\models\NovelModel;
use Illuminate\Support\Facades\Config;
use App\Libs\Caches\lib\libredis;
//use Illuminate\Support\Facades\Redis;
use DB;

class WebController extends ApiController
{
    /**
     * 获取分类数据
     */
    public function postCates(Request $request)
    {
        $confredis = Config::get('database.redis.default');
        $redis = new libredis([$confredis['host'], $confredis['port']]);

        $cates = $redis->get("cates");
        if ($cates) {
            $data = json_decode($cates,true);
        } else {
            $data = BaseModel::factory('category')->select(['cateid','name'])->orderBy('sort', 'ASC')->get();
            $redis->set("web|cates",json_encode($data),false,true,3600*2);
        }

        return json_encode(['status'=>1, 'message'=>'成功', 'data'=>$data]);
    }

    /**
     * 根据分类前多少随机展示多少个
     */
    public function postCatesRecommended(Request $request)
    {
        $start = $request->input('start', 0);
        $limit = $request->input('limit', 10);
        $num = $request->input('num', 10);

        $novelModel = new NovelModel();
        $novelid = $novelModel->categoryTop($start,$limit,$num);
        $data = $novelModel->getNovels($novelid);

        return json_encode(['status'=>1, 'message'=>'成功', 'data'=>$data]);
    }

    /**
     * 根据阅读量获取小说,多少天前多少名
     */
    public function postRead(Request $request)
    {
        $day = $request->input('day', 10);
        $num = $request->input('num', 10);

        $novelModel = new NovelModel();
        $novelid = $novelModel->readByDay($day,$num);
        $data = $novelModel->getNovels($novelid);

        return json_encode(['status'=>1, 'message'=>'成功', 'data'=>$data]);
    }

    /**
     * 根据阅读量获取小说,多少天前多少名
     */
    public function postStatus(Request $request)
    {
        $status = $request->input('status', 0);
        $num = $request->input('num', 10);
        $status = $status == 0 ? "连载中" : "已完成";

        $novelModel = new NovelModel();
        $novelid = $novelModel->novelidByStatus($status,$num);
        $data = $novelModel->getNovels($novelid);

        return json_encode(['status'=>1, 'message'=>'成功', 'data'=>$data]);
    }

    /**
     * 获取广告
     */
    public function postAdvert(Request $request)
    {
        $platform = $request->input('platform', '1');

        $time = time();
        $data = [];
        $advert = BaseModel::factory('web_advert')->select(['title','url','img','type'])
        ->where('start', '<=' , $time)
        ->where('end', '>=' , $time)
        ->where('status', 1)
        ->where('platform', $platform)
        ->orderBy('sort', 'ASC')->get()->toArray();
        foreach ($advert as $v) {
            $data[$v['type']][] = $v;
        }
        return json_encode(['status'=>1, 'message'=>'成功', 'data'=>$data]);
    }

    /**
     * 获取分类数据
     */
    public function postGetNovelByCate(Request $request)
    {
        $cateid = $request->input('cateid', 1);
        $page = intval($request->input('page', 1));
        $size = $request->input('size', 20);

        $data = BaseModel::factory('novel')->select(['novelid','cateid','name','status','chapter','author','img'])
        ->where('cateid', $cateid)
        ->orderBy('sort', 'ASC')->orderBy('add_time', 'DESC')->paginate($size)->toArray();

        self::getCateName($data['data']);
        $p = ['total'=>$data['total'], 'curpage'=>$page];
        return json_encode(['status'=>1, 'message'=>'成功', 'page'=>$p, 'data'=>$data['data']]);
    }

    /**
     * 根据名字获取数据
     */
    public function postGetNovelByName(Request $request)
    {
        $name = $request->input('name', '');

        $data = BaseModel::factory('novel')->select(['novelid','cateid','name','status','chapter','author','img'])
        ->where('name', 'like', '%'.$name.'%')
        ->where('author', 'like', '%'.$name.'%')
        ->where('summary', 'like', '%'.$name.'%')
        ->orderBy('sort', 'ASC')->orderBy('add_time', 'DESC')->skip(0)->take(20)->get()->toArray();

        foreach ($data as $value) {
            $sql = "update novel set search=search+1 where novelid={$value['novelid']}";
            DB::select($sql); //查询 
        }

        self::getCateName($data);
        return json_encode(['status'=>1, 'message'=>'成功', 'data'=>$data]);
    }

    /**
     * 随机搜索关键字,随机15个
     */
    public function postSearchHistory(Request $request)
    {
        $novelModel = new NovelModel();
        $novelid = $novelModel->novelidBySearch(15);
        $data = $novelModel->getNovels($novelid);

        return json_encode(['status'=>1, 'message'=>'成功', 'data'=>$data]);
    }

    /**
     * 获取目录数据
     */
    public function postGetChapter(Request $request)
    {
        $novelid = $request->input('novelid', 1);
        $sourceid = $request->input('sourceid', 1);
        $page = intval($request->input('page', 1));
        $size = $request->input('size', 100);

        $source = BaseModel::factory('novel_source')->select(['sourceid','chapter_name'])
        ->where('sourceid', $sourceid)
        ->first();

        $t = $source->chapter_name . intval($novelid / 10000);

        $data = BaseModel::factory($t)->select(['novelid','chapter','name'])
        ->where('novelid', $novelid)
        ->orderBy('chapter', 'ASC')->paginate($size)->toArray();

        $p = ['total'=>$data['total'], 'curpage'=>$page];
        return json_encode(['status'=>1, 'message'=>'成功', 'page'=>$p, 'data'=>$data['data']]);
    }

    /**
     * 获取小说数据
     */
    public function postGetNovelById(Request $request)
    {
        $novelid = $request->input('novelid', 1);

        $data = BaseModel::factory('novel')->select(['novelid','name','summary','author','img', 'status', 'chapter','sourceid','refresh_time','cateid'])
        ->whereIn('novelid', explode(',',$novelid))
        ->get()->toArray();

        $source = BaseModel::factory('novel_source')->select(['sourceid','name','url','chapter_name'])->get();
        foreach ($source as $key => $value) {
            $s[$value->sourceid] = $value;
        }
        foreach ($data as $key => $value) {
            $sources = explode(',', $value['sourceid']);
            $data[$key]['chapter'] = $data[$key]['chapter_name'] = '';
            foreach ($sources as $v) {
                $c = $s[$v];
                $t = $c->chapter_name . intval($novelid / 10000);
                $chapter = BaseModel::factory($t)->select(['name','chapter'])
                ->where('novelid', $novelid)
                ->orderBy('chapter','desc')
                ->first();

                $c->chapter_name = $chapter['name'];
                $c->refresh_time = $value['refresh_time'];
                $c->chapter = $chapter['chapter'];
                $data[$key]['source'][] = $c;

                if (!$data[$key]['chapter'] || $data[$key]['chapter'] < $c->chapter) {
                    $data[$key]['chapter'] = $c->chapter;
                    $data[$key]['chapter_name'] = $c->chapter_name;
                }
            }

            $sql ="SELECT novelid,name,img from novel where cateid={$value['cateid']} order by rand() desc limit 0,3";
            $data[$key]['random'] = DB::select($sql); //查询 
        }


        return json_encode(['status'=>1, 'message'=>'成功', 'data'=>$data]);
    }

    /**
     * 获取小说数据
     */
    public function postGetContent(Request $request)
    {
        $novelid = $request->input('novelid', 1);
        $chapterid = $request->input('chapter', 1);
        $sourceid = $request->input('sourceid', 1);

        $source = BaseModel::factory('novel_source')->select(['sourceid','chapter_name','start','end'])
        ->where('sourceid', $sourceid)
        ->first();

        $t = $source->chapter_name . intval($novelid / 10000);
        $chapter = BaseModel::factory($t)->select(['name','url'])
        ->where('novelid', $novelid)
        ->where('chapter', $chapterid)
        ->first();

        if ($chapterid == 1) {
            $sql = "update novel set `read`=`read`+1 where novelid={$novelid}";
            DB::select($sql); //查询 
        }

        if ($chapter) {
            $path = env('CONTENT_PATH').$t.'_'.$novelid.'_'.$chapterid.'.txt';
            if (file_exists($path)) {
                $c = file_get_contents($path);
            } else {
                $error = 0;
                for ($i=0; $i < 5 ; $i++) { 
                    $sql ="select ip from `ip` order by rand() limit 1"; //SQL语句
                    $ip = DB::select($sql);
                    $ip = $ip[0]->ip;
                    $content = curlGet($chapter->url, $ip);
                    if (strpos($content, 'error-wrapper') !== false) {
                        $error=1;
                        sleep(1);
                        continue;
                    }
                    $s = strpos($content, $source->start) + strlen($source->start);
                    $e = strrpos($content, $source->end);
                    $c = substr($content, $s, $e-$s);
                    if (!strpos($content, '<meta charset="utf-8">')) {
                        $c=mb_convert_encoding($c, "UTF-8", "GB2312"); 
                    }

                    if (mb_strpos($c, "您访问太过频繁") || empty($c)) {
                        $error = 1;
                        sleep(1);
                        continue;
                    }

                    //$c = preg_replace("/第.*章.*<br \/>/", "", $c);
                    $c=str_replace('<br />',"\r\n", $c);
                    $c=str_replace('<br/>',"\r\n", $c);
                    $c=str_replace("<br\/>","\r\n", $c);
                    $c=str_replace('&nbsp;',' ', $c);
                    $c=str_replace('</div>','', $c);
                    $c=str_replace('<div class="ad">','', $c);
                    $c=str_replace("\r\n\r\n\r\n", "\r\n", $c);

                    if ($sourceid != 2)
                        $c = $chapter->name . $c;

                    if (mb_strpos($c,'章节数据正在同步中') === false && $c) {
                        $error = 0;
                        file_put_contents($path,$c);
                        break;
                    }
                }
                if ($error || empty($c)) {
                   return json_encode(['status'=>0, 'message'=>'下载失败']);
               }
           }

           $data['title'] = $chapter->name;
           $data['content'] = $c;
           $data['url'] = $chapter->url;
           return json_encode(['status'=>1, 'message'=>'成功', 'data'=>$data]);
       }
       return json_encode(['status'=>0, 'message'=>'失败']);
   }

    public function getCateName(&$data) {
        $sql = "select name,cateid from category";
        $name = DB::select($sql);
        foreach ($name as $key => $value) {
            $names[$value->cateid] = $value->name;
        }

        foreach ($data as $key => $value) {
           if (isset($names[$value['cateid']])) {
            $data[$key]['catename'] = $names[$value['cateid']];
        }
    }
}
}
