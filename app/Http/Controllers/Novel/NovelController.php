<?php

namespace App\Http\Controllers\Novel;

use App\Role;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Facades\Auth;
use App\models\BaseModel;
use App\models\NvNovelModel;
use App\models\CateModel;

use Qiniu\Auth as Auths;
use Qiniu\Storage\UploadManager;


use Request as Ret;

class NovelController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
//    public function getIndex1()
//    {
//        $request = Request();
//        $novel = DB::table('novel');
//        $where = [];
//        $novelid = $request->input('novelid', '');
//        if ($novelid && is_numeric($novelid)) {
//            $where['novelid'] = $novelid;
//            $novel = $novel->where('novelid', $novelid);
//        }
//        $name = trim($request->input('name', ''));
//        if ($name && !empty($name)) {
//            $where['name'] = $name;
//            $novel = $novel->where('name', 'like', '%' . $name . '%');
//        }
//
//        $cateid = trim($request->input('cateid', 0));
//        $where['cateid'] = $cateid;
//        if ($cateid && !empty($cateid)) {
//            $novel = $novel->where('cateid', $cateid);
//        }
//        $novels = $novel->orderBy('sort', 'asc')->orderBy('read', 'desc')->orderBy('search', 'desc')->orderBy('add_time', 'DESC')->paginate(23);
//
//        return view('novel/novel', ['page_title' => '小说管理', 'novels' => $novels, 'where' => $where, 'novelid' => $novelid, 'name' => $name, 'cates' => getCates()]);
//    }

    public function getIndex(){
        $request = Request();
        $cate = trim($request->input('cateid', 0));
        $sort = trim($request->input('sort', 1));
        $name = trim($request->input('name', ''));
        $novelid = $request->input('novelid', '');
        $CateModel = new CateModel();
        $NvNovelModel = new NvNovelModel();

        $allCate = $CateModel->getCate();//查询有哪些小说分类

        $where = [];
        $where['cateid']=0;
        $where['sort']=$sort;
        if($cate && !empty($cate)){
            $where['cateid'] = $cate;
        }
        if($name && !empty($name)){
            $where['name'] = $name;
        }
        if($novelid && !empty($novelid)){
            $where['novelid'] = $novelid;
        }

        $novel = $NvNovelModel->getNovelByCondition($cate,$name,$novelid);
        $novel = $novel->selectRaw('(click_nums+click_nums_real) as click_nums_all')->selectRaw('(down_nums+down_nums_real) as down_nums_all')->selectRaw('(shelf_nums+shelf_nums_real) as shelf_nums_all')->where('is_finished',1);
        switch ($sort){
            case 1:
                $novels = $novel ->orderBy('id', 'asc')->paginate(23);
                break;
            case 2:
                $novels = $novel ->orderBy('sort', 'desc')->paginate(23);
                break;
            case 3:
                $novels = $novel  ->orderBy('click_nums_all', 'desc')->paginate(23);
                break;
            case 4:
                $novels = $novel  ->orderBy('down_nums_all', 'desc')->paginate(23);
                break;
            case 5:
                $novels = $novel  ->orderBy('shelf_nums_all', 'desc')->paginate(23);
                break;
            case 6:
                $novels = $novel ->orderBy('click_nums_real', 'desc')->paginate(23);
                break;
            case 7:
                $novels = $novel->orderBy('down_nums_real', 'desc')->paginate(23);
                break;
            case 8:
                $novels = $novel ->orderBy('shelf_nums_real', 'desc')->paginate(23);
                break;
            case 9:
                $novels = $novel ->orderBy('updated_at', 'desc')->paginate(23);
                break;
            case 10:
                $novels = $novel ->orderBy('created_at', 'desc')->paginate(23);
                break;
            case 11:
                $novels = $novel ->orderBy('chapter', 'desc')->paginate(23);
                break;
        }

        foreach ($novels as $k=>$v){
            $nove_line_cate_id = $v->nove_line_cate_id;
            $catename = $CateModel->getCateName($nove_line_cate_id);
            if(!empty($catename)){
                $novels[$k]->cate=$catename->name;
            }else{
                $novels[$k]->cate="未设置";
            }

        }
        return view('novel/novel', ['page_title' => '小说管理', 'novels' => $novels, 'where' => $where , 'novelid' => $novelid, 'name' => $name, 'cates' => $allCate,'cateid'=>$cate,'query'=>$novel]);

    }

    public function postOperateStatus(){
        $request = Request();
        $id = $request->input('id', '');
        $status = $request->input('status', '');
        $NvNovelModel = new NvNovelModel();
        $NvNovelModel->operate($id,$status);
        return json_encode(['code'=>1]);
    }

    public function UploadImg($file){
        $filePath = $file->getRealPath();   //临时文件的绝对路径

        require_once dirname(dirname(dirname(dirname(__FILE__))))."/Qiniu/phpsdk/autoload.php";
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
        return  "http://novelpic.fjhwsp.com/".$ret['key'];
    }

    public function postEditNvnovel(){
        $request = Request();
        $novelid = $request->input('novelid', '');
        $click_nums = $request->input('click_nums', 0);
        $down_nums = $request->input('down_nums', 0);
        $shelf_nums = $request->input('shelf_nums', 0);
        $cateid = $request->input('cateid', 0);
        $summary = $request->input('summary', '');

        $cover = '';
        $file = $request->file('cover');
        if ($file && $file->isValid()) {

//            $filePath = $file->getRealPath();   //临时文件的绝对路径
//
//            require dirname(dirname(dirname(dirname(__FILE__))))."\Qiniu\phpsdk\autoload.php";
//            $accessKey = 'zj0Y_E4ppmM3MwES4iuFbYk_DssjnKuyfMQhKJiy';
//            $secretKey = 'aFhsKB5gOWoSyKossrUkxkUeq-GxOP0sCDBstWkd';
//            $auth = new Auths($accessKey, $secretKey);
//            $bucket = 'novel_php';
//
//            // 生成上传Token
//
//            $expires = 3600;
//            $returnBody = '{"key":"$(key)","hash":"$(etag)","fsize":$(fsize),"name":"$(x:name)"}';
//            $policy = array(
//                'returnBody' => $returnBody
//            );
//            $token = $auth->uploadToken($bucket, null, $expires, $policy, true);
//
//            // 构建 UploadManager 对象
//            $uploadMgr = new UploadManager();
//
//            // 上传到七牛后保存的文件名
//            $originalName = $file->getClientOriginalName(); // 文件原名
//            $key     = uniqid() . $originalName;
//            // 初始化 UploadManager 对象并进行文件的上传。
//            $uploadMgr = new UploadManager();
//            // 调用 UploadManager 的 putFile 方法进行文件的上传。
//            list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
//            $cover = "http://novelpic.fjhwsp.com/".$ret['key'];

            $cover = $this->UploadImg($file);

        }


        $NvNovelModel = new NvNovelModel();
        $ret = $NvNovelModel->updateNovelInfo($novelid,$click_nums,$down_nums,$shelf_nums,$cover,$cateid,$summary);
        return redirect($_SERVER['HTTP_REFERER']);

    }
    public function postEditNvnovel1(){
        $request = Request();
        $novelid = $request->input('novelid', '');
        $click_nums = $request->input('click_nums', 0);
        $down_nums = $request->input('down_nums', 0);
        $shelf_nums = $request->input('shelf_nums', 0);
        $cateid = $request->input('cateid', 0);
        $summary = $request->input('summary', '');

        $cover = '';
        $file = $request->file('cover');
        if ($file && $file->isValid()) {
            // 获取文件相关信息
            $originalName = $file->getClientOriginalName(); // 文件原名
            $ext = $file->getClientOriginalExtension();     // 扩展名
            $realPath = $file->getRealPath();   //临时文件的绝对路径
            $type = $file->getClientMimeType();     // image/jpeg
            // 上传文件
            $filename = time() . uniqid() . '.' . $ext;
            // 使用我们新建的uploads本地存储空间（目录）
            file_put_contents('images/' . $filename, file_get_contents($realPath));
            $cover = url('images/' . $filename);

        }

        $NvNovelModel = new NvNovelModel();
        $ret = $NvNovelModel->updateNovelInfo($novelid,$click_nums,$down_nums,$shelf_nums,$cover,$cateid,$summary);
        return redirect($_SERVER['HTTP_REFERER']);

    }
    public function getFeedback()
    {
        $request = Request();
        $feedback = DB::table('feedback');
        $platform = $where['platform'] = trim($request->input('platform', 0));
        if ($platform && !empty($platform)) {
            $where['platform'] = $platform;
            $feedback = $feedback->where('platform', $platform);
        }
        $feedbacks = $feedback->orderBy('add_time', 'DESC')->paginate(23);;

        return view('novel/feedback', ['page_title' => '小说管理', 'feedbacks' => $feedbacks, 'where' => $where]);
    }

    public function postUploadart(){
        $request = Request();

        $file = $request->file('imgFile');
//        if ($file && $file->isValid()) {
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
//            return json_encode(['code'=>1,'url'=>$data['img'] ]);
//        }

        if ($file && $file->isValid()) {

//            $filePath = $file->getRealPath();   //临时文件的绝对路径
//
//            require dirname(dirname(dirname(dirname(__FILE__))))."\Qiniu\phpsdk\autoload.php";
//            $accessKey = 'zj0Y_E4ppmM3MwES4iuFbYk_DssjnKuyfMQhKJiy';
//            $secretKey = 'aFhsKB5gOWoSyKossrUkxkUeq-GxOP0sCDBstWkd';
//            $auth = new Auths($accessKey, $secretKey);
//            $bucket = 'novel_php';
//
//            // 生成上传Token
//
//            $expires = 3600;
//            $returnBody = '{"key":"$(key)","hash":"$(etag)","fsize":$(fsize),"name":"$(x:name)"}';
//            $policy = array(
//                'returnBody' => $returnBody
//            );
//            $token = $auth->uploadToken($bucket, null, $expires, $policy, true);
//
//            // 构建 UploadManager 对象
//            $uploadMgr = new UploadManager();
//
//            // 上传到七牛后保存的文件名
//            $originalName = $file->getClientOriginalName(); // 文件原名
//            $key     = uniqid() . $originalName;
//            // 初始化 UploadManager 对象并进行文件的上传。
//            $uploadMgr = new UploadManager();
//            // 调用 UploadManager 的 putFile 方法进行文件的上传。
//            list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
//
//
//            $cover = "http://novelpic.fjhwsp.com/".$ret['key'];
            $cover = $this->UploadImg($file);
            return json_encode(['code'=>1,'url'=>$cover ]);

        }

        return json_encode(['code'=>0]);

    }


    public function postEditNovel()
    {
        $request = Request();
        $novelid = $request->input('novelid', '');
        if (empty($novelid)) {
            return view('show', ['message' => '小说id不能为空！']);
        }

        $data['sort'] = $request->input('sort', 999);
        $data['cateid'] = $request->input('cateid', 1);
        $data['read'] = $request->input('read', 0);
        $data['summary'] = $request->input('summary', '');
        $data['show'] = $request->input('show', 1);

        $file = $request->file('imgFile');
        if ($file && $file->isValid()) {
//            // 获取文件相关信息
//            $originalName = $file->getClientOriginalName(); // 文件原名
//            $ext = $file->getClientOriginalExtension();     // 扩展名
//            $realPath = $file->getRealPath();   //临时文件的绝对路径
//            $type = $file->getClientMimeType();     // image/jpeg
//            // 上传文件
//            $filename = time() . uniqid() . '.' . $ext;
//            // 使用我们新建的uploads本地存储空间（目录）
//            file_put_contents('images/' . $filename, file_get_contents($realPath));
            $cover = $this->UploadImg($file);
            $data['img'] = $cover;
        }
        BaseModel::factory('novel')->where('novelid', $novelid)->update($data);
        return redirect($_SERVER['HTTP_REFERER']);
    }

    public function postEditChannelname()
    {
        $request = Request();
        $cid = $request->input('cid', '');


        $data['channel'] = $request->input('name', '');

        BaseModel::factory('android_update')->where('pack_name', $cid)->update($data);
        return redirect($_SERVER['HTTP_REFERER']);
    }
    public function postEditChannelstatus()
    {
        $request = Request();
        $cid = $request->input('id', '');


        $data['ifsend'] = $request->input('status', '');

        BaseModel::factory('android_update')->where('pack_name', $cid)->update($data);
        return redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCategory()
    {
        $request = Request();
        $category = DB::table('category');
        $where = [];
        $name = trim($request->input('name', ''));
        if ($name && !empty($name)) {
            $where['name'] = $name;
            $category = $category->where('name', 'like', '%' . $name . '%');
        }
        $categorys = $category->orderBy('sort', 'asc')->paginate(23);;

        return view('novel/category', ['page_title' => '分类管理', 'categorys' => $categorys, 'where' => $where, 'name' => $name]);
    }

    public function getChannelset()
    {
        $request = Request();
        $android_update = DB::table('android_update');

        $android_updates = $android_update->orderBy('id', 'asc')->paginate(23);;
        $where = array();
        return view('novel/channelset', ['page_title' => '分类管理', 'android_updates' => $android_updates, 'where' => $where, ]);
    }



    public function postEditCategory()
    {
        $request = Request();
        $cateid = $request->input('cateid', '');
        if (empty($cateid)) {
            return view('show', ['message' => '分类id不能为空！']);
        }

        $data['sort'] = $request->input('sort', 999);
        $data['keyword'] = $request->input('keyword', '');
        $data['name'] = $request->input('name', '');
        $data['update_time'] = date("Y-m-d H:i:s");

        $file = $request->file('imgFile');
        if ($file && $file->isValid()) {

            $cover = $this->UploadImg($file);

//            // 获取文件相关信息
//            $originalName = $file->getClientOriginalName(); // 文件原名
//            $ext = $file->getClientOriginalExtension();     // 扩展名
//            $realPath = $file->getRealPath();   //临时文件的绝对路径
//            $type = $file->getClientMimeType();     // image/jpeg
//            // 上传文件
//            $filename = time() . uniqid() . '.' . $ext;
//            // 使用我们新建的uploads本地存储空间（目录）
//            file_put_contents('images/' . $filename, file_get_contents($realPath));

            $data['img'] = $cover;
        }

        BaseModel::factory('category')->where('cateid', $cateid)->update($data);
        return redirect('/novel/category');
    }

    public function postAddCategory()
    {
        $request = Request();
        $data['sort'] = $request->input('sort', 999);
        $data['keyword'] = $request->input('keyword', '');
        $data['name'] = $request->input('name', '');

        if (empty($data['keyword']) && empty($data['name'])) {
            return view('show', ['message' => '分类名称和分类关键字不能为空']);
        }

        $data['add_time'] = date("Y-m-d H:i:s");
        $file = $request->file('imgFile');
        if ($file && $file->isValid()) {
            // 获取文件相关信息
//            $originalName = $file->getClientOriginalName(); // 文件原名
//            $ext = $file->getClientOriginalExtension();     // 扩展名
//            $realPath = $file->getRealPath();   //临时文件的绝对路径
//            $type = $file->getClientMimeType();     // image/jpeg
//            // 上传文件
//            $filename = time() . uniqid() . '.' . $ext;
//            // 使用我们新建的uploads本地存储空间（目录）
//            file_put_contents('images/' . $filename, file_get_contents($realPath));


            $cover = $this->UploadImg($file);

            $data['img'] = $cover;
        }

        BaseModel::factory('category')->insert([$data]);
        return redirect('/novel/category');
    }


    public function postDelCategory(Request $request)
    {
        $ids = $request->input('cateids', '');
        $state = false;

        foreach ($ids as $k => $id) {
            $c = BaseModel::factory('category')->where('cateid', $id)->first();
            if ($c && $c->num == 0) {
                BaseModel::factory('category')->where('cateid', $id)->delete();
                $state = true;
            }
        }
        return json_encode(['state' => $state]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSource()
    {
        $request = Request();
        $source = DB::table('novel_source');
        $sources = $source->orderBy('sourceid', 'asc')->paginate(23);;

        return view('novel/source', ['page_title' => '小说源管理', 'sources' => $sources]);
    }

    public function postEditSource()
    {
        $request = Request();
        $sourceid = $request->input('sourceid', '');
        if (empty($sourceid)) {
            return view('show', ['message' => '源id不能为空！']);
        }

        $data['name'] = $request->input('name', '');
        $data['url'] = $request->input('url', '');
        $data['chapter_name'] = $request->input('chapter_name', '');
        $data['start'] = $request->input('start', '');
        $data['end'] = $request->input('end', '');
        $data['update_time'] = date("Y-m-d H:i:s");
        BaseModel::factory('novel_source')->where('sourceid', $sourceid)->update($data);
        return redirect('/novel/source');
    }

    public function postAddSource()
    {
        $request = Request();
        $data['name'] = $request->input('name', '');
        $data['url'] = $request->input('url', '');
        $data['chapter_name'] = $request->input('chapter_name', '');
        $data['start'] = $request->input('start', '');
        $data['end'] = $request->input('end', '');

        if (empty($data['name']) && empty($data['chapter_name'])) {
            return view('show', ['message' => '名称和章节英文名不能为空']);
        }

        $data['add_time'] = date("Y-m-d H:i:s");
        BaseModel::factory('novel_source')->insert([$data]);
        return redirect('/novel/source');
    }

    public function postDelSource(Request $request)
    {
        $ids = $request->input('sourceids', '');

        if (BaseModel::factory('novel_source')->whereIn('sourceid', $ids)->delete()) {
            return json_encode(['state' => true]);
        } else {
            return json_encode(['state' => false]);
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAdvert()
    {
        $request = Request();
        $advert = DB::table('advert');
        $where = [];
        $title = trim($request->input('title', ''));
        if ($title && !empty($title)) {
            $where['title'] = $title;
            $advert = $advert->where('title', 'like', '%' . $title . '%');
        }
        $adverts = $advert->orderBy('packid', 'asc')->orderBy('platform', 'asc')->paginate(23);
        $packids = DB::table('packid')->get();

        return view('novel/advert', ['page_title' => '广告管理', 'adverts' => $adverts, 'where' => $where, 'title' => $title, 'packids' => $packids]);
    }

    public function postEditAdvert()
    {
        $request = Request();
        $advertid = $request->input('advertid', '');
        if (empty($advertid)) {
            return view('show', ['message' => '广告id不能为空！']);
        }

        $data['title'] = $request->input('title', '');
        $data['url'] = $request->input('url', '');
        $data['sort'] = $request->input('sort', 999);
        $data['platform'] = $request->input('platform', 1);
        $data['start'] = strtotime($request->input('start', ''));
        $data['end'] = strtotime($request->input('end', ''));
        $data['type'] = $request->input('type', 1);
        $data['packid'] = $request->input('packid', '');
        $data['status'] = $request->input('status', 1);

        $file = $request->file('imgFile');
        if ($file && $file->isValid()) {
            // 获取文件相关信息
            $originalName = $file->getClientOriginalName(); // 文件原名
            $ext = $file->getClientOriginalExtension();     // 扩展名
            $data['adver_type'] = $request->input('adver_type', 1);
            if ($data['type'] == 1 && !in_array($ext, ['png', 'jpg'])) {
                return view('show', ['message' => '首页图片类型只能是png或jpg']);
            }
            if ($data['type'] == 4 && $ext != 'mp4') {
                return view('show', ['message' => '首页视频类型只能是mp4']);
            }
            if ($data['adver_type'] == 2 && $ext != 'mp4') {
                return view('show', ['message' => '视频只能传mp4格式']);
            }

            if ($data['adver_type'] == 1 && !in_array($ext, ['png', 'jpg'])) {
                return view('show', ['message' => '图片只能传png,jpg格式']);
            }

            $realPath = $file->getRealPath();   //临时文件的绝对路径
            $type = $file->getClientMimeType();     // image/jpeg
            // 上传文件
            $filename = time() . uniqid() . '.' . $ext;
            // 使用我们新建的uploads本地存储空间（目录）
            file_put_contents('images/' . $filename, file_get_contents($realPath));
            $data['img'] = url('images/' . $filename);
        }
        $data['update_time'] = date("Y-m-d H:i:s");
        BaseModel::factory('advert')->where('advertid', $advertid)->update($data);
        return redirect($_SERVER['HTTP_REFERER']);
    }

    public function postAddAdvert()
    {
        $request = Request();
        $data['title'] = $request->input('title', '');
        $data['url'] = $request->input('url', '');
        $data['sort'] = $request->input('sort', 999);
        $data['platform'] = $request->input('platform', 1);
        $data['start'] = strtotime($request->input('start', ''));
        $data['end'] = strtotime($request->input('end', ''));
        $data['type'] = $request->input('type', 1);
        $data['packid'] = $request->input('packid', '');
        $data['status'] = $request->input('status', 1);

        $file = $request->file('imgFile');
        if ($file && $file->isValid()) {
            // 获取文件相关信息
            $originalName = $file->getClientOriginalName(); // 文件原名
            $ext = $file->getClientOriginalExtension();     // 扩展名
            $data['adver_type'] = $request->input('adver_type', 1);
            if ($data['type'] == 1 && !in_array($ext, ['png', 'jpg'])) {
                return view('show', ['message' => '首页图片类型只能是png或jpg']);
            }

            if ($data['type'] == 4 && $ext != 'mp4') {
                return view('show', ['message' => '首页视频类型只能是mp4']);
            }

            if ($data['adver_type'] == 2 && $ext != 'mp4') {
                return view('show', ['message' => '视频只能传mp4格式']);
            }

            if ($data['adver_type'] == 1 && !in_array($ext, ['png', 'jpg'])) {
                return view('show', ['message' => '图片只能传png,jpg格式']);
            }
            $realPath = $file->getRealPath();   //临时文件的绝对路径
            $type = $file->getClientMimeType();     // image/jpeg
            // 上传文件
            $filename = time() . uniqid() . '.' . $ext;
            // 使用我们新建的uploads本地存储空间（目录）
            file_put_contents('images/' . $filename, file_get_contents($realPath));
            $data['img'] = url('images/' . $filename);
        }

        $data['add_time'] = date("Y-m-d H:i:s");
        BaseModel::factory('advert')->insert([$data]);
        return redirect('/novel/advert');
    }

    public function postDelAdvert(Request $request)
    {
        $ids = $request->input('advertids', '');

        if (BaseModel::factory('advert')->whereIn('advertid', $ids)->delete()) {
            return json_encode(['state' => true]);
        } else {
            return json_encode(['state' => false]);
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getWebAdvert()
    {
        $request = Request();
        $advert = DB::table('advert_web');
        $where = [];
        $title = trim($request->input('title', ''));
        if ($title && !empty($title)) {
            $where['title'] = $title;
            $advert = $advert->where('title', 'like', '%' . $title . '%');
        }
        $adverts = $advert->orderBy('platform', 'asc')->paginate(23);
        return view('novel/web-advert', ['page_title' => '广告管理', 'adverts' => $adverts, 'where' => $where, 'title' => $title]);
    }

    public function postEditWebAdvert()
    {
        $request = Request();
        $advertid = $request->input('advertid', '');
        if (empty($advertid)) {
            return view('show', ['message' => '广告id不能为空！']);
        }

        $data['title'] = $request->input('title', '');
        $data['url'] = $request->input('url', '');
        $data['sort'] = $request->input('sort', 999);
        $data['platform'] = $request->input('platform', '');
        $data['start'] = strtotime($request->input('start', ''));
        $data['end'] = strtotime($request->input('end', ''));
        $data['type'] = $request->input('type', 1);
        $data['status'] = $request->input('status', 1);

        $file = $request->file('imgFile');
        if ($file && $file->isValid()) {
            // 获取文件相关信息
            $originalName = $file->getClientOriginalName(); // 文件原名
            $ext = $file->getClientOriginalExtension();     // 扩展名
            $data['adver_type'] = $request->input('adver_type', 1);
            if ($data['type'] == 1 && !in_array($ext, ['png', 'jpg'])) {
                return view('show', ['message' => '首页图片类型只能是png或jpg']);
            }
            if ($data['type'] == 4 && $ext != 'mp4') {
                return view('show', ['message' => '首页视频类型只能是mp4']);
            }
            if ($data['adver_type'] == 2 && $ext != 'mp4') {
                return view('show', ['message' => '视频只能传mp4格式']);
            }

            if ($data['adver_type'] == 1 && !in_array($ext, ['png', 'jpg'])) {
                return view('show', ['message' => '图片只能传png,jpg格式']);
            }

            $realPath = $file->getRealPath();   //临时文件的绝对路径
            $type = $file->getClientMimeType();     // image/jpeg
            // 上传文件
            $filename = time() . uniqid() . '.' . $ext;
            // 使用我们新建的uploads本地存储空间（目录）
            file_put_contents('images/' . $filename, file_get_contents($realPath));
            $data['img'] = url('images/' . $filename);
        }
        $data['update_time'] = date("Y-m-d H:i:s");
        BaseModel::factory('advert_web')->where('advertid', $advertid)->update($data);
        return redirect('/novel/web-advert');
    }

    public function postAddWebAdvert()
    {
        $request = Request();
        $data['title'] = $request->input('title', '');
        $data['url'] = $request->input('url', '');
        $data['sort'] = $request->input('sort', 999);
        $data['platform'] = $request->input('platform', '');
        $data['start'] = strtotime($request->input('start', ''));
        $data['end'] = strtotime($request->input('end', ''));
        $data['type'] = $request->input('type', 1);
        $data['status'] = $request->input('status', 1);

        $file = $request->file('imgFile');
        if ($file && $file->isValid()) {
            // 获取文件相关信息
            $originalName = $file->getClientOriginalName(); // 文件原名
            $ext = $file->getClientOriginalExtension();     // 扩展名
            $data['adver_type'] = $request->input('adver_type', 1);
            if ($data['type'] == 1 && !in_array($ext, ['png', 'jpg'])) {
                return view('show', ['message' => '首页图片类型只能是png或jpg']);
            }

            if ($data['type'] == 4 && $ext != 'mp4') {
                return view('show', ['message' => '首页视频类型只能是mp4']);
            }

            if ($data['adver_type'] == 2 && $ext != 'mp4') {
                return view('show', ['message' => '视频只能传mp4格式']);
            }

            if ($data['adver_type'] == 1 && !in_array($ext, ['png', 'jpg'])) {
                return view('show', ['message' => '图片只能传png,jpg格式']);
            }
            $realPath = $file->getRealPath();   //临时文件的绝对路径
            $type = $file->getClientMimeType();     // image/jpeg
            // 上传文件
            $filename = time() . uniqid() . '.' . $ext;
            // 使用我们新建的uploads本地存储空间（目录）
            file_put_contents('images/' . $filename, file_get_contents($realPath));
            $data['img'] = url('images/' . $filename);
        }

        $data['add_time'] = date("Y-m-d H:i:s");
        BaseModel::factory('advert_web')->insert([$data]);
        return redirect('/novel/web-advert');
    }

    public function postDelWebAdvert(Request $request)
    {
        $ids = $request->input('advertids', '');

        if (BaseModel::factory('advert_web')->whereIn('advertid', $ids)->delete()) {
            return json_encode(['state' => true]);
        } else {
            return json_encode(['state' => false]);
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getVersion()
    {
        $request = Request();
        $version = DB::table('android_update');
        $where = [];
        $packid = trim($request->input('packid', ''));
        if ($packid && !empty($packid)) {
            $where['packid'] = $packid;
            $version = $version->where('packid', $packid);
        }
        $version = $version->orderBy('update_time', 'asc')->paginate(23);
        $packids = DB::table('packid')->get();

        return view('novel/version', ['page_title' => '安卓版本管理', 'versions' => $version, 'where' => $where, 'packid' => $packid]);
    }

    public function postEditVersion()
    {
        $request = Request();
        $id = $request->input('id', '');
        if (empty($id)) {
            return view('show', ['message' => '版本id不能为空！']);
        }

        $data['packid'] = $request->input('packid', '');
        $data['pack_name'] = $request->input('channel', '');
        $data['url'] = $request->input('url', '');
        $data['update'] = $request->input('update', 0);
        $data['remark'] = $request->input('remark', 1);
        $data['version'] = $request->input('version', 1);
        $v = BaseModel::factory('android_update')->where('packid', $data['packid'])->first();
//        if ($v && $v->id != $id) {
//            return view('show', ['message' => $data['packid'] . "已存在"]);
//        }

        $data['update_time'] = date("Y-m-d H:i:s");
        BaseModel::factory('android_update')->where('id', $id)->update($data);
        return redirect('/novel/version');
    }

    public function postAddVersion()
    {
        $request = Request();
        $data['packid'] = $request->input('packid', '');
        $data['pack_name'] = $request->input('channel', '');
        $data['url'] = $request->input('url', '');
        $data['update'] = $request->input('update', 0);
        $data['remark'] = $request->input('remark', 1);
        $data['version'] = $request->input('version', 1);

        $v = BaseModel::factory('android_update')->where('packid', $data['packid'])->first();
//        if ($v && $v->id != $id) {
//            return view('show', ['message' => $data['packid'] . "已存在"]);
//        }

        $data['update_time'] = date("Y-m-d H:i:s");
        BaseModel::factory('android_update')->insert([$data]);
        return redirect('/novel/version');
    }

    public function postDelVersion(Request $request)
    {
        $ids = $request->input('versionids', '');

        if (BaseModel::factory('android_update')->whereIn('id', $ids)->delete()) {
            return json_encode(['state' => true]);
        } else {
            return json_encode(['state' => false]);
        }
    }

    public function getSearch()
    {
        $request = Request();
        $advert = DB::table('novel');
        $where = [];
        $title = trim($request->input('title', ''));
        if ($title && !empty($title)) {
            $where['title'] = $title;
            $advert = $advert->where('name', 'like', '%' . $title . '%');
        }
        $advert = $advert->where('search', '>', '1');
        $adverts = $advert->orderBy('show', 'desc')->paginate(23);

        return view('novel/search', ['page_title' => '广告管理', 'adverts' => $adverts, 'where' => $where, 'title' => $title]);
    }

    public function getSearchLog()
    {
        $request = Request();
        $feedback = DB::table('search_log');
        $where['platform'] = trim($request->input('platform', 0));
        $feedbacks = $feedback->orderBy('add_time', 'DESC')->paginate(23);;

        return view('novel/search-log', ['page_title' => '搜索统计', 'feedbacks' => $feedbacks, 'where' => $where]);
    }


    //---begin
    /**
     * 新增短文分类
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     *
     */
    public function postAddEssay(){
        $request = Request();
        $data['sort'] = $request->input('sort', '');
        $data['name'] = $request->input('name', '');

        if (empty($data['name'])) {
            return view('show', ['message' => '分类名称不能为空']);
        }
        if (empty($data['sort'])) {
            return view('show', ['message' => '排序不能为空']);
        }
        if (strlen($data['name'])>40) {
            return view('show', ['message' => '分类名称不能超过40个字符']);
        }

        $data['addtime'] = date("Y-m-d H:i:s");
        $file = $request->file('imgFile');
        if ($file && $file->isValid()) {
//            // 获取文件相关信息
//            $originalName = $file->getClientOriginalName(); // 文件原名
//            $ext = $file->getClientOriginalExtension();     // 扩展名
//            $realPath = $file->getRealPath();   //临时文件的绝对路径
//            $type = $file->getClientMimeType();     // image/jpeg
//            // 上传文件
//            $filename = time() . uniqid() . '.' . $ext;
//            // 使用我们新建的uploads本地存储空间（目录）
//            file_put_contents('images/' . $filename, file_get_contents($realPath));

            $cover = $this->UploadImg($file);
            $data['img'] = $cover;
        }else{
            return view('show', ['message' => '请上传封面图片']);
        }
        $filebanner = $request->file('imgFilebanner');
        if ($filebanner && $filebanner->isValid()) {
            // 获取文件相关信息
//            $originalName = $filebanner->getClientOriginalName(); // 文件原名
//            $ext = $filebanner->getClientOriginalExtension();     // 扩展名
//            $realPath = $filebanner->getRealPath();   //临时文件的绝对路径
//            $type = $filebanner->getClientMimeType();     // image/jpeg
//            // 上传文件
//            $filename = time() . uniqid() . '.' . $ext;
//            // 使用我们新建的uploads本地存储空间（目录）
//            file_put_contents('images/' . $filename, file_get_contents($realPath));

            $cover = $this->UploadImg($file);
            $data['banner'] = $cover;
        }else{
            return view('show', ['message' => '请上传二级菜单图片']);
        }

        BaseModel::factory('shortessay')->insert([$data]);
        return redirect('/novel/shortessay');
    }

    /**
     * 编辑短文分类
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     *
     */
    public function postEditEssay(){
        $request = Request();
        $data['sort'] = $request->input('sort', '');
        $data['name'] = $request->input('name', '');
        $cateid = $request->input('cateid', '');
        $file = $request->file('imgFile');
        if (empty($data['name'])) {
            return view('show', ['message' => '分类名称不能为空']);
        }
        if (empty($data['sort'])) {
            return view('show', ['message' => '排序不能为空']);
        }
        if (strlen($data['name'])>40) {
            return view('show', ['message' => '分类名称不能超过40个字符']);
        }
        if ($file && $file->isValid()) {
            // 获取文件相关信息
//            $originalName = $file->getClientOriginalName(); // 文件原名
//            $ext = $file->getClientOriginalExtension();     // 扩展名
//            $realPath = $file->getRealPath();   //临时文件的绝对路径
//            $type = $file->getClientMimeType();     // image/jpeg
//            // 上传文件
//            $filename = time() . uniqid() . '.' . $ext;
//            // 使用我们新建的uploads本地存储空间（目录）
//            file_put_contents('images/' . $filename, file_get_contents($realPath));
            $cover = $this->UploadImg($file);
            $data['img'] = $cover;
        }
        $filebanner = $request->file('imgFilebanner');
        if ($filebanner && $filebanner->isValid()) {
            // 获取文件相关信息
//            $originalName = $filebanner->getClientOriginalName(); // 文件原名
//            $ext = $filebanner->getClientOriginalExtension();     // 扩展名
//            $realPath = $filebanner->getRealPath();   //临时文件的绝对路径
//            $type = $filebanner->getClientMimeType();     // image/jpeg
//            // 上传文件
//            $filename = time() . uniqid() . '.' . $ext;
//            // 使用我们新建的uploads本地存储空间（目录）
//            file_put_contents('images/' . $filename, file_get_contents($realPath));
            $cover = $this->UploadImg($filebanner);
            $data['banner'] = $cover;
        }
        BaseModel::factory('shortessay')->where('id', $cateid)->update($data);
        return redirect('/novel/shortessay');
    }

    /**
     * 新增短文内容
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function postAddContent(){
        $request = Request();
        $data['sort'] = $request->input('sort', 999);
        $data['name'] = $request->input('name', '');
        $data['content'] = $request->input('content', '');
        $data['eid'] = $request->input('selectname', '');
        $data['status']=1;//默认下架状态
        if (empty($data['content'])) {
            return view('show', ['message' => '短文内容不能为空']);
        }

        $data['addtime'] = date("Y-m-d H:i:s");

        BaseModel::factory('essaycontent')->insert([$data]);
        return redirect('/novel/essaycontent');
    }


    /**
     * 公告管理页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getAnnounce(){
        $request = Request();
        $announce = DB::table('announce');
        $where = [];
        $content = trim($request->input('content', ''));
        if ($content && !empty($content)) {
            $where['content'] = $content;
            $announce = $announce->where('content', 'like', '%' . $content . '%');
        }
        $announce = $announce->where('status',1)->orderBy('id', 'asc')->paginate(23);

        return view('novel/announce', ['page_title' => '公共管理', 'announce' => $announce, 'where' => $where, 'name' => $content]);
    }
    /**
     * 新增频道
     * @return false|string
     */
    public function postAddChannel(){
        $request = Request();
//        $cateid = $request->input('cateid', 999);
        $data['name'] = $request->input('name', 999);
        $data['kind'] = $request->input('kind', 999);
        $data['kindname'] = $request->input('kindname', 999);
        $data['status'] = 1;
        $data['addtime'] = date("Y-m-d H:i:s");
        BaseModel::factory('channel')->insert([$data]);
        return json_encode(['code'=>1]);
    }

    /**
     * 编辑频道
     * @return false|string
     */
    public function postEditChannel(){
        $request = Request();
        $cid = $request->input('cid', '');
        $data['name'] = $request->input('name', 999);
        $data['kind'] = $request->input('kind', 999);
        $data['kindname'] = $request->input('kindname', 999);
        $data['addtime'] = date("Y-m-d H:i:s");
        BaseModel::factory('channel')->where('id', $cid)->update($data);
        return json_encode(['code'=>1]);
    }

    /**
     * 删除频道
     * @return false|string
     */
    public function postDelChannel(){
        $request = Request();
        $cid = $request->input('ids', '');
        $data['status'] =0;
        BaseModel::factory('channel')->whereIn('id', $cid)->update($data);
        return json_encode(['code'=>1]);
    }
    public function getChannel()
    {
        $request = Request();
//        $category = DB::table('category');
        $category = DB::table('newcate');
        $where = [];
//        $name = trim($request->input('name', ''));
//        if ($name && !empty($name)) {
//            $where['name'] = $name;
//            $category = $category->where('name', 'like', '%' . $name . '%');
//        }
        $categorys = $category->where('status',1)->orderBy('sort', 'asc')->get();

        $channels = DB::table('channel')->where('status',1)->orderBy('id','asc')->paginate(23);

        return view('novel/channel', ['page_title' => '频道管理', 'categorys' => $categorys,'where' => $where,'channels'=>$channels]);
    }

    /**
     * @return false|string
     * 搜索广告
     */
    public function getSearchgg(){

        $request = Request();
        $advert = DB::table('advert');
        $where = [];
        $title = trim($request->input('ggname', ''));
        if ($title && !empty($title)) {
            $where['title'] = $title;
            $advert = $advert->where('title', 'like', '%' . $title . '%');
        }
        $adverts = $advert->orderBy('packid', 'asc')->orderBy('platform', 'asc')->get();
        return json_encode(
            [
                'code'=>1,
                'adverts'=>$adverts,
            ]
        );
    }

    public function getDeldata(){
        BaseModel::factory('selection')->where('status',1)->delete();
        return json_encode(
            [
                'code'=>1,
            ]
        );
    }


    public function getSavedata(){
        $request = Request();
        $bookArr = $request->input('thridareaid');
        $title = $request->input('title');
        $pailie = $request->input('palie');
        $paixu = $request->input('paixu');
        for($i=0;$i<count($bookArr);$i++){
            $data['name'] = $title;
            $data['type'] = 4;
            $data['content'] = $bookArr[$i];
            $data['addtime'] = time();
            $data['index'] = $paixu;//外部排序
            $data['status'] = 1;
            $data['sort'] = $i;//内部排序
            $data['showtype'] = $pailie;//展示形式；横向0；纵向1
            BaseModel::factory('selection')->insert([$data]);
        }
        return json_encode(
            [
                'code'=>1,
//                'adverts'=>json_encode($bookArr),
            ]
        );
    }

    public function getSaveone(){
        $request = Request();
        $onebookid = $request->input('onebookid');
        $title = $request->input('onebookhead');

        $data['name'] = $title;
        $data['type'] = 2;
        $data['content'] = $onebookid[0];
        $data['addtime'] = time();
        $data['index'] = "";//外部排序
        $data['status'] = 1;
        $data['sort'] = "";//内部排序
        $data['showtype'] = "";//展示形式；横向0；纵向1
        BaseModel::factory('selection')->insert([$data]);

        return json_encode(
            [
                'code'=>1,
//                'adverts'=>json_encode($bookArr),
            ]
        );
    }


    public function getSavemore(){
        $request = Request();
        $morebookid = $request->input('morebookid');
        for($i=0;$i<count($morebookid);$i++){
            $data['name'] = "热推下的栏目";
            $data['type'] = 3;
            $data['content'] = $morebookid[$i];
            $data['addtime'] = time();
            $data['index'] = "";//外部排序
            $data['status'] = 1;
            $data['sort'] = $i;//内部排序
            $data['showtype'] = 0;//展示形式；横向0；纵向1
            BaseModel::factory('selection')->insert([$data]);
        }
        return json_encode(
            [
                'code'=>1,
//                'adverts'=>json_encode($bookArr),
            ]
        );
    }

    public function getSavebanner(){
        $request = Request();
        $bannerid = $request->input('bannerid');
        $bannertypeArr = $request->input('bannertypeArr');
        for($i=0;$i<count($bannerid);$i++){
            $data['name'] = "banner";
            $data['type'] = 1;
            $data['content'] = $bannerid[$i];
            $data['addtime'] = time();
            $data['index'] = "";//存储banner时值为1：广告banner，2：小说banner'
            $data['status'] = 1;
            $data['sort'] = $i;//内部排序
            $data['showtype'] = 0;
            $data['extra'] = $bannertypeArr[$i];
            BaseModel::factory('selection')->insert([$data]);
        }
        return json_encode(
            [
                'code'=>1,
//                'adverts'=>json_encode($bookArr),
            ]
        );

    }


    public function getSaveart(){
        $request = Request();
        $artindex = $request->input('artindex');
        $artids = $request->input('artids');
        $imgurl = $request->input('imgurl');

        for($i=0;$i<count($artids);$i++){
            $data['name'] = "作者栏";
            $data['type'] = 5;
            $data['content'] = $artids[$i];
            $data['addtime'] = time();
            $data['index'] = $artindex;
            $data['status'] = 1;
            $data['sort'] = $i;//内部排序
            $data['showtype'] = 0;
            $data['extra'] = $imgurl[$i];
            BaseModel::factory('selection')->insert([$data]);
        }
        return json_encode(
            [
                'code'=>1,
//                'adverts'=>json_encode($bookArr),
            ]
        );
    }

    /**
     * @return false|string
     * 获取广告
     */
    public function getGg()
    {
        $request = Request();
        $advert = DB::table('advert');
        $where = [];
        $title = trim($request->input('title', ''));
        if ($title && !empty($title)) {
            $where['title'] = $title;
            $advert = $advert->where('title', 'like', '%' . $title . '%');
        }
        $adverts = $advert->orderBy('packid', 'asc')->orderBy('platform', 'asc')->get();
        $packids = DB::table('packid')->get();

//        return view('novel/advert', ['page_title' => '广告管理', 'adverts' => $adverts, 'where' => $where, 'title' => $title, 'packids' => $packids]);
        return json_encode(
            [
                'code'=>1,
                'adverts'=>$adverts,
                'packids'=>$packids,
            ]
        );
    }

    /**
     * @return false|string
     * 获取小说分类
     */
    public function getCatelist(){
//        $request = Request();
        $category = DB::table('category');
        $where = [];
//        $name = trim($request->input('name', ''));
//        if ($name && !empty($name)) {
//            $where['name'] = $name;
//            $category = $category->where('name', 'like', '%' . $name . '%');
//        }
        $categorys = $category->orderBy('sort', 'asc')->get();
        return json_encode(
            [
                'code'=>1,
                'categorys'=>$categorys,
            ]
        );
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * 书城精选管理首页
     */
    public function getSelection()
    {
//        $selection = DB::table('selection');
//        $banner = $selection->where('type',1)->where('status',1)->get();
        return view('novel/selection', ['page_title' => '书城精选管理']);
    }


    public function getAlldata(){

        //banner
        $banners =  DB::table('selection')->where('status',1)->where('type',1)->orderBy('sort','ASC')->get();
        if(count($banners)>0){
            foreach ($banners as $k=>$vv){
                if($vv->extra ==1){//广告banner
                    $name =  DB::table('advert')->where('advertid',intval($vv->content))->first();
                    $banners[$k]->title=$name->title;
                }else{//
                    $name =  DB::table('novel')->where('novelid',intval($vv->content))->first();
                    $banners[$k]->title=$name->name;
                }

            }

        }

        //hot one begin
        $hotone =  DB::table('selection')->where('status',1)->where('type',2)->first();
        if(count($hotone)>0){
            //根据书id去查询书名
            $bookname = $this->bookname($hotone->content);
            $hotone->title=$bookname;
        }

        //hot one end



        $hotmore = DB::table('selection')->where('status',1)->where('type',3)->orderBy('sort','ASC')->get();

        if(count($hotmore)>0){
            foreach ($hotmore as $k=>$value){
                $hotmore[$k]->title = $this->bookname($value->content);
            }
        }


        $morepart= DB::table('selection')->where('status',1)->where('type',4)->orderBy('index','ASC')->groupBy("name")->get();
        if(count($morepart)>0){
            foreach ($morepart as $k=>$v){
//            $morepart[$k]->include =   DB::table('selection')->where('name',$v->name)->orderBy('sort','ASC')->get();
                $include = DB::table('selection')->where('name',$v->name)->orderBy('sort','ASC')->get();
                foreach ($include as $key=>$val){
                    $include[$key]->title = $this->bookname($val->content);
                }
                $morepart[$k]->include = $include;
            }
        }


        $art = DB::table('selection')->where('status',1)->where('type',5)->orderBy('sort','ASC')->get();

        return json_encode(
            [
                'code'=>1,
                'banner'=>$banners,
                'hotone'=>$hotone,
                'hotmore'=>$hotmore,
                'art'=>$art,
                'morepart'=>$morepart,

            ]
        );
    }


    public function bookname($id){
//        $novel = DB::table('novel');
        $name =  DB::table('novel')->where('novelid',$id)->first();
        return $name->name;
    }

    public function getShortessay()
    {
        $request = Request();
        $shortessay = DB::table('shortessay');
        $where = [];
        $name = trim($request->input('name', ''));
        if ($name && !empty($name)) {
            $where['name'] = $name;
            $shortessay = $shortessay->where('name', 'like', '%' . $name . '%');
        }
        $shortessay = $shortessay->where('status',1)->orderBy('sort', 'asc')->paginate(23);;

        return view('novel/shortessay', ['page_title' => '短文分类管理', 'categorys' => $shortessay, 'where' => $where, 'name' => $name]);
    }
    public function postDelShortessay(){

        $request = Request();
        $id = $request->input('id', 999);

        $data['status'] = 0;

        BaseModel::factory('shortessay')->where('id',$id)->update($data);
        BaseModel::factory('essaycontent')->where('eid',$id)->update($data);
        return json_encode(['code'=>1]);
    }

    public function getEssaycontent()
    {
        $request = Request();
        $category = DB::table('essaycontent');
        $where = [];
        $name = trim($request->input('name', ''));
        if ($name && !empty($name)) {
            $where['name'] = $name;
            $category = $category->where('name', 'like', '%' . $name . '%');
        }
        $categorys = $category->where('status',1)->orderBy('sort', 'asc')->paginate(23);;

        $essay = DB::table('shortessay')->where('status',1)->orderBy('sort','asc')->get();
        return view('novel/essaycontent', ['page_title' => '短文内容管理', 'categorys' => $categorys, 'where' => $where, 'name' => $name,'essays'=>$essay]);
    }

    public function getEssayall(){
        $essay = DB::table('shortessay')->orderBy('sort','asc')->get();

        return json_encode(['code'=>1,'essay'=>$essay]);
    }

    public function postEditEssaycontent(){

        $request = Request();
        $data['sort'] = $request->input('sort', 999);
        $conid = $request->input('conid', 999);
        $data['name'] = $request->input('name', '');
        $data['content'] = $request->input('content', '');
        $data['eid'] = $request->input('selectname', '');
//        $data['status']=0;//默认下架状态
        if (empty($data['content'])) {
            return view('show', ['message' => '短文内容不能为空']);
        }

        $data['addtime'] = date("Y-m-d H:i:s");

        BaseModel::factory('essaycontent')->where('id',$conid)->update($data);
        return redirect('/novel/essaycontent');
    }


    public function getOperate(){
        $request = Request();
        $status = trim($request->input('status', ''));
        $id = trim($request->input('id', ''));
        $data['status']=$status;
        DB::table('essaycontent')->where('id',$id)->update($data);
        return json_encode(['code'=>1]);
    }


    /**
     * @return false|string
     * 筛选小说
     */
    public function getNovelsel(){
        $request = Request();
        $bookrel = trim($request->input('bookrel', ''));
        $optionval = trim($request->input('optionval', ''));
        $page = trim($request->input('page', 1));
        $offset = ($page - 1) * 10;
        if(!empty($bookrel) && !empty($optionval)){
            $novels = BaseModel::factory('novel')
                ->where('cateid',$optionval)
                ->where('show',1)
                ->where(function($query) use($bookrel){
                    $query->where('author', 'like', '%'.$bookrel . '%')
                        ->orWhere(function($query) use($bookrel){
                            $query->where('name', 'like','%'. $bookrel . '%');
                        })->orWhere(function($query) use($bookrel){
                            $query->where('novelid', 'like', '%'.$bookrel . '%');
                        });
                })->orderBy('update_time', 'DESC') ->offset($offset)->limit(10)->get();
            $novelnum = BaseModel::factory('novel')
                ->where('cateid',$optionval)
                ->where('show',1)
                ->where(function($query) use($bookrel){
                    $query->where('author', 'like', '%'.$bookrel . '%')
                        ->orWhere(function($query) use($bookrel){
                            $query->where('name', 'like','%'. $bookrel . '%');
                        })->orWhere(function($query) use($bookrel){
                            $query->where('novelid', 'like', '%'.$bookrel . '%');
                        });
                })->count();

        }else if(!empty($bookrel) && empty($optionval)){

            $novels = BaseModel::factory('novel')
                ->where('show',1)
                ->where(function($query) use($bookrel){
                    $query->where('author', 'like', '%'.$bookrel . '%')
                        ->orWhere(function($query) use($bookrel){
                            $query->where('name', 'like', '%'.$bookrel . '%');
                        })->orWhere(function($query) use($bookrel){
                            $query->where('novelid', 'like', '%'.$bookrel . '%');
                        });
                })->orderBy('update_time', 'DESC')->offset($offset)->limit(10)->get();
            $novelnum = BaseModel::factory('novel')
                ->where('show',1)
                ->where(function($query) use($bookrel){
                    $query->where('author', 'like', '%'.$bookrel . '%')
                        ->orWhere(function($query) use($bookrel){
                            $query->where('name', 'like', '%'.$bookrel . '%');
                        })->orWhere(function($query) use($bookrel){
                            $query->where('novelid', 'like', '%'.$bookrel . '%');
                        });
                })->count();

        }else if(empty($bookrel) && !empty($optionval)){
            $novels = BaseModel::factory('novel')
                ->where('cateid',$optionval)
                ->where('show',1)
                ->orderBy('update_time', 'DESC')->offset($offset)->limit(10)->get();
            $novelnum = BaseModel::factory('novel')
                ->where('cateid',$optionval)
                ->where('show',1)
                ->count();
        }

        foreach ($novels as $k=>$v){
            $cate = BaseModel::factory('category')->select('name')->where('cateid',$v['cateid'])->first();
            $novels[$k]['catename'] = $cate['name'];
        }
        if($novelnum%10==0){
            $novelnum = $novelnum/10;
        }else{
            $novelnum = intval($novelnum/10)+1;
        }
        return json_encode(
            [
                'code'=>1,
                'novels'=>$novels,
                'novelnum'=>$novelnum,
                'page'=>$page,
            ]
        );

    }


    /**
     * 添加公告内容
     * @return false|string
     */
    public function postAddAnnounce(){
        $request = Request();
        $data['content'] = $request->input('cont', '');
        $data['jump'] = $request->input('jump', 0);
        $data['novelid'] = $request->input('addnovel', 0);
        $data['status'] = 1;
        $data['pushstatus'] = 0;
        $pushtime = $request->input('ptime', '');
        $data['pushtime'] = strtotime($pushtime);
        BaseModel::factory('announce')->insert([$data]);
        return json_encode(['code'=>1]);
    }
    /**
     * 编辑公告内容
     * @return false|string
     */
    public function postEditAnnounce(){
        $request = Request();
        $data['content'] = $request->input('cont', '');
        $data['jump'] = $request->input('jump', 0);
        $data['novelid'] = $request->input('addnovel', 0);
        $aid = $request->input('aid', 1);
//        $data['status'] = 1;
//        $data['pushstatus'] = 0;
        $pushtime = $request->input('ptime', '');
        $data['pushtime'] = strtotime($pushtime);
        BaseModel::factory('announce')->where('id',$aid)->update($data);
        return json_encode(['code'=>1]);
    }
    /**
     * 删除公告内容
     * @return false|string
     */
    public function postDelAnnounce(){
        $request = Request();
        $ids = $request->input('ids', '');
        $data['status']=0;
        BaseModel::factory('announce')->whereIn('id',$ids)->update($data);
        return json_encode(['code'=>1]);
    }


    public function getSuggestion(){

        $request = Request();
        $suggestion = DB::table('suggestion');
        $type = $where['type'] = trim($request->input('platform', 0));
        if ($type && !empty($type)) {
            $where['type'] = $type;
            $suggestion = $suggestion->where('type', $type);
        }
        $img = array();
        $suggestion = $suggestion->orderBy('addtime', 'DESC')->paginate(23);
        if(!empty($suggestion)){
            foreach ($suggestion as $k=>$v){
                $res = DB::table('novel_user')->where('id',$v->uuid)->first();
                if(empty($res->phone)){
                    $suggestion[$k]->phone ='';
                }else{
                    $suggestion[$k]->phone =$res->phone;
                }

            }
        }

//        foreach ($suggestion as $k=>$v){
//            $res = DB::table('suggestion_img')->where('sid',$v->id)->get();
//            if(!empty($res)){
//                foreach ($res as $key=>$value){
////                    $img.$k[$key] = $value->img;
////                    array_push($img,$value->img);
//                    $suggestion[$k]->img = $value->img;
//                }
//
//            }else{
//                $suggestion[$k]->img = [];
//            }
//        }

        return view('novel/suggestion', ['page_title' => '反馈记录管理', 'suggestions' => $suggestion, 'where' => $where]);
    }

    public function postGetSuggestionImg(){
        $request = Request();
        $id = trim($request->input('id', 0));
        $res = DB::table('suggestion_img')->where('sid',$id)->get();
        return json_encode(['code'=>1,'data'=>$res]);
    }

    /**
     * 后台小说用户管理
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getUsernovel()
    {
        $request = Request();
        $novel_user = DB::table('novel_user');
        $where = [];
        $name = trim($request->input('name', ''));
        if ($name && !empty($name)) {
            $where['name'] = $name;
            $novel_user = $novel_user->where(function($query) use($name){
                $query->where('id', 'like', '%'.$name . '%')
                    ->orWhere(function($query) use($name){
                        $query->where('nickname', 'like', '%'.$name . '%');
                    })->orWhere(function($query) use($name){
                        $query->where('phone', 'like', '%'. $name . '%');
                    });
            });
        }
        $users = $novel_user->orderBy('regtime', 'desc')->paginate(23);

        if(!empty($users)){
            foreach ($users as $k=>$value){
                $uuid = $value->id;
                //用户的累计阅读时间
                $sql = "select sum(`long`) as readtime from user_read_record where uuid={$uuid}";
                $readtime = DB::selectone($sql);

                $users[$k]->readtime = $readtime->readtime;

                //用户的累计阅读书籍
                $sql1 = "select count(*) as readbook from user_read_record where uuid={$uuid} group by(`novelid`)";
                $readbook = DB::selectone($sql1);
                if(!empty($readbook)){
                    $book = $readbook->readbook;
                }else{
                    $book = 0;
                }
                $users[$k]->readbook = $book;


                //分享次数

                $num = DB::table('novel_user')->where('userfrom',$uuid)->count();
                $users[$k]->shareuser = $num;

            }
        }



        return view('novel/usernovel', ['users' => $users, 'where' => $where, 'name' => $name]);
    }

    public function getSpreaduser()
    {
        $request = Request();
        $novel_user = DB::table('novel_user');
        $where = [];
        $name = trim($request->input('name', ''));
        if ($name && !empty($name)) {
            $where['name'] = $name;
            $novel_user = $novel_user->where(function($query) use($name){
                $query->where('id', 'like', '%'.$name . '%')
                    ->orWhere(function($query) use($name){
                        $query->where('nickname', 'like', '%'.$name . '%');
                    })->orWhere(function($query) use($name){
                        $query->where('phone', 'like', '%'. $name . '%');
                    });
            });
        }
        $users = $novel_user->where('userfrom','>',1)->orwhere('channel',99)->orderBy('regtime', 'desc')->paginate(23);

        if(!empty($users)){
            foreach ($users as $k=>$value){
                $uuid = $value->id;
                //用户的累计阅读时间
                $sql = "select sum(`long`) as readtime from user_read_record where uuid={$uuid}";
                $readtime = DB::selectone($sql);

                $users[$k]->readtime = $readtime->readtime;

                //用户的累计阅读书籍
                $sql1 = "select count(*) as readbook from user_read_record where uuid={$uuid} group by(`novelid`)";
                $readbook = DB::selectone($sql1);
                if(!empty($readbook)){
                    $book = $readbook->readbook;
                }else{
                    $book = 0;
                }
                $users[$k]->readbook = $book;


                //分享次数

                $num = DB::table('novel_user')->where('userfrom',$uuid)->count();
                $users[$k]->shareuser = $num;


            }
        }



        return view('novel/spreaduser', [ 'users' => $users, 'where' => $where, 'name' => $name]);
    }

    public function postGetPrefer(){
        $request = Request();
        $uuid = trim($request->input('id', ''));
        //查看用户的偏好
        $ret = DB::table('bookshelf')->where('user_id',$uuid)->where('status',1)->orderBy('addtime','desc')->first();
        $data=[];
        if(!empty($ret)){
            $prefer = $ret->prefer;
            if(!empty($prefer)){
                $preferArr = json_decode($prefer,1);
                $CateModel = new CateModel();
                for($i=0;$i<count($preferArr);$i++){

                    $temp = $CateModel ->getCateName($preferArr[$i]);
                    $data[$i] = $temp->name;
                }

            }

        }
        return json_encode(['code'=>1,'data'=>$data]);
    }


    /**
     * 收藏记录管理
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getCollection()
    {
        $request = Request();
        $uuid = trim($request->input('uuid', ''));
        $behavior = DB::table('user_behavior_record');
        $behaviors = $behavior->where('uuid',$uuid)->where('type',2)->where('status',1)->orderBy('addtime', 'asc')->paginate(23);

        return view('novel/collection', ['page_title' => '收藏记录管理', 'behaviors' => $behaviors]);
    }

    public function getBookshelf()
    {
        $NvNovelModel = new NvNovelModel();
        $request = Request();
        $uuid = trim($request->input('uuid', ''));
        $bookshelf = DB::table('bookshelf');
        $bookshelfs = $bookshelf->where('user_id',$uuid)->where('status',1)->orderBy('addtime', 'asc')->paginate(23);

        if(!empty($bookshelfs)){
            foreach ($bookshelfs as $k=>$v){
                $data = $NvNovelModel->getNovelData($v->novelid);
                $bookshelfs[$k]->novelname = $data->name;
                $bookshelfs[$k]->catename = $data->catename;
            }
        }
        return view('novel/bookshelf', [ 'bookshelfs' => $bookshelfs]);
    }
    public function getShareuser()
    {
        $request = Request();
        $uuid = trim($request->input('uuid', ''));
        $user = DB::table('novel_user');
        $users = $user->where('userfrom',$uuid)->orderBy('regtime', 'asc')->paginate(23);

        return view('novel/shareuser', [ 'users' => $users]);
    }

    /**
     * 书籍缓存管理
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getDown()
    {
        $request = Request();
        $uuid = trim($request->input('uuid', ''));
        $behavior = DB::table('user_behavior_record');
        $behaviors = $behavior->where('uuid',$uuid)->where('type',1)->orderBy('addtime', 'asc')->paginate(23);

        return view('novel/down', ['page_title' => '书籍缓存管理', 'behaviors' => $behaviors]);
    }

    /**
     * 阅读记录管理
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getRead()
    {
        $request = Request();
        $uuid = trim($request->input('uuid', ''));
        $read = DB::table('user_read_record');
        $reads = $read->where('uuid',$uuid)->orderBy('addtime', 'desc')->paginate(23);


        return view('novel/read', ['page_title' => '阅读记录管理', 'reads' => $reads]);
    }


    public function getChoiceness()
    {
        $request = Request();
        $choiceness = DB::table('choiceness');
        $where = [];
        $type =$where['type']= trim($request->input('platform', 0));
        $status =$where['status']= trim($request->input('platform1', 0));
        if ($type && !empty($type)) {
            $where['type'] = $type;
            $choiceness = $choiceness->where('type',$type);
        }
        if ($status && !empty($status)) {
            $where['status'] = $status;
            $choiceness = $choiceness->where('status',$status);
        }
        $choicenesss = $choiceness->where('isdel',1)->orderBy('sort', 'asc')->paginate(23);

        foreach ($choicenesss as $k=>$v){
            if($v->type == 1 || $v->type == 4){
                $ret = DB::table('novel_banner')->where('status',1)->where('choiceid',$v->id)->count();
                $choicenesss[$k]->num = $ret;
            }else{
                $ret = DB::table('choice_novel')->where('status',1)->where('choiceid',$v->id)->count();
                $choicenesss[$k]->num = $ret;
            }
        }

        return view('novel/choiceness', ['page_title' => '精选配置-专题列表管理', 'choicenesss' => $choicenesss, 'where' => $where]);
    }


    public function postAddChoiceness(){
        $request = Request();
//        $choiceness = DB::table('choiceness');
        $add_type = trim($request->input('add_type', 0));
        $add_showtype = trim($request->input('add_showtype', 0));
        $sort = trim($request->input('sort', 0));
        $name = trim($request->input('name', 0));

        if($add_showtype ==3){
            $add_type =2;
        }
//        return view('show', ['message' => $data['packid'] . "已存在"]);
        if($add_type == 1){
            $res = DB::table('choiceness')->where('type',1)->where('status',1)->where('isdel',1)->get();
            if(!empty($res)){
                return view('show', ['message' => "只可以添加一个banner专栏"]);
            }
        }
//        else if($add_type == 2){
//            $res = DB::table('choiceness')->where('type',2)->where('status',1)->get();
//            if(!empty($res)){
//                return view('show', ['message' => "只可以添加一个热推专栏"]);
//            }
//        }
        else if($add_type == 4){
            $res = DB::table('choiceness')->where('type',4)->where('status',1)->where('isdel',1)->get();
            if(!empty($res)){
                return view('show', ['message' => "只可以添加一个作者专栏"]);
            }
        }

        $data = array(
            'sort' => $sort,
            'type' => $add_type,
            'addtime' => date('Y-m-d H:i:s' ,time()),
            'name' => $name,
            'status' => 2,
            'isdel' => 1,
            'showtype' => $add_showtype,
        );
        BaseModel::factory('choiceness')->insert([$data]);
        return redirect('/novel/choiceness');
    }
    public function postEditChoiceness(){
        $request = Request();
//        $choiceness = DB::table('choiceness');
        $add_type = trim($request->input('add_type', 0));
        $add_showtype = trim($request->input('add_showtype', 0));
        $sort = trim($request->input('sort', 0));
        $name = trim($request->input('name', 0));
        $cid = trim($request->input('cid', 0));

        $data = array(
            'sort' => $sort,
            'type' => $add_type,
            'addtime' => date('Y-m-d H:i:s' ,time()),
            'name' => $name,
//            'status' => 2,
            'showtype' => $add_showtype,
        );
        BaseModel::factory('choiceness')->where('id',$cid)->update($data);
        return redirect('/novel/choiceness');
    }
    public function postOperateChoiceness(){
        $request = Request();
        $id = trim($request->input('id', 0));
        $type = trim($request->input('type', 0));
        $status = trim($request->input('status', 0));
        $data=array(
            'status'=>$status
        );

        BaseModel::factory('choiceness')->where('id',$id)->update($data);

        if($status == 1){
            if($type == 2|| $type==5){
                $mess = array(
                    'status'=>1
                );
                BaseModel::factory('choice_novel')->where('choiceid',$id)->update($mess);
            }

            if($type == 1||$type==4){
                $mess = array(
                    'status'=>1
                );
                BaseModel::factory('novel_banner')->where('choiceid',$id)->update($mess);
            }
        }else{
            if($type == 2|| $type==5){
                $mess = array(
                    'status'=>2
                );
                BaseModel::factory('choice_novel')->where('choiceid',$id)->update($mess);
            }

            if($type == 1||$type==4){
                $mess = array(
                    'status'=>2
                );
                BaseModel::factory('novel_banner')->where('choiceid',$id)->update($mess);
            }
        }

        return json_encode(['code'=>1]);
    }
    public function postDelChoiceness(){
        $request = Request();
        $id = trim($request->input('id', 0));
        $type = trim($request->input('type', 0));
        $data=array(
            'isdel'=>0
        );
        BaseModel::factory('choiceness')->where('id',$id)->update($data);

        if($type == 2|| $type==5){
            $mess = array(
                'status'=>2
            );
            BaseModel::factory('choice_novel')->where('choiceid',$id)->update($mess);
        }

        if($type == 1||$type==4){
            $mess = array(
                'status'=>2
            );
            BaseModel::factory('novel_banner')->where('choiceid',$id)->update($mess);
        }
        return json_encode(['code'=>1]);
    }

//    public function getList(){
//        $request = Request();
//        $category = DB::table('category');
//        $where = [];
//        $name = trim($request->input('name', ''));
//        if ($name && !empty($name)) {
//            $where['name'] = $name;
//            $category = $category->where('name', 'like', '%' . $name . '%');
//        }
//        $categorys = $category->orderBy('sort', 'asc')->paginate(23);;
//
//        return view('novel/list', ['page_title' => '分类管理', 'categorys' => $categorys, 'where' => $where, 'name' => $name]);
//    }


    public function getBannercolumn()
    {
        $request = Request();
        $novel_banner = DB::table('novel_banner');
//        $where = [];
        $id = trim($request->input('id', 1));
//        if ($name && !empty($name)) {
//            $where['name'] = $name;
//            $category = $category->where('name', 'like', '%' . $name . '%');
//        }
        $novel_banners = $novel_banner->where('choiceid',$id)->where('type',1)->orderBy('sort','asc')->paginate(23);;

        return view('novel/bannercolumn', ['page_title' => 'banner专栏管理', 'novel_banners' => $novel_banners,'bannerid'=>$id ]);
    }

    public function getAuthorcolumn()
    {
        $request = Request();
        $novel_banner = DB::table('novel_banner');

        $id = trim($request->input('id', 1));

        $novel_banners=array();
        $novel_banners = $novel_banner->where('choiceid',$id)->where('type',2)->orderBy('addtime', 'desc')->orderBy('sort','asc')->paginate(23);
//        $authorDataquery = "select count(*) as num,author from novel group by author order by num desc limit 50";
//        $authorData = DB::select($authorDataquery);

        return view('novel/authorcolumn', ['page_title' => '作者专栏管理', 'novel_banners' => $novel_banners,'bannerid'=>$id ]);
    }

    public function postAddBanner(){
        $request = Request();
        $bannerid = trim($request->input('bannerid', 1));
        $link = trim($request->input('link', ""));
        $linktype = trim($request->input('linktype', 0));
        $sort = trim($request->input('sort', ""));
        $file = $request->file('imgFile');
        if ($file && $file->isValid()) {
//            // 获取文件相关信息
//            $originalName = $file->getClientOriginalName(); // 文件原名
//            $ext = $file->getClientOriginalExtension();     // 扩展名
//            $realPath = $file->getRealPath();   //临时文件的绝对路径
//            $type = $file->getClientMimeType();     // image/jpeg
//            // 上传文件
//            $filename = time() . uniqid() . '.' . $ext;
//            // 使用我们新建的uploads本地存储空间（目录）
//            file_put_contents('images/' . $filename, file_get_contents($realPath));
            $cover = $this->UploadImg($file);
            $data['img'] = $cover;
        }
        $data['link'] = $link;
        $data['linktype'] = $linktype;
        $data['status'] = 1;
        $data['type'] = 1;
        $data['choiceid'] = $bannerid;
        $data['sort'] = $sort;
        $data['addtime'] = date('Y-m-d H:i:s',time());

        BaseModel::factory('novel_banner')->insert([$data]);

        $novel_banner = DB::table('novel_banner');

        $novel_banners = $novel_banner->where('choiceid',$bannerid)->orderBy('addtime', 'desc')->orderBy('sort','desc')->paginate(23);;

        return view("novel/bannercolumn", ['page_title' => 'banner专栏管理', 'novel_banners' => $novel_banners,'bannerid'=>$bannerid ]);
    }

    public function postAddAuthor(){
        $request = Request();
        $bannerid = trim($request->input('bannerid', 1));
        $author = trim($request->input('author', ""));
        $sort = trim($request->input('sort', ""));
        $file = $request->file('imgFile');
        if ($file && $file->isValid()) {
//            // 获取文件相关信息
//            $originalName = $file->getClientOriginalName(); // 文件原名
//            $ext = $file->getClientOriginalExtension();     // 扩展名
//            $realPath = $file->getRealPath();   //临时文件的绝对路径
//            $type = $file->getClientMimeType();     // image/jpeg
//            // 上传文件
//            $filename = time() . uniqid() . '.' . $ext;
//            // 使用我们新建的uploads本地存储空间（目录）
//            file_put_contents('images/' . $filename, file_get_contents($realPath));
            $cover = $this->UploadImg($file);
            $data['img'] = $cover;
        }else{
            return view('show', ['message' => "作者图片未上传"]);
        }

        $NvNovelModel = new NvNovelModel();
        $authorinfo = $NvNovelModel->getAuthor($author);
        if(empty($authorinfo)){
            return view('show', ['message' => "未查询到相关作者"]);
        }
        $data['link'] = $author;
        $data['status'] = 1;
        $data['type'] = 2;
        $data['choiceid'] = $bannerid;
        $data['sort'] = $sort;
        $data['addtime'] = date('Y-m-d H:i:s',time());

        BaseModel::factory('novel_banner')->insert([$data]);

        $novel_banner = DB::table('novel_banner');

        $novel_banners = $novel_banner->where('choiceid',$bannerid)->orderBy('addtime', 'desc')->orderBy('sort','desc')->paginate(23);;


        $authorDataquery = "select count(*) as num,author from novel group by author order by num desc limit 50";
        $authorData = DB::select($authorDataquery);

        return view("novel/authorcolumn", ['page_title' => '作者专栏管理', 'novel_banners' => $novel_banners,'bannerid'=>$bannerid ,'authorData'=>$authorData]);
    }

    public function postEditBanner(){
        $request = Request();
        $id = trim($request->input('id', 1));
        $bannerid = trim($request->input('bannerid', 1));
        $link = trim($request->input('link', ""));
        $linktype = trim($request->input('linktype', 0));
        $sort = trim($request->input('sort', ""));
        $file = $request->file('imgFile');
        if ($file && $file->isValid()) {
            // 获取文件相关信息
//            $originalName = $file->getClientOriginalName(); // 文件原名
//            $ext = $file->getClientOriginalExtension();     // 扩展名
//            $realPath = $file->getRealPath();   //临时文件的绝对路径
//            $type = $file->getClientMimeType();     // image/jpeg
//            // 上传文件
//            $filename = time() . uniqid() . '.' . $ext;
//            // 使用我们新建的uploads本地存储空间（目录）
//            file_put_contents('images/' . $filename, file_get_contents($realPath));

            $cover = $this->UploadImg($file);
            $data['img'] = $cover;
        }

        $data['link'] = $link;
        $data['linktype'] = $linktype;
        $data['sort'] = $sort;
        BaseModel::factory('novel_banner')->where('id', $id)->update($data);
        $novel_banner = DB::table('novel_banner');

        $novel_banners = $novel_banner->where('choiceid',$bannerid)->orderBy('addtime', 'desc')->orderBy('sort','desc')->paginate(23);;



        return view("novel/bannercolumn", ['page_title' => 'banner专栏管理', 'novel_banners' => $novel_banners,'bannerid'=>$bannerid]);
    }
    public function postEditAuthor(){
        $request = Request();
        $id = trim($request->input('id', 1));
        $bannerid = trim($request->input('bannerid', 1));
        $link = trim($request->input('author', ""));
        $sort = trim($request->input('sort', ""));
        $file = $request->file('imgFile');
        if ($file && $file->isValid()) {
            // 获取文件相关信息
//            $originalName = $file->getClientOriginalName(); // 文件原名
//            $ext = $file->getClientOriginalExtension();     // 扩展名
//            $realPath = $file->getRealPath();   //临时文件的绝对路径
//            $type = $file->getClientMimeType();     // image/jpeg
//            // 上传文件
//            $filename = time() . uniqid() . '.' . $ext;
//            // 使用我们新建的uploads本地存储空间（目录）
//            file_put_contents('images/' . $filename, file_get_contents($realPath));
            $cover = $this->UploadImg($file);
            $data['img'] = $cover;
        }
        $NvNovelModel = new NvNovelModel();
        $authorinfo = $NvNovelModel->getAuthor($link);
        if(empty($authorinfo)){
            return view('show', ['message' => "未查询到相关作者"]);
        }
        $data['link'] = $link;
        $data['sort'] = $sort;
        BaseModel::factory('novel_banner')->where('id', $id)->update($data);
        $novel_banner = DB::table('novel_banner');

        $novel_banners = $novel_banner->where('choiceid',$bannerid)->orderBy('addtime', 'desc')->orderBy('sort','desc')->paginate(23);;
//        $authorDataquery = "select count(*) as num,author from novel group by author order by num desc limit 50";
//        $authorData = DB::select($authorDataquery);
        return view("novel/authorcolumn", ['page_title' => '作者专栏管理', 'novel_banners' => $novel_banners,'bannerid'=>$bannerid ]);
    }
    public function postEditStatus(){
        $request = Request();
        $id = trim($request->input('id', 1));
        $status = trim($request->input('status', 1));
        $data['status'] = $status;
        BaseModel::factory('novel_banner')->where('id', $id)->update($data);
        return json_encode(['code'=>1]);
    }

    public function getHotonecolumn(){

        $request = Request();
        $id = trim($request->input('id', 1));
        $choice_novel = DB::table('choice_novel');
        $where = [];
//        $name = trim($request->input('name', ''));
//        if ($name && !empty($name)) {
//            $where['name'] = $name;
//            $category = $category->where('name', 'like', '%' . $name . '%');
//        }
        $choice_novels = $choice_novel->where('choiceid',$id)->where('status',1)->orderBy('sort', 'asc')->orderBy('addtime', 'desc')->paginate(23);
        $NvNovelModel = new NvNovelModel();
        if(!empty($choice_novels)){
            foreach ($choice_novels as $k=>$v){

//                $query = "select novelid,novel.name,novel.img,novel.img,author,status,chapter,category.name as catename from novel LEFT JOIN category on novel.cateid=category.cateid where novelid={$v->novelid}";
//                $novel = DB::selectone($query);
                $novel = $NvNovelModel->getNovelById($v->novelid);
                if(!empty($novel) && !empty($novel->name)){
                    $choice_novels[$k]->name = $novel->name;
                    $choice_novels[$k]->author = $novel->author;
                    $choice_novels[$k]->img = $novel->img;
                    $choice_novels[$k]->catename = $novel->nove_line_cate_id;
                }
//                $choice_novels[$k]->name = $novel->name;
//                $choice_novels[$k]->author = $novel->author;
//                $choice_novels[$k]->img = $novel->img;
//                $choice_novels[$k]->catename = $novel->nove_line_cate_id;
//                $choice_novels[$k]=$novel;

            }
        }

        return view('novel/hotonecolumn', ['page_title' => '热推一本专栏管理', 'choice_novels' => $choice_novels, 'where' => $where,'id'=>$id]);
    }

    public function getAddnovel(){

        $request = Request();
        $id = trim($request->input('id', 1));
        $type = trim($request->input('type', 1));

        $cate = trim($request->input('novel_name', 0));
        $name = trim($request->input('name', ''));


        $CateModel = new CateModel();
        $NvNovelModel = new NvNovelModel();

        $allCate = $CateModel->getCate();//查询有哪些小说分类

        $where = [];

        if($cate && !empty($cate)){
            $where['novel_name'] = $cate;
        }
        if($name && !empty($name)){
            $where['name'] = $name;
        }
        $where['id'] = $id;
        $where['type'] = $type;

        $novel = $NvNovelModel->getNovelByCondition($cate,$name,0);
        $novels = $novel->where('status',1)->where('is_finished',1)->orderBy('chapter', 'desc')->paginate(23);;

        return view('novel/addnovel', ['page_title' => '添加小说管理', 'novels'=>$novels,'cate'=>$cate,'name'=>$name,  'where' => $where,'id'=>$id,'type'=>$type,'categorys'=>$allCate]);
    }


    public function postAddNovelcolumn(){
        $request = Request();
        $choiceid = trim($request->input('choiceid', 1));
        $novelid = trim($request->input('novelid', 1));
        $sort = trim($request->input('sort', 1));
        $res = DB::table('choice_novel')->where('novelid',$novelid)->where('choiceid',$choiceid)->first();
        if(!empty($res)){
            return json_encode(['code'=>0,'msg'=>'该书已存在该专题下']);
        }else{
            $data = array(
                'novelid'=>$novelid,
                'choiceid'=>$choiceid,
                'sort'=>$sort,
                'addtime'=>date('Y-m-d H:i:s',time()),
                'status'=>1
            );
            BaseModel::factory('choice_novel')->insert([$data]);
        }

        return json_encode(['code'=>1]);
    }

    public function postOperateChoicenovel(){
        $request = Request();
        $choiceid = trim($request->input('choiceid', 1));
        $status = trim($request->input('status', ''));
        $data['status']=$status;
        BaseModel::factory('choice_novel')->where('id',$choiceid)->update($data);
        return json_encode(['code'=>1]);
    }



    public function postEditSort(){
        $request = Request();
        $id = trim($request->input('id', 1));
        $sort = trim($request->input('sort', ''));
        $data['sort']=$sort;
        BaseModel::factory('choice_novel')->where('id',$id)->update($data);
        return json_encode(['code'=>1]);
    }
    public function getHotmorecolumn(){

        $request = Request();
        $id = trim($request->input('id', 1));
        $choice_novel = DB::table('choice_novel');
        $where = [];
//        $name = trim($request->input('name', ''));
//        if ($name && !empty($name)) {
//            $where['name'] = $name;
//            $category = $category->where('name', 'like', '%' . $name . '%');
//        }
        $choice_novels = $choice_novel->where('choiceid',$id)->orderBy('sort', 'asc')->orderBy('addtime', 'desc')->paginate(23);

        if(!empty($choice_novels)){
            foreach ($choice_novels as $k=>$v){

                $query = "select novelid,novel.name,novel.img,novel.img,author,status,chapter,category.name as catename from novel LEFT JOIN category on novel.cateid=category.cateid where novelid={$v->novelid}";
                $novel = DB::selectone($query);
                $choice_novels[$k]->name = $novel->name;
                $choice_novels[$k]->author = $novel->author;
                $choice_novels[$k]->img = $novel->img;
                $choice_novels[$k]->catename = $novel->catename;
            }
        }

        return view('novel/hotmorecolumn', ['page_title' => '热推多本专栏管理', 'choice_novels' => $choice_novels, 'where' => $where,'id'=>$id]);
    }
    public function getNovelcolumn(){

        $request = Request();
        $id = trim($request->input('id', 1));
        $choice_novel = DB::table('choice_novel');
        $where = [];
        $NvNovelModel = new NvNovelModel();

        $choice_novels=$choice_novel1s=array();
        $novelchoice=$choice_novel2=array();
        $choice_novels = $choice_novel->where('choiceid',$id)->where('status',1)->orderBy('sort', 'asc')->orderBy('addtime', 'desc')->paginate(40);

        if(!empty($choice_novels)){
            foreach ($choice_novels as $k=>$v){
                $novel = $NvNovelModel->getNovelById($v->novelid);

                if(!empty($novel)){
                    $choice_novel1['name'] = $novel->name;
                    $choice_novel1['author'] = $novel->author;
                    $choice_novel1['img'] = $novel->img;
                    $choice_novel1['catename'] = $novel->nove_line_cate_id;

                    $choice_novel1['id'] = $v->id;
                    $choice_novel1['novelid'] = $v->novelid;
                    $choice_novel1['addtime'] = $v->addtime;
                    $choice_novel1['sort'] = $v->sort;
                    $choice_novel1['status'] = $v->status;
                    $choice_novel2[] = $choice_novel1;
//                    $choice_novels[$k] = $novel;

//                    $novelchoice[]['name'] = $novel->name;
//                    $novelchoice[]['author'] = $novel->author;
//                    $novelchoice[]['img'] = $novel->img;
//                    $novelchoice[]['catename'] = $novel->nove_line_cate_id;
//                    $novelchoice[]['id'] = $v->id;
//                    $novelchoice[]['novelid'] = $v->novelid;
//                    $novelchoice[]['sort'] = $v->sort;
//                    $novelchoice[]['status'] = $novel->status;
                }




            }
        }

        return view('novel/novelcolumn', ['page_title' => '小说专栏管理', 'choice_novels' => $choice_novel2, 'where' => $where,'id'=>$id]);
    }



    public function getMessage()
    {
        $request = Request();
        $message = DB::table('message');
        $where = [];
        $service = trim($request->input('service', ''));
        if ($service && !empty($service)) {
            $where['service'] = $service;
            $message = $message->where('service', 'like', '%' . $service . '%');
        }
        $messages = $message->paginate(23);;

        return view('novel/message', ['messages' => $messages, 'where' => $where, 'service' => $service]);
    }



    public function postAddService(){
        $request = Request();
        $service = trim($request->input('service', ''));
        $domain = trim($request->input('domain', ''));
        $data=array(
            'service'=>$service,
            'domain'=>$domain,
            'status'=>2
        );
        BaseModel::factory('message')->insert([$data]);
        return redirect('/novel/message');
    }

    public function postEditService(){
        $request = Request();
        $service = trim($request->input('service', ''));
        $domain = trim($request->input('domain', ''));
        $id = trim($request->input('id', ''));
        $data=array(
            'service'=>$service,
            'domain'=>$domain,
        );
        BaseModel::factory('message')->where('id',$id)->update($data);
        return redirect('/novel/message');
    }


    public function postGetService(){
//        $request = request();
        $query = "select * from message where status=1";
        $res = DB::select($query);
        if(!empty($res)){
            return json_encode(['code'=>1,'data'=>$res]);
        }else{
            return json_encode(['code'=>0,'data'=>$res]);
        }
    }
    public function postOperateMessage(){
        $request = Request();
        $status = trim($request->input('status', ''));
        $id = trim($request->input('id', ''));
        $data=array(
            'status'=>$status,
        );
        BaseModel::factory('message')->where('id',$id)->update($data);
        return json_encode(['code'=>1]);
    }

    public function getPublish()
    {
        $request = Request();
        $publish = DB::table('publish');
        $where = [];
        $type =$where['type']= trim($request->input('type', 0));
        $channel =$where['channel']= trim($request->input('channel', 0));
        $platform =$where['platform']= trim($request->input('platform', 0));
        if ($type && !empty($type)) {
            $where['type'] = $type;
            $publish = $publish->where('type',$type);
        }
        if ($channel && !empty($channel)) {
            $where['channel'] = $channel;
            $publish = $publish->where('channel',$channel);
        }
        if ($platform && !empty($platform)) {
            $where['platform'] = $platform;
            $publish = $publish->where('platform',$platform);
        }
        $publishes = $publish->where('status',1)->orderBy('addtime', 'desc')->paginate(23);

        $packArr = DB::table('android_update')->select('id','pack_name')->get();
        return view('novel/publish', [ 'publishes' => $publishes, 'where' => $where,'packArr'=>$packArr]);
    }

    public function postAddPublish(){
        $request = Request();
        $title = trim($request->input('title', ''));
        $channel = trim($request->input('channel', ''));
        $platform = trim($request->input('platform', ''));
        $type = trim($request->input('publish_type', ''));
        $showtype = trim($request->input('showtype', ''));
        $urlink = trim($request->input('urlink', ''));
        $novelid = trim($request->input('novelid', ''));
        $content = trim($request->input('content', ''));

        $res = DB::table('publish')->where('channel',$channel)->where('platform',$platform)->where('status',1)->first();
        if(!empty($res)){
            return view('show', ['message' => '不可以建相同类型的公告']);
        }
        $file = $request->file('imgFile');
        if($type !=3 && empty($file)){
            return view('show', ['message' => '该通告类型需要上传图片']);
        }

        if(strlen($content)>90){
            return view('show', ['message' => '纯文本通告最多输入30个中文字符']);
        }
        if($type == 2 && empty($urlink)){
            return view('show', ['message' => '该通告需要输入url链接']);
        }
        if($type == 3 && empty($content)){
            return view('show', ['message' => '纯文本通告需要输入内容']);
        }
        if($type == 4 && empty($novelid)){
            return view('show', ['message' => '该通告类型需要输入小说id']);
        }

        if ($file && $file->isValid()) {
            // 获取文件相关信息
//            $originalName = $file->getClientOriginalName(); // 文件原名
//            $ext = $file->getClientOriginalExtension();     // 扩展名
//            $realPath = $file->getRealPath();   //临时文件的绝对路径
//            $type1 = $file->getClientMimeType();     // image/jpeg
//            // 上传文件
//            $filename = time() . uniqid() . '.' . $ext;
//            // 使用我们新建的uploads本地存储空间（目录）
//            file_put_contents('images/' . $filename, file_get_contents($realPath));
            $cover = $this->UploadImg($file);
            $data['img'] = $cover;

        }
        if($type == 1){
            $data['content'] ="";
        }elseif ($type == 2){
            $data['content'] =$urlink;
        }elseif ($type == 3){
            $data['content'] =$content;
        }else{
            $data['content'] =$novelid;
        }

        $data['title']=$title;
        $data['type']=$type;
        $data['platform']=$platform;
        $data['channel']=$channel;
        $data['showtype']=$showtype;
        $data['status']=1;
        $data['addtime']=date("Y-m-d H:i:s",time());
        BaseModel::factory('publish')->insert([$data]);
        return redirect('/novel/publish');
    }
    public function postDelPublish(){
        $request = Request();
        $id = trim($request->input('id', 0));
        $data=array(
            'status'=>2
        );
        BaseModel::factory('publish')->where('id',$id)->update($data);
        return json_encode(['code'=>1]);
    }


    public function postGetPublish(){
        $request = Request();
        $id = trim($request->input('id', 0));
        $data = BaseModel::factory('publish')->where('id',$id)->first();
        return json_encode(['code'=>1,'data'=>$data]);
    }
    public function postEditPublish(){
        $request = Request();
        $title = trim($request->input('title', ''));
        $id = trim($request->input('pid', ''));
        $channel = trim($request->input('channel', ''));
        $platform = trim($request->input('platform', ''));
        $type = trim($request->input('publish_type', ''));
        $showtype = trim($request->input('showtype', ''));
        $urlink = trim($request->input('urlink', ''));
        $novelid = trim($request->input('novelid', ''));
        $content = trim($request->input('content', ''));
        $file = $request->file('imgFile');
        $res = DB::table('publish')->where('channel',$channel)->where('platform',$platform)->where('status',1)->where('id','<>',$id)->first();
        if(!empty($res)){
            return view('show', ['message' => '不可以建相同类型的公告']);
        }

        if(strlen($content)>90){
            return view('show', ['message' => '纯文本通告最多输入30个中文字符']);
        }
        if($type == 2 && empty($urlink)){
            return view('show', ['message' => '该通告需要输入url链接']);
        }
        if($type == 3 && empty($content)){
            return view('show', ['message' => '纯文本通告需要输入内容']);
        }
        if($type == 4 && empty($novelid)){
            return view('show', ['message' => '该通告类型需要输入小说id']);
        }
        if ($file && $file->isValid()) {
            // 获取文件相关信息
//            $originalName = $file->getClientOriginalName(); // 文件原名
//            $ext = $file->getClientOriginalExtension();     // 扩展名
//            $realPath = $file->getRealPath();   //临时文件的绝对路径
//            $type1 = $file->getClientMimeType();     // image/jpeg
//            // 上传文件
//            $filename = time() . uniqid() . '.' . $ext;
//            // 使用我们新建的uploads本地存储空间（目录）
//            file_put_contents('images/' . $filename, file_get_contents($realPath));
            $cover = $this->UploadImg($file);
            $data['img'] = $cover;

        }
        if($type == 1){
            $data['content'] ="";
        }elseif ($type == 2){
            $data['content'] =$urlink;
        }elseif ($type == 3){
            $data['content'] =$content;
        }else{
            $data['content'] =$novelid;
        }

        $data['title']=$title;
        $data['type']=$type;
        $data['platform']=$platform;
        $data['channel']=$channel;
        $data['showtype']=$showtype;
        $data['status']=1;
        $data['addtime']=date("Y-m-d H:i:s",time());
        BaseModel::factory('publish')->where('id',$id)->update($data);
        return redirect('/novel/publish');
    }


    public function getReadrecord()
    {

        $request = Request();
        $statistics = DB::table('novel_read_statistics');
        $where = [];
        $uuid = trim($request->input('uuid', ''));
        $imei = trim($request->input('imei', ''));
        $lastupdatetime = trim($request->input('lastupdatetime', ''));
        $day = trim($request->input('day', ''));
        $begintime = trim($request->input('begintime', ''));
        $endtime = trim($request->input('endtime', ''));
        $readday = trim($request->input('readday', 1));
        if ($uuid && !empty($uuid)) {
            $where['uuid'] = $uuid;
            $statistics = $statistics->where('uuid', 'like', '%' . $uuid . '%');
        }
        if ($imei && !empty($imei)) {
            $where['imei'] = $imei;
            $statistics = $statistics->where('imei', 'like', '%' . $imei . '%');
        }


        if(empty($lastupdatetime)){
            $lastupdatetime = date("Y-m-d",time());
        }
        if ($lastupdatetime && !empty($lastupdatetime)) {
            $time = strtotime($lastupdatetime);
            $time1 = strtotime(date('Y-m-d',$time));
            $where['lastupdatetime'] = $time1+86400;
            $statistics = $statistics->where('lastupdatetime', '<' ,$time1+86400);
        }

        if($readday == 1){
            if ($day && !empty($day)) {
                $where['day'] = $day;
                $statistics = $statistics->where('day', $day);
            }
        }elseif ($readday == 2){
            if ($day && !empty($day)) {
                $where['day'] = $day;
                $statistics = $statistics->where('day', '>=' ,$day);
            }
        }elseif ($readday == 3){
            if ($day && !empty($day)) {
                $where['day'] = $day;
                $statistics = $statistics->where('day', '<=' ,$day);
            }
        }


        if ($begintime && !empty($begintime)) {
            $where['time'] =  $begintime;
            $statistics = $statistics->where('time', '>' ,$begintime*60000);
        }
        if ($endtime && !empty($endtime)) {
            $where['time'] = $endtime;
            $statistics = $statistics->where('time', '<' ,$endtime*60000);
        }

        $reads = $statistics->orderBy('id', 'asc')->paginate(23);

        return view('novel/readrecord',
            [
                'reads' => $reads,
                'where' => $where,
                'uuid' => $uuid,
                'imei' => $imei,
                'lastupdatetime' => $lastupdatetime,
                'day' => $day,
                'begintime' => $begintime,
                'endtime' => $endtime,
                'readday'=>$readday
            ]);
    }
    public function getReaddetail()
    {

        $request = Request();
        $record = DB::table('novel_read_record');
        $where = [];
        $uuid = trim($request->input('uuid', ''));

        $begintime = trim($request->input('begintime', date('Y-m-01', strtotime(date("Y-m-d")))));
        $endtime = trim($request->input('endtime', date('Y-m-d',time())));


        $days = $this->getDays($begintime, $endtime);

        for($i=0;$i<count($days);$i++){
            $data[$i]['day'] = $days[$i];
            $tobegin = strtotime($days[$i]);
            $toend = strtotime($days[$i])+86400;
            $query = "select time from novel_read_statistics where uuid='".$uuid."' and  lastupdatetime>$tobegin and lastupdatetime<$toend";
            $res = DB::selectone($query);
            if(!empty($res)){
                $data[$i]['time'] = round($res->time/60000);
            }else{
                $data[$i]['time'] = 0;
            }

            $do = "select *  from novel_read_statistics where uuid='".$uuid."' and lastupdatetime<=$toend";
            $result = DB::select($do);
            if(!empty($result)){
                $data[$i]['time1'] = count($result);
            }else{
                $data[$i]['time1'] = 0;
            }
            $data[$i]['time2'] = $do;
        }



        return view('novel/readdetail',
            [
                'datas' => $data,
                'where' => $where,
                'uuid' => $uuid,
                'begintime' => $begintime,
                'endtime' => $endtime
            ]);
    }
    public function getDays($beginDate, $endDate)
    {
        $today = date('Y-m-d', time());

        $days[] = $beginDate;
        $d = '';
        for ($i = 1; $i <= 356 && $d != $endDate; $i++) {
            $d = date('Y-m-d', strtotime($beginDate) + 24 * 3600 * $i);
            if ($d > $today || $d > $endDate) {
                break;
            }
            $days[] = $d;
        }

        return $days;
    }


    public function getSendcode(Request $request){

        $phone = $request->input('phone', '');
        $type = $request->input('type', 1);
        $time = time();
        $sig = md5('sc%7*g'.$time.'@!$%');
        $code = rand(1000,9999);
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

            //查询用户发送的验证码信息，10分钟之内只能发送三次，24小时只能发送5次
            $wheretime = time()-600;
            $wheretime24 = time()-86400;
            $res = DB::table('sendcode_record')->where('phone',$phone)->where('type',$type)->where('addtime','>',$wheretime)->get();
            if(count($res) <3){
                //判断24小时是否大于5次
                $res24 = DB::table('sendcode_record')->where('phone',$phone)->where('type',$type)->where('addtime','>',$wheretime24)->get();
                if(count($res24) <5){//可以发送

                    $domain = $data->domain;
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



//            $response='';



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


    public function getRegister(Request $request){
        $phone = $request->input('phone', '');
        $pass = $request->input('password', '');
        $userfrom = $request->input('userfrom', '');
        $code = $request->input('code', '');
        $judgepass = md5('abc'.$pass);

        $ret = DB::table('sendcode_record')->where('phone',$phone)->orderBy('addtime','desc')->first();
        if(!empty($ret)){
            if($ret->code != $code){
                return json_encode(['status'=>0,'message'=>'验证码错误']);
            }
        }else{
            return json_encode(['status'=>0,'message'=>'验证码错误']);
        }


        $res = DB::table('novel_user')->where('phone',$phone)->first();



        if(!empty($res)){
            return json_encode(['status'=>0,'message'=>'该手机号已注册']);
        }
        $spreadcode = $this->createSpreadCode();
        $retu = DB::table('novel_user')->where('spreadcode',$spreadcode)->first();
        if(!empty($retu)){
            $spreadcode = $this->createSpreadCode();
            $data = array(
                'regtime'=>date('Y-m-d H:i:s',time()),
                'phone'=>$phone,
                'password'=>$judgepass,
                'sex'=>3,
                'userfrom'=>$userfrom,
                'spreadcode'=>$spreadcode
            );
            DB::table('novel_user')->insert($data);
            $uid = DB::table('novel_user')->insertGetId($data);
        }else{
            $data = array(
                'regtime'=>date('Y-m-d H:i:s',time()),
                'phone'=>$phone,
                'password'=>$judgepass,
                'sex'=>3,
                'userfrom'=>$userfrom,
                'spreadcode'=>$spreadcode
            );
            DB::table('novel_user')->insert($data);
        }

        //给userfrom用户增加短文阅读次数
        if(!empty($userfrom)){
            $shareUser = DB::table('read_times_set')->where('type',3)->first();
            $frequency = $shareUser->times;
            $data = array(
                'uuid'=>$userfrom,
                'addtime'=>time(),
                'frequency'=>$frequency,
                'type'=>3,
                'kind'=>1,
            );
            DB::table('essay_read_times')->insert($data);
        }




        return json_encode(['status'=>1,'message'=>'注册成功']);
//        if(!empty($res)){
//            if(empty($res->imei)){
//                $datas['imei']=$imei;
//            }
//            DB::table('novel_user')->where('phone',$phone)->update($datas);
//            $uid = $res->id;
//            $token = $this->token($uid);
//            $data['uuid']=$uid;
//            $data['token']=$token;
//            return json_encode(['status'=>1,'message'=>'成功','data'=>$data]);
//        }else{
//            return json_encode(['status'=>0,'message'=>'账号或密码错误']);
//        }
    }


    public function getWebset()
    {
        $request = Request();
        $category = DB::table('category');
        $where = [];
        $name = trim($request->input('name', ''));
        if ($name && !empty($name)) {
            $where['name'] = $name;
            $category = $category->where('name', 'like', '%' . $name . '%');
        }
        $categorys = $category->orderBy('sort', 'asc')->paginate(23);;

        return view('novel/webset', [ 'categorys' => $categorys, 'where' => $where, 'name' => $name]);
    }
    public function getReadfrequencyset()
    {

        $data1 = DB::table('read_times_set')->where('type',1)->first();
        $data2 = DB::table('read_times_set')->where('type',2)->first();
        $data3 = DB::table('read_times_set')->where('type',3)->first();

        return view('novel/readfrequencyset',[ 'data1'=>$data1,'data2'=>$data2,'data3'=>$data3 ]);
    }

    public function postEditReadfrequency(){
        $request = Request();
        $type1 = trim($request->input('type1', ''));
        $type2 = trim($request->input('type2', ''));
        $type3 = trim($request->input('type3', ''));
        $extra = trim($request->input('extra', ''));


        if(!empty($type1)){
            $data1=array(
                'times'=>$type1,
            );
            BaseModel::factory('read_times_set')->where('type',1)->update($data1);
        }

        if(!empty($type3)){
            $data3=array(
                'times'=>$type3,
            );
            BaseModel::factory('read_times_set')->where('type',3)->update($data3);
        }

        if(!empty($type2)){
            $data2=array(
                'times'=>$type2,
            );
            BaseModel::factory('read_times_set')->where('type',2)->update($data2);
        }
        if(!empty($extra)){

            $data = array(
                'extra'=>$extra
            );
            BaseModel::factory('read_times_set')->where('type',2)->update($data);
        }


        return redirect('/novel/readfrequencyset');
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



    public function getNewcate()
    {
        $CateModelModel = new CateModel();
        $categorys = $CateModelModel->getCate();

        $where=[];

        $newcate = DB::table('newcate')->where('status',1)->orderBy('id','asc')->paginate(23);

        return view('novel/newcate', [ 'where'=>$where, 'categorys' => $categorys ,'channels'=>$newcate]);
    }




    public function postAddNewcate1(){
        $request = Request();
//        $cateid = $request->input('cateid', 999);
        $data['name'] = $request->input('name', 999);
        $data['sort'] = $request->input('sort', 999);
        $data['cate'] = $request->input('kind', 999);
        $data['catename'] = $request->input('kindname', 999);
        $data['status'] = 1;
        $data['addtime'] = date("Y-m-d H:i:s");
        BaseModel::factory('newcate')->insert([$data]);
        return json_encode(['code'=>1]);
    }


    public function postAddNewcate(){
        $request = Request();
        $name = trim($request->input('name', ''));
        $sort = trim($request->input('sort', ''));
        $cateids = $request->input('cateids', '');
        $cate = implode(',',$cateids);
        if(empty($name)){
            return view('show', ['message' => '分类名称必填']);
        }
        if(empty($sort)){
            return view('show', ['message' => '排序必填']);
        }
        if(empty($cateids)){
            return view('show', ['message' => '子分类必选']);
        }

        $file = $request->file('imgFile');


        if ($file && $file->isValid()) {
            // 获取文件相关信息
//            $originalName = $file->getClientOriginalName(); // 文件原名
//            $ext = $file->getClientOriginalExtension();     // 扩展名
//            $realPath = $file->getRealPath();   //临时文件的绝对路径
//            $type1 = $file->getClientMimeType();     // image/jpeg
//            // 上传文件
//            $filename = time() . uniqid() . '.' . $ext;
//            // 使用我们新建的uploads本地存储空间（目录）
//            file_put_contents('images/' . $filename, file_get_contents($realPath));
            $cover = $this->UploadImg($file);
            $data['img'] = $cover;

        }else{
            return view('show', ['message' => '分类图片未上传']);
        }


        $data['name']=$name;
        $data['sort']=$sort;
        $data['cate']=$cate;

        $data['status']=1;
        $data['addtime']=date("Y-m-d H:i:s",time());
        BaseModel::factory('newcate')->insert([$data]);
        return redirect('/novel/newcate');
    }
    public function postEditNewcate(){
        $request = Request();
        $cid = trim($request->input('cid', ''));
        $chname = trim($request->input('chname', ''));
        $esort = trim($request->input('esort', ''));
        $ecateids = $request->input('ecateids', '');
        $cate = implode(',',$ecateids);

        if(empty($chname)){
            return view('show', ['message' => '分类名称必填']);
        }
        if(empty($esort)){
            return view('show', ['message' => '排序必填']);
        }
        if(empty($ecateids)){
            return view('show', ['message' => '子分类必选']);
        }

        $file = $request->file('imgFile');


        if ($file && $file->isValid()) {
            // 获取文件相关信息
//            $originalName = $file->getClientOriginalName(); // 文件原名
//            $ext = $file->getClientOriginalExtension();     // 扩展名
//            $realPath = $file->getRealPath();   //临时文件的绝对路径
//            $type1 = $file->getClientMimeType();     // image/jpeg
//            // 上传文件
//            $filename = time() . uniqid() . '.' . $ext;
//            // 使用我们新建的uploads本地存储空间（目录）
//            file_put_contents('images/' . $filename, file_get_contents($realPath));
//            $data['img'] = url('images/' . $filename);

            $cover = $this->UploadImg($file);
            $data['img'] = $cover;

        }


        $data['name']=$chname;
        $data['sort']=$esort;
        $data['cate']=$cate;

        $data['status']=1;
        $data['addtime']=date("Y-m-d H:i:s",time());
        BaseModel::factory('newcate')->where('id',$cid)->update($data);
        return redirect('/novel/newcate');
    }
    public function postDelNewcate(){
        $request = Request();
        $cid = $request->input('ids', '');
        $data['status'] =0;
        BaseModel::factory('newcate')->whereIn('id', $cid)->update($data);
        return json_encode(['code'=>1]);
    }


    public function getNovelclick()
    {
        $request = Request();
        $click = DB::table('novel_click_record');
        $where = [];

        $clicks = $click->select('novelid','num','name')
            ->orderBy('num','desc')
            ->paginate(23);

        return view('novel/novelclick', [  'categorys' => $clicks, 'where' => $where ]);
    }
    public function getNovelsearch()
    {
        $request = Request();
        $search = DB::table('name_search_record');
        $where = [];
        $searchs = $search->select(DB::raw('count(name) as num'),'name','id')
            ->groupBy('name')
            ->orderBy('num','desc')
            ->paginate(23);
        return view('novel/novelsearch', [ 'categorys' => $searchs, 'where' => $where]);
    }
    //---end


}

