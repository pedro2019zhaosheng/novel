<!DOCTYPE html>
<!-- saved from url=(0034){{url('helper')}}/1 -->
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>首页  游戏后台帮助手册</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
    <meta content="telephone=no" name="format-detection">
    <link href='{{asset("/bower_components/helper/base.css")}}' rel="stylesheet" type="text/css">
    <link href="{{asset('/bower_components/helper')}}/layout.css" rel="stylesheet" type="text/css">
    <link href="{{asset('/bower_components/helper')}}/help.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="{{asset('/bower_components/helper')}}/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" src="{{asset('/bower_components/helper')}}/Widgets.js"></script>
</head>
<body>
<div class="container">
<!-- header开始-->
<ul class="help-problem">
    <li><h4><b>·</b>&nbsp;游戏用户管理</h4>
        <ul class="hide" @if($hlperid==1 || $hlperid==2) style="display: block;" @endif>
            <li><a href="{{url('helper')}}?hlperid=1" @if($hlperid==1) style="color: rgb(66, 152, 186);" @endif>是否禁止用户登入游戏</a></li>
            <li><a href="{{url('helper')}}?hlperid=2" @if($hlperid==2) style="color: rgb(66, 152, 186);" @endif>重置游戏密码和保险箱密码</a></li>
        </ul>
    </li>
    <li><h4><b>·</b>&nbsp;用户充值管理</h4>
        <ul class="hide" @if($hlperid==3 || $hlperid==4) style="display: block;" @endif>
            <li><a href="{{url('helper')}}?hlperid=3" @if($hlperid==3) style="color: rgb(66, 152, 186);" @endif>充值记录</a></li>
            <li><a href="{{url('helper')}}?hlperid=4" @if($hlperid==4) style="color: rgb(66, 152, 186);" @endif>充值金币调整</a></li>
        </ul>
    </li>
    <li><h4><b>·</b>&nbsp;提现管理</h4>
        <ul class="hide" @if($hlperid==5 || $hlperid==6) style="display: block;" @endif>
            <li><a href="{{url('helper')}}?hlperid=5" @if($hlperid==5) style="color: rgb(66, 152, 186);" @endif>提现审核</a></li>
            <li><a href="{{url('helper')}}?hlperid=6" @if($hlperid==6) style="color: rgb(66, 152, 186);" @endif>提现记录</a></li>
        </ul>
    </li>
    <li><h4><b>·</b>&nbsp;用户反馈管理</h4>
        <ul class="hide" @if($hlperid==7) style="display: block;" @endif>
            <li><a href="{{url('helper')}}?hlperid=7" @if($hlperid==7) style="color: rgb(66, 152, 186);" @endif>回复反馈和忽略反馈</a></li>
        </ul>
    </li>
    <li><h4><b>·</b>&nbsp;用户记录</h4>
        <ul class="hide" @if($hlperid==8 || $hlperid==9 || $hlperid==10) style="display: block;" @endif>
            <li><a href="{{url('helper')}}?hlperid=8" @if($hlperid==8) style="color: rgb(66, 152, 186);" @endif>保险库记录</a></li>
            <li><a href="{{url('helper')}}?hlperid=9" @if($hlperid==9) style="color: rgb(66, 152, 186);" @endif>游戏记录</a></li>
            <li><a href="{{url('helper')}}?hlperid=10" @if($hlperid==10) style="color: rgb(66, 152, 186);" @endif>游戏输赢记录</a></li>
        </ul>
    </li>
    <li><h4><b>·</b>&nbsp;产品管理</h4>
        <ul class="hide" @if($hlperid==11 || $hlperid==12 || $hlperid==13) style="display: block;" @endif>
            <li><a href="{{url('helper')}}?hlperid=11" @if($hlperid==11) style="color: rgb(66, 152, 186);" @endif>添加产品</a></li>
            <li><a href="{{url('helper')}}?hlperid=12" @if($hlperid==12) style="color: rgb(66, 152, 186);" @endif>添加版本</a></li>
            <li><a href="{{url('helper')}}?hlperid=13" @if($hlperid==13) style="color: rgb(66, 152, 186);" @endif>产品公告管理</a></li>
        </ul>
    </li>
    <li><h4><b>·</b>&nbsp;后台登录用户管理</h4>
        <ul class="hide" @if($hlperid==14 || $hlperid==15 || $hlperid==16 || $hlperid==17 || $hlperid==18 || $hlperid==19) style="display: block;" @endif>
            <li><a href="{{url('helper')}}?hlperid=14" @if($hlperid==14) style="color: rgb(66, 152, 186);" @endif>用户管理</a></li>
            <li><a href="{{url('helper')}}?hlperid=15" @if($hlperid==15) style="color: rgb(66, 152, 186);" @endif>用户添加，编辑和删除</a></li>
            <li><a href="{{url('helper')}}?hlperid=16" @if($hlperid==16) style="color: rgb(66, 152, 186);" @endif>角色管理</a></li>
            <li><a href="{{url('helper')}}?hlperid=17" @if($hlperid==17) style="color: rgb(66, 152, 186);" @endif>角色分配权限</a></li>
            <li><a href="{{url('helper')}}?hlperid=18" @if($hlperid==18) style="color: rgb(66, 152, 186);" @endif>权限管理</a></li>
            <li><a href="{{url('helper')}}?hlperid=19" @if($hlperid==19) style="color: rgb(66, 152, 186);" @endif>系统日志</a></li>
        </ul>
    </li>
    <li><h4><b>·</b>&nbsp;账号管理</h4>
        <ul class="hide" @if($hlperid==20 || $hlperid==21 || $hlperid==22 || $hlperid==23) style="display: block;" @endif>
            <li><a href="{{url('helper')}}?hlperid=20" @if($hlperid==20) style="color: rgb(66, 152, 186);" @endif>提现账号</a></li>
            <li><a href="{{url('helper')}}?hlperid=21" @if($hlperid==21) style="color: rgb(66, 152, 186);" @endif>支付宝充值账号</a></li>
            <li><a href="{{url('helper')}}?hlperid=22" @if($hlperid==22) style="color: rgb(66, 152, 186);" @endif>微信充值账号</a></li>
            <li><a href="{{url('helper')}}?hlperid=23" @if($hlperid==23) style="color: rgb(66, 152, 186);" @endif>汇付宝充值账号</a></li>
        </ul>
    </li>
