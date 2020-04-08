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
            你当前位置：小说管理 - 短信配置管理
        </td>
    </tr>
</table>
<!-- 头部菜单 End -->
<script src='{{ asset("/bower_components/AdminLTE/plugins/my97date/WdatePicker.js")}}'></script>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="titleQueBg">
    <tr>
        <form name="form1" method="get" action="{{url('novel/message')}}" id="form1">
            <td align="center" style="width: 80px">
                查询：
            </td>
            <td>
                <input type="text" class="text" value="{{$service}}" name="service" placeholder="服务商" style="width:150px;display: inline-block;">
                <input type="submit" name="btnQuery" value="查询" id="btnQuery" class="btn wd1"/>

            </td>
        </form>
    </tr>
</table>

<div class="clear"></div>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="Tmg7">
    <tr>
        <td height="39" class="titleOpBg">
            <button type="button" name="search" id="search-btn" class="wd1 l search-btn add" data-target="#myModal">新增</button>
            {{--<button type="button" name="del" id="del-btn" class="wd1 l del">删除</button>--}}
        </td>
    </tr>
</table>

<div id="content">
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="box" id="list">
        <tr align="center" class="bold">
            {{--<td class="listTitle">--}}
                {{--<input type="checkbox" name="chkAll" onclick="SelectAll(this.checked);"/>--}}
            {{--</td>--}}
            <td class="listTitle">id</td>
            <td class="listTitle">服务商</td>
            <td class="listTitle">域名</td>
            <td class="listTitle">状态</td>
            <td class="listTitle">操作</td>
        </tr>

        <?php $i = 1;?>
        @foreach($messages as $message)
            <tr align="center" @if($i++%2==0) class="list" @else class="listBg"
                @endif onmouseover="currentcolor=this.style.backgroundColor;this.style.backgroundColor='#caebfc';this.style.cursor='default';"
                onmouseout="this.style.backgroundColor=currentcolor">

                <td>{{$message->id}}</td>
                <td>
                    {{ $message->service }}
                </td>
                <td>{{$message->domain }}</td>

                @if($message->status==1) <td>已上线</td> @endif
                @if($message->status==2) <td>已下线</td> @endif

                <td>
                    <button class="myEdit wd1" id="{{$message->id}}" service="{{$message->service}}"   domain="{{$message->domain}}">编辑</button>
                    @if($message->status==1)<button onclick="operate(2,{{$message->id}})">下线</button>@endif
                    @if($message->status==2)<button onclick="operate(1,{{$message->id}})">上线</button>@endif
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
                        <td valign="bottom" nowrap="true" style="width:40%;">总记录：{{$messages->total()}}
                            　页码：{{$messages->currentPage()}}/{{$messages->count()}}　每页：{{$messages->perPage()}}
                        </td>
                        <td valign="bottom" nowrap="true"
                            style="width:60%;">{{ $messages->appends($where)->render() }}</td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>
<!-- Modal -->
<div class="modal fade" id="myModalEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabelEdit">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabelEdit">编辑服务商</h4>
            </div>
            <div class="modal-body">
                <form id="thisEditPro" class="form-horizontal" method="post"
                      action="{{url('novel/edit-service')}}" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">id</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="id" value="" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">服务商</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="service" value="" placeholder="服务商"  required="required">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">域名</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="domain" value="" placeholder="域名"  required="required">
                        </div>
                    </div>

                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="wd1 btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" id="savePro" class="wd1 btn btn-primary">更新</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModalAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabelEdit">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabelEdit">添加服务商</h4>
            </div>
            <div class="modal-body">
                <form id="thisEditPro" class="form-horizontal" method="post"
                      action="{{url('novel/add-service')}}" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">服务商</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="service" value="" placeholder="服务商" required="required">
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-3 control-label">域名</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="domain" value="" placeholder="域名" required="required">
                        </div>
                    </div>

                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="modal-footer">
                        <button type="submit" id="savePro" class="wd1 btn btn-primary">添加</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$('.myEdit').click(function () {
    $('#myModalEdit').modal();
    $('#thisEditPro input[name=service]').val($(this).attr('service'));
    $('#thisEditPro input[name=domain]').val($(this).attr('domain'));
    $('#thisEditPro input[name=id]').val($(this).attr('id'));

    $('#savePro').click(function () {
        $('#thisEditPro').submit();
    });
        
});

$('.add').click(function () {
    $('#myModalAdd').modal();    
});
function operate(status,id) {
    if(status==2){
        $.ajax({
            type: "POST",
            url: "/novel/operate-message",
            data: {id:id,status: status ,_token: "{{csrf_token()}}"},
            dataType: "json",
            success: function (data) {
                if (data.code == 1) {
                    alert("操作成功");
                    window.location.reload();
                }
            }
        })
    }else{
        //先查询是否有上线的服务商
        $.ajax({
            type: "POST",
            url: "/novel/get-service",
            data: {_token: "{{csrf_token()}}"},
            dataType: "json",
            success: function (data) {
                if (data.code == 1) {

                    alert("仅允许一个服务商处于上线状态");
                    return;



                }else{
                    $.ajax({
                        type: "POST",
                        url: "/novel/operate-message",
                        data: {id:id,status: status ,_token: "{{csrf_token()}}"},
                        dataType: "json",
                        success: function (data) {
                            if (data.code == 1) {
                                alert("操作成功");
                                window.location.reload();
                            }
                        }
                    })
                }
            }
        })
    }


}

</script>
</body>
</html>
