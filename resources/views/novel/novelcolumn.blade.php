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
            你当前位置：小说管理 - 小说专栏管理
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
            {{--<button type="button" name="search" id="search-btn" class="wd1 l search-btn add" data-target="#myModal">新增</button>--}}
            <a href="{{url('novel/addnovel?type=5&id='.$id)}}">添加小说</a>
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
            <td class="listTitle">小说id</td>
            <td class="listTitle">小说名称</td>
            <td class="listTitle">小说封面</td>
            <td class="listTitle">小说作者</td>
            {{--<td class="listTitle">小说类型</td>--}}
            <td class="listTitle">状态</td>
            <td class="listTitle">添加时间</td>
            <td class="listTitle">排序</td>
            <td class="listTitle">操作</td>
        </tr>

        <?php $i = 1;?>
        @foreach($choice_novels as $choice_novel)
            <tr align="center" @if($i++%2==0) class="list" @else class="listBg"
                @endif onmouseover="currentcolor=this.style.backgroundColor;this.style.backgroundColor='#caebfc';this.style.cursor='default';"
                onmouseout="this.style.backgroundColor=currentcolor">

                {{--<td><input type="checkbox" class="cateids" name="cateids[]" value="{{$choice_novel->cateid}}"></td>--}}
                <td>{{$choice_novel['id']}}</td>
                <td>
                    {{ $choice_novel['novelid'] }}
                </td>
                <td>{{$choice_novel['name'] }}</td>
                <td>
                    <a href="{{$choice_novel['img']}}" target="_Blank">{{$choice_novel['img']}}</a>
                </td>
                <td>{{$choice_novel['author'] }}</td>
                {{--<td>{{$choice_novel->catename }}</td>--}}
                @if($choice_novel['status']==1) <td>已上线</td> @endif
                @if($choice_novel['status']==2) <td>已下线</td> @endif
                {{--<td>{{$choice_novel->novelstatus }}</td>--}}
                <td>{{$choice_novel['addtime'] }}</td>
                <td>{{$choice_novel['sort'] }}</td>
                <td>
                    <button onclick="editsort({{$choice_novel['id']}},{{$choice_novel['sort']}},'{{$choice_novel['name']}}')">编辑排序</button>

                    @if($choice_novel['status']==1)  <button onclick="operate(2,{{$choice_novel['id']}})">下线</button>@endif
                    @if($choice_novel['status']==2)  <button onclick="operate(1,{{$choice_novel['id']}})">上线</button>@endif
                </td>
            </tr>
        @endforeach
    </table>
</div>
{{--<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">--}}
    {{--<tr>--}}
        {{--<td class="listTitleBg">--}}
        {{--</td>--}}
        {{--<td align="right" class="page">--}}
            {{--<div id="anpPage">--}}
                {{--<table width="100%" border="0" cellpadding="0" cellspacing="0">--}}
                    {{--<tr>--}}
                        {{--<td valign="bottom" nowrap="true" style="width:40%;">总记录：{{$choice_novels->total()}}--}}
                            {{--　页码：{{$choice_novels->currentPage()}}/{{$choice_novels->count()}}　每页：{{$choice_novels->perPage()}}--}}
                        {{--</td>--}}
                        {{--<td valign="bottom" nowrap="true"--}}
                            {{--style="width:60%;">{{ $choice_novels->appends($where)->render() }}</td>--}}
                    {{--</tr>--}}
                {{--</table>--}}
            {{--</div>--}}
        {{--</td>--}}
    {{--</tr>--}}
{{--</table>--}}
<!-- Modal -->
<div class="modal fade" id="myModalEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabelEdit">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabelEdit">编辑排序</h4>
            </div>
            <div class="modal-body">
                <div  class="form-horizontal" >
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">id</label>
                        <div class="col-sm-4">
                            <input type="text" id="cid" class="form-control" name="cid" value="" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">小说名称</label>
                        <div class="col-sm-4">
                            <input type="text" id="novelname" class="form-control" name="name" value="" placeholder="小说名称" readonly>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-3 control-label">小说排序</label>
                        <div class="col-sm-8">
                            <input type="text" id="sort" class="form-control" name="sort" value="" placeholder="小说排序" required="required">
                        </div>
                    </div>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </div>

                <div class="modal-footer">
                    <button type="button" class="wd1 btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" onclick="savePro()" class="wd1 btn btn-primary">更新</button>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
    function operate(status,choiceid) {
        $.ajax({
            type: "POST",
            url: "/novel/operate-choicenovel",
            data: {choiceid:choiceid,status: status ,_token: "{{csrf_token()}}"},
            dataType: "json",
            success: function (data) {
                if (data.code == 1) {
                    alert("操作成功");
                    window.location.reload();
                }
            }
        })
    }
    function editsort(id,sort,name) {
        $('#myModalEdit').modal();
        $("#cid").val(id);
        $("#novelname").val(name);
        $("#sort").val(sort);
    }
    function savePro() {
        var id=$("#cid").val();
        var sort=$("#sort").val();
        $.ajax({
            type: "POST",
            url: "/novel/edit-sort",
            data: {id:id,sort: sort ,_token: "{{csrf_token()}}"},
            dataType: "json",
            success: function (data) {
                if (data.code == 1) {
                    alert("操作成功");
                    window.location.reload();
                }
            }
        })
    }
</script>
</body>
</html>
