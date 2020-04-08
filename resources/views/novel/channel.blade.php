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
            你当前位置：小说管理 - 频道管理
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
            <td class="listTitle">频道id</td>
            <td class="listTitle">频道</td>
            <td class="listTitle">种类</td>
            <td class="listTitle">更新时间</td>
            <td class="listTitle">操作</td>
        </tr>

        <?php $i = 1;?>
        @foreach($channels as $category)
            <tr align="center" @if($i++%2==0) class="list" @else class="listBg"
                @endif onmouseover="currentcolor=this.style.backgroundColor;this.style.backgroundColor='#caebfc';this.style.cursor='default';"
                onmouseout="this.style.backgroundColor=currentcolor">

                <td><input type="checkbox" class="cateids" name="cateids[]" value="{{$category->id}}"></td>
                <td>{{$category->id}}</td>
                {{--<td>--}}
                    {{--{{ $category->sort }}--}}
                {{--</td>--}}
                <td>{{$category->name }}</td>
                {{--<td>--}}
                    {{--<a href="{{$category->img}}" target="_Blank">{{$category->img}}</a>--}}
                {{--</td>--}}
                <td>
                    {{$category->kindname}}
                </td>
                <td>
                    {{$category->addtime}}
                </td>
                <td>
                    <button class="myEdit wd1" id="{{$category->id}}" name="{{$category->name}}" kind="{{$category->kind}}" kindname="{{$category->kindname}}" >编辑</button>
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
                        <td valign="bottom" nowrap="true" style="width:40%;">总记录：{{$channels->total()}}
                            　页码：{{$channels->currentPage()}}/{{$channels->count()}}　每页：{{$channels->perPage()}}
                        </td>
                        <td valign="bottom" nowrap="true"
                            style="width:60%;">{{ $channels->appends($where)->render() }}</td>
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
                <h4 class="modal-title" id="myModalLabelEdit">编辑频道</h4>
            </div>
            <div class="modal-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">频道id</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="cid" name="cid" value="" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">频道名称</label>
                        <div class="col-sm-4">
                            <input type="text" id="chname" class="form-control" name="chname" value="" placeholder="分类名称">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">种类</label>
                        <div class="col-sm-8" id="kindlist">
                            @foreach($categorys as $category)
                                <div><input name="ecateid" value="{{$category->id}}" kindname="{{$category->name}}" type="checkbox">{{$category->name }}</div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="wd1 btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" id="savePro" onclick="editchannel()" class="wd1 btn btn-primary">更新</button>
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
                <h4 class="modal-title" id="myModalLabelEdit">新增频道</h4>
            </div>
            <div class="modal-body">
                <div class="form-horizontal" >
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">频道</label>
                        <div class="col-sm-4">
                            <input type="text" id="achname" class="form-control" name="name" value="" placeholder="分类名称">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">种类</label>
                        <div class="col-sm-8">
                            @foreach($categorys as $category)
                                <div><input name="cateid" value="{{$category->id}}" kindname="{{$category->name}}" type="checkbox">{{$category->name }}</div>
                            @endforeach

                        </div>
                    </div>

                    {{--<input type="hidden" name="_token" value="{{ csrf_token() }}">--}}

                    <div class="modal-footer">
                        <button type="submit" onclick="addChannel()" id="savePro" class="wd1 btn btn-primary">添加</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$('.myEdit').click(function () {
    $('#myModalEdit').modal();
    $('#myModalEdit input[name=cid]').val($(this).attr('id'));
    $('#myModalEdit input[name=chname]').val($(this).attr('name'));

    var kind=$(this).attr('kind');
    var kindArr = kind.split(",");

    for(var i=0;i<kindArr.length;i++){
        $("input[name='ecateid']").each(function(){
            if($(this).val() == kindArr[i]){
                $(this).attr('checked', 'checked');
            }
        });
    }
        
});
function editchannel(){
    var name = $("#chname").val();
    var cid = $("#cid").val();
    var ekind = [];
    var ekindname = [];
    $("input[name='ecateid']").each(function(){
        if($(this).is(":checked")){
            ekind.push($(this).val());
            ekindname.push($(this).attr("kindname"));
        }
    });
    if(name==''||name==null||name==undefined){
        alert("请填写频道名");return;
    }
    if(name.length > 4){
        alert("最多填写五个汉字");return;
    }
    var kindstr = ekind.join(",");
    var kindnamestr = ekindname.join(",");
    if(ekind.length==0){
        alert('至少选择一种分类');return;
    }

    console.log(kindstr);
    $.ajax({
        type: "POST",
        url: "/novel/edit-channel",
        data: {cid:cid,kind:kindstr,name:name,kindname:kindnamestr,_token:"{{csrf_token()}}"},
        dataType: "json",
        success: function (data) {
            if(data.code==1){
                window.location.href="{{url('/novel/channel')}}";
            }
        }
    })

}
$('.add').click(function () {
    $('#myModalAdd').modal();    
});
function addChannel() {
    var name = $("#achname").val();
    if(name==''||name==null||name==undefined){
        alert("请填写频道名");return;
    }
    if(name.length > 4){
        alert("最多填写五个汉字");return;
    }
    var kind = [];
    var kindname = [];
    $("input[name='cateid']").each(function(){
        if($(this).is(":checked")){
            kind.push($(this).val());
            kindname.push($(this).attr("kindname"));
        }
    });
    if(kind.length==0){
        alert('至少选择一种分类');return;
    }
    var kindstr = kind.join(",");
    var kindnamestr = kindname.join(",");

    $.ajax({
        type: "POST",
        url: "/novel/add-channel",
        data: {kind:kindstr,name:name,kindname:kindnamestr,_token:"{{csrf_token()}}"},
        dataType: "json",
        success: function (data) {
            if(data.code==1){
                window.location.href="{{url('/novel/channel')}}";
            }
        }
    })

}

$('.del').click(function () {
    var ids = new Array();
    $('.cateids').each(function () {
        if ($(this).is(':checked')) {
            ids.push($(this).val());
        }
    });
    if (0 == ids.length) {
        alert('请至少选择一条记录');
        return false;
    }
    $.ajax({
        type: "POST",
        url: "/novel/del-channel",
        data: {ids:ids,_token:"{{csrf_token()}}"},
        dataType: "json",
        success: function (data) {
            if(data.code==1){
                window.location.href="{{url('/novel/channel')}}";
            }
        }
    })

});
</script>
</body>
</html>
