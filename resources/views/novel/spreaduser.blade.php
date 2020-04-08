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
    <script src="{{ asset("/bower_components/AdminLTE/bootstrap/js/bootstrap.js")}}"></script>
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
            你当前位置：小说管理 - 小说裂变用户管理
        </td>
    </tr>
</table>
<!-- 头部菜单 End -->
<script src='{{ asset("/bower_components/AdminLTE/plugins/my97date/WdatePicker.js")}}'></script>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="titleQueBg">
    <tr>
        <form name="form1" method="get" action="" id="form1">
            <td align="center" style="width: 150px">
                会员ID/昵称/手机号：
            </td>
            <td>
                <input type="text" class="text" value="{{$name}}" name="name" placeholder="" style="width:150px;display: inline-block;">
                <input type="submit" name="btnQuery" value="查询" id="btnQuery" class="btn wd1"/>
                {{--<input type="button" name="btnRefresh" value="刷新" onclick="window.location.href='{{url('sysuser')}}'"--}}
                       {{--class="btn wd1"/>--}}
            </td>
        </form>
    </tr>
</table>

<div class="clear"></div>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="Tmg7">
    <tr>
        {{--<td height="39" class="titleOpBg">--}}
            {{--<button type="button" name="search" id="search-btn" class="wd1 l search-btn add" data-target="#myModal">新增</button>--}}
            {{--<button type="button" name="del" id="del-btn" class="wd1 l del">删除</button>--}}
        {{--</td>--}}
    </tr>
</table>

<div id="content">
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="box" id="list">
        <tr align="center" class="bold">
            {{--<td class="listTitle">--}}
                {{--<input type="checkbox" name="chkAll" onclick="SelectAll(this.checked);"/>--}}
            {{--</td>--}}
            <td class="listTitle">会员id</td>
            <td class="listTitle">上级用户id</td>
            <td class="listTitle">会员昵称</td>
            <td class="listTitle">手机号</td>
            {{--<td class="listTitle">性别</td>--}}
            {{--<td class="listTitle">渠道</td>--}}
            <td class="listTitle">裂变类型</td>
            <td class="listTitle">注册时间</td>

            <td class="listTitle">分享成功次数</td>
            <td class="listTitle">累计阅读时间</td>
            <td class="listTitle">累计阅读书籍量</td>
            {{--<td class="listTitle">看完整本书籍量</td>--}}
            <td class="listTitle">操作</td>
        </tr>

        <?php $i = 1;?>
        @foreach($users as $user)
            <tr align="center" @if($i++%2==0) class="list" @else class="listBg"
                @endif onmouseover="currentcolor=this.style.backgroundColor;this.style.backgroundColor='#caebfc';this.style.cursor='default';"
                onmouseout="this.style.backgroundColor=currentcolor">

                {{--<td><input type="checkbox" class="cateids" name="cateids[]" value="{{$category->cateid}}"></td>--}}
                <td>{{$user->id}}</td>
                <td>
                    @if ($user->channel != 99 )  {{ $user->userfrom }}@endif
                </td>
                <td>
                    {{ $user->nickname }}
                </td>
                <td>{{ $user->phone }}</td>
                {{--<td>--}}
                    {{--@if ($user->sex == 1) 男 @endif--}}
                    {{--@if ($user->sex == 2) 女 @endif--}}
                    {{--@if ($user->sex == 3) 未设置 @endif--}}
                {{--</td>--}}
                {{--<td>--}}
                    {{--渠道{{ $user->channel }}--}}
                {{--</td>--}}
                <td>
                    @if ($user->channel == 99 )  裂变渠道包@endif
                    @if ($user->channel != 99) H5 @endif
                </td>
                <td>
                   {{ $user->regtime }}
                </td>

                <td>
                    {{ $user->shareuser }}
                </td>
                <td>
                    <?php if($user->readtime ){ ?>
                        {{$user->readtime}}
                    <?php }else{ ?>
                        0
                    <?php } ?>

                </td>
                <td>
                    {{ $user->readbook }}
                </td>
                {{--<td>--}}
                    {{--{{ $user->readendbook }}--}}
                {{--</td>--}}

                <td>
                    <a href="{{url('novel/collection?uuid=').$user->id}}" style="display: inline-block;width:65px;">收藏记录</a>
                    {{--<a href="{{url('novel/down?uuid=').$user->id}}"  style="display: inline-block;width:65px;">缓存记录</a>--}}
                    <a href="{{url('novel/read?uuid=').$user->id}}" style="display: inline-block;width:65px;">阅读记录</a>
                    <a href="{{url('novel/bookshelf?uuid=').$user->id}}" style="display: inline-block;width:65px;">书架记录</a>
                    <a href="{{url('novel/shareuser?uuid=').$user->id}}" style="display: inline-block;width:65px;">分享记录</a>
                    {{--<a href="{{url('novel/userprefer?uuid=').$user->id}}" style="display: inline-block;width:65px;">用户偏好</a>--}}
                    <button class="myEdit wd1" uid = "{{$user->id}}">偏好</button>
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
                        <td valign="bottom" nowrap="true" style="width:40%;">总记录：{{$users->total()}}
                            　页码：{{$users->currentPage()}}/{{$users->count()}}　每页：{{$users->perPage()}}
                        </td>
                        <td valign="bottom" nowrap="true"
                            style="width:60%;">{{ $users->appends($where)->render() }}</td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>

<div class="modal fade" id="myModalEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabelEdit">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabelEdit">用户偏好</h4>
            </div>
            <div class="modal-body">
                <div id="thisEditPro" class="form-horizontal">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">用户id</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="uid" value="" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">用户偏好</label>
                        <div class="col-sm-4" id="preferpart">

                        </div>
                    </div>


                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="wd1 btn btn-default" data-dismiss="modal">关闭</button>
                {{--<button type="button" id="savePro" class="wd1 btn btn-primary">更新</button>--}}
            </div>
        </div>
    </div>
</div>
<script>
    $('.myEdit').click(function () {
        $('#myModalEdit').modal();

        var str = '';
        var id = $(this).attr('uid')
        $('#thisEditPro input[name=uid]').val(id);
        $.ajax({
            type: "POST",
            url: "/novel/get-prefer",
            data: {id:id,_token:"{{csrf_token()}}"},
            dataType: "json",
            success: function (data) {
                if(data.code==1){


                    if(data.data.length >0){

                        for(var i=0;i<data.data.length;i++){
                            str += "<p>"+data.data[i]+"</p>";
                        }


                    }else{
                        str = "未设置偏好";
                    }
                    $("#preferpart").html(str);
                }
            }
        })

    });
</script>
</body>
</html>
