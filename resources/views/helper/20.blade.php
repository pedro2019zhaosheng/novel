@extends('layouts.dashbord')
@section('content')
    <h2>账号管理</h2>
    <p>用于游戏用户的提现，充值的账号统一管理</p>
    <h2>提现账号</h2>
    <p>登录后台 -> 账号管理 -> 提现账号，充值的账号统一管理</p>
    <img src="{{asset('bower_components/helper')}}/image057.png">
    <p style="color:red;">
        提现账号当前只能使用一个，通过是否启用的切换按钮选择当前提现账号
        当编辑或添加提现账号时，状态若选为启用，则当前账号被启用
    </p>
    <img src="{{asset('bower_components/helper')}}/image059.png">
@endsection