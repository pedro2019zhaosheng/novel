@extends('layouts.dashbord')
@section('content')
    <h2>权限管理</h2>
    <p>
        登录后台 -> 系统用户管理 -> 权限管理<br/>
        添加：点击添加权限按钮<br/>
        删除，编辑点相应链接 <br/>
    </p>
    <img src="{{asset('bower_components/helper')}}/image051.png">
    <p style="color:red;">添加权限注意：</p>
    <img src="{{asset('bower_components/helper')}}/image053.png">
    <p>
        权限名称：为访问当前操作的url路由<br/>
        标签：为当前操作的简单描述，此标签记录到系统日志的操作描述里<br/>
        勾选记录系统日志单选，当前的每次操作会记录到系统日志里 <br/>
    </p>
@endsection