</ul>
<script>
url = '{{url("helper")}}/1'


a = $("a[href='" + url + "']");
a.parent().parent().slideDown();
a.css("color", "#4298ba");

$("h4").toggle(function(){
    $(this).next("ul").slideDown(300);
},function(){
    $(this).next("ul").slideUp(300);
})

</script>
<!-- header结束-->



<!-- 一级导航nav -->
    <div class="title">
        <p>帮助中心 <span style="float:right;"><a href="{{url('user')}}" style="color: #FFFFFF;"><<返回</a></span></p>
    </div>
    <div class="help-main">
        @yield('content')
    </div>
    </div>
<script type="text/javascript">


function placeholderSupport() {
    return 'placeholder' in document.createElement('input');
}

$(function(){
  if(!placeholderSupport()){
    $("input[type!='password'],textarea").bind({
        "focus":function(){
            var placeholderVal = $(this).attr("placeholder");
            var realVal = $(this).val();
            if($.trim(realVal)==placeholderVal){
                $(this).val("");
                $(this).css('color', '#000');
            }
        },
        "blur":function(){
            var placeholderVal = $(this).attr("placeholder");
            var realVal = $(this).val();
            if($.trim(realVal)==""){
                $(this).val(placeholderVal);
                $(this).css('color', '#aaa');
            }
        }
    });

    $("input[type='text'],textarea").each(function(i,n){
        if ($(this).val() != "")
          return;
        $(this).val($(this).attr("placeholder"));
        $(this).css('color', '#aaa');
    });


    $("input[type='password']").bind({
        "focus":function(){
            var placeholderVal = $(this).attr("placeholder");
            var realVal = $(this).val();
            if($.trim(realVal)==placeholderVal){
                var copy_this = $(this).clone(true,true);
                $(copy_this).attr("type","password");
                $(copy_this).insertAfter($(this));
                $(this).remove();
                $(copy_this).val("");
                $(copy_this).focus();
                $(this).css('color', '#000');
            }
        },
        "blur":function(){
            var placeholderVal = $(this).attr("placeholder");
            var realVal = $(this).val();
            if($.trim(realVal)==""){
                var copy_this = $(this).clone(true,true);
                $(copy_this).attr("type","text");
                $(copy_this).insertAfter($(this));
                $(this).remove();
                $(copy_this).val(placeholderVal);
                $(copy_this).css('color', '#aaa');
            }
        }
    });

    $("input[type='password']").each(function(i,n){
        var placeHolderVal = $(this).attr("placeholder");
        var copy_this = $(this).clone(true,true);
        $(copy_this).attr("type","text");
        $(copy_this).insertAfter($(this));
        $(this).remove();
        $(copy_this).val(placeHolderVal);
        $(copy_this).css('color', '#aaa');
    });
  }
});
 </script>
</body></html>