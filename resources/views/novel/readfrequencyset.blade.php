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
            你当前位置：小说管理 - 短文阅读次数设置
        </td>
    </tr>
</table>
<!-- 头部菜单 End -->
<script src='{{ asset("/bower_components/AdminLTE/plugins/my97date/WdatePicker.js")}}'></script>




<div id="content">
    <form id="thisEditPro" class="form-horizontal" method="post"
          action="{{url('novel/edit-readfrequency')}}" enctype="multipart/form-data">
        <div style="margin-top: 20px;margin-left: 20px;">
            <div class="" style="height: 40px;line-height: 40px;">
                登录：每天首次打开APP，获得<input type="text" onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}" onafterpaste="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}" name="type1" style="height: 30px;width: 100px;"  placeholder="{{$data1->times}} ">次短文阅读次数。
            </div>
            <div class="" style="height: 40px;line-height: 40px;margin-top: 40px;">
                每成功分享一次，可获得<input onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}" onafterpaste="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}" type="text" name="type2" style="height: 30px;width: 100px;" placeholder="{{$data2->times}} ">次短文阅读次数。
                每天前<input onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}" onafterpaste="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}" type="text" name="extra" style="height: 30px;width: 100px;" placeholder="{{$data2->extra}} ">次，分享成功有效。
            </div>
            <div class="" style="height: 40px;line-height: 40px;margin-top: 40px;">
                分享好友每注册成功一个，可获得<input onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}" onafterpaste="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}" type="text"  name="type3"  style="height: 30px;width: 100px;" placeholder="{{$data3->times}} ">次短文阅读次数。
            </div>
            <a style="color: red;">输入框限制输入正整数。</a>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <p>
                <button type="button" id="savePro" class="wd1 btn btn-primary">修改</button>
            </p>

        </div>


    </form>


</div>


<script>
    $('#savePro').click(function () {
        $('#thisEditPro').submit();
    });

</script>
</body>
</html>
