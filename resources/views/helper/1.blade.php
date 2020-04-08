@extends('layouts.dashbord')
@section('content')
    <h2>游戏用户管理</h2>
    <p>游戏用户列表查看，设置禁止用户登录，重置密码和重置保险箱密码</p>
    <img src="{{asset('bower_components/helper')}}/image001.png" style="height: 327px;">
    <p>可以启用高级搜索，机器码，注册日期区间，金币区间为总金币之间的筛选</p>
    <img src="{{asset('bower_components/helper')}}/image100.png">
    <h2 id="s003">是否禁止用户登入游戏</h2>
    <h3>游戏用户管理->状态的切换设置是否允许登入</h3>
    <img src="{{asset('bower_components/helper')}}/image003.png">

@endsection