@extends('layouts.dashbord')
@section('content')
    <h2>添加产品</h2>
    <p>
        登录后台 -> 游戏设置 -> 产品管理 -> 添加产品<br/>
        需填入产品名称和描述，生成的产品id
        需告诉app开发人员
    </p>
    <img src="{{asset('bower_components/helper')}}/image021.png">
    <img src="{{asset('bower_components/helper')}}/image023.png">
    <p>
        添加产品的充值号,支付宝号，微信号，汇付宝号在账号管理的相应地方添加<br/>
        <span style="color:red;">注意：若有第三方支付，要在这加上汇付宝号</span>
    </p>
@endsection