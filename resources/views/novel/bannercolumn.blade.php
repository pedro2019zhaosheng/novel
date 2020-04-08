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
            你当前位置：小说管理 - banner专栏管理
        </td>
    </tr>
</table>
<!-- 头部菜单 End -->
<script src='{{ asset("/bower_components/AdminLTE/plugins/my97date/WdatePicker.js")}}'></script>
{{--<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="titleQueBg">--}}
    {{--<tr>--}}
        {{--<form name="form1" method="get" action="" id="form1">--}}
            {{--<td align="center" style="width: 80px">--}}
                {{--查询：--}}
            {{--</td>--}}
            {{--<td>--}}
                {{--<input type="text" class="text" value="{{$name}}" name="name" placeholder="小说分类" style="width:150px;display: inline-block;">--}}
                {{--<input type="submit" name="btnQuery" value="查询" id="btnQuery" class="btn wd1"/>--}}
                {{--<input type="button" name="btnRefresh" value="刷新" onclick="window.location.href='{{url('sysuser')}}'"--}}
                       {{--class="btn wd1"/>--}}
            {{--</td>--}}
        {{--</form>--}}
    {{--</tr>--}}
{{--</table>--}}

<div class="clear"></div>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="Tmg7">
    <tr>
        <td height="39" class="titleOpBg">
            <button type="button" name="search" id="search-btn" class="wd1 l search-btn add" data-target="#myModal">新增</button>
            <a href="{{url('novel/choiceness')}}">返回</a>
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
            <td class="listTitle">bannerid</td>
            <td class="listTitle">img</td>
            <td class="listTitle">状态</td>

            <td class="listTitle">链接/小说id</td>
            <td class="listTitle">排序</td>
            <td class="listTitle">创建时间</td>
            <td class="listTitle">操作</td>
        </tr>

        <?php $i = 1;?>
        @foreach($novel_banners as $novel_banner)
            <tr align="center" @if($i++%2==0) class="list" @else class="listBg"
                @endif onmouseover="currentcolor=this.style.backgroundColor;this.style.backgroundColor='#caebfc';this.style.cursor='default';"
                onmouseout="this.style.backgroundColor=currentcolor">

                {{--<td><input type="checkbox" class="cateids" name="cateids[]" value="{{$category->cateid}}"></td>--}}
                <td>{{$novel_banner->id}}</td>
                <td>{{$novel_banner->choiceid}}</td>

                <td>
                    <a href="{{$novel_banner->img}}" target="_Blank">{{$novel_banner->img}}</a>
                </td>
                <td>
                    @if ($novel_banner->status == 1) 已上线 @endif
                    @if ($novel_banner->status == 2) 未上线 @endif
                </td>
                <td>
                    <a href="{{$novel_banner->link}}" target="_Blank">{{$novel_banner->link}}</a>
                </td>
                <td>{{$novel_banner->sort}}</td>
                <td>
                    {{$novel_banner->addtime}}
                </td>
                <td>
                    @if ($novel_banner->status == 1) <button onclick="editstatus(2,{{$novel_banner->id}})">下线</button> @endif
                    @if ($novel_banner->status == 2) <button onclick="editstatus(1,{{$novel_banner->id}})">上线</button> @endif
                    <button class="myEdit wd1" id="{{$novel_banner->id}}" link="{{$novel_banner->link}}" linktype="{{$novel_banner->linktype}}" img="{{$novel_banner->img}}" sort="{{$novel_banner->sort}}">编辑</button>

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
                        <td valign="bottom" nowrap="true" style="width:40%;">总记录：{{$novel_banners->total()}}
                            　页码：{{$novel_banners->currentPage()}}/{{$novel_banners->count()}}　每页：{{$novel_banners->perPage()}}
                        </td>
                        <td valign="bottom" nowrap="true"
                            style="width:60%;">{{ $novel_banners->render() }}</td>
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
                <h4 class="modal-title" id="myModalLabelEdit">编辑banner</h4>
            </div>
            <div class="modal-body">
                <form id="thisEditPro" class="form-horizontal" method="post"
                      action="{{url('novel/edit-banner')}}" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">id</label>
                        <div class="col-sm-4">
                            <input type="text" id="eid" class="form-control" name="id" value="" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">
                            <select name="linktype" id="linktype">
                                <option value="1">链接地址</option>
                                <option value="2">小说id</option>
                            </select>
                        </label>
                        <div class="col-sm-4">
                            <input type="text" id="elink" class="form-control" name="link" value="" placeholder="链接地址/小说id">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">排序</label>
                        <div class="col-sm-4">
                            <input type="text" id="esort" class="form-control" name="sort" value="" placeholder="排序" required="required">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">分类图片</label>
                        <div class="col-sm-8">
                            <input type="file" id="eimg" name="imgFile" value="" placeholder="广告图片" required="required">
                        </div>
                    </div>

                    <input type="hidden" name="bannerid" value="{{$bannerid}}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="wd1 btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" id="savePro"  class="wd1 btn btn-primary">更新</button>
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
                <h4 class="modal-title" id="myModalLabelEdit">新增banner</h4>
            </div>
            <div class="modal-body">
                <form id="thisEditPro" class="form-horizontal" method="post"
                      action="{{url('novel/add-banner')}}" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">
                            <select name="linktype">
                                <option value="1">链接地址</option>
                                <option value="2">小说id</option>
                            </select>

                        </label>
                        <div class="col-sm-4">
                            <input type="text"  class="form-control" name="link" value="" placeholder="链接地址/小说id">非必填
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">排序</label>
                        <div class="col-sm-4">
                            <input type="text"  class="form-control" name="sort" value="" placeholder="排序" required="required">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">banner图片</label>
                        <div class="col-sm-8">
                            <input type="file" id="eimg" name="imgFile" value="" placeholder="banner图片" required="required">
                        </div>
                    </div>

                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="bannerid" value="{{$bannerid}}">

                    <div class="modal-footer">
                        <button type="submit" id="savePro" class="wd1 btn btn-primary">添加</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$('.add').click(function () {
    $('#myModalAdd').modal();
});
$('#savePro').click(function () {
    $('#thisEditPro').submit();
});

$('.myEdit').click(function () {
    $('#myModalEdit').modal();

     $("#elink").val($(this).attr('link'));
     $("#linktype").val($(this).attr('linktype'));
     $("#esort").val($(this).attr('sort'));

     $("#eid").val($(this).attr('id'));
    $("#eimg").attr('value',$(this).attr('img'));

});
function editstatus(status,id) {
    $.ajax({
        type: "POST",
        url: "/novel/edit-status",
        data: {status:status,id:id,_token:"{{csrf_token()}}"},
        dataType: "json",
        success: function (data) {
            if(data.code==1){
                window.location.reload();
            }
        }
    })
}

</script>
</body>
</html>
