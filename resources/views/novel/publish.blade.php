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
            你当前位置：小说管理 - 通告管理
        </td>
    </tr>
</table>
<!-- 头部菜单 End -->
<script src='{{ asset("/bower_components/AdminLTE/plugins/my97date/WdatePicker.js")}}'></script>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="titleQueBg">
    <tr>
        <form name="form1" method="get" action="{{url('novel/publish')}}" id="form1">

            <td>
                公告类型：<select type="text"  name="type">
                    <option value="0">--请选择类型--</option>
                    <option value="1" @if ($where['type'] == 1) selected @endif>静态通告</option>
                    <option value="2" @if ($where['type'] == 2) selected @endif>url链接通告</option>
                    <option value="3" @if ($where['type'] == 3) selected @endif>纯文本通告</option>
                    <option value="4" @if ($where['type'] == 4) selected @endif>跳转至小说</option>
                </select>
                渠道：<select type="text"  name="channel">
                    <option value="0">--请选择类型--</option>
                    <option value="-1" @if ($where['channel'] == -1) selected @endif>全渠道</option>

                    @foreach($packArr as $pack)
                        <?php if($where['channel'] == $pack->pack_name){ ?>
                        <option value="{{$pack->pack_name}}" selected>渠道{{$pack->pack_name}}</option>
                        <?php }else{?>
                        <option value="{{$pack->pack_name}}">渠道{{$pack->pack_name}}</option>
                        <?php }?>

                    @endforeach


                </select>
                平台：<select type="text"  name="platform">
                    <option value="0">--请选择类型--</option>
                    <option value="3" @if ($where['platform'] == 3) selected @endif>全平台</option>
                    <option value="1" @if ($where['platform'] == 1) selected @endif>安卓</option>
                    <option value="2" @if ($where['platform'] == 2) selected @endif>IOS</option>
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
            <td class="listTitle">公告id</td>
            <td class="listTitle">公告标题</td>
            <td class="listTitle">公告类型</td>
            <td class="listTitle">公告内容</td>
            <td class="listTitle">公告图片</td>
            <td class="listTitle">渠道</td>
            <td class="listTitle">平台</td>
            <td class="listTitle">添加时间</td>
            <td class="listTitle">操作</td>
        </tr>

        <?php $i = 1;?>
        @foreach($publishes as $publishe)
            <tr align="center" @if($i++%2==0) class="list" @else class="listBg"
                @endif onmouseover="currentcolor=this.style.backgroundColor;this.style.backgroundColor='#caebfc';this.style.cursor='default';"
                onmouseout="this.style.backgroundColor=currentcolor">

                {{--<td><input type="checkbox" class="cateids" name="cateids[]" value="{{$category->cateid}}"></td>--}}
                <td>{{$publishe->id}}</td>
                <td>
                    {{ $publishe->title }}
                </td>

                <td>
                    @if ($publishe->type == 1) 静态通告 @endif
                    @if ($publishe->type == 2) url链接通告 @endif
                    @if ($publishe->type == 4) 跳至小说通告 @endif
                    @if ($publishe->type == 3) 纯文本通告 @endif
                </td>
                <td>
                    {{ $publishe->content }}
                </td>
                <td>
                    {{ $publishe->img }}
                </td>

                <td>
                    <?php if($publishe->channel == -1){ ?>
                    全渠道
                    <?php }else{?>
                    渠道{{$publishe->channel}}
                    <?php }?>

                </td>

                <td>
                    @if ($publishe->platform == 3) 全平台 @endif
                    @if ($publishe->platform == 1) 安卓 @endif
                    @if ($publishe->platform == 2) IOS @endif
                </td>

                <td>{{$publishe->addtime }}</td>
                <td>
                    <a style="display: inline-block;width:65px;" onclick="edit({{$publishe->id}})">编辑</a>
                    <a style="display: inline-block;width:65px;" onclick="del({{$publishe->id}})">删除</a>
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
                        <td valign="bottom" nowrap="true" style="width:40%;">总记录：{{$publishes->total()}}
                            　页码：{{$publishes->currentPage()}}/{{$publishes->count()}}　每页：{{$publishes->perPage()}}
                        </td>
                        <td valign="bottom" nowrap="true"
                            style="width:60%;">{{ $publishes->appends($where)->render() }}</td>
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
                <h4 class="modal-title" id="myModalLabelEdit">编辑通告</h4>
            </div>
            <div class="modal-body">
                <form id="thisEditPro" class="form-horizontal" method="post"
                      action="{{url('novel/edit-publish')}}" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">通告标题</label>
                        <div class="col-sm-4">
                            <input type="text" id="ptitle1" class="form-control" name="title" value="" placeholder="通告标题" required="required">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">渠道</label>
                        <div class="col-sm-8">
                            <select id="pchannel1"  name="channel"  >
                                <option value="-1">全渠道</option>

                                @foreach($packArr as $pack)
                                    <option value="{{$pack->pack_name}}">渠道{{$pack->pack_name}}</option>

                                @endforeach

                            </select>
                        </div>
                    </div>
                    <input type="hidden" id="pid" name="pid" value="">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">平台</label>
                        <div class="col-sm-8">
                            <select id="pplatform1" name="platform"  >
                                <option value="3">全平台</option>
                                <option value="1">安卓</option>
                                <option value="2">IOS</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">通告类型</label>
                        <div class="col-sm-8">
                            <select class="add_type1" id="ppublish_type1" name="publish_type" onchange="selecttype1()">
                                <option value="1" selected="selected">静态通告</option>
                                <option value="2">url链接通告</option>
                                <option value="3">纯文本通告</option>
                                <option value="4">跳转至小说通告</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">展示类型</label>
                        <div class="col-sm-8">
                            <select  name="showtype" id="pshowtype1">
                                <option value="1">首次打开展示</option>
                                <option value="2">每天首次打开展示</option>
                                <option value="3">每次打开展示</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="id_img1">
                        <label class="col-sm-3 control-label">展示图片</label>
                        <div class="col-sm-8">
                            <input type="file" id="pimg1" name="imgFile" value="" placeholder="展示图片">建议600*600尺寸图片
                        </div>
                    </div>

                    <div class="form-group" id="id_url1" style="display: none;">
                        <label class="col-sm-3 control-label">url链接</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="purl1" name="urlink" value="" placeholder="url链接"><p style="color:red;">地址需要以https://或者http://开头</p>
                        </div>
                    </div>
                    <div class="form-group" id="id_novelid1"  style="display: none;">
                        <label class="col-sm-3 control-label">小说id</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="pnovelid1" name="novelid" value="" placeholder="输入准确的小说id进行查询">
                            不支持模糊查询
                        </div>
                    </div>
                    <div class="form-group" id="id_content1"  style="display: none;">
                        <label class="col-sm-3 control-label">内容</label>
                        <div class="col-sm-8">
                            <textarea id="ptext1" name="content" style="width: 360px;height: 150px;"></textarea><p style="color:red;">最多输入30个中文字</p>
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
                <h4 class="modal-title" id="myModalLabelEdit">新增通告</h4>
            </div>
            <div class="modal-body">
                <form id="thisEditPro" class="form-horizontal" method="post"
                      action="{{url('novel/add-publish')}}" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">通告标题</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="title" value="" placeholder="通告标题" required="required">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">渠道</label>
                        <div class="col-sm-8">
                            <select   name="channel"  >
                                <option value="-1">全渠道</option>
                                @foreach($packArr as $pack)
                                    <option value="{{$pack->pack_name}}">渠道{{$pack->pack_name}}</option>

                                @endforeach

                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">平台</label>
                        <div class="col-sm-8">
                            <select  name="platform"  >
                                <option value="3">全平台</option>
                                <option value="1">安卓</option>
                                <option value="2">IOS</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">通告类型</label>
                        <div class="col-sm-8">
                            <select class="add_type" name="publish_type" onchange="selecttype()">
                                <option value="1" selected="selected">静态通告</option>
                                <option value="2">url链接通告</option>
                                <option value="3">纯文本通告</option>
                                <option value="4">跳转至小说通告</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">展示类型</label>
                        <div class="col-sm-8">
                            <select  name="showtype" >
                                <option value="1">首次打开展示</option>
                                <option value="2">每天首次打开展示</option>
                                <option value="3">每次打开展示</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="id_img">
                        <label class="col-sm-3 control-label">展示图片</label>
                        <div class="col-sm-8">
                            <input type="file" name="imgFile" value="" placeholder="展示图片"><p style="color:red;">建议600*600尺寸图片</p>
                        </div>
                    </div>

                    <div class="form-group" id="id_url" style="display: none;">
                        <label class="col-sm-3 control-label">url链接</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="urlink" value="" placeholder="url链接"><p style="color:red;">地址需要以https://或者http://开头</p>
                        </div>
                    </div>
                    <div class="form-group" id="id_novelid"  style="display: none;">
                        <label class="col-sm-3 control-label">小说id</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="novelid" value="" placeholder="输入准确的小说id进行查询">
                            不支持模糊查询
                        </div>
                    </div>
                    <div class="form-group" id="id_content"  style="display: none;">
                        <label class="col-sm-3 control-label">内容</label>
                        <div class="col-sm-8">
                            <textarea name="content" style="width: 360px;height: 150px;"></textarea><p style="color:red;">最多输入30个中文字</p>
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
        if(add_type ==1){
            $("#id_img").show();
            $("#id_url").hide();
            $("#id_novelid").hide();
            $("#id_content").hide();
        }else if(add_type ==2){
            $("#id_img").show();
            $("#id_url").show();
            $("#id_novelid").hide();
            $("#id_content").hide();
        }else if(add_type ==3){
            $("#id_img").hide();
            $("#id_url").hide();
            $("#id_novelid").hide();
            $("#id_content").show();
        }else{
            $("#id_img").show();
            $("#id_url").hide();
            $("#id_novelid").show();
            $("#id_content").hide();
        }

    }
    function selecttype1() {
        var add_type = $('.add_type1 option:selected') .val();
        if(add_type ==1){
            $("#id_img1").show();
            $("#id_url1").hide();
            $("#id_novelid1").hide();
            $("#id_content1").hide();
        }else if(add_type ==2){
            $("#id_img1").show();
            $("#id_url1").show();
            $("#id_novelid1").hide();
            $("#id_content1").hide();
        }else if(add_type ==3){
            $("#id_img1").hide();
            $("#id_url1").hide();
            $("#id_novelid1").hide();
            $("#id_content1").show();
        }else{
            $("#id_img1").show();
            $("#id_url1").hide();
            $("#id_novelid1").show();
            $("#id_content1").hide();
        }
    }
    function del(id) {
        if(confirm("确认删除吗？")) {
            $.ajax({
                type: "POST",
                url: "/novel/del-publish",
                data: {id: id, _token: "{{csrf_token()}}"},
                dataType: "json",
                success: function (data) {
                    if (data.code == 1) {
                        window.location.href = "{{url('/novel/publish')}}";
                    }
                }
            })
        }
    }

    function savePro() {
        $("#thisEditPro").submit();
    }
    function edit(id){
        // $("#cid").val(id);
        // $("#sort").val(sort);
        // $("#cname").val(name);
        // $("#add_type").find("option[value="+type+"]").attr("selected",true);
        // $("#add_showtype").find("option[value="+showtype+"]").attr("selected",true);
        $('#myModalEdit').modal();
        $.ajax({
            type: "POST",
            url: "/novel/get-publish",
            data: {id:id ,_token:"{{csrf_token()}}"},
            dataType: "json",
            success: function (data) {
                if(data.code==1){

                    var data = data.data;
                    var add_type = data.type;
                    if(add_type ==1){
                        $("#id_img1").show();
                        $("#id_url1").hide();
                        $("#id_novelid1").hide();
                        $("#id_content1").hide();
                    }else if(add_type ==2){
                        $("#id_img1").show();
                        $("#id_url1").show();
                        $("#id_novelid1").hide();
                        $("#id_content1").hide();
                        $("#purl1").val(data.content);
                    }else if(add_type ==3){
                        $("#id_img1").hide();
                        $("#id_url1").hide();
                        $("#id_novelid1").hide();
                        $("#id_content1").show();
                        $("#ptext1").text(data.content);
                    }else if(add_type ==4){
                        $("#id_img1").show();
                        $("#id_url1").hide();
                        $("#id_novelid1").show();
                        $("#id_content1").hide();
                        $("#pnovelid1").val(data.content);
                    }
                    $("#ptitle1").val(data.title);
                    $("#pid").val(data.id);

                    // $("#pimg1").val(data.img);





                    var channel = data.channel;
                    var platform = data.platform;
                    var showtype = data.showtype;
                    // $("#add_type").find("option[value="+type+"]").attr("selected",true);


                    // $("#pchannel1").find("option[value="+channel+"]").attr("selected",true);
                    // $("#pplatform1").find("option[value="+platform+"]").attr("selected",true);
                    // $("#ppublish_type1").find("option[value="+add_type+"]").attr("selected",true);
                    // $("#pshowtype1").find("option[value="+showtype+"]").attr("selected",true);


                    $("#pchannel1").val(channel);
                    $("#pplatform1").val(platform);
                    $("#ppublish_type1").val(add_type);
                    $("#pshowtype1").val(showtype);

                }
            }
        })

    }
    $('.add').click(function () {
        $('#myModalAdd').modal();
    });


</script>
</body>
</html>
