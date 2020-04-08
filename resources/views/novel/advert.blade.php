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
            你当前位置：小说管理 - 广告管理
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
                <input type="text" class="text" value="{{$title}}" name="title" placeholder="广告名称" style="width:150px;display: inline-block;">
                <input type="submit" name="btnQuery" value="查询" id="btnQuery" class="btn wd1"/>
                <input type="button" name="btnRefresh" value="刷新" onclick="window.location.href='{{url('sysuser')}}'"
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
            <td class="listTitle">广告名称</td>
            <td class="listTitle">平台</td>
            <td class="listTitle">包id</td>
            <td class="listTitle">类型</td>
            <td class="listTitle">链接</td>
            <td class="listTitle">图片</td>
            <td class="listTitle">状态</td>
            <td class="listTitle">起始时间</td>
            <td class="listTitle">终止时间</td>
            <td class="listTitle">操作</td>
        </tr>
        <?php $i = 1;?>
        @foreach($adverts as $advert)
            <tr align="center" @if($i++%2==0) class="list" @else class="listBg"
                @endif onmouseover="currentcolor=this.style.backgroundColor;this.style.backgroundColor='#caebfc';this.style.cursor='default';"
                onmouseout="this.style.backgroundColor=currentcolor">
                <td><input type="checkbox" class="advertids" name="advertids[]" value="{{$advert->advertid}}"></td>
                <td>{{ $advert->title }}</td>
                <td>
                    @if ($advert->platform == 1) 安卓 
                    @elseif ($advert->platform == 2)ios
                    @elseif ($advert->platform == 3) shuke.shuhun.com网站
                    @endif
                </td>
                <td>
                    @if ($advert->packid) {{$advert->packid}} @else 默认 @endif
                </td>
                <td>
                    @if ($advert->type == 1) splash(首页图片) @elseif ($advert->type == 2) 弹窗 @elseif ($advert->type == 3) 底部  @elseif ($advert->type == 4) 首页视频 @elseif ($advert->type == 5) 首页banner @endif
                </td>
                <td>
                    <a  class='url' style='width:100%;text-overflow: ellipsis;overflow: hidden;display: block;' href="{{$advert->url}}" target="_Blank">{{$advert->url}}</a>
                    <span class='url_span' style='display:none;height: 50px;padding: 15px;position: absolute;background-color: #d9edf7;color: #333;font-size: 18px;'>{{$advert->url}}</span>
                </td>
                <td>
                    <a href="{{$advert->img}}" target="_Blank">{{$advert->img}}</a>
                </td>
                <td>
                    @if ($advert->status == 1) 开启 @else 关闭 @endif
                </td>
                <td>{{ date('Y-m-d H:i:s', $advert->start) }}</td>
                <td>{{ date('Y-m-d H:i:s', $advert->end) }}</td>
                <td>
                    <button class="myEdit wd1" adver_type='{{$advert->adver_type}}'packid="{{$advert->packid}}" mytype="{{$advert->type}}" platform="{{$advert->platform}}" start="{{date('Y-m-d H:i:s', $advert->start)}}" end="{{date('Y-m-d H:i:s', $advert->end)}}" status="{{$advert->status}}" url="{{$advert->url}}" advertid="{{$advert->advertid}}" title="{{$advert->title}}" sort="{{$advert->sort}}" img="{{$advert->img}}">编辑</button>
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
                        <td valign="bottom" nowrap="true" style="width:40%;">总记录：{{$adverts->total()}}
                            　页码：{{$adverts->currentPage()}}/{{$adverts->count()}}　每页：{{$adverts->perPage()}}
                        </td>
                        <td valign="bottom" nowrap="true"
                            style="width:60%;">{{ $adverts->appends($where)->render() }}</td>
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
                <h4 class="modal-title" id="myModalLabelEdit">编辑广告</h4>
            </div>
            <div class="modal-body">
                <form id="thisEditPro" class="form-horizontal" method="post"
                      action="{{url('novel/edit-advert')}}"  enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">广告id</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="advertid" value="" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">广告名称</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="title" value="" placeholder="广告名称">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">广告平台</label>
                        <div class="col-sm-4">
                            <select type="text"  name="platform">
                                <option value="1">安卓</option>
                                <option value="2">ios</option>
                                <option value="3">shuke.shuhun.com网站</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">包id</label>
                        <div class="col-sm-4">
                            <select type="text"  name="packid">
                                <option value="">默认</option>
                                @foreach($packids as $pack)
                                <option value="{{$pack->packid}}"  platform="{{$pack->platform}}">{{$pack->packid}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">广告显示位置</label>
                        <div class="col-sm-4">
                            <select type="text"  name="type">
                                <option value="1">splash(首页图片)</option>
                                <option value="2">弹窗</option>
                                <option value="3">底部</option>
                                <option value="4">首页视频</option>
                                <option value="5">首页banner</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">广告跳转连接</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="url" value="" placeholder="广告跳转连接，如果是banner广告可直接输入小说ID">
                        </div>
                    </div>
                     <div class="form-group">
                        <label class="col-sm-3 control-label">广告类型</label>
                        <div class="col-sm-8">
                            <select type="text"  name="adver_type">
                                    <option value="1">图片</option>
                                    <option value="2">视频</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">广告图片或视频</label>
                        <div class="col-sm-8">
                            <input type="file" name="imgFile" value="" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">广告排序</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="sort" value="" placeholder="广告排序">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">广告状态</label>
                        <div class="col-sm-8">
                            <select type="text"  name="status">
                                    <option value="0">关闭</option>
                                    <option value="1">开启</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">广告开始时间</label>
                        <div class="col-sm-8">
                            <input type="text" name="start" class="form-control" value="" id="ptime"
                       placeholder="广告开始时间" style="width:200px;display: inline-block;" onclick="WdatePicker()"  js="datepicker">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">广告结束时间</label>
                        <div class="col-sm-8">
                            <input type="text" name="end" class="form-control" value="" id="ptime"
                       placeholder="广告结束时间" style="width:200px;display: inline-block;" onclick="WdatePicker()"  js="datepicker">
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
                      action="{{url('novel/add-advert')}}"  enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">广告名称</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="title" value="" placeholder="广告名称">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">广告平台</label>
                        <div class="col-sm-4">
                            <select type="text"  name="platform">
                                <option value="1">安卓</option>
                                <option value="2">ios</option>
                                <option value="3">shuke.shuhun.com网站</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">包id</label>
                        <div class="col-sm-4">
                            <select type="text"  name="packid">
                                <option value="">默认</option>
                                @foreach($packids as $pack)
                                <option value="{{$pack->packid}}"  platform="{{$pack->platform}}">{{$pack->packid}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">广告显示位置</label>
                        <div class="col-sm-4">
                            <select type="text"  name="type">
                                <option value="1">splash(首页图片)</option>
                                <option value="2">弹窗</option>
                                <option value="3">底部</option>
                                <option value="4">首页视频</option>
                                <option value="5">首页banner</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">广告跳转连接</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="url" value="" placeholder="广告跳转连接，如果是banner广告可直接输入小说ID">
                        </div>
                    </div>
                <div class="form-group">
                        <label class="col-sm-3 control-label">广告类型</label>
                        <div class="col-sm-8">
                            <select type="text"  name="adver_type">
                                    <option value="1">图片</option>
                                    <option value="2">视频</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">广告图片或视频</label>
                        <div class="col-sm-8">
                            <input type="file" name="imgFile" value="" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">广告排序</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="sort" value="" placeholder="广告排序">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">广告状态</label>
                        <div class="col-sm-8">
                                <select type="text"  name="status">
                                    <option value="0">关闭</option>
                                    <option value="1">开启</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">广告开始时间</label>
                        <div class="col-sm-8">
                            <input type="text" name="start" class="form-control" value="" id="ptime"
                       placeholder="广告开始时间" style="width:200px;display: inline-block;" onclick="WdatePicker()"  js="datepicker">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">广告结束时间</label>
                        <div class="col-sm-8">
                            <input type="text" name="end" class="form-control" value="" id="ptime"
                       placeholder="广告结束时间" style="width:200px;display: inline-block;" onclick="WdatePicker()"  js="datepicker">
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
$('.url').hover(function() {
    $('.url_span').css('display','none');
    $(this).next().css('display','block');
})
$('.url').mouseleave(function() {
    $('.url_span').css('display','none');
})
$('.myEdit').click(function () {
    $('#myModalEdit').modal();
    $('#myModalEdit input[name=title]').val($(this).attr('title'));
    $('#myModalEdit input[name=advertid]').val($(this).attr('advertid'));
    $('#myModalEdit input[name=url]').val($(this).attr('url'));
    $('#myModalEdit input[name=sort]').val($(this).attr('sort'));
    $('#myModalEdit input[name=start]').val($(this).attr('start'));
    $('#myModalEdit input[name=end]').val($(this).attr('end'));
    $('#myModalEdit select[name=status]').val($(this).attr('status'));
    $('#myModalEdit select[name=platform]').val($(this).attr('platform'));
    $('#myModalEdit select[name=type]').val($(this).attr('mytype'));
    $("[name='platform']").trigger("change");
    $('#myModalEdit select[name=packid]').val($(this).attr('packid'));
    $('#myModalEdit input[name=img]').val($(this).attr('img'));
    $('#myModalEdit select[name=adver_type]').val($(this).attr('adver_type'));
    
    $('#savePro').click(function () {
        $('#thisEditPro').submit();
    });
        
});

$('.add').click(function () {
    $("[name='platform']").trigger("change");
    $('#myModalAdd').modal();    
});

$('.del').click(function () {
    var ids = new Array();
    $('.advertids').each(function () {
        if ($(this).is(':checked')) {
            ids.push($(this).val());
        }
    });
    if (0 == ids.length) {
        alert('请至少选择一条记录');
        return false;
    }
    $.post("{{url('novel/del-advert')}}", {
        _token: "{{csrf_token()}}",
        advertids: ids,
        status: 1
    }, function (data) {
        if (data.state) {
            window.history.go(0);
        } else {
            alert('操作失败');
        }
    }, 'json');
});

$("[name='platform']").change(function(){
  $("select[name='packid'] option").css('display','none');
  $("select[name='packid'] option[platform='" + $(this).val()+"']").css('display','');
  $("select[name='packid'] option[value='']").css('display','');
  $("select[name='packid']").val('');
});
</script>
</body>
</html>
