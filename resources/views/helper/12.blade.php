@extends('layouts.dashbord')
@section('content')
    <h2>添加版本</h2>
    <p>在产品列表页，可对相应的产品添加版本，每个版本的版本号不能一样，为整型。可对每个版本设置不同的游戏开关</p>
    <img src="{{asset('bower_components/helper')}}/image025.png">
    <p>此产品下的版本号不可低于之前的，版本状态为线上表示使用线上的应用路由url</p>
    <img src="{{asset('bower_components/helper')}}/image027.png">
    <p>支付类型可选5种:ios支付，微信和支付宝支付，汇付宝支付，微信支付，支付宝支付</p>
    <img src="{{asset('bower_components/helper')}}/image029.png">
@endsection