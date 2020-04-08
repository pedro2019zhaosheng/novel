<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head id="Head1">
    <title></title>
    <link rel="stylesheet" href='{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.css")}}'>
    <link href="{{asset('/newsty')}}/layout.css" rel="stylesheet" type="text/css"/>
    <script src="{{ asset("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js")}}"></script>
    <script type="text/javascript" src="{{asset('/newsty')}}/common.js"></script>
    <script src="{{ asset("/bower_components/AdminLTE/bootstrap/js/bootstrap.js")}}"></script>
    <script type="text/javascript" src="{{asset('/newsty')}}/comm.js"></script>
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
            你当前位置：小说管理 - 用户收藏管理
        </td>
    </tr>
</table>
<!-- 头部菜单 End -->
<script src='{{ asset("/bower_components/AdminLTE/plugins/my97date/WdatePicker.js")}}'></script>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="titleQueBg">
    <tr>

        <td>
            <input type="button" name="btnRefresh" value="返回" onclick="window.location.href='{{url('novel/usernovel')}}'"
                   class="btn wd1"/>
        </td>
    </tr>
</table>

<div class="clear"></div>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="Tmg7">
</table>

<div id="content">
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="box" id="list">
        <tr align="center" class="bold">

            <td class="listTitle">书籍id</td>
            <td class="listTitle">书籍名称</td>
            <td class="listTitle">书籍类型</td>
            <td class="listTitle">缓存时间</td>

        </tr>

        <?php $i = 1;?>
        @foreach($behaviors as $behavior)
            <tr align="center" @if($i++%2==0) class="list" @else class="listBg"
                @endif onmouseover="currentcolor=this.style.backgroundColor;this.style.backgroundColor='#caebfc';this.style.cursor='default';"
                onmouseout="this.style.backgroundColor=currentcolor">

                <td>{{$behavior->novelid}}</td>
                <td>
                    {{ $behavior->name }}
                </td>

                <?php if($behavior->kind ==1){?>
                <td>长篇连载</td>
                <?php }else{?>
                <td>短文</td>
                <?php }?>
                <td>
                    {{ $behavior->addtime }}
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
                        <td valign="bottom" nowrap="true" style="width:40%;">总记录：{{$behaviors->total()}}
                            　页码：{{$behaviors->currentPage()}}/{{$behaviors->count()}}　每页：{{$behaviors->perPage()}}
                        </td>
                        <td valign="bottom" nowrap="true"
                            style="width:60%;">{{ $behaviors->render() }}</td>
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
