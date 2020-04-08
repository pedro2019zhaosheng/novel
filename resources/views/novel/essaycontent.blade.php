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
            你当前位置：小说管理 - 短文内容管理
        </td>
    </tr>
</table>
<!-- 头部菜单 End -->
<script src='{{ asset("/bower_components/AdminLTE/plugins/my97date/WdatePicker.js")}}'></script>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="titleQueBg">
    <tr>
        <form name="form1" method="get" action="{{url('novel/essaycontent')}}" id="form1">
            <td align="center" style="width: 80px">
                查询：
            </td>
            <td>
                <input type="text" class="text" value="{{$name}}" name="name" placeholder="短文内容" style="width:150px;display: inline-block;">
                <input type="submit" onclick="btnQuery()" name="btnQuery" value="查询" id="btnQuery" class="btn wd1"/>
                {{--<input type="button" name="btnRefresh" value="刷新" onclick="window.location.href='{{url('sysuser')}}'"--}}
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
            <td class="listTitle">内容id</td>
            <td class="listTitle">排序值</td>
            <td class="listTitle">短文名称</td>

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
                {{--<td>--}}
                {{--<a href="{{$category->img}}" target="_Blank">{{$category->img}}</a>--}}
                {{--</td>--}}
                {{--<td>--}}
                {{--{{$category->keyword}}--}}
                {{--</td>--}}

                <td>
                    {{$category->addtime}}
                </td>
                <td>
                    <button class="myEdit wd1" eid="{{$category->eid}}" cateid="{{$category->id}}" cateName="{{$category->name}}" sort="{{$category->sort}}" content="{{$category->content}}">编辑</button>

                    <button class=" wd1" eid="{{$category->eid}}"  cateid="{{$category->id}}" cateName="{{$category->name}}" sort="{{$category->sort}}" onclick="operate(0,{{$category->id}})">删除</button>

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
                <h4 class="modal-title" id="myModalLabelEdit">编辑短文内容</h4>
            </div>
            <div class="modal-body">
                <form id="thisconEditPro" class="form-horizontal" method="post"
                      action="{{url('novel/edit-essaycontent')}}" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">短文分类</label>
                        <div class="col-sm-4" id="selpart">
                            <select id="essayselect1" name="seled" onchange="changeval()">
                                @foreach($essays as $essay)
                                    <option name="essayselect" id="{{$essay->id}}">{{$essay->name}}</option>
                                @endforeach
                            </select>
                            <input type="hidden" id="selectname" name="selectname" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">短文名称</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="name" value="" placeholder="短文名称">
                        </div>
                    </div>
                    <input type="hidden" name="conid" value=""/>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">排序</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="sort" value="" placeholder="短文排序">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">内容</label>
                        <div class="col-sm-4">
                            <textarea style="height: 200px;width: 300px;" id="textcon" name="content"></textarea>
                        </div>
                    </div>

                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="wd1 btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" onclick="savePro()" id="savePro" class="wd1 btn btn-primary">更新</button>
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
                <h4 class="modal-title" id="myModalLabelEdit">新增短文内容</h4>
            </div>
            <div class="modal-body">
                <form id="thisEditPro" class="form-horizontal" method="post"
                      action="{{url('novel/add-content')}}" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">短文分类</label>
                        <div class="col-sm-4">
                            <select id="essayselect123" name="seled" onchange="changeval()">
                                @foreach($essays as $essay)
                                    <option name="essayselect"  id="{{$essay->id}}">{{$essay->name}}</option>
                                @endforeach
                            </select>
                            <?php if(!empty($essays)) { ?>
                            <input type="hidden" id="selectname123" name="selectname" value="{{$essays['0']->id}}">
                            <?php }?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">短文名称</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="name" value="" placeholder="短文名称">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">排序</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="sort" value="" placeholder="短文排序">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">内容</label>
                        <div class="col-sm-4">
                            <textarea style="height: 200px;width: 300px;" name="content"></textarea>
                        </div>
                    </div>

                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
            </div>
            <div class="modal-footer">
                <button type="submit" onclick="savePro()" id="savePro" class="wd1 btn btn-primary">添加</button>
            </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('.myEdit').click(function () {
        $('#myModalEdit').modal();
        $('#thisconEditPro input[name=name]').val($(this).attr('cateName'));
        $('#thisconEditPro input[name=conid]').val($(this).attr('cateid'));
        // $('#thisEditPro input[name=cateid]').val($(this).attr('cateid'));
        // $('#thisEditPro input[name=content]').text($(this).attr('content'));
        $('#thisconEditPro input[name=sort]').val($(this).attr('sort'));
        $("#textcon").text($(this).attr('content'));

        // var essay = $(this).attr('essay');
        var eid = $(this).attr('eid');

        $.ajax({
            type: "GET",
            url: "/novel/essayall",
            data: {},
            dataType: "json",
            success: function (data) {
                if(data.code == 1){
                    var essay = data.essay;
                    var _html='';
                    _html += '<select id="essayselect234" name="seled" onchange="changeval1()">';
                    for(var i=0;i<essay.length;i++){
                        if(eid == essay[i].id){
                            _html += '<option selected="selected" name="essayselect" id="'+essay[i].id+'">'+essay[i].name+'</option>';
                        }else{
                            _html += '<option name="essayselect" id="'+essay[i].id+'">'+essay[i].name+'</option>';
                        }
                    }
                    _html += '</select>';
                    _html += ' <input type="hidden" id="selectname234" name="selectname" value="'+eid+'">';

                    $("#selpart").html(_html);

                }

            }
        })

        // console.log(essay);

    });
    function operate(status,id){

        $.ajax({
            type: "GET",
            url: "/novel/operate",
            data: {status:status,id:id},
            dataType: "json",
            success: function (data) {
                if(data.code == 1){
                    alert("操作成功");
                    window.location.href='{{url('novel/essaycontent')}}';

                }

            }
        })
    }
    $('.add').click(function () {
        $('#myModalAdd').modal();
    });

    function savePro() {
        $('#thisconEditPro').submit();

    }
    function changeval() {

        var val = $('#essayselect123 option:selected').attr('id');
        $("#selectname123").attr('value',val);
    }
    function changeval1() {

        var val = $('#essayselect234 option:selected').attr('id');
        $("#selectname234").attr('value',val);
    }
    function btnQuery() {
        $('#form1').submit();
    }

</script>
</body>
</html>
