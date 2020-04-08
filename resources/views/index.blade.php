
<!-- saved from url=(0037)http://www.168023.com:8080/Index.aspx -->
<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href='{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.css")}}'>
    <script type="text/javascript" src="{{asset('/newsty')}}/jquery.js"></script>
    <script src="{{ asset("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js")}}"></script>
    <link href="{{asset('/newsty')}}//layout.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="{{asset('/newsty')}}/common.js"></script>
    <script type="text/javascript" src="{{asset('/newsty')}}/comm.js"></script>
    <script type="text/javascript" src="{{asset('/newsty')}}/ping.js"></script>
    <title>
        小说后台管理系统
    </title></head>
<table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
    <tbody>
    <tr>
        <td class="topIndex">
            <div class="logo left">
                <span style="font-size: 28px; color: white;  font-weight: 600;">
                    小说管理系统
                </span>
            </div>
            <div class="left hui f12 Tmg20 lh Lmg10">
                <div>
                    欢迎您,<a class="cheng f" data-target="#myModal">{{ Auth::user()->name }}</a>【{{$role}}】
                </div>
                <div>
                    <a href="{{url('home')}}" class="f">后台首页</a>
                    |
                    <a href="{{ url('/logout') }}" class="f">安全退出</a>
                    |
                    <a class="f">网络状态:</a><label id='ping-msg' style="color:#ff0;font-weight:bold;"></label>
                </div>
            </div>

        </td>
    </tr>
    <tr>
        <td>
            <div class="sidebar_a">
                <iframe src="{{url('home/left')}}" frameborder="0" style="width: 173px; height: 100%; visibility: inherit"></iframe>
            </div>
            <div class="sidebar_b">
                <iframe src="{{url('home/home')}}"  name="frm_main_content" id="frm_main_content" height="100%" frameborder="no" width="100%"></iframe>
            </div>
        </td>
    </tr>
    </tbody>
</table>


<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabeledit">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabeledit">修改密码</h4>
            </div>
            <div class="modal-body">
                <form id="thisAddPro" class="form-horizontal" method="post"
                      action="{{url('sysuser/password')}}">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">{{$role}}:</label>
                        <div class="col-sm-8">
                            {{ Auth::user()->name }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">修改密码</label>
                        <div class="col-sm-8">
                            <input type="password" class="form-control" name="password" placeholder="输入密码">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">二次确认</label>
                        <div class="col-sm-8">
                            <input type="password" class="form-control" name="repassword" placeholder="输入密码">
                        </div>
                    </div>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="wd2" data-dismiss="modal">关闭</button>
                <button type="button" id="subPro" class="wd2">更新</button>
            </div>
        </div>
    </div>
</div>

<div id="msgBoxDIV" style="position: absolute; display:none; width: 100%; padding-top: 4px; height: 24px; top: 55px; text-align: center;">
    <span class="msg" id="spnTopMsg"></span>
</div>
</body>
{{--<script type="text/javascript">--}}
{{--$.ping({--}}
    {{--url : window.location.href, --}}
    {{--beforePing : function(){$('#ping-msg').html('检测中...')},--}}
    {{--afterPing : function(ping){$('#ping-msg').html((ping>500?'较慢':'良好')+'('+ping+')')}, --}}
    {{--interval : 8--}}
{{--});--}}

{{--$(".cheng").click(function(){--}}
    {{--$('#myModal').modal({backdrop: 'static'});--}}
        {{--$('#subPro').click(function () {--}}
            {{--$('#thisAddPro').submit();--}}
            {{--$('#myModal').modal('hide');--}}
        {{--});--}}
{{--})--}}
{{--</script>--}}
</html>