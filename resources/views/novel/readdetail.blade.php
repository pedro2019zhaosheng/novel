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
            你当前位置：小说管理 - 阅读统计
        </td>
    </tr>
</table>
<!-- 头部菜单 End -->
<script src='{{ asset("/bower_components/AdminLTE/plugins/my97date/WdatePicker.js")}}'></script>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="titleQueBg">
    <tr>
        <form name="form1" method="get" action="{{url('novel/readdetail')}}" id="form1">
            <td align="center" style="width: 80px">
                查询：
            </td>
            <td>
                <input type="hidden" name="uuid" value="{{$uuid}}">
                时间：<input type="text" name="begintime" class="text" value="{{ $begintime }}" id="ptime"
                          placeholder="起始时间" style="width:200px;display: inline-block;" onclick="WdatePicker()" js="datepicker">
                至<input type="text" name="endtime" class="text" value="{{ $endtime }}" id="ptime"
                        placeholder="终止时间" style="width:200px;display: inline-block;" onclick="WdatePicker()" js="datepicker">
                <input type="submit" name="btnQuery" value="查询" id="btnQuery" class="btn wd1"/>

            </td>
        </form>
    </tr>
</table>

<div class="clear"></div>


<div id="content">
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="box" id="list">
        <tr align="center" class="bold">

            <td class="listTitle">日期</td>
            <td class="listTitle">当天阅读时长/分钟</td>
            <td class="listTitle">总阅读天数</td>
        </tr>

        <?php $i = 1;?>
        @foreach($datas as $data)
            <tr align="center" @if($i++%2==0) class="list" @else class="listBg"
                @endif onmouseover="currentcolor=this.style.backgroundColor;this.style.backgroundColor='#caebfc';this.style.cursor='default';"
                onmouseout="this.style.backgroundColor=currentcolor">


                <td>{{$data['day']}}</td>
                <td>{{$data['time']}}</td>
                <td>{{$data['time1']}}</td>


            </tr>
        @endforeach
    </table>
</div>

</body>
</html>
