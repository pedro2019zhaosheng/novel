@extends('layouts.dashbord')
@section('content')
    <h2>提现审核</h2>
    <p>
        登录后台 -> 提现管理 -> 提现审核<br/>
        <span style="color:red;">注意：该操作需要支付宝证书，证书只能在ie上运行，需要支付宝支付密码</span><br/>
        审核提现页面，勾选要提现的用户，如果不通过审核，要注明不通过原因，这个操作会发送到app的客服通知消息，审核通过会跳转到支付宝支付页面，成功付款后，提现通过
    </pre>
    <img src="{{asset('bower_components/helper')}}/image017.png">
@endsection