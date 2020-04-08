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
            你当前位置：小说管理 - 用户书架记录
        </td>
    </tr>
</table>
<!-- 头部菜单 End -->
<script src='{{ asset("/bower_components/AdminLTE/plugins/my97date/WdatePicker.js")}}'></script>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="titleQueBg">
    <tr>
        {{--<form name="form1" method="get" action="" id="form1">--}}

            <td>
                {{--<input type="text" class="text" value="{{$name}}" name="name" placeholder="小说分类" style="width:150px;display: inline-block;">--}}
                {{--<input type="submit" name="btnQuery" value="查询" id="btnQuery" class="btn wd1"/>--}}
                <input type="button" name="btnRefresh" value="返回" onclick="window.location.href='{{url('novel/usernovel')}}'"
                       class="btn wd1"/>
            </td>
        {{--</form>--}}
    </tr>
</table>

<div class="clear"></div>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="Tmg7">
    {{--<tr>--}}
        {{--<td height="39" class="titleOpBg">--}}
            {{--<button type="button" name="search" id="search-btn" class="wd1 l search-btn add" data-target="#myModal">新增</button>--}}
            {{--<button type="button" name="del" id="del-btn" class="wd1 l del">删除</button>--}}
        {{--</td>--}}
    {{--</tr>--}}
</table>

<div id="content">
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="box" id="list">
        <tr align="center" class="bold">

            <td class="listTitle">书籍id</td>
            <td class="listTitle">书籍名称</td>
            <td class="listTitle">书籍类型</td>
            <td class="listTitle">加入时间</td>

        </tr>

        <?php $i = 1;?>
        @foreach($bookshelfs as $bookshelf)
            <tr align="center" @if($i++%2==0) class="list" @else class="listBg"
                @endif onmouseover="currentcolor=this.style.backgroundColor;this.style.backgroundColor='#caebfc';this.style.cursor='default';"
                onmouseout="this.style.backgroundColor=currentcolor">

                <td>{{$bookshelf->novelid}}</td>
                <td>{{$bookshelf->novelname}}</td>
                <td>{{$bookshelf->catename}}</td>

                <td>
                    {{ $bookshelf->addtime }}
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
                        <td valign="bottom" nowrap="true" style="width:40%;">总记录：{{$bookshelfs->total()}}
                            　页码：{{$bookshelfs->currentPage()}}/{{$bookshelfs->count()}}　每页：{{$bookshelfs->perPage()}}
                        </td>
                        <td valign="bottom" nowrap="true"
                            style="width:60%;">{{ $bookshelfs->render() }}</td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>


<script>

</script>
</body>
</html>
