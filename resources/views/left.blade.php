<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- saved from url=(0036)http://www.168023.com:8080/Left.aspx -->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>

    </title>
    <link href="{{asset('/newsty')}}/layout1.css" rel="stylesheet" type="text/css">
    <script src="{{ asset("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js")}}"></script>
    <style type="text/css">
        body {
            background-image: url(/images/sideBg.gif);
            background-repeat: repeat-y;
        }
    </style>
    <script type="text/javascript">
        function ShowHide(obj) {
            var oStyle = obj.style;
            var imgId = obj.id.replace("M", "S");
            oStyle.display == "none" ? oStyle.display = "block" : oStyle.display = "none";
            oStyle.display == "none" ? document.getElementById(imgId).src = "{{asset('newsty')}}/images/arrBig1.gif" : document.getElementById(imgId).src = "{{asset('newsty')}}/images/arrBig.gif";
        }

        function ShowUrl(obj) {
            $(".game").css("display", "none");
            obj[0].style.display = "";
            obj[1].style.display = "";
            obj[2].style.display = "";
        }

        function GetUrl(obj, url) {
            //加入一个随机防止OPEN的缓存
            vNum = Math.random()
            vNum = Math.round(vNum * 1000)
            if (url.indexOf("?") > 0) {
                url = url + "&" + vNum;
            }
            else {
                url = url + "?" + vNum;
            }
            window.open(url, "frm_main_content");
            var trList = document.getElementsByTagName("tr");
            for (var i = 0; i < trList.length; i++) {
                if (trList[i].className == "linkBg") {
                    trList[i].className = "s";
                } else if (trList[i].className == "linkBg game") {
                    trList[i].className = "s game";
                }
            }

            if (obj.className == "s game") {
                obj.className = "linkBg game";
            } else {
                obj.className = "linkBg";
            }

        }
    </script>
