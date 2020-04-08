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
            你当前位置：小说管理 - 专题列表管理
        </td>
    </tr>
</table>
<!-- 头部菜单 End -->
<script src='{{ asset("/bower_components/AdminLTE/plugins/my97date/WdatePicker.js")}}'></script>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="titleQueBg">
    <tr>
        <form name="form1" method="get" action="{{url('novel/choiceness')}}" id="form1">

            <td>
                专题类型：<select type="text"  name="platform">
                    <option value="0">--请选择类型--</option>
                    <option value="1" @if ($where['type'] == 1) selected @endif>banner专栏</option>
                    {{--<option value="2" @if ($where['type'] == 2) selected @endif>小说专栏</option>--}}
                    {{--<option value="3" @if ($where['type'] == 3) selected @endif>热推多本专栏</option>--}}
                    <option value="4" @if ($where['type'] == 4) selected @endif>作者专栏</option>
                    <option value="5" @if ($where['type'] == 5) selected @endif>小说专栏</option>
                </select>
                当前状态：<select type="text"  name="platform1">
                    <option value="0">--请选择类型--</option>
                    <option value="1" @if ($where['status'] == 1) selected @endif>已上线</option>
                    <option value="2" @if ($where['status'] == 2) selected @endif>未上线</option>
                </select>

                <input type="submit" name="btnQuery" value="查询" id="btnQuery" class="btn wd1"/>
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
            <td class="listTitle">专题id</td>
            <td class="listTitle">排序值</td>
            <td class="listTitle">专题标题</td>
            <td class="listTitle">专题类型</td>
            <td class="listTitle">数量</td>
            <td class="listTitle">当前状态</td>
            <td class="listTitle">添加时间</td>
            <td class="listTitle">操作</td>
        </tr>

        <?php $i = 1;?>
        @foreach($choicenesss as $choiceness)
            <tr align="center" @if($i++%2==0) class="list" @else class="listBg"
                @endif onmouseover="currentcolor=this.style.backgroundColor;this.style.backgroundColor='#caebfc';this.style.cursor='default';"
                onmouseout="this.style.backgroundColor=currentcolor">

                {{--<td><input type="checkbox" class="cateids" name="cateids[]" value="{{$category->cateid}}"></td>--}}
                <td>{{$choiceness->id}}</td>
                <td>
                    {{ $choiceness->sort }}
                </td>
                <td>{{$choiceness->name }}</td>
                <td>
                    @if ($choiceness->type == 1) banner专栏 @endif
                    {{--热推--}}
                    @if ($choiceness->type == 2) 小说专栏 @endif
                    {{--@if ($choiceness->type == 3) 热推多本专栏 @endif--}}
                    @if ($choiceness->type == 4) 作者专栏 @endif
                    @if ($choiceness->type == 5) 小说专栏 @endif
                </td>
                <td>{{$choiceness->num }}</td>
                <td>
                    @if ($choiceness->status == 1) 已上线 @endif
                    @if ($choiceness->status == 2) 未上线 @endif
                </td>
                <td>{{$choiceness->addtime }}</td>
                <td>
                    @if ($choiceness->status == 1) <a style="display: inline-block;width:65px;" onclick="operate({{$choiceness->id}},2,{{$choiceness->type}})">下线</a> @endif
                    @if ($choiceness->status == 2) <a style="display: inline-block;width:65px;" onclick="operate({{$choiceness->id}},1,{{$choiceness->type}})">上线</a> @endif

                    @if ($choiceness->type == 1) <a href="{{url('novel/bannercolumn?id=').$choiceness->id}}"  style="display: inline-block;width:65px;">列表</a> @endif
                    @if ($choiceness->type == 2) <a href="{{url('novel/hotonecolumn?id=').$choiceness->id}}"  style="display: inline-block;width:65px;">列表</a> @endif
                    {{--@if ($choiceness->type == 3) <a href="{{url('novel/hotmorecolumn?id=').$choiceness->id}}"  style="display: inline-block;width:65px;">列表</a> @endif--}}
                    @if ($choiceness->type == 4) <a href="{{url('novel/authorcolumn?id=').$choiceness->id}}"  style="display: inline-block;width:65px;">列表</a> @endif
                    @if ($choiceness->type == 5) <a href="{{url('novel/novelcolumn?id=').$choiceness->id}}"  style="display: inline-block;width:65px;">列表</a> @endif


                    <a onclick="editchoiceness({{$choiceness->id}},'{{$choiceness->name}}',{{$choiceness->type}},{{$choiceness->showtype}},{{$choiceness->sort}})" style="display: inline-block;width:65px;">编辑</a>
                    <a style="display: inline-block;width:65px;" onclick="del({{$choiceness->id}},{{$choiceness->type}})">删除</a>
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
                        <td valign="bottom" nowrap="true" style="width:40%;">总记录：{{$choicenesss->total()}}
                            　页码：{{$choicenesss->currentPage()}}/{{$choicenesss->count()}}　每页：{{$choicenesss->perPage()}}
                        </td>
                        <td valign="bottom" nowrap="true"
                            style="width:60%;">{{ $choicenesss->appends($where)->render() }}</td>
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
                <h4 class="modal-title" id="myModalLabelEdit">编辑专题</h4>
            </div>
            <div class="modal-body">
                <form id="thisEditPro" class="form-horizontal" method="post"
                      action="{{url('novel/edit-choiceness')}}" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">专题id</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="cid" id="cid" value="" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">排序</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="sort" id="sort" value="" placeholder="排序" required="required">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">专题标题</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="name" id="cname" value="" placeholder="专题标题" required="required">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">专题类型</label>
                        <div class="col-sm-8">
                            <select name="add_type" id="add_type" class="add_type1" onchange="selecttype1()">
                                <option value="1">banner</option>
                                {{--<option value="2">热推</option>--}}
                                {{--<option value="3">热推多本</option>--}}
                                <option value="4">作者</option>
                                <option value="5">小说</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="add_showtype1id" style="display: none;">
                        <label class="col-sm-3 control-label">版型</label>
                        <div class="col-sm-8">
                            <select name="add_showtype" id="add_showtype" class="add_showtype1">
                                <option value="1">横板(1*3)</option>
                                <option value="2">竖版(3*1)</option>
                                <option value="3">热推特殊版(1*1+3*1)</option>
                                <option value="4">特殊版(1*3+3*1)</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="wd1 btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" onclick="savePro()" class="wd1 btn btn-primary">更新</button>
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
                <h4 class="modal-title" id="myModalLabelEdit">新增专题</h4>
            </div>
            <div class="modal-body">
                <form id="thisEditPro" class="form-horizontal" method="post"
                      action="{{url('novel/add-choiceness')}}" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">排序</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="sort" value="" placeholder="排序" required="required">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">专题标题</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="name" value="" placeholder="专题标题" required="required">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">专题类型</label>
                        <div class="col-sm-8">
                            <select class="add_type" name="add_type" onchange="selecttype()">
                                <option value="1">banner</option>
                                {{--<option value="2">热推</option>--}}
                                {{--<option value="3">热推多本</option>--}}
                                <option value="4">作者</option>
                                <option value="5">小说</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="add_showtypeid" style="display: none;">
                        <label class="col-sm-3 control-label">版型</label>
                        <div class="col-sm-8">
                            <select name="add_showtype" class="add_showtype">
                                <option value="1">横板(1*3)</option>
                                <option value="2">竖版(3*1)</option>
                                <option value="3">热推特殊版(1*1+3*1)</option>
                                <option value="4">特殊版(1*3+3*1)</option>
                            </select>
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
    function selecttype() {
        var add_type = $('.add_type option:selected') .val();
        if(add_type ==2){
            $(".add_showtype").val(3);
            $(".add_showtype").attr("disabled","disabled").css("background-color","#EEEEEE");
        }else{
            $(".add_showtype").attr("disabled",false).css("background-color","#ffffff")
        }
        if(add_type ==1 || add_type==4){
            $("#add_showtypeid").hide();
        }else{
            $("#add_showtypeid").show();
        }
    }
    function selecttype1() {
        var add_type = $('.add_type1 option:selected') .val();
        if(add_type ==2){
            $(".add_showtype").val(3);
            $(".add_showtype1").attr("disabled","disabled").css("background-color","#EEEEEE");
        }else{
            $(".add_showtype1").attr("disabled",false).css("background-color","#ffffff")
        }
        if(add_type ==1 || add_type==4){
            $("#add_showtypeid").hide();
        }else{
            $("#add_showtypeid").show();
        }
    }
    function del(id,type) {
        if(confirm("确认删除该专题及专题下内容吗？")) {
            $.ajax({
                type: "POST",
                url: "/novel/del-choiceness",
                data: {id: id,type: type, _token: "{{csrf_token()}}"},
                dataType: "json",
                success: function (data) {
                    if (data.code == 1) {
                        window.location.href = "{{url('/novel/choiceness')}}";
                    }
                }
            })
        }
    }
    function operate(id,status,type) {
        $.ajax({
            type: "POST",
            url: "/novel/operate-choiceness",
            data: {id:id,status:status,type:type,_token:"{{csrf_token()}}"},
            dataType: "json",
            success: function (data) {
                if(data.code==1){
                    window.location.href="{{url('/novel/choiceness')}}";
                }
            }
        })
    }
    function savePro() {
        $("#thisEditPro").submit();
    }
    function editchoiceness(id,name,type,showtype,sort){
        $('#myModalEdit').modal();
        $("#cid").val(id);
        $("#sort").val(sort);
        $("#cname").val(name);
        $("#add_type").find("option[value="+type+"]").attr("selected",true);
        $("#add_showtype").find("option[value="+showtype+"]").attr("selected",true);
    }
    $('.add').click(function () {
        $('#myModalAdd').modal();
    });


</script>
</body>
</html>
