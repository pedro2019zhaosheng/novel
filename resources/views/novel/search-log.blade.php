<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
            你当前位置：小说管理 - 小说管理
        </td>
    </tr>
</table>
<!-- 头部菜单 End -->
<script src='{{ asset("/bower_components/AdminLTE/plugins/my97date/WdatePicker.js")}}'></script>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="titleQueBg">
    <tr>
        <td align="center" style="width: 20px"></td>
        <td>
            <input type="button" name="btnRefresh" value="刷新" onclick="window.location.href='{{url('novel/search-log')}}'" class="btn wd1"/>
        </td>
    </tr>
</table>
<div class="clear"></div>
<div id="content">
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="box" id="list">
        <tr align="center" class="bold">
            <td class="listTitle">搜索词</td>
            <td class="listTitle">搜索次数</td>
            <td class="listTitle">搜索时间</td>
        </tr>

        <?php $i = 1;?>
        @foreach($feedbacks as $feedback)
            <tr align="center" @if($i++%2==0) class="list" @else class="listBg"
                @endif onmouseover="currentcolor=this.style.backgroundColor;this.style.backgroundColor='#caebfc';this.style.cursor='default';"
                onmouseout="this.style.backgroundColor=currentcolor">
                <td>{{ $feedback->keyword }}</td>
                <td>{{ $feedback->num }}</td>
                <td>
                    {{$feedback->add_time}}
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
                        <td valign="center" nowrap="true" style="width:40%;">总记录：{{$feedbacks->total()}}
                            　页码：{{$feedbacks->currentPage()}}/{{$feedbacks->count()}}　每页：{{$feedbacks->perPage()}}
                        </td>
                        <td valign="center" nowrap="true"
                            style="width:60%;">{{ $feedbacks->appends($where)->render() }}</td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>

</body>
</html>