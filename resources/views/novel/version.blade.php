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
            你当前位置：小说管理 - 安卓版本管理
        </td>
    </tr>
</table>
<!-- 头部菜单 End -->
<script src='{{ asset("/bower_components/AdminLTE/plugins/my97date/WdatePicker.js")}}'></script>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="titleQueBg">
    <tr>
        <form name="form1" method="get" action="" id="form1">
            <td align="center" style="width: 80px">
                查询：
            </td>
            <td>
                <input type="text" class="text" value="{{$packid}}" name="packid" placeholder="包名称" style="width:150px;display: inline-block;">
                <input type="submit" name="btnQuery" value="查询" id="btnQuery" class="btn wd1"/>
                <input type="button" name="btnRefresh" value="刷新" onclick="window.location.href='{{url('novel/version')}}'"
                       class="btn wd1"/>
            </td>
        </form>
    </tr>
</table>
<div class="clear"></div>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="Tmg7">
    <tr>
        <td height="39" class="titleOpBg">
            <button type="button" name="search" id="search-btn" class="wd1 l search-btn add" data-target="#myModal">新增</button>
            <button type="button" name="del" id="del-btn" class="wd1 l del">删除</button>
        </td>
    </tr>
</table>

<div id="content">
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="box" id="list">
        <tr align="center" class="bold">
            <td class="listTitle">
                <input type="checkbox" name="chkAll" onclick="SelectAll(this.checked);"/>
            </td>
            <td class="listTitle">包id</td>
            <td class="listTitle">版本号</td>
            <td class="listTitle">渠道号</td>
            <td class="listTitle">下载地址</td>
            <td class="listTitle">更新</td>
            <td class="listTitle">更新内容</td>
            <td class="listTitle">操作</td>
        </tr>
        <?php $i = 1;?>
        @foreach($versions as $version)
            <tr align="center" @if($i++%2==0) class="list" @else class="listBg"
                @endif onmouseover="currentcolor=this.style.backgroundColor;this.style.backgroundColor='#caebfc';this.style.cursor='default';"
                onmouseout="this.style.backgroundColor=currentcolor">
                <td><input type="checkbox" class="versionids" name="versionids[]" value="{{$version->id}}"></td>
                <td>{{$version->packid}}</td>
                <td>{{$version->version}}</td>
                <td>{{$version->pack_name}}</td>
                <td><a href="{{$version->url}}" target="_Blank">{{$version->url}}</a></td>
                <td>
                    @if ($version->update == 1) 强制更新 @else 非强制更新 @endif
                </td>
                <td>{{$version->remark}}</td>
                <td>
                    <button class="myEdit wd1" packid="{{$version->packid}}" channel="{{$version->pack_name}}" version="{{$version->version}}" url="{{$version->url}}" update="{{$version->update}}" url="{{$version->url}}" versionid="{{$version->id}}" remark="{{$version->remark}}">编辑</button>
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
                        <td valign="bottom" nowrap="true" style="width:40%;">总记录：{{$versions->total()}}
                            　页码：{{$versions->currentPage()}}/{{$versions->count()}}　每页：{{$versions->perPage()}}
                        </td>
                        <td valign="bottom" nowrap="true"
                            style="width:60%;">{{ $versions->appends($where)->render() }}</td>
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
                <h4 class="modal-title" id="myModalLabelEdit">编辑版本</h4>
            </div>
            <div class="modal-body">
                <form id="thisEditPro" class="form-horizontal" method="post"
                      action="{{url('novel/edit-version')}}"  enctype="multipart/form-data">
                    <input type="hidden" class="form-control" name="id" value="" readonly>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">包id</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="packid" value="" placeholder="包id">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">版本号</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="version" value="" placeholder="版本号">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">渠道号</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="channel" value="" placeholder="渠道号">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">下载连接</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="url" value="" placeholder="下载连接">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">更新</label>
                        <div class="col-sm-4">
                            <select type="text"  name="update">
                                <option value="1">强制更新</option>
                                <option value="0">非强制更新</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">更新内容</label>
                        <div class="col-sm-8">
                            <input class="form-control" type="text" name="remark" value="" placeholder="更新内容">
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
                <h4 class="modal-title" id="myModalLabelEdit">添加广告</h4>
            </div>
            <div class="modal-body">
                <form id="thisEditPro" class="form-horizontal" method="post"
                      action="{{url('novel/add-version')}}"  enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">包id</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="packid" value="" placeholder="包id">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">版本号</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="version" value="" placeholder="版本号">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">渠道号</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="channel" value="" placeholder="渠道号">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">下载连接</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="url" value="" placeholder="下载连接">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">更新</label>
                        <div class="col-sm-4">
                            <select type="text"  name="update">
                                <option value="1">强制更新</option>
                                <option value="0">非强制更新</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">更新内容</label>
                        <div class="col-sm-8">
                            <input class="form-control" type="text" name="remark" value="" placeholder="更新内容">
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

<script>
$('.myEdit').click(function () {
    $('#myModalEdit').modal();
    $('#thisEditPro input[name=id]').val($(this).attr('versionid'));
    $('#thisEditPro input[name=packid]').val($(this).attr('packid'));
    $('#thisEditPro input[name=url]').val($(this).attr('url'));
    $('#thisEditPro input[name=remark]').val($(this).attr('remark'));
    $('#thisEditPro input[name=channel]').val($(this).attr('channel'));
    $('#thisEditPro input[name=version]').val($(this).attr('version'));
    $('#thisEditPro select[name=update]').val($(this).attr('update'));

    $('#savePro').click(function () {
        $('#thisEditPro').submit();
    });
        
});

$('.add').click(function () {
    $('#myModalAdd').modal();    
});

$('.del').click(function () {
    var ids = new Array();
    $('.versionids').each(function () {
        if ($(this).is(':checked')) {
            ids.push($(this).val());
        }
    });
    if (0 == ids.length) {
        alert('请至少选择一条记录');
        return false;
    }
    $.post("{{url('novel/del-version')}}", {
        _token: "{{csrf_token()}}",
        versionids: ids,
        status: 1
    }, function (data) {
        if (data.state) {
            window.history.go(0);
        } else {
            alert('操作失败');
        }
    }, 'json');
});
</script>
</body>
</html>
