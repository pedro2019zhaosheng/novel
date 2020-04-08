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
            你当前位置：小说管理 - 推广渠道数据设置
        </td>
    </tr>
</table>
<!-- 头部菜单 End -->
<script src='{{ asset("/bower_components/AdminLTE/plugins/my97date/WdatePicker.js")}}'></script>


<div class="clear"></div>


<div id="content">
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="box" id="list">
        <tr align="center" class="bold">

            <td class="listTitle">渠道号</td>
            <td class="listTitle">渠道名称</td>
            <td class="listTitle">发送状态</td>

            <td class="listTitle">操作</td>
        </tr>

        <?php $i = 1;?>
        @foreach($android_updates as $android_update)
            <tr align="center" @if($i++%2==0) class="list" @else class="listBg"
                @endif onmouseover="currentcolor=this.style.backgroundColor;this.style.backgroundColor='#caebfc';this.style.cursor='default';"
                onmouseout="this.style.backgroundColor=currentcolor">

                <td>{{$android_update->pack_name}}</td>
                <td>{{$android_update->channel}}</td>

                 <td>
                     开启：<input type="radio" onclick="setsend({{$android_update->pack_name}},1)" name="send{{$android_update->pack_name}}" value="1" @if($android_update->ifsend ==1)checked="checked" @endif>
                     关闭：<input type="radio" onclick="setsend({{$android_update->pack_name}},0)" name="send{{$android_update->pack_name}}" value="0" @if($android_update->ifsend ==0)checked="checked" @endif>
                 </td>

                <td>
                    <button class="myEdit wd1" cateid="{{$android_update->pack_name}}" cName="{{$android_update->channel}}"  >编辑</button>
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
                        <td valign="bottom" nowrap="true" style="width:40%;">总记录：{{$android_updates->total()}}
                            　页码：{{$android_updates->currentPage()}}/{{$android_updates->count()}}　每页：{{$android_updates->perPage()}}
                        </td>
                        <td valign="bottom" nowrap="true"
                            style="width:60%;">{{ $android_updates->appends($where)->render() }}</td>
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
                <h4 class="modal-title" id="myModalLabelEdit">编辑渠道名</h4>
            </div>
            <div class="modal-body">
                <form id="thisEditPro" class="form-horizontal" method="post"
                      action="{{url('novel/edit-channelname')}}" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">渠道号</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="cid" value="" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">渠道名称</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="name" value="" placeholder="渠道名称">
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



<script>
$('.myEdit').click(function () {
    $('#myModalEdit').modal();
    $('#thisEditPro input[name=cid]').val($(this).attr('cateid'));
    $('#thisEditPro input[name=name]').val($(this).attr('cName'));

    $('#savePro').click(function () {
        $('#thisEditPro').submit();
    });
        
});

$('.add').click(function () {
    $('#myModalAdd').modal();    
});

function setsend(id,status) {
    $.post("{{url('novel/edit-channelstatus')}}", {
        _token: "{{csrf_token()}}",
        id: id,
        status: status
    }, function (data) {
        if (data.state) {
            window.history.go(0);
        } else {
            alert('操作失败，请重试');
        }
    }, 'json');
}

</script>
</body>
</html>
