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
            你当前位置：小说管理 - 用户阅读时长管理
        </td>
    </tr>
</table>
<!-- 头部菜单 End -->
<script src='{{ asset("/bower_components/AdminLTE/plugins/my97date/WdatePicker.js")}}'></script>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="titleQueBg">
    <tr>
        <form name="form1" method="get" action="{{url('novel/readrecord')}}" id="form1">
            <td align="center" style="width: 80px">
                查询：
            </td>
            <td>
                用户ID：<input type="text" class="text" value="{{$uuid}}" name="uuid" placeholder="用户ID" style="width:150px;display: inline-block;">
                IMEI：<input type="text" class="text" value="{{$imei}}" name="imei" placeholder="IMEI" style="width:150px;display: inline-block;">
                统计截止日期：<input type="text" name="lastupdatetime" class="text" value="{{ $lastupdatetime }}" id="ptime"
                              placeholder="统计日期" style="width:200px;display: inline-block;" onclick="WdatePicker()" js="datepicker">
                <select name="readday">

                    <option value="1"  @if ($readday==1)selected="selected"@endif>累计阅读天数等于</option>
                    <option value="2"  @if ($readday==2)selected="selected"@endif>累计阅读天数大于等于</option>
                    <option value="3"  @if ($readday==3)selected="selected"@endif>累计阅读天数小于等于</option>


                </select>：<input type="text" class="text" value="{{$day}}" name="day" placeholder="累计阅读天数" style="width:150px;display: inline-block;">
                阅读时长范围：<input type="text" class="text" value="{{$begintime}}" name="begintime" placeholder="" style="width:150px;display: inline-block;">
                至<input type="text" class="text" value="{{$endtime}}" name="endtime" placeholder="" style="width:150px;display: inline-block;">
                <input type="submit" name="btnQuery" value="查询" id="btnQuery" class="btn wd1"/>

            </td>
        </form>
    </tr>
</table>

<div class="clear"></div>


<div id="content">
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="box" id="list">
        <tr align="center" class="bold">

            <td class="listTitle">id</td>
            <td class="listTitle">用户ID</td>
            <td class="listTitle">IMEI</td>
            <td class="listTitle">总阅读天数</td>
            <td class="listTitle">总阅读时长（分钟）</td>
            <td class="listTitle">操作</td>
        </tr>

        <?php $i = 1;?>
        @foreach($reads as $read)
            <tr align="center" @if($i++%2==0) class="list" @else class="listBg"
                @endif onmouseover="currentcolor=this.style.backgroundColor;this.style.backgroundColor='#caebfc';this.style.cursor='default';"
                onmouseout="this.style.backgroundColor=currentcolor">


                <td>{{$read->id}}</td>
                <td>
                    {{ $read->uuid }}
                </td>
                <td>{{$read->imei }}</td>
                <td>
                    {{$read->day }}
                </td>
                <td>
                    {{round($read->time/60000)}}
                </td>

                <td>
                    <a href="{{url('novel/readdetail?uuid=').$read->uuid }}">详情</a>
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
                        <td valign="bottom" nowrap="true" style="width:40%;">总记录：{{$reads->total()}}
                            　页码：{{$reads->currentPage()}}/{{$reads->count()}}　每页：{{$reads->perPage()}}
                        </td>
                        <td valign="bottom" nowrap="true"
                            style="width:60%;">{{ $reads->appends($where)->render() }}</td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>

</body>
</html>
