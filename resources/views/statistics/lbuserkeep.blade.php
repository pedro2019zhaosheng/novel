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
        <form name="form1" method="get" action="{{url('statistics/lbuserkeep')}}" id="form1">
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
{{--<div style="float: right;margin-right: 5%;">--}}
    {{--<input type="button" onclick="showhide(1)" value="留存率"  class=" wd1" style="width: 70px;!important;"/>--}}
    {{--<input type="button" onclick="showhide(2)" value="留存数"  class=" wd1" style="width: 70px;!important;"/>--}}
{{--</div>--}}
<div id="content">
    <div id="container" style="min-width:310px; max-width:96%; min-height:400px;"></div>
    <div id="container1" style="min-width:310px; max-width:96%; min-height:400px;"></div>

    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="box per" id="list">
        <tr align="center" class="bold">

            <td class="listTitle">日期</td>
            <td class="listTitle">裂变用户</td>
            <td class="listTitle">1天后</td>
            <td class="listTitle">2天后</td>
            <td class="listTitle">3天后</td>
            <td class="listTitle">4天后</td>
            <td class="listTitle">5天后</td>
            <td class="listTitle">6天后</td>
            <td class="listTitle">7天后</td>
            <td class="listTitle">14天后</td>
            <td class="listTitle">30天后</td>


        </tr>


        <?php $i = 1;?>
        @foreach($datas as $data)
            <tr   align="center" @if($i++%2==0) class="list" @else class="listBg"
                  @endif onmouseover="currentcolor=this.style.backgroundColor;this.style.backgroundColor='#caebfc';this.style.cursor='default';"
                  onmouseout="this.style.backgroundColor=currentcolor">

                <td>{{$data['day']}}</td>
                <td>{{$data['today_num']}}</td>
                <td>{{$data['onelater_per']}}%</td>
                <td>{{$data['twolater_per']}}%</td>
                <td>{{$data['threelater_per']}}%</td>
                <td>{{$data['fourlater_per']}}%</td>
                <td>{{$data['fivelater_per']}}%</td>
                <td>{{$data['sixlater_per']}}%</td>
                <td>{{$data['senvenlater_per']}}%</td>
                <td>{{$data['fourteenlater_per']}}%</td>
                <td>{{$data['thritylater_per']}}%</td>
            </tr>

        @endforeach

    </table>


</div>
<script type="text/javascript">
    function showhide(o) {

        if(o==1){
            $(".per").show();
            $("#container").show();
            $(".num").hide();
            $("#container1").hide();
        }else{
            $(".per").hide();
            $("#container").hide();
            $(".num").show();
            $("#container1").show();
        }
    }

    $(function () {

        var myChart = echarts.init(document.getElementById('container'));
        myChart.setOption({
            title: {
                text: '裂变留存统计-留存率',
                // subtext: 'yyyyyy',
                x: 'center'
            },
            color: ['#439dfb','#9B30FF','#CD950C','#CD5555'],
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
                data: ['一天后留存','三天后留存','七天后留存','三十天后留存']
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
                    data: <?php echo $onelaterperyAxis;?>,
                    name:"一天后留存",
                    type: 'line'
                },
                {
                    data: <?php echo $threelaterperyAxis;?>,
                    name:"三天后留存",
                    type: 'line'
                },
                {
                    data: <?php echo $senvenlaterperyAxis;?>,
                    name:"七天后留存",
                    type: 'line'
                },
                {
                    data: <?php echo $thritylaterperyAxis;?>,
                    name:"三十天后留存",
                    type: 'line'
                },
            ]
        });

        var myChart1 = echarts.init(document.getElementById('container1'));
        myChart1.setOption({
            title: {
                text: '裂变留存统计-留存数',
                // subtext: 'yyyyyy',
                x: 'center'
            },
            color: ['#439dfb','#9B30FF','#CD950C','#CD5555'],
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
                data: ['一天后留存','三天后留存','七天后留存','三十天后留存']
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
                    data: <?php echo $onelaternumyAxis;?>,
                    name:"一天后留存",
                    type: 'line'
                },
                {
                    data: <?php echo $threelaternumyAxis;?>,
                    name:"三天后留存",
                    type: 'line'
                },
                {
                    data: <?php echo $senvenlaternumyAxis;?>,
                    name:"七天后留存",
                    type: 'line'
                },
                {
                    data: <?php echo $thritylaternumyAxis;?>,
                    name:"三十天后留存",
                    type: 'line'
                },
            ]
        });
    });
</script>

</body>
</html>