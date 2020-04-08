<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head id="Head1">
    <title></title>
    <link href="{{asset('/newsty')}}/layout.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href='{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.css")}}'>
    <script src="{{ asset("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js")}}"></script>
    <script type="text/javascript" src="{{asset('/newsty')}}/common.js"></script>
    <script src="{{ asset("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js")}}"></script>
    <script type="text/javascript" src="{{asset('/newsty')}}/comm.js"></script>
    <style type="text/css">
        .pagination li {
            padding-left: 5px;
        !important;
        }

        .text {
            height: 26px;
        }

        .button {
            padding: 5px 10px;
            background: #CCCCCC;
            display: inline-block;
            border: 1px solid #666666;
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
            你当前位置：数据分析 - {{$page_title}}
        </td>
    </tr>
</table>
<!-- 头部菜单 End -->
<script src='{{ asset("/bower_components/AdminLTE/plugins/my97date/WdatePicker.js")}}'></script>
<script src='{{ asset("/highcharts/js/highcharts.js") }}'></script>
<script src='{{ asset("/echarts/echarts.common.min.js") }}'></script>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="titleQueBg">
    <tr>
        <form name="form1" method="get" action="{{url('statistics/modulepv')}}" id="form1">
            <input type="hidden" name="type" value="1"/>
            <td align="center" style="width: 80px">
                查询：
            </td>
            <td>
                <?php if($roleid != 3):?>
                    <select type="text" name="pack_name">
                        <option value="0">所有渠道</option>
                        @foreach($packArr as $pack)
                            <?php if($pack_name == $pack->pack_name){ ?>
                            <option value="{{$pack->pack_name}}" selected>渠道{{$pack->pack_name}}</option>
                            <?php }else{?>
                            <option value="{{$pack->pack_name}}">渠道{{$pack->pack_name}}</option>
                            <?php }?>

                        @endforeach
                    </select>
                <?php endif;?>
                <input type="hidden" name="platform" value=""/>
                <input type="text" name="start" class="text" value="{{ $start }}" id="ptime"
                       placeholder="起始时间" style="width:200px;display: inline-block;" onclick="WdatePicker()" js="datepicker">
                至
                <input type="text" name="end" class="text" value="{{ $end }}" id="ptime"
                       placeholder="终止时间" style="width:200px;display: inline-block;" onclick="WdatePicker()" js="datepicker">
                <input type="submit" name="btnQuery" value="查询" id="btnQuery" class=" wd1"/>
                <input type="submit" name="btnQuery" value="导出" id="btnQuery" class=" wd1"/>
            </td>

        </form>
    </tr>
</table>
<div class="clear"></div>
<div id="content">
    {{--<div id="container" style="min-width:310px; max-width:96%; min-height:400px;"></div>--}}

    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="box" id="list">
        <tr align="center" class="bold">

            <td class="listTitle">模块</td>
            <td class="listTitle">今日点击量（pv）</td>
            <td class="listTitle">操作</td>

        </tr>

        <?php $i = 1;?>
        @foreach($datas as $data)

            <tr align="center" @if($i++%2==0) class="list" @else class="listBg"
                @endif onmouseover="currentcolor=this.style.backgroundColor;this.style.backgroundColor='#caebfc';this.style.cursor='default';"
                onmouseout="this.style.backgroundColor=currentcolor">

                <td>{{$data['module']}}</td>

                <td>{{$data['todaypv']}}</td>
                <td><a href="{{url('statistics/history?module='.$data['module'])}}">历史详情</a></td>
            </tr>

        @endforeach
    </table>
</div>

</body>
</html>