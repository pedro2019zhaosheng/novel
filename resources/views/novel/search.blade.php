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
            你当前位置：小说管理 - 搜索记录管理
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
                <input type="text" class="text" value="{{$title}}" name="title" placeholder="广告名称" style="width:150px;display: inline-block;">
                <input type="submit" name="btnQuery" value="查询" id="btnQuery" class="btn wd1"/>
                <input type="button" name="btnRefresh" value="刷新" onclick="window.location.href='{{url('sysuser')}}'"
                       class="btn wd1"/>
            </td>
        </form>
    </tr>
</table>
<div class="clear"></div>
<div id="content">
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="box" id="list">
        <tr align="center" class="bold">
            <td class="listTitle">
                <input type="checkbox" name="chkAll"/>
            </td>
            <td class="listTitle">广告名称</td>
            <td class="listTitle">平台</td>
            <td class="listTitle">包id</td>
            <td class="listTitle">类型</td>
            <td class="listTitle">链接</td>
            <td class="listTitle">图片</td>
            <td class="listTitle">状态</td>
            <td class="listTitle">起始时间</td>
            <td class="listTitle">终止时间</td>
            <td class="listTitle">操作</td>
        </tr>
        <?php $i = 1;?>
        @foreach($adverts as $advert)
            <tr align="center" @if($i++%2==0) class="list" @else class="listBg"
                @endif onmouseover="currentcolor=this.style.backgroundColor;this.style.backgroundColor='#caebfc';this.style.cursor='default';"
                onmouseout="this.style.backgroundColor=currentcolor">
                <td><input type="checkbox" class="advertids" name="advertids[]" value="{{$advert->advertid}}"></td>
                <td>{{ $advert->title }}</td>
                <td>
                    @if ($advert->platform == 1) 安卓
                    @elseif ($advert->platform == 2)ios
                    @elseif ($advert->platform == 3) shuke.shuhun.com网站
                    @endif
                </td>
                <td>
                    @if ($advert->packid) {{$advert->packid}} @else 默认 @endif
                </td>
                <td>
                    @if ($advert->type == 1) splash(首页图片) @elseif ($advert->type == 2) 弹窗 @elseif ($advert->type == 3) 底部  @elseif ($advert->type == 4) 首页视频 @elseif ($advert->type == 5) 首页banner @endif
                </td>
                <td>
                    <a class='url' style='width:100%;text-overflow: ellipsis;overflow: hidden;display: block;' href="{{$advert->url}}" target="_Blank">{{$advert->url}}</a>
                    <span class='url_span' style='display:none;height: 50px;padding: 15px;position: absolute;background-color: #d9edf7;color: #333;font-size: 18px;'>{{$advert->url}}</span>
                </td>
                <td>
                    <a href="{{$advert->img}}" target="_Blank">{{$advert->img}}</a>
                </td>
                <td>
                    @if ($advert->status == 1) 开启 @else 关闭 @endif
                </td>
                <td>{{ date('Y-m-d H:i:s', $advert->start) }}</td>
                <td>{{ date('Y-m-d H:i:s', $advert->end) }}</td>
                <td>
                    <button class="myEdit wd1" adver_type='{{$advert->adver_type}}' packid="{{$advert->packid}}" mytype="{{$advert->type}}" platform="{{$advert->platform}}" start="{{date('Y-m-d H:i:s', $advert->start)}}" end="{{date('Y-m-d H:i:s', $advert->end)}}" status="{{$advert->status}}"
                            url="{{$advert->url}}" advertid="{{$advert->advertid}}" title="{{$advert->title}}" sort="{{$advert->sort}}" img="{{$advert->img}}">编辑
                    </button>
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
                        <td valign="bottom" nowrap="true" style="width:40%;">总记录：{{$adverts->total()}}
                            　页码：{{$adverts->currentPage()}}/{{$adverts->count()}}　每页：{{$adverts->perPage()}}
                        </td>
                        <td valign="bottom" nowrap="true"
                            style="width:60%;">{{ $adverts->appends($where)->render() }}</td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>
<!-- Modal -->
</body>
</html>
