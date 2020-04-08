<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/announce', function () {
    return view('announce');
});

Route::get('/service', function () {
    return view('service');
});

Route::get('novel/share', function () {
    $request = Request();
    $id= $request->input('uuid',0);
    return view('share',[ 'uuid' => $id]);
});


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/


//小说二期接口begin
Route::post('api/novel/edition/book-store', 'Api\NovelEditionController@postBookStore');
Route::post('api/novel/edition/list', 'Api\NovelEditionController@postList');
Route::post('api/novel/edition/get-novel-under-rank', 'Api\NovelEditionController@postGetNovelUnderRank');
Route::post('api/novel/edition/edit-user-data', 'Api\NovelEditionController@postEditUserData');
Route::post('api/novel/edition/get-another-batch', 'Api\NovelEditionController@postGetAnotherBatch');
Route::post('api/novel/edition/get-my-spread', 'Api\NovelEditionController@postGetMySpread');
Route::post('api/novel/edition/get-another-search', 'Api\NovelEditionController@postGetAnotherSearch');

//小说二期接口end

Route::group(['middleware' => 'throttle:50000,1'], function () {
    //Api
    Route::controller('api/novel', 'Api\NovelController');
//    Route::controller('api/noveledition', 'Api\NovelEditionController');
});

Route::group(['middleware' => 'web'], function () {
    Route::auth();
    Route::controller('auth', 'Auth\AuthController');

    Route::get('show', 'HomeController@show');
    Route::get('home', 'HomeController@index');

    //后台用户手册
    Route::controller('helper', 'Manual\HelpController');
    //系统用户管理
    Route::controller('sysuser', 'Laravel\UserController');
    //角色管理
    Route::controller('role', 'Laravel\RoleController');
    //权限管理
    Route::controller('syslog', 'Laravel\SyslogController');
    //权限管理
    Route::controller('novel', 'Novel\NovelController');
    Route::controller('premission', 'Laravel\PremissionController');
    Route::controller('home', 'HomeController');
    Route::controller('statistics', 'Novel\StatisticsController');
});

Route::get('permissions', function () {
    return view('permissions');
});


