@extends('layouts.dashbord')
@section('content')
    <h2>金币充值调整</h2>
    <p>登录后台 ->充值管理 ->金币充值调整</p>
    <img src="{{asset('bower_components/helper')}}/image011.png">
    <p>点击金币调整按钮</p>
    <img src="{{asset('bower_components/helper')}}/image013.png">
    <p>说明：先输入搜索内容，为要充值人的游戏mid或者昵称，再点搜索</p>
    <img src="{{asset('bower_components/helper')}}/image015.png">
    <p style="color:red;">金币调整：正数表示加金币，负数减金币</p>
@endsection