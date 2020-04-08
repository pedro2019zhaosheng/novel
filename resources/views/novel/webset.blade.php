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
            你当前位置：小说管理 - H5小说设置
        </td>
    </tr>
</table>
<!-- 头部菜单 End -->
<script src='{{ asset("/bower_components/AdminLTE/plugins/my97date/WdatePicker.js")}}'></script>




<div id="content">
    <div style="margin-top: 20px;margin-left: 20px;">
        <div class="" style="height: 40px;line-height: 40px;">
            前<input type="text" style="height: 30px;width: 100px;">章，可以阅读，后续阅读，<a style="color: red;">需要登录</a>；
            <p>*温馨提示，不设置或者为0时，表示没有任何限制；同时需要下载APP设置的章节大于需要登录的设置；</p>
        </div>

        <div class="" style="height: 40px;line-height: 40px;margin-top: 40px;">
            前<input type="text" style="height: 30px;width: 100px;">章，可以阅读，后续阅读，<a style="color: red;">需要下载APP</a>；
            <p>*温馨提示，不设置或者为0时，表示没有任何限制；同时需要下载APP设置的章节大于需要登录的设置；</p>
        </div>
    </div>



</div>



</body>
</html>