</head>
<body>
{{--*/ $roleid = \app\Role::getRoleByid(Auth::user()->id)->role_id;/*--}}
<?php if ($roleid == 1 || app("Illuminate\Contracts\Auth\Access\Gate")->check("novel")): ?>
<table cellpadding="0" cellspacing="0" width="100%" border="0">
    <tbody>
    <tr>
        <td class="hui f14 bold pd32 hand" height="30" onclick="JavaScript:ShowHide(M_1);">
            <img src="{{asset('/newsty')}}/arrBig1.gif" width="11" height="11" id="S_1"> 小说管理
        </td>
    </tr>
    <tr>
        <td id="M_1" style="display: none;">
            <table width="93%" border="0" align="right" cellpadding="0" cellspacing="0" class="hui">

                <tbody>
                @can("novel")
                    <tr class="s" onclick="GetUrl(this,'{{url('novel')}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">小说管理</td>
                    </tr>
                @endcan
                {{--@can("novel/category")--}}
                    {{--<tr class="s" onclick="GetUrl(this,'{{url('novel/category')}}')">--}}
                        {{--<td width="313" height="25" align="right"></td>--}}
                        {{--<td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">--}}
                        {{--</td>--}}
                        {{--<td width="725" height="25" align="left">分类管理</td>--}}
                    {{--</tr>--}}
                {{--@endcan--}}

                @can("novel/newcate")
                    <tr class="s" onclick="GetUrl(this,'{{url('novel/newcate')}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">小说分类管理（新）</td>
                    </tr>
                @endcan

                {{--@can("novel/selection")--}}
                {{--<tr class="s" onclick="GetUrl(this,'{{url('novel/selection')}}')">--}}
                {{--<td width="313" height="25" align="right"></td>--}}
                {{--<td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">--}}
                {{--</td>--}}
                {{--<td width="725" height="25" align="left">书城精选管理</td>--}}
                {{--</tr>--}}
                {{--@endcan--}}
                @can("novel/choiceness")
                    <tr class="s" onclick="GetUrl(this,'{{url('novel/choiceness')}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">书城精选专题管理</td>
                    </tr>
                @endcan
                @can("novel/shortessay")
                    <tr class="s" onclick="GetUrl(this,'{{url('novel/shortessay')}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">短文分类管理</td>
                    </tr>
                @endcan
                @can("novel/essaycontent")
                    <tr class="s" onclick="GetUrl(this,'{{url('novel/essaycontent')}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">短文内容管理</td>
                    </tr>
                @endcan
                @can("novel/essaycontent")
                    <tr class="s" onclick="GetUrl(this,'{{url('novel/channel')}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">频道管理</td>
                    </tr>
                @endcan
                {{--@can("novel/search")--}}
                    {{--<tr class="s" onclick="GetUrl(this,'{{url('novel/search')}}')">--}}
                        {{--<td width="313" height="25" align="right"></td>--}}
                        {{--<td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">--}}
                        {{--</td>--}}
                        {{--<td width="725" height="25" align="left">小说搜索管理</td>--}}
                    {{--</tr>--}}
                {{--@endcan--}}
                {{--@can("novel/source")--}}
                    {{--<tr class="s" onclick="GetUrl(this,'{{url('novel/source')}}')">--}}
                        {{--<td width="313" height="25" align="right"></td>--}}
                        {{--<td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">--}}
                        {{--</td>--}}
                        {{--<td width="725" height="25" align="left">小说源管理</td>--}}
                    {{--</tr>--}}
                {{--@endcan--}}
                @can("novel/announce")
                    <tr class="s" onclick="GetUrl(this,'{{url('novel/announce')}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">公告管理</td>
                    </tr>
                @endcan
                @can("novel/suggestion")
                    <tr class="s" onclick="GetUrl(this,'{{url('novel/suggestion')}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">反馈记录管理</td>
                    </tr>
                @endcan
                @can("novel/message")
                    <tr class="s" onclick="GetUrl(this,'{{url('novel/message')}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">短信配置管理</td>
                    </tr>
                @endcan
                @can("novel/publish")
                    <tr class="s" onclick="GetUrl(this,'{{url('novel/publish')}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">通告管理</td>
                    </tr>
                @endcan
                @can("novel/readrecord")
                    <tr class="s" onclick="GetUrl(this,'{{url('novel/readrecord')}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">阅读时长管理</td>
                    </tr>
                @endcan
                @can("novel/usernovel")
                    <tr class="s" onclick="GetUrl(this,'{{url('novel/usernovel')}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">小说用户管理</td>
                    </tr>
                @endcan
                @can("novel/spreaduser")
                    <tr class="s" onclick="GetUrl(this,'{{url('novel/spreaduser')}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">小说裂变用户管理</td>
                    </tr>
                @endcan
                @can("novel/advert")
                    <tr class="s" onclick="GetUrl(this,'{{url('novel/advert')}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">APP广告管理</td>
                    </tr>
                @endcan
                {{--@can("novel/webset")--}}
                    {{--<tr class="s" onclick="GetUrl(this,'{{url('novel/webset')}}')">--}}
                        {{--<td width="313" height="25" align="right"></td>--}}
                        {{--<td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">--}}
                        {{--</td>--}}
                        {{--<td width="725" height="25" align="left">H5小说配置</td>--}}
                    {{--</tr>--}}
                {{--@endcan--}}
                @can("novel/web-advert")
                    <tr class="s" onclick="GetUrl(this,'{{url('novel/web-advert')}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">网站广告管理</td>
                    </tr>
                @endcan
                @can("novel/readfrequencyset")
                    <tr class="s" onclick="GetUrl(this,'{{url('novel/readfrequencyset')}}')">
                    <td width="313" height="25" align="right"></td>
                    <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                    </td>
                    <td width="725" height="25" align="left">短文阅读次数设置</td>
                    </tr>
                @endcan
                @can("novel/channelset")
                    <tr class="s" onclick="GetUrl(this,'{{url('novel/channelset')}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">推广渠道数据设置</td>
                    </tr>
                @endcan
                @can("novel/version")
                    <tr class="s" onclick="GetUrl(this,'{{url('novel/version')}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">安卓版本更新</td>
                    </tr>
                @endcan
                @can("novel/novelsearch")
                    <tr class="s" onclick="GetUrl(this,'{{url('novel/novelsearch')}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">搜索热词管理</td>
                    </tr>
                @endcan
                @can("novel/novelclick")
                    <tr class="s" onclick="GetUrl(this,'{{url('novel/novelclick')}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">小说点击排名管理</td>
                    </tr>
                @endcan
                {{--@can("novel/feedback")--}}
                {{--<tr class="s" onclick="GetUrl(this,'{{url('novel/feedback')}}')">--}}
                {{--<td width="313" height="25" align="right"></td>--}}
                {{--<td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">--}}
                {{--</td>--}}
                {{--<td width="725" height="25" align="left">用户反馈</td>--}}
                {{--</tr>--}}
                {{--@endcan--}}
                {{--@can("novel/search-log")--}}
                    {{--<tr class="s" onclick="GetUrl(this,'{{url('novel/search-log')}}')">--}}
                        {{--<td width="313" height="25" align="right"></td>--}}
                        {{--<td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">--}}
                        {{--</td>--}}
                        {{--<td width="725" height="25" align="left">搜索统计</td>--}}
                    {{--</tr>--}}
                {{--@endcan--}}
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
<?php endif; ?>

<?php if ($roleid == 1 || $roleid == 3 || app("Illuminate\Contracts\Auth\Access\Gate")->check("statistics")): ?>
<table cellpadding="0" cellspacing="0" width="100%" border="0">
    <tbody>
    <tr>
        <td class="hui f14 bold pd32 hand" height="30" onclick="JavaScript:ShowHide(M_2);">
            <img src="{{asset('/newsty')}}/arrBig1.gif" width="11" height="11" id="S_2"> 数据分析
        </td>
    </tr>
    <tr>
        <td id="M_2" style="display: none;">
            <table width="93%" border="0" align="right" cellpadding="0" cellspacing="0" class="hui">

                <tbody>
                {{--@can("statistics/statistics")--}}
                    {{--<tr class="s" onclick="GetUrl(this,'{{url('statistics/statistics')}}?platform=2&pack_name={{$pack_name}}')">--}}
                        {{--<td width="313" height="25" align="right"></td>--}}
                        {{--<td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">--}}
                        {{--</td>--}}
                        {{--<td width="725" height="25" align="left">ios统计</td>--}}
                    {{--</tr>--}}
                {{--@endcan--}}
                {{--@can("statistics/statistics")--}}
                    {{--<tr class="s" onclick="GetUrl(this,'{{url('statistics/statistics')}}?platform=1&pack_name={{$pack_name}}')">--}}
                        {{--<td width="313" height="25" align="right"></td>--}}
                        {{--<td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">--}}
                        {{--</td>--}}
                        {{--<td width="725" height="25" align="left">安卓统计</td>--}}
                    {{--</tr>--}}
                {{--@endcan--}}
                {{--@can("statistics/daily")--}}
                    {{--<tr class="s" onclick="GetUrl(this,'{{url('statistics/daily')}}?pack_name={{$pack_name}}')">--}}
                        {{--<td width="313" height="25" align="right"></td>--}}
                        {{--<td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">--}}
                        {{--</td>--}}
                        {{--<td width="725" height="25" align="left">日活统计</td>--}}
                    {{--</tr>--}}
                {{--@endcan--}}
                {{--@can("statistics/keep")--}}
                    {{--<tr class="s" onclick="GetUrl(this,'{{url('statistics/keep')}}?pack_name={{$pack_name}}')">--}}
                        {{--<td width="313" height="25" align="right"></td>--}}
                        {{--<td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">--}}
                        {{--</td>--}}
                        {{--<td width="725" height="25" align="left">留存统计</td>--}}
                    {{--</tr>--}}
                {{--@endcan--}}




                @can("statistics/android")
                    <tr class="s" onclick="GetUrl(this,'{{url('statistics/android')}}?pack_name={{$pack_name}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">安卓/IOS下载激活统计</td>
                    </tr>
                @endcan

                @can("statistics/newuser")
                    <tr class="s" onclick="GetUrl(this,'{{url('statistics/newuser')}}?pack_name={{$pack_name}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">新增用户统计</td>
                    </tr>
                @endcan


                @can("statistics/dayactive")
                    <tr class="s" onclick="GetUrl(this,'{{url('statistics/dayactive')}}?pack_name={{$pack_name}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">活跃用户统计</td>
                    </tr>
                @endcan
                @can("statistics/houractive")
                    <tr class="s" onclick="GetUrl(this,'{{url('statistics/houractive')}}?pack_name={{$pack_name}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">用户活跃时段统计</td>
                    </tr>
                @endcan

                @can("statistics/appstart")
                    <tr class="s" onclick="GetUrl(this,'{{url('statistics/appstart')}}?pack_name={{$pack_name}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">启动次数统计</td>
                    </tr>
                @endcan

                @can("statistics/uvpv")
                    <tr class="s" onclick="GetUrl(this,'{{url('statistics/uvpv')}}?pack_name={{$pack_name}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">UV/PV统计</td>
                    </tr>
                @endcan
                @can("statistics/newkeep")
                    <tr class="s" onclick="GetUrl(this,'{{url('statistics/newkeep')}}?pack_name={{$pack_name}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">新增用户留存率</td>
                    </tr>
                @endcan
                @can("statistics/activekeep")
                    <tr class="s" onclick="GetUrl(this,'{{url('statistics/activekeep')}}?pack_name={{$pack_name}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">活跃用户留存率</td>
                    </tr>
                @endcan
                @can("statistics/lbuserkeep")
                    <tr class="s" onclick="GetUrl(this,'{{url('statistics/lbuserkeep')}}?pack_name={{$pack_name}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">裂变用户留存率</td>
                    </tr>
                @endcan
                @can("statistics/modulepv")
                    <tr class="s" onclick="GetUrl(this,'{{url('statistics/modulepv')}}?pack_name={{$pack_name}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">模块PV统计</td>
                    </tr>
                @endcan


                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
<?php endif; ?>

<?php if ($roleid == 1 || app("Illuminate\Contracts\Auth\Access\Gate")->check("sysuser")
|| app("Illuminate\Contracts\Auth\Access\Gate")->check("role") ||
app("Illuminate\Contracts\Auth\Access\Gate")->check("premission")): ?>
<table cellpadding="0" cellspacing="0" width="100%" border="0">
    <tbody>
    <tr>
        <td class="hui f14 bold pd32 hand" height="30" onclick="JavaScript:ShowHide(M_17);">
            <img src="{{asset('/newsty')}}/arrBig1.gif" width="11" height="11" id="S_17"> 系统管理
        </td>
    </tr>
    <tr>
        <td id="M_17" style="display: none;">
            <table width="93%" border="0" align="right" cellpadding="0" cellspacing="0" class="hui">

                <tbody>
                @can("role")
                    <tr class="s" onclick="GetUrl(this,'{{url('role')}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">角色管理</td>
                    </tr>
                @endcan
                @can("sysuser")
                    <tr class="s" onclick="GetUrl(this,'{{url('sysuser')}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">用户管理</td>
                    </tr>
                @endcan
                @can("premission")
                    <tr class="s" onclick="GetUrl(this,'{{url('premission')}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">权限管理</td>
                    </tr>
                @endcan
                @can("syslog")
                    <tr class="s" onclick="GetUrl(this,'{{url('syslog')}}')">
                        <td width="313" height="25" align="right"></td>
                        <td width="67" height="25"><img src="{{asset('/newsty')}}/arrSmall.gif" width="8" height="7">
                        </td>
                        <td width="725" height="25" align="left">系统日志</td>
                    </tr>
                @endcan
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
<?php endif; ?>

<div id="bg_music_btn" state='1' style="display:none;">关闭背景音乐</div>
<!--背景音乐-->
<div id="bg_music"></div>

<div id="bg_music_btnkefu" state='1' style="display:none;">客服消息</div>
<!--背景音乐-->
<div id="bg_musickefu"></div>
<script>
</script>
</body>
</html>