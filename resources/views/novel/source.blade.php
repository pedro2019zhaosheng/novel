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
            你当前位置：小说管理 - 小说源管理
        </td>
    </tr>
</table>
<!-- 头部菜单 End -->
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="Tmg7">
    <tr>
        <td height="39" class="titleOpBg">
            <button type="button" name="search" id="search-btn" class="wd1 l search-btn add" data-target="#myModal">新增</button>
            <button type="button" name="del" id="del-btn" class="wd1 l del">删除</button>
        </td>
    </tr>
</table>

<div class="clear"></div>
<div id="content">
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="box" id="list">
        <tr align="center" class="bold">
            <td class="listTitle">
                <input type="checkbox" name="chkAll" onclick="SelectAll(this.checked);"/>
            </td>
            <td class="listTitle">源id</td>
            <td class="listTitle">源名称</td>
            <td class="listTitle">源连接</td>
            <td class="listTitle">章节英文名</td>
            <td class="listTitle">章节起始值</td>
            <td class="listTitle">章节终止值</td>
            <td class="listTitle">操作</td>
        </tr>

        <?php $i = 1;?>
        @foreach($sources as $source)
            <tr align="center" @if($i++%2==0) class="list" @else class="listBg"
                @endif onmouseover="currentcolor=this.style.backgroundColor;this.style.backgroundColor='#caebfc';this.style.cursor='default';"
                onmouseout="this.style.backgroundColor=currentcolor">

                <td><input type="checkbox" class="sourceids" name="sourceids[]" value="{{$source->sourceid}}"></td>
                <td>{{$source->sourceid}}</td>
                <td>{{$source->name }}</td>
                <td>
                <a href="{{$source->url}}" target="_Blank">{{$source->url}}</a>
                </td>
                <td>{{$source->chapter_name }}</td>
                <td>
                    {{ $source->start }}
                </td>
                <td>
                    {{$source->end}}
                </td>
                <td>
                    <button class="myEdit wd1" start="{{$source->start}}" end="{{$source->end}}" sourceid="{{$source->sourceid}}" sourceName="{{$source->name}}" url="{{$source->url}}" chapter_name="{{$source->chapter_name}}">编辑</button>
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
                        <td valign="bottom" nowrap="true" style="width:40%;">总记录：{{$sources->total()}}
                            　页码：{{$sources->currentPage()}}/{{$sources->count()}}　每页：{{$sources->perPage()}}
                        </td>
                        <td valign="bottom" nowrap="true"
                            style="width:60%;">{{ $sources->render() }}</td>
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
                <h4 class="modal-title" id="myModalLabelEdit">编辑小说源</h4>
            </div>
            <div class="modal-body">
                <form id="thisEditPro" class="form-horizontal" method="post"
                      action="{{url('novel/edit-source')}}">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">小说源id</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="sourceid" value="" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">小说源名称</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="name" value="" placeholder="小说源名称">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">小说源url</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="url" value="" placeholder="小说源url">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">该小说源章节名(切勿随意更改)</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="chapter_name" value="" placeholder="该小说源章节名">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">章节起始值</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="start" value="" placeholder="章节起始值">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">章节终止值</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="end" value="" placeholder="章节终止值">
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
                <h4 class="modal-title" id="myModalLabelEdit">添加分类</h4>
            </div>
            <div class="modal-body">
                <form id="thisEditPro" class="form-horizontal" method="post"
                      action="{{url('novel/add-source')}}">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">小说源名称</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="name" value="" placeholder="小说源名称">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">小说源url</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="url" value="" placeholder="小说源url">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">该小说源章节名</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="chapter_name" value="" placeholder="该小说源章节名">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">章节起始值</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="start" value="" placeholder="章节起始值">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">章节终止值</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="end" value="" placeholder="章节终止值">
                        </div>
                    </div>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
            </div>
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
    $('#thisEditPro input[name=name]').val($(this).attr('sourceName'));
    $('#thisEditPro input[name=sourceid]').val($(this).attr('sourceid'));
    $('#thisEditPro input[name=url]').val($(this).attr('url'));
    $('#thisEditPro input[name=chapter_name]').val($(this).attr('chapter_name'));
    $('#thisEditPro input[name=start]').val($(this).attr('start'));
    $('#thisEditPro input[name=end]').val($(this).attr('end'));
    $('#savePro').click(function () {
        $('#thisEditPro').submit();
    });
        
});

$('.add').click(function () {
    $('#myModalAdd').modal();    
});

$('.del').click(function () {
    var ids = new Array();
    $('.sourceids').each(function () {
        if ($(this).is(':checked')) {
            ids.push($(this).val());
        }
    });
    if (0 == ids.length) {
        alert('请至少选择一条记录');
        return false;
    }
    $.post("{{url('novel/del-source')}}", {
        _token: "{{csrf_token()}}",
        sourceids: ids,
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
