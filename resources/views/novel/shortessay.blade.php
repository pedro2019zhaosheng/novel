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
            你当前位置：小说管理 - 短文分类管理
        </td>
    </tr>
</table>
<!-- 头部菜单 End -->
<script src='{{ asset("/bower_components/AdminLTE/plugins/my97date/WdatePicker.js")}}'></script>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="titleQueBg">
    <tr>
        <form name="form1" method="get" action="{{url('novel/shortessay')}}" enctype="multipart/form-data" id="form1">
            <td align="center" style="width: 80px">
                查询：
            </td>
            <td>
                <input type="text" class="text" value="{{$name}}" name="name" placeholder="短文分类" style="width:150px;display: inline-block;">
                <input type="submit" name="btnQuery" value="查询" id="btnQuery" onclick="btnQuery()" class="btn wd1"/>
                {{--<input type="button" name="btnRefresh" value="新增"--}}
                       {{--class="btn wd1"/>--}}
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
            <td class="listTitle">分类id</td>
            <td class="listTitle">排序值</td>
            <td class="listTitle">名称</td>
            <td class="listTitle">封面图片</td>
            <td class="listTitle">二级菜单banner图</td>
            <td class="listTitle">更新时间</td>
            <td class="listTitle">操作</td>
        </tr>

        <?php $i = 1;?>
        @foreach($categorys as $category)
            <tr align="center" @if($i++%2==0) class="list" @else class="listBg"
                @endif onmouseover="currentcolor=this.style.backgroundColor;this.style.backgroundColor='#caebfc';this.style.cursor='default';"
                onmouseout="this.style.backgroundColor=currentcolor">

                {{--<td><input type="checkbox" class="cateids" name="cateids[]" value="{{$category->cateid}}"></td>--}}
                <td>{{$category->id}}</td>
                <td>
                    {{ $category->sort }}
                </td>
                <td>{{$category->name }}</td>
                <td>
                    <a href="{{$category->img}}" target="_Blank">{{$category->img}}</a>
                </td>
                <td>
                    <a href="{{$category->banner}}" target="_Blank">{{$category->banner}}</a>
                </td>
                <td>
                    {{$category->addtime}}
                </td>
                <td>
                    <button  class="myEdit wd1" banner="{{$category->banner}}" img="{{$category->img}}" cateid="{{$category->id}}" cateName="{{$category->name}}" sort="{{$category->sort}}" >编辑</button>
                    <button  class="wd1" onclick="del({{$category->id}})" >删除</button>
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
                        <td valign="bottom" nowrap="true" style="width:40%;">总记录：{{$categorys->total()}}
                            　页码：{{$categorys->currentPage()}}/{{$categorys->count()}}　每页：{{$categorys->perPage()}}
                        </td>
                        <td valign="bottom" nowrap="true"
                            style="width:60%;">{{ $categorys->appends($where)->render() }}</td>
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
                <h4 class="modal-title" id="myModalLabelEdit">编辑分类</h4>
            </div>
            <div class="modal-body">
                <form id="thisEditPro" class="form-horizontal" method="post"
                      action="{{url('novel/edit-essay')}}" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">分类id</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="cateid" value="" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">分类名称</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="name" value="" placeholder="分类名称">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">排序</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="sort" value="" placeholder="排序" required="required">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">封面图片</label>
                        <div class="col-sm-8">
                            <input type="file" id="cover" name="imgFile" value="" placeholder="封面图片">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">二级菜单图片</label>
                        <div class="col-sm-8">
                            <input type="file" id="banner" name="imgFilebanner" value="" placeholder="二级菜单图片">
                        </div>
                    </div>

                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="wd1 btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" onclick="saveEdit()" id="savePro" class="wd1 btn btn-primary">更新</button>
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
                <h4 class="modal-title" id="myModalLabelEdit">新增短文分类</h4>
            </div>
            <div class="modal-body">
                <form id="thisAddPro" class="form-horizontal" method="post"
                      action="{{url('novel/add-essay')}}" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">分类名称</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="name" value="" placeholder="分类名称" required="required">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">排序</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="sort" value="" placeholder="排序" required="required">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">封面图片</label>
                        <div class="col-sm-8">
                            <input type="file" name="imgFile" value="" placeholder="封面图片" required="required">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">二级菜单图片</label>
                        <div class="col-sm-8">
                            <input type="file" name="imgFilebanner" value="" placeholder="二级菜单图片" required="required">
                        </div>
                    </div>

                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </form>
            </div>

            <div class="modal-footer">
                <button type="submit" onclick="savePro()" class="wd1 btn btn-primary">添加</button>
            </div>

        </div>
    </div>
</div>

<script>
$('.myEdit').click(function () {
    $('#myModalEdit').modal();
    $('#thisEditPro input[name=name]').val($(this).attr('cateName'));
    $('#thisEditPro input[name=cateid]').val($(this).attr('cateid'));
    $('#thisEditPro input[name=sort]').val($(this).attr('sort'));
    $('#cover').attr('value',$(this).attr('img'));
    $('#banner').attr('value',$(this).attr('banner'));

});
// $('#savePro').click(function () {
//     alert(1);
//     $('#thisAddPro').submit();
// });
$('.add').click(function () {
    $('#myModalAdd').modal();    
});
function btnQuery() {
    $('#form1').submit();
}
function savePro() {
    $('#thisAddPro').submit();
}
function saveEdit(){
    $('#thisEditPro').submit();
}
function del(id) {
    if(confirm("删除后该分类下内容将同步删除！")){
        $.ajax({
            type: "POST",
            url: "/novel/del-shortessay",
            data: {id:id, _token: "{{csrf_token()}}"},
            dataType: "json",
            success: function (data) {
                if (data.code == 1) {
                    alert("删除成功");
                    window.location.reload();
                }else{
                    alert("删除失败");
                }

            }
        })
    }
}
</script>
</body>
</html>
