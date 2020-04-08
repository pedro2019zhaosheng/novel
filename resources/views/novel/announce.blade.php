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
    <script src='{{ asset("/bower_components/AdminLTE/plugins/my97date/WdatePicker.js")}}'></script>
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
            你当前位置：小说管理 - 公告配置
        </td>
    </tr>
</table>
<!-- 头部菜单 End -->
<script src='{{ asset("/bower_components/AdminLTE/plugins/my97date/WdatePicker.js")}}'></script>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="titleQueBg">
    <tr>
        <form name="form1" method="get" action="{{url('novel/announce')}}" id="form1">
            <td align="center" style="width: 80px">
                查询：
            </td>
            <td>
                <input type="text" class="text" value="{{$name}}" name="content" placeholder="公告内容" style="width:150px;display: inline-block;">
                <input type="submit" value="查询" onclick="btnQuery()" />
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
            <td class="listTitle">公告id</td>
            <td class="listTitle">公告内容</td>
            <td class="listTitle">推送时间</td>
            <td class="listTitle">跳转</td>
            <td class="listTitle">状态</td>
            <td class="listTitle">操作</td>
        </tr>

        <?php $i = 1;?>
        @foreach($announce as $announc)
            <tr align="center" @if($i++%2==0) class="list" @else class="listBg"
                @endif onmouseover="currentcolor=this.style.backgroundColor;this.style.backgroundColor='#52bffc';this.style.cursor='default';"
                onmouseout="this.style.backgroundColor=currentcolor">

                <td><input type="checkbox" class="cateids" name="cateids[]" value="{{$announc->id}}"></td>
                <td>{{$announc->id}}</td>
                <td>
                    {{ $announc->content }}
                </td>
                <td>{{date("Y-m-d H:i:s", $announc->pushtime) }}</td>
                <td>
                    <?php if($announc->jump == 1){ ?>
                    签到页
                    <?php }elseif($announc->jump == 2){?>
                    小说主页
                    <?php }else{?>

                    不跳转

                    <?php }?>
                </td>
                <td>
                    <?php if($announc->pushstatus == 1){ ?>
                    已推送
                    <?php }else{?>
                    未推送
                    <?php }?>
                </td>

                <td>
                    <button class="myEdit wd1"  aid="{{$announc->id}}" novelid="{{$announc->novelid}}" ptime="{{date("Y-m-d H:i:s",$announc->pushtime)}}" content="{{$announc->content}}" jump="{{$announc->jump}}" >编辑</button>
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
                        <td valign="bottom" nowrap="true" style="width:40%;">总记录：{{$announce->total()}}
                            　页码：{{$announce->currentPage()}}/{{$announce->count()}}　每页：{{$announce->perPage()}}
                        </td>
                        <td valign="bottom" nowrap="true"
                            style="width:60%;">{{ $announce->appends($where)->render() }}</td>
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
                <h4 class="modal-title" id="myModalLabelEdit">编辑公告</h4>
            </div>
            <div class="modal-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">公告内容</label>
                        <div class="col-sm-4">
                            <textarea rows="5" cols="5" style="width: 300px !important;" id="editcont"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">推送时间</label>
                        <div class="col-sm-8">
                            <input type="text" name="start" class="text"  id="eptime"
                                   placeholder="推送时间" style="width:200px;height:40px;display: inline-block;" onclick="WdatePicker()" js="datepicker">
                        </div>
                    </div>
                    <input type="hidden" id="aid" value=""/>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">跳转</label>
                        <div class="col-sm-8">
                            <select id="editjumpselect" onchange="editshownovelid()">
                                <option value="0">无</option>
                                {{--<option value="1">签到页</option>--}}
                                <option value="2">小说主页</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="editnovelpart" style="display: none;">
                        <label for="inputEmail3" class="col-sm-3 control-label">小说id</label>
                        <div class="col-sm-4">
                            <input type="text" id="editnovel" class="form-control" name="novelid" value="" >
                        </div>
                    </div>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="modal-footer">
                        <button type="submit" onclick="editannounce()" class="wd1 btn btn-primary">添加</button>
                    </div>
                </div>
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
                <h4 class="modal-title" id="myModalLabelEdit">新增公告</h4>
            </div>
            <div class="modal-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">公告内容</label>
                        <div class="col-sm-4">
                            <textarea id="cont" rows="5" cols="5" style="width: 300px !important;"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">推送时间</label>
                        <div class="col-sm-8">
                            <input type="text" name="start" class="text"  id="ptime"
                                   placeholder="推送时间" style="width:200px;height:40px;display: inline-block;" onclick="WdatePicker()" js="datepicker">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">跳转</label>
                        <div class="col-sm-8">
                            <select id="addjumpselect" onchange="shownovelid()">
                                <option value="0">无</option>
                                {{--<option value="1">签到页</option>--}}
                                <option value="2">小说主页</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="addnovelpart" style="display: none;">
                        <label for="inputEmail3" class="col-sm-3 control-label">小说id</label>
                        <div class="col-sm-4">
                            <input type="text" id="addnovel" class="form-control" name="novelid" value="" >
                        </div>
                    </div>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="modal-footer">
                        <button type="submit" onclick="addannounce()" class="wd1 btn btn-primary">添加</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function shownovelid() {
        var jump = $("#addjumpselect").val();

        if(jump ==0){
            $("#addnovelpart").hide();
        }else{
            $("#addnovelpart").show();
        }
    }
    function editshownovelid() {
        var jump = $("#editjumpselect").val();
        if(jump ==0){
            $("#editnovelpart").hide();
        }else{
            $("#editnovelpart").show();
        }
    }
    function addannounce() {
        var cont = $("#cont").val();

        var jump = $("#addjumpselect").val();
        var ptime = $("#ptime").val();
        var addnovel = $("#addnovel").val();
        if(jump == 2 ){
            if(addnovel==''||addnovel == null){
                alert('小说id未填写');return;
            }

        }
        if(cont == ''||cont == null || cont.length ==0){
            alert('请填写公告内容');return;
        }
        if(cont.length >100){
            alert('公告内容过长');return;
        }
        $.ajax({
            type: "POST",
            url: "/novel/add-announce",
            data: {cont:cont,jump:jump,ptime:ptime,addnovel:addnovel,_token:"{{csrf_token()}}"},
            dataType: "json",
            success: function (data) {
                if(data.code==1){
                    window.location.href="{{url('/novel/announce')}}";
                }
            }
        })
    }




    $('.myEdit').click(function () {
        $('#myModalEdit').modal();
        $("#editcont").text($(this).attr('content'));
        $("#eptime").val($(this).attr('ptime'));
        $("#aid").val($(this).attr('aid'));
        var jump=$(this).attr('jump');
        $("#editjumpselect").find("option[value="+jump+"]").attr("selected",true);

        if(jump == 2){
            $("#editnovelpart").show();
            $("#editnovel").val($(this).attr('novelid'));
        }
    });

    $('.add').click(function () {
        $('#myModalAdd').modal();

    });
    function editannounce() {
        var cont = $("#editcont").val();
        if(cont == ''||cont == null || cont.length ==0){
            alert('请填写公告内容');return;
        }
        if(cont.length >100){
            alert('公告内容过长');return;
        }
        var aid = $("#aid").val();
        var jump = $("#editjumpselect").val();
        var ptime = $("#eptime").val();
        var addnovel = $("#editnovel").val();
        if(jump == 2 ){
            if(addnovel==''||addnovel == null){
                alert('小说id未填写');return;
            }

        }
        $.ajax({
            type: "POST",
            url: "/novel/edit-announce",
            data: {cont:cont,jump:jump,ptime:ptime,addnovel:addnovel,aid:aid,_token:"{{csrf_token()}}"},
            dataType: "json",
            success: function (data) {
                if(data.code==1){
                    window.location.href="{{url('/novel/announce')}}";
                }
            }
        })
    }
    function btnQuery() {
        $("#form1").submit();
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
            url: "/novel/del-announce",
            data: {ids:ids,_token:"{{csrf_token()}}"},
            dataType: "json",
            success: function (data) {
                if(data.code==1){
                    window.location.href="{{url('/novel/announce')}}";
                }
            }
        })
    });
</script>
</body>
</html>
