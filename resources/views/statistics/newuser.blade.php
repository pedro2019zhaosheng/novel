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
        <form name="form1" method="get" action="{{url('statistics/newuser')}}" id="form1">
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
    <div id="container" style="min-width:310px; max-width:96%; min-height:400px;"></div>

    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="box" id="list">
        <tr align="center" class="bold">

            <td class="listTitle">日期</td>
            <td class="listTitle">安卓</td>
            <td class="listTitle">IOS</td>
            <td class="listTitle">新增用户数总数</td>


        </tr>

        <?php $i = 1;?>
        @foreach($datas as $data)
            <tr align="center" @if($i++%2==0) class="list" @else class="listBg"
                @endif onmouseover="currentcolor=this.style.backgroundColor;this.style.backgroundColor='#caebfc';this.style.cursor='default';"
                onmouseout="this.style.backgroundColor=currentcolor">

                    <td>{{$data['day']}}</td>
                    <td>{{$data['android']}}</td>
                    <td>{{$data['ios']}}</td>
                    <td>{{$data['al']}}</td>
            </tr>
        @endforeach
    </table>
</div>
<script>
    $(function () {

        var myChart = echarts.init(document.getElementById('container'));
        myChart.setOption({
            title: {
                text: '每日新增用户',
                // subtext: 'yyyyyy',
                x: 'center'
            },
            color: ['#439dfb','#9B30FF','#CD950C'],
            grid: {
                top: "15%",
                bottom: "5%",
                left: "5%",
                // right: "5%",
                containLabel: true
            },
            legend: {
                show: true,

                right: "5%",
                data: ['新增用户数总数','新增IOS用户数','新增安卓用户数']
            },
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'cross',
                    label: {
                        backgroundColor: '#439dfb'
                    }
                },
                formatter: '{b0}: {c0}'
            },
            xAxis: {
                type: 'category',
                boundaryGap: true,
                axisTick: { //决定是否显示坐标刻度
                    alignWithLabel: true,
                    show: true
                },
                data:<?php echo $xAxis;?>
            },
            yAxis: {
                type: 'value'
            },
            series: [
                {
                    data: <?php echo $allyAxis;?>,
                    name:"新增用户数总数",
                    type: 'line'
                },
                {
                    data: <?php echo $iosyAxis;?>,
                    name:"新增IOS用户数",
                    type: 'line'
                },
                {
                    data: <?php echo $andyAxis;?>,
                    name:"新增安卓用户数",
                    type: 'line'
                },
            ]
        });
    });
</script>
</body>
</html>