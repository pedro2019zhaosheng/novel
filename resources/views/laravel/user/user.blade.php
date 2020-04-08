<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head id="Head1">
    <title></title>
    <link rel="stylesheet" href='{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css")}}'>
      <link rel="stylesheet" href='{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}'>
    <link href="{{asset('/newsty')}}/layout.css" rel="stylesheet" type="text/css"/>
    <script src="{{ asset("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js")}}"></script>
    <script type="text/javascript" src="{{asset('/newsty')}}/common.js"></script>
    <style type="text/css">
        .pagination li {
            padding-left: 5px;
        }
        .jconfirm.jconfirm-white .jconfirm-box .buttons button.btn-default:hover {
            background: #ddd;
        }

        .jconfirm.jconfirm-white .jconfirm-box .buttons button.btn-default {
            box-shadow: none;
            color: #333;
        }
        .jconfirm.jconfirm-white .jconfirm-box .buttons button {
            border: none;
            background-image: none;
            text-transform: uppercase;
            font-size: 14px;
            font-weight: bold;
            text-shadow: none;
            -webkit-transition: background .1s;
            transition: background .1s;
            color: #fff;
        }
        .col-md-offset-4 {
            margin-left: 33.33333333%;
        }

        .col-md-4 {
            width: 33.33333333%;
        }

        .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9 {
            float: left;
        }

        .col-sm-offset-3 {
            margin-left: 25%;
        }

        .col-sm-6 {
            width: 50%;
        }

        .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9 {
            float: left;
        }

        .col-xs-offset-1 {
            margin-left: 8.33333333%;
        }

        .col-xs-10 {
            width: 83.33333333%;
        }

        .col-xs-1, .col-xs-10, .col-xs-11, .col-xs-12, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9 {
            float: left;
        }

        .col-lg-1, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-xs-1, .col-xs-10, .col-xs-11, .col-xs-12, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9 {
            position: relative;
            min-height: 1px;
            padding-right: 32%;
            padding-left: 32%;
        }
        .jconfirm .jconfirm-box div.title-c .title {
            font-size: inherit;
            font-family: inherit;
            display: inline-block;
            vertical-align: middle;
            padding-bottom: 15px;
        }
        .jconfirm .jconfirm-box div.content-pane .content {
            position: absolute;
            top: 0;
            left: 0;
            -webkit-transition: all .2s ease-in;
            transition: all .2s ease-in;
            right: 0;
        }
        .content {
            clip: rect(0px 170px 250px -100px)!important;
        }
        .content {
            min-height: 30px;
            padding: 15px;
            margin-right: auto;
            margin-left: auto;
            padding-left: 15px;
            padding-right: 15px;
        }
        .jconfirm.jconfirm-white .jconfirm-box .buttons {
            float: right;
        }
        .jconfirm .jconfirm-box .buttons {
            padding-bottom: 15px;
        }

        * {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        .jconfirm.jconfirm-white .jconfirm-box {
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
        }

        .jconfirm .jconfirm-box {
            opacity: 1;
            -webkit-transition-property: -webkit-transform, opacity, box-shadow;
            transition-property: transform, opacity, box-shadow;
        }

        .jconfirm .jconfirm-box {
            background: #fff;
            border-radius: 4px;
            position: relative;
            outline: none;
            padding: 15px 15px 0;
        }
        .text{
            height: 26px;
        }
    </style>
</head>
<body>
<!-- 头部菜单 Start -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="title">
    <tr>
        <td width="19" height="25" valign="top" class="Lpd10">
            <div class="arr">
            </div>
        </td>
        <td width="1232" height="25" valign="top" align="left">
            你当前位置：系统用户管理 - 用户管理
        </td>
    </tr>
</table>
<!-- 头部菜单 End -->
<script src='{{ asset("/bower_components/AdminLTE/plugins/my97date/WdatePicker.js")}}'></script>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="titleQueBg">
    <tr>
        <form name="form1" method="get" action="" id="form1">
            <td align="center" style="width: 80px">
                查询：
            </td>
            <td>
                <input type="text" class="text" value="{{$id}}" name="id" placeholder="用户id" style="width:100px;display: inline-block;">
                <input type="text" class="text" value="{{$name}}" name="name" placeholder="昵称" style="width:150px;display: inline-block;">
                <input type="text" class="text" value="{{$email}}" name="email" placeholder="用户账号" style="width:150px;display: inline-block;">
                <input type="submit" name="btnQuery" value="查询" id="btnQuery" class="btn wd1"/>
                <input type="button" name="btnRefresh" value="刷新" onclick="window.location.href='{{url('sysuser')}}'"
                       class="btn wd1"/>
                <a type="button" href="{{url('sysuser/user-add')}}" class="btn btn-default l" style="margin-left: 5%">添加用户</a>
            </td>
        </form>
    </tr>
</table>
<div class="clear"></div>
<div id="content">
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="box" id="list">
        <tr align="center" class="bold">
            <td class="listTitle">用户id</td>
            <td class="listTitle">昵称</td>
            <td class="listTitle">用户账号</td>
            <td class="listTitle">用户角色</td>
            <td class="listTitle">创建时间</td>
            <td class="listTitle">最后更新时间</td>
            <td class="listTitle">操作</td>
        </tr>

        <?php $i = 1;?>
        @foreach($users as $user)
            <tr align="center" @if($i++%2==0) class="list" @else class="listBg"
                @endif onmouseover="currentcolor=this.style.backgroundColor;this.style.backgroundColor='#caebfc';this.style.cursor='default';"
                onmouseout="this.style.backgroundColor=currentcolor">

                <td>{{$user->id}}</td>
                <td>{{ $user->name }}</td>
                <td>
                    {{$user->email}}
                </td>
                <td>
                    {{$user->role}}
                </td>
                <td>
                    {{$user->created_at}}
                </td>
                <td>
                    {{ $user->updated_at }}
                </td>
                <td>
                    <a href="{{url('sysuser/user-edit',['id'=>$user->id])}}">编辑</a>
                    <a href="#" val="{{$user->id}}" class="userdel">删除</a>
                </td>
            </tr>
        @endforeach
    </table>
</div>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
        <td class="listTitleBg">
        </td>
        <td align="right" class="page">
            <div id="anpPage">
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td valign="center" nowrap="true" style="width:40%;">总记录：{{$users->total()}}
                            　页码：{{$users->currentPage()}}/{{$users->count()}}　每页：{{$users->perPage()}}
                        </td>
                        <td valign="center" nowrap="true"
                            style="width:60%;">{{ $users->appends($where)->render() }}</td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>
<script>
    $(function () {
        $('.userdel').click(function(){
            var thisid=$(this).attr('val');
            $.confirm({
                confirmButton: '确定',
                cancelButton: '取消',
                title: '确认删除？!',
                content: '确定要删除这条用户吗?',
                columnClass:'col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-10 col-xs-offset-1',
                confirm: function(){
                    window.location.href="{{url('sysuser/del-user')}}/"+thisid;
                },
            });
        });

    });
</script>
</body>
</html>
