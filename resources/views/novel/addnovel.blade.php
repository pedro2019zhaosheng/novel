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
            你当前位置：小说管理 - 添加小说
        </td>
    </tr>
</table>
<!-- 头部菜单 End -->
<script src='{{ asset("/bower_components/AdminLTE/plugins/my97date/WdatePicker.js")}}'></script>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="titleQueBg">
    <tr>
        <form name="form1" method="get" action="{{url('novel/addnovel')}}" id="form1">
            <td align="center" style="width: 80px">
                查询：
            </td>

            <td>
                <select type="text" name="novel_name">
                    <option value="0">所有小说</option>
                    @foreach($categorys as $allCate)
                        <?php if($cate == $allCate->cateid){ ?>
                            <option value="{{$allCate->cateid}}" selected>{{$allCate->name}}</option>
                        <?php }else{?>
                            <option value="{{$allCate->cateid}}">{{$allCate->name}}</option>
                        <?php }?>

                    @endforeach
                    {{--<option value="1" @if($pack_name==1) selected @endif>sd11e5r.cn</option>--}}
                    {{--<option value="2" @if($pack_name==2) selected @endif>sd11ler.cn</option>--}}
                    {{--<option value="3" @if($pack_name==3) selected @endif>sd11e3r.cn</option>--}}
                    {{--<option value="4" @if($pack_name==4) selected @endif>sd11ber.cn</option>--}}
                    {{--<option value="5" @if($pack_name==5) selected @endif>sd11ert.cn</option>--}}
                    {{--<option value="6" @if($pack_name==6) selected @endif>ugm6.cn</option>--}}
                </select>
                <input type="text" class="text" value="{{$name}}" name="name" placeholder="小说名称" style="width:150px;display: inline-block;">
                <input type="hidden" name="id" value="{{$id}}">
                <input type="hidden" name="type" value="{{$type}}">
                <input type="submit" name="btnQuery" value="查询" id="btnQuery" class="btn wd1"/>

            </td>
        </form>
    </tr>
</table>

<div class="clear"></div>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="Tmg7">
    <tr>
        <td height="39" class="titleOpBg">
            {{--<button type="button" name="search" id="search-btn" class="wd1 l search-btn add" data-target="#myModal">新增</button>--}}
            {{--<a href="{{url('novel/addnovel?id='.$id)}}">添加小说</a>--}}
            <?php if($type == 2){ ?>
                <a href="{{url('novel/hotonecolumn?id='.$id)}}">返回热推专栏管理页</a>

            <?php }else if($type == 5){?>
                <a href="{{url('novel/novelcolumn?id='.$id)}}">返回小说专栏</a>
            <?php }?>
        </td>
    </tr>
</table>

<div id="content">
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="box" id="list">
        <tr align="center" class="bold">
            <td class="listTitle">

            </td>
            <td class="listTitle">小说id</td>
            <td class="listTitle">小说名称</td>
            <td class="listTitle">小说封面</td>
            <td class="listTitle">小说作者</td>
            <td class="listTitle">小说章节数</td>
            {{--<td class="listTitle">小说类型</td>--}}

            {{--<td class="listTitle">操作</td>--}}
        </tr>

        <?php $i = 1;?>
        @foreach($novels as $novel)
            <tr align="center" @if($i++%2==0) class="list" @else class="listBg"
                @endif onmouseover="currentcolor=this.style.backgroundColor;this.style.backgroundColor='#caebfc';this.style.cursor='default';"
                onmouseout="this.style.backgroundColor=currentcolor">

                <td><input onclick="addBook(this)" novelname="{{$novel->name }}" type="checkbox" class="novelid" name="novelids" value="{{$novel->id}}"></td>

                <td>
                    {{ $novel->id }}
                </td>
                <td>{{$novel->name }}</td>
                <td>
                    <a href="{{$novel->img}}" target="_Blank">{{$novel->img}}</a>
                </td>
                <td>{{$novel->author }}</td>
                <td>{{$novel->chapter }}</td>
                {{--@if($novel->cateid==1) <td>都市言情</td> @endif--}}
                {{--@if($novel->cateid==2) <td>玄幻奇幻</td> @endif--}}
                {{--@if($novel->cateid==3) <td>青春校园</td> @endif--}}
                {{--@if($novel->cateid==4) <td>灵异异能</td> @endif--}}
                {{--@if($novel->cateid==5) <td>科幻悬疑</td> @endif--}}
                {{--@if($novel->cateid==6) <td>武侠仙侠</td> @endif--}}
                {{--@if($novel->cateid==7) <td>历史军事</td> @endif--}}
                {{--@if($novel->cateid==8) <td>游戏竞技</td> @endif--}}
                {{--@if($novel->cateid==9) <td>穿越重生</td> @endif--}}
                {{--@if($novel->cateid==11) <td>其他</td> @endif--}}


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
                        <td valign="bottom" nowrap="true" style="width:40%;">总记录：{{$novels->total()}}
                            　页码：{{$novels->currentPage()}}/{{$novels->count()}}　每页：{{$novels->perPage()}}
                        </td>
                        <td valign="bottom" nowrap="true"
                            style="width:60%;">{{ $novels->appends($where)->render() }}</td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>


<!-- Modal -->
<div class="modal fade" id="myModalAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabelEdit">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabelEdit">添加小说至该专栏下</h4>
            </div>
            <div class="modal-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">小说名称</label>
                        <div class="col-sm-4">
                            <input type="text" id="novelname" class="form-control" name="name" value="" placeholder="小说名称" readonly>
                        </div>
                    </div>

                    <input type="hidden" id="novelid" name="novelid" value=""/>
                    <input type="hidden" id="choiceid"   value="{{$id}}"/>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">排序</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="sort" name="keyword" value="" placeholder="排序" required="required">
                        </div>
                    </div>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="modal-footer">
                        <button type="button" class="wd1 btn btn-default" data-dismiss="modal" onclick="removecheck()">关闭</button>
                        <button type="submit" onclick="savePro()" class="wd1 btn btn-primary">添加</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function addBook(o) {

    $('#myModalAdd').modal();
    var name = $(o).attr('novelname');
    var id = $(o).val();
    $("#novelname").val(name);
    $("#novelid").val(id);
}
function removecheck(){
    $("input[name='novelids']").prop('checked',false);
}
function savePro() {
    var novelid = $("#novelid").val();
    var sort = $("#sort").val();
    var choiceid = $("#choiceid").val();

    $.ajax({
        type: "POST",
        url: "/novel/add-novelcolumn",
        data: {choiceid:choiceid,novelid: novelid,sort:sort, _token: "{{csrf_token()}}"},
        dataType: "json",
        success: function (data) {
            if (data.code == 1) {
                alert("添加成功");
                window.location.reload();
            }else{
                alert("该专题下已存在该书");
            }

        }
    })
}
</script>
</body>
</html>
