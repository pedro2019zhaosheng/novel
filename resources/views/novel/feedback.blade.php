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
            你当前位置：小说管理 - 小说管理
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
                <select type="text"  name="platform">
                    <option value="0">--请选择平台--</option>
                        <option value="0" @if ($where['platform'] == 0) selected @endif>全部</option>
                        <option value="1" @if ($where['platform'] == 1) selected @endif>安卓</option>
                        <option value="2" @if ($where['platform'] == 2) selected @endif>Ios</option>
                </select>
                <input type="submit" name="btnQuery" value="查询" id="btnQuery" class="btn wd1"/>
                <input type="button" name="btnRefresh" value="刷新" onclick="window.location.href='{{url('novel/feedback')}}'"
                       class="btn wd1"/>
            </td>
        </form>
    </tr>
</table>
<div class="clear"></div>
<div id="content">
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="box" id="list">
        <tr align="center" class="bold">
            <td class="listTitle">反馈平台</td>
            <td class="listTitle">反馈内容</td>
            <td class="listTitle">联系方式</td>
            <td class="listTitle">反馈时间</td>
        </tr>

        <?php $i = 1;?>
        @foreach($feedbacks as $feedback)
            <tr align="center" @if($i++%2==0) class="list" @else class="listBg"
                @endif onmouseover="currentcolor=this.style.backgroundColor;this.style.backgroundColor='#caebfc';this.style.cursor='default';"
                onmouseout="this.style.backgroundColor=currentcolor">

                <td>@if($feedback->platform == 1) 安卓 @else Ios @endif</td>
                <td>{{ $feedback->feedback }}</td>
                <td>{{ $feedback->contact }}</td>
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
