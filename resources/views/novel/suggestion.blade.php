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
            你当前位置：小说管理 - 反馈记录管理
        </td>
    </tr>
</table>
<!-- 头部菜单 End -->
<script src='{{ asset("/bower_components/AdminLTE/plugins/my97date/WdatePicker.js")}}'></script>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="titleQueBg">
    <tr>
        <form name="form1" method="get" action="{{url('novel/suggestion')}}" id="form1">
            <td align="center" style="width: 80px">
                查询：
            </td>
            <td>
                <select type="text"  name="platform">
                    <option value="0">--请选择类型--</option>
                    <option value="1" @if ($where['type'] == 1) selected @endif>阅读功能</option>
                    <option value="2" @if ($where['type'] == 2) selected @endif>产品建议</option>
                    <option value="3" @if ($where['type'] == 3) selected @endif>书籍内容</option>
                    <option value="4" @if ($where['type'] == 4) selected @endif>其他</option>
                </select>

                <input type="submit" name="btnQuery" value="查询" id="btnQuery" class="btn wd1"/>
                <input type="button" name="btnRefresh" value="导出" class="btn wd1"/>
            </td>
        </form>
    </tr>
</table>

<div class="clear"></div>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="Tmg7">
    <tr>
        {{--<td height="39" class="titleOpBg">--}}
        {{--<button type="button" name="search" id="search-btn" class="wd1 l search-btn add" data-target="#myModal">新增</button>--}}
        {{--<button type="button" name="del" id="del-btn" class="wd1 l del">删除</button>--}}
        {{--</td>--}}
    </tr>
</table>

<div id="content">
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="box" id="list">
        <tr align="center" class="bold">

            <td class="listTitle">反馈id</td>
            <td class="listTitle">会员手机号</td>
            <td class="listTitle">会员id</td>
            <td class="listTitle">反馈类型</td>
            <td class="listTitle">反馈内容</td>
            <td class="listTitle">反馈时间</td>
            <td class="listTitle">反馈截图</td>
        </tr>

        <?php $i = 1;?>
        @foreach($suggestions as $suggestion)
            <tr align="center" @if($i++%2==0) class="list" @else class="listBg"
                @endif onmouseover="currentcolor=this.style.backgroundColor;this.style.backgroundColor='#caebfc';this.style.cursor='default';"
                onmouseout="this.style.backgroundColor=currentcolor">

                {{--<td><input type="checkbox" class="cateids" name="cateids[]" value="{{$category->id}}"></td>--}}
                <td>{{$suggestion->id}}</td>
                <td>
                    {{ $suggestion->phone }}
                </td>
                <td>
                    {{ $suggestion->uuid }}
                </td>
                <td>
                    <?php if($suggestion->type == 1){?>
                    阅读功能
                    <?php }elseif($suggestion->type == 2){?>
                    产品建议
                    <?php }elseif($suggestion->type == 3){?>
                    书籍内容
                    <?php }else{?>
                    其他
                    <?php }?>
                </td>
                <td>
                    {{$suggestion->content}}
                </td>
                <td>
                    {{$suggestion->addtime}}
                </td>
                <td>
                    <a onclick="getdetail({{$suggestion->id}})">详情</a>
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
                        <td valign="bottom" nowrap="true" style="width:40%;">总记录：{{$suggestions->total()}}
                            　页码：{{$suggestions->currentPage()}}/{{$suggestions->count()}}　每页：{{$suggestions->perPage()}}
                        </td>
                        <td valign="bottom" nowrap="true"
                            style="width:60%;">{{ $suggestions->appends($where)->render() }}</td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>
<div class="modal fade" id="myModalEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabelEdit">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabelEdit">反馈图片详情</h4>
            </div>
            <div class="modal-body">
                <div  class="form-horizontal" >

                </div>

                <div class="modal-footer">
                    <button type="button" class="wd1 btn btn-default" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function getdetail(id) {
        $("#myModalEdit").modal();
        $.ajax({
            type: "POST",
            url: "/novel/get-suggestion-img",
            data: {id:id,_token: "{{csrf_token()}}"},
            dataType: "json",
            success: function (data) {
                if (data.code == 1) {

                    var info = data.data;
                    if(info.length == 0){
                        $(".form-horizontal").html("用户未上传反馈图片信息");
                    }else{
                        var _html='';
                        for(var i=0;i<info.length;i++){
                            _html += "<div><img style='height: 100px;width: 100px;' src='"+info[i].img+"'></div>";

                        }
                        $(".form-horizontal").html(_html);
                    }
                }
            }
        })
    }
</script>
</body>
</html>
