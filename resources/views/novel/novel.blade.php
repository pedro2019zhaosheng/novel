<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head id="Head1">
    <title></title>
    <link rel="stylesheet" href='{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css")}}'>
    <link rel="stylesheet" href='{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}'>
    <link href="{{asset('/newsty')}}/layout.css" rel="stylesheet" type="text/css"/>
    <script src="{{ asset("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js")}}"></script>
    <script type="text/javascript" src="{{asset('/newsty')}}/common.js"></script>
    <script src="{{ asset("/bower_components/AdminLTE/bootstrap/js/bootstrap.js")}}"></script>
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
            你当前位置：小说管理 - 小说管理
        </td>
    </tr>
</table>
<!-- 头部菜单 End -->
<script src='{{ asset("/bower_components/AdminLTE/plugins/my97date/WdatePicker.js")}}'></script>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="titleQueBg">
<tr>
<form name="form1" method="get" action="{{url('novel/index')}}" id="form1">
<td align="center" style="width: 80px">
查询：
</td>
<td>
    <input type="text" class="text" value="{{$name}}" name="name" placeholder="小说名称" style="width:150px;display: inline-block;">
    <select type="text" name="cateid">
    <option value="0">--请选择分类--</option>
    @foreach($cates as $k=>$v)

    <option value="{{$v->id}}"   @if ($v->id == $where['cateid']) selected  @endif>{{$v->name}}</option>
    @endforeach

    </select>

    排序 <select type="text" name="sort">
        <option value="1" @if ($where['sort'] ==1) selected  @endif>小说id</option>
        <option value="2" @if ($where['sort'] ==2) selected  @endif>排序值</option>
        <option value="3" @if ($where['sort'] ==3) selected  @endif>总阅读量</option>
        <option value="4" @if ($where['sort'] ==4) selected  @endif>总下载量</option>
        <option value="5" @if ($where['sort'] ==5) selected  @endif>总书架量</option>
        <option value="6" @if ($where['sort'] ==6) selected  @endif>真实阅读量</option>
        <option value="7" @if ($where['sort'] ==7) selected  @endif>真实下载量</option>
        <option value="8" @if ($where['sort'] ==8) selected  @endif>真实书架量</option>
        <option value="9" @if ($where['sort'] ==9) selected  @endif>最后更新时间</option>
        <option value="10" @if ($where['sort'] ==10) selected  @endif>创建时间</option>
        <option value="11" @if ($where['sort'] ==11) selected  @endif>章节数</option>
    </select>
