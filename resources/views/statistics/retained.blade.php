<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="titleQueBg">
    <tr>
        <form name="form1" method="get" action="" id="form1">
            <input type="hidden" name="type" value="1"/>
            <td align="center" style="width: 80px">
                查询：
            </td>
            <td>
                <select type="text" name="platform">
                    <option value="0">所有平台</option>
                    <option value="1" @if($platform==1) selected @endif>安卓</option>
                    <option value="2" @if($platform==2) selected @endif>ios</option>
                </select>
                <?php if($roleid != 3):?>
                <select type="text" name="pack_name">
                    <option value="0">所有渠道</option>
                    <option value="1" @if($pack_name==1) selected @endif>sd11e5r.cn</option>
                    <option value="2" @if($pack_name==2) selected @endif>sd11ler.cn</option>
                    <option value="3" @if($pack_name==3) selected @endif>sd11e3r.cn</option>
                    <option value="4" @if($pack_name==4) selected @endif>sd11ber.cn</option>
                    <option value="5" @if($pack_name==5) selected @endif>sd11ert.cn</option>
                    <option value="6" @if($pack_name==6) selected @endif>ugm6.cn</option>
                </select>
                <?php endif;?>
                <input type="text" name="start" class="text" value="{{ $start }}" id="ptime"
                       placeholder="起始时间" style="width:200px;display: inline-block;" onclick="WdatePicker()" js="datepicker">
                至
                <input type="text" name="end" class="text" value="{{ $end }}" id="ptime"
                       placeholder="终止时间" style="width:200px;display: inline-block;" onclick="WdatePicker()" js="datepicker">
                <input type="submit" name="btnQuery" value="查询" id="btnQuery" class=" wd1"/>
            </td>
            {{--<td style="text-align: right; padding-right: 100px;">--}}
            {{--<a class="button" href="{{url('statistics/keep')}}">所有渠道</a>--}}
            {{--<a class="button" href="{{url('statistics/keep')}}?pack_name=1">渠道1</a>--}}
            {{--<a class="button" href="{{url('statistics/keep')}}?pack_name=2">渠道2</a>--}}
            {{--<a class="button" href="{{url('statistics/keep')}}?pack_name=3">渠道3</a>--}}
            {{--<a class="button" href="{{url('statistics/keep')}}?pack_name=4">渠道4</a>--}}
            {{--<a class="button" href="{{url('statistics/keep')}}?pack_name=5">渠道5</a>--}}
            {{--<a class="button" href="{{url('statistics/keep')}}?pack_name=6">渠道6</a>--}}
            {{--</td>--}}
        </form>
    </tr>
</table>
<div class="clear"></div>
<div id="content">
    <div id="container" style="min-width:310px; max-width:96%; min-height:400px;"></div>
</div>
<script>
  $(function () {
    xAxis = <?php echo $xAxis;?>;
    data = <?php echo $data;?>;
    $('#container').highcharts({
      credits: false,
      chart: {
        type: 'line',
        plotBorderWidth: 1,
        inverted: false//反转
      },
      title: {
        text: '{{$page_title}}统计'
      },
      subtitle: {
        text: '{{$page_title}}汇总'
      },
      xAxis: {
        categories: xAxis,
        title: '{{$page_title}}',
        plotLines: [{
          color: '#ff0000',       //线的颜色，定义为红色
          dashStyle: 'dash',   //标示线的样式，默认是solid（实线），这里定义为长虚线
          value: "0", //定义在哪个值上显示标示线，这里是在x轴上刻度为3的值处垂直化一条线
          width: (0 ? 1 : 0), //标示线的宽度，2px
        }],
        tickLength: 0
      },
      yAxis: {
        // categories: ,
        title: {
          text: '{{$page_title}}汇总'
        },
        allowDecimals: false,
        min: 0,
        tickLength: 0
      },
      plotOptions: {
        columnrange: {
          dataLabels: {
            enabled: true,
            formatter: function () {
              return;
            }
          }
        }
      },
      legend: {
        enabled: true,
        align: 'right',
        verticalAlign: 'bottom',
        borderWidth: 0
      },
      series: data,
    });
  });
</script>
</body>
</html>