<input type="submit" name="btnQuery" value="查询" id="btnQuery" class="btn wd1"/>
<i  nput type="button" name="btnRefresh" value="刷新" onclick="window.location.href='{{url('novel/index')}}'" class="btn wd1"/>
</td>
</form>
</tr>
</table>
<div class="clear"></div>
<div id="content">
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="box" id="list">
        <tr align="center" class="bold">
            <td class="listTitle">小说id</td>
            <td class="listTitle">分类</td>
            <td class="listTitle">小说名称</td>
            <td class="listTitle">排序值</td>

            <td class="listTitle">真实阅读量</td>
            <td class="listTitle">真实下载量</td>
            <td class="listTitle">真实书架量</td>
            <td class="listTitle">总阅读量</td>
            <td class="listTitle">总下载量</td>
            <td class="listTitle">总书架量</td>
            {{--<td class="listTitle">搜索量</td>--}}
            <td class="listTitle">作者</td>
            {{--<td class="listTitle">封面</td>--}}
            <td class="listTitle">状态</td>
            <td class="listTitle">章节数</td>
            <td class="listTitle">显示</td>
            <td class="listTitle">创建时间</td>
            <td class="listTitle">最后更新时间</td>
            <td class="listTitle">操作</td>
        </tr>

        <?php $i = 1;?>

        @foreach($novels as $novel)
            <tr align="center" @if($i++%2==0) class="list" @else class="listBg"
                @endif onmouseover="currentcolor=this.style.backgroundColor;this.style.backgroundColor='#caebfc';this.style.cursor='default';"
                onmouseout="this.style.backgroundColor=currentcolor">

                <td>{{$novel->id}}</td>
                {{--                <td>@if(isset($cates[$novel->nove_line_cate_id]->name)) {{$cates[$novel->nove_line_cate_id]->name}} @else 无分类 @endif</td>--}}

                <td>{{$novel->cate}}</td>
                <td>{{ $novel->name }}</td>
                <td>{{ $novel->sort }}</td>
                <td>{{ $novel->click_nums_real }}</td>
                <td>{{ $novel->down_nums_real }}</td>
                <td>{{ $novel->shelf_nums_real }}</td>
                <td>{{ $novel->click_nums_all }}</td>
                <td>{{ $novel->down_nums_all }}</td>
                <td>{{ $novel->shelf_nums_all }}</td>
                {{--<td>{{ $novel->search }}</td>--}}
                <td>
                    {{$novel->author}}
                </td>
                {{--<td>--}}
                    {{--<a href="{{$novel->img}}" target="_Blank">{{$novel->img}}</a>--}}
                {{--</td>--}}
                <td>
                    @if ($novel->progress_status == 2) 已完本  @endif
                    @if ($novel->progress_status == 1) 连载中  @endif
                    @if ($novel->progress_status == 3) 未知状态  @endif
                </td>
                <td>
                    {{$novel->chapter}}
                </td>
                <td>
                    @if ($novel->status == 1) 已上架  @endif
                    @if ($novel->status == 0) 已下架  @endif
                    @if ($novel->status == 2) 已删除  @endif

                </td>
                <td>
                    {{$novel->created_at}}
                </td>
                <td>
                    {{ $novel->updated_at }}
                </td>
                <td>
                <button class='wd1 myEdit' novelid="{{ $novel->id }}" imgcover="{{ $novel->img }}" summary="{{$novel->summary}}" nove_line_cate_id="{{$novel->nove_line_cate_id}}" novelName="{{ $novel->name }}" click_nums="{{ $novel->click_nums }}" down_nums="{{ $novel->down_nums }}" shelf_nums="{{ $novel->shelf_nums }}" >编辑</button>

                @if ($novel->status == 1)
                        <button onclick="oprate({{ $novel->id }},0)" >下架</button>
                @endif
                @if ($novel->status == 0)
                    <button  onclick="oprate({{ $novel->id }},1)" >上架</button>
                @endif
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
                        <td valign="center" nowrap="true" style="width:40%;">总记录：{{$novels->total()}}
                            　页码：{{$novels->currentPage()}}/{{$novels->count()}}　每页：{{$novels->perPage()}}
                        </td>
                        <td valign="center" nowrap="true"
                            style="width:60%;">{{ $novels->appends($where)->render() }}</td>
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
                <h4 class="modal-title" id="myModalLabelEdit">编辑小说</h4>
            </div>
            <div class="modal-body">
                <form id="thisEditPro" class="form-horizontal" method="post"
                      action="{{url('novel/edit-nvnovel')}}" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">小说id</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="novelid" value="" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">小说名称</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" readonly name="name" value="" placeholder="小说名称">
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-3 control-label">虚拟阅读量</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="click_nums" value="" placeholder="小说阅读量">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">虚拟下载量</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="down_nums" value="" placeholder="小说下载量">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">虚拟书架量</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="shelf_nums" value="" placeholder="小说书架量">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">小说分类</label>
                        <div class="col-sm-8">
                            <select id="cateselect" name="cateid">
                                @foreach($cates as $k=>$v)

                                    <option value="{{$v->id}}"   @if ($v->id == $where['cateid']) selected  @endif>{{$v->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">小说简介</label>
                        <div class="col-sm-8">
                            <textarea id="summary" name="summary"  rows="6" style="width: 100%;"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">小说封面</label>
                        <div class="col-sm-8">
                            <img id="novelcover" src="" height="100px;" width="100px;">
                            <input type="file" name="cover">
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

<script>
    $('.myEdit').click(function () {
        $('#myModalEdit').modal();
        $('#thisEditPro input[name=name]').val($(this).attr('novelName'));
        $('#thisEditPro input[name=novelid]').val($(this).attr('novelid'));
        $('#thisEditPro input[name=click_nums]').val($(this).attr('click_nums'));
        $('#thisEditPro input[name=down_nums]').val($(this).attr('down_nums'));
        $('#thisEditPro input[name=shelf_nums]').val($(this).attr('shelf_nums'));

        $('#summary').val($(this).attr('summary'));
        $('#cateselect').val($(this).attr('nove_line_cate_id'));
        $('#novelcover').attr('src',$(this).attr('imgcover'));

        $('#savePro').click(function () {
            $('#thisEditPro').submit();
        });

    });

    function oprate(id,status) {
        if(confirm("确认进行该操作？")){
            $.ajax({
                type: "POST",
                url: "/novel/operate-status",
                data: {id:id,status:status,_token:"{{csrf_token()}}"},
                dataType: "json",
                success: function (data) {
                    if(data.code==1){
                        window.location.href="{{url('/novel/index')}}";
                    }
                }
            })
        }

    }
</script>
</body>
</html>
