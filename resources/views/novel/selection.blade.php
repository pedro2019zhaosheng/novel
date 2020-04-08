<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head id="Head1">
    <title></title>
    <link rel="stylesheet" href='{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.css")}}'>
    <link href="{{asset('/newsty')}}/layout.css" rel="stylesheet" type="text/css"/>
    <link href="{{asset('/newsty')}}/selection.css?v=2.1" rel="stylesheet" type="text/css"/>
    <script src="{{ asset("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js")}}"></script>
    <script type="text/javascript" src="{{asset('/newsty')}}/common.js"></script>
    <script src="{{ asset("/bower_components/AdminLTE/bootstrap/js/bootstrap.js")}}"></script>
    <script type="text/javascript" src="{{asset('/newsty')}}/comm.js"></script>
</head>
<style type="text/css">
    ul.pagination {
        display: inline-block;
        padding: 0;
        margin-top: 5px;
    }

    ul.pagination li {display: inline;}

    ul.pagination li a {
        color: black;
        float: left;
        padding: 8px 16px;
        text-decoration: none;
        transition: background-color .3s;
        border: 1px solid #ddd;
        /*margin: 0 4px;*/
    }

    ul.pagination li a.active {
        background-color: #a7d3ea;
        color: white;
        border: 1px solid #a7d3ea;
    }

    ul.pagination li a:hover:not(.active) {background-color: #ddd;}
</style>
<body>
<!-- 头部菜单 Start -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="title">
    <tr>
        <td width="19" height="25" valign="top" class="Lpd10">
            <div class="arr">
            </div>
        </td>
        <td width="1232" height="25" valign="top" align="left">
            你当前位置：小说管理 - 书城精选管理
        </td>
    </tr>
</table>
<!-- 头部菜单 End -->
<script src='{{ asset("/bower_components/AdminLTE/plugins/my97date/WdatePicker.js")}}'></script>
<div id="leftpart">

    <div id="banner_part">
        <input type="button" class="butt" value="添加BANNER" onclick="selectbanner()">
        <div id="banner_id_list">

        </div>
    </div>


    <div id="hottitle"><input id="onebookhead" type="text" placeholder="请输入标题"></div>
    <div id="hotone_part">
        <input type="button"  class="butt" value="添加书籍" onclick="addbookone()">
        <div style="text-align: center;"><a style="font-size: 16px;color:red;text-decoration: none;">热推一本</a></div>
        <div id="onebookarea">书名区域</div>
    </div>
    <div id="hotmore_part">
        <input type="button" class="butt" value="添加书籍" onclick="addhotmore()">
        <div style="text-align: center;"><a style="font-size: 16px;color:red;text-decoration: none;">横向排列</a></div>
        <div id="hotmorearea">书名区域</div>
    </div>

</div>


<div id="parthree" >

    <div id="epart" class="eachpart">
        <i style="display: none"></i>
        <div id="morebook">
            <div style="">
                <input type="text" id="booktitle0" placeholder="请输入标题">
                <input type="text" id="paixu0" placeholder="排序值" style="width: 40px;">
                <select id="indextype0">
                    <option value="0">横向排列</option>
                    <option value="1">纵向排列</option>
                </select>
            </div>
            <div>
                <input type="button"  class="butt" value="添加书籍" onclick="parthreeaddbook(0);">
                <div id="thridarea0">书名区域</div>

            </div>

        </div>
        <div style="margin-bottom: 50px;">
            <input type="button" class="butts" value="增加一块" onclick="addbookarea()">
            <input type="button" class="butts" value="删除该块" onclick="delbookarea(this)">
        </div>
    </div>

    


</div>

<div id="part_art">
    <div class="artitle">
        <label>明星作者</label>
        <input type="text" id="artindex" placeholder="请输入排序" style="float: right"/>
    </div>



    <span id="span0">
    <div class="arteachpart">

        <form id="artmp0" class="" method="post" action="" enctype="multipart/form-data"  onsubmit="return false">
            <div class="arteachpart_left" id="urlget0"  imgurl="">
                <input type="text" id="artid0" style="width: 80%;margin-left: 10%;margin-top: 10px;"/>
                <input type="file" name="imgFile" value="" placeholder="作者名片" style="margin-left: 40%;margin-top:10px;">
                <input type="button" class="butts" onclick="uploadart(0)" value="上传作者名片" style="margin-left: 40%;">

            </div>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
        </form>
        <div class="arteachpart_right">
            <input type="button" class="opbtn" value="添加" onclick="addart()">
            <input type="button"  class="opbtn" value="删除" onclick="delart()">
        </div>
    </div>
    </span>

</div>

<div class="savebtnfour" style="float: left">
    <input type="button" class="saveall" value="全部保存" onclick="saveall()">
</div>
<!-- Banner select Modal -->
<div class="modal fade" id="myModalbanner" tabindex="-1" role="dialog" aria-labelledby="myModalLabelEdit">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabelEdit">选择banner类型</h4>
            </div>
            <div class="modal-body">
                <input type="button" class="butt" value="添加广告banner" onclick="selectgbanner()">
                <input type="button" class="butt" value="添加小说banner" onclick="selectxbanner()">
            </div>
            <div class="modal-footer">
                <button type="button" class="wd1 btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>

<!--banner guanggao Modal -->
<div class="modal fade" id="myModalggAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabelEdit">
    <div class="modal-dialog" role="document" style="width:730px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabelEdit">添加广告banner</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">

                    <label>广告名称：</label>
                    <input type="text" id="ggname"/>
                    <input type="button" class="searchbtn" value="查询" onclick="searchgg()">
                </div>

                <div id="conts">
                    <table id="customers">

                        <tr>
                            <th>ID</th>
                            <th>广告名称</th>
                            <th>链接</th>
                            <th>图片</th>
                            <th>操作</th>
                        </tr>
                    </table>
                </div>
                <div style="margin-top:20px;min-height:40px;">
                    <label>BANNER池：</label>

                    <div id="selectedbanner">


                        {{--<p>--}}
                            {{--<span>id:1</span>--}}
                            {{--<span>gg</span>--}}
                            {{--<span><input type="button" value="上移"><input type="button" value="上移"><input type="button" value="上移"></span>--}}
                        {{--</p><p>--}}
                            {{--<span>id:1</span>--}}
                            {{--<span>gg</span>--}}
                            {{--<span><input type="button" value="上移"><input type="button" value="上移"><input type="button" value="上移"></span>--}}
                        {{--</p>--}}
                    </div>

                </div>

                <div style="margin-bottom: 20px;height: 55px;">
                    <input type="button" style="margin-left: 0;" class="butt" value="完成" onclick="overs()">
                </div>
            </div>
        </div>
    </div>
</div>

<!--add xiaoshuo Modal 选择书籍   热推的一本 -->
<div class="modal fade" id="myModalAddxs" tabindex="-1" role="dialog" aria-labelledby="myModalLabelEdit">
    <div class="modal-dialog" role="document" style="width:730px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabelEdit">添加书籍</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">

                    <label>书ID/书名/作者：</label>
                    <input type="text" id="bookrname"/>
                    <label>种类</label>
                    <a id="addselectline">

                    </a>

                    <input type="button" class="searchbtn" value="查询" onclick="addsearchxs()">
                    {{--<input type="button" class="searchbtn" value="全部添加" onclick="addall()">--}}
                </div>

                <div id="addxsconts">
                    <table id="customers">

                        <tr>
                            <th><input type="checkbox"></th>
                            <th>书ID</th>
                            <th>书名</th>
                            <th>作者</th>
                            <th>种类</th>
                            <th>操作</th>
                        </tr>

                        <tr><td></td><td>还没有数据，请查询</td></tr>

                    </table>
                </div>
                <div style="margin-top:20px;min-height:40px;"><label>小说池：</label>
                    <div id="oneselectedxs">

                    </div>

                    <div style="margin-bottom: 20px;height: 55px;">
                        <input type="button" style="margin-left: 0;" class="butt" value="完成" onclick="onexsovers()">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--add xiaoshuo Modal 选择书籍   热推的多本 -->
<div class="modal fade" id="myModaladdmorehot" tabindex="-1" role="dialog" aria-labelledby="myModalLabelEdit">
    <div class="modal-dialog" role="document" style="width:730px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabelEdit">添加书籍</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">

                    <label>书ID/书名/作者：</label>
                    <input type="text" id="hotbookrname"/>
                    <label>种类</label>
                    <a id="addselectmoreline">

                    </a>

                    <input type="button" class="searchbtn" value="查询" onclick="searchmorehot()">
                    <input type="button" class="searchbtn" value="全部添加" onclick="addall()">
                </div>

                <div id="addmorehotconts">
                    <table id="customers">

                        <tr>
                            <th><input type="checkbox"></th>
                            <th>书ID</th>
                            <th>书名</th>
                            <th>作者</th>
                            <th>种类</th>
                            <th>操作</th>
                        </tr>

                        <tr><td></td><td>还没有数据，请查询</td></tr>

                    </table>
                </div>
                <div style="margin-top:20px;min-height:40px;"><label>小说池：</label>
                    <div id="moreselectedxs">

                    </div>

                    <div style="margin-bottom: 20px;height: 55px;">
                        <input type="button" style="margin-left: 0;" class="butt" value="完成" onclick="morexsovers()">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--add xiaoshuo Modal 选择书籍   每个栏目的多本 -->
<div class="modal fade" id="myModalthridbook" tabindex="-1" role="dialog" aria-labelledby="myModalLabelEdit" overid="">
    <div class="modal-dialog" role="document" style="width:730px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabelEdit">添加书籍</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">

                    <label>书ID/书名/作者：</label>
                    <input type="text" id="thridbookrname"/>
                    <label>种类</label>
                    <a id="addselectthridline">

                    </a>

                    <input type="button" class="searchbtn" value="查询" onclick="thridsearch()">
                    <input type="button" class="searchbtn" value="全部添加" onclick="addall()">
                </div>

                <div id="thridconts">
                    <table id="customers">

                        <tr>
                            <th><input type="checkbox"></th>
                            <th>书ID</th>
                            <th>书名</th>
                            <th>作者</th>
                            <th>种类</th>
                            <th>操作</th>
                        </tr>

                        <tr><td></td><td>还没有数据，请查询</td></tr>

                    </table>
                </div>
                <div style="margin-top:20px;min-height:40px;"><label>小说池：</label>
                    <div id="thridchi">

                    </div>

                    <div style="margin-bottom: 20px;height: 55px;">
                        <input type="button" style="margin-left: 0;" class="butt" value="完成" onclick="thrideachovers()">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!--banner xiaoshuo Modal -->
<div class="modal fade" id="myModalxsAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabelEdit">
    <div class="modal-dialog" role="document" style="width:730px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabelEdit">添加小说banner</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">

                    <label>书ID/书名/作者：</label>
                    <input type="text" id="bookrel"/>
                    <label>种类</label>
                    <a id="selectline">

                    </a>

                    <input type="button" class="searchbtn" value="查询" onclick="searchxs()">
                </div>

                <div id="xsconts">
                    <table id="customers">

                        <tr>
                            <th>书ID</th>
                            <th>书名</th>
                            <th>作者</th>
                            <th>种类</th>
                            <th>操作</th>
                        </tr>

                        <tr><td>还没有数据，请查询</td></tr>

                    </table>
                </div>
                <ul class="pagination" id="xsbannerfy" style="display: none;">
                    {{--<li><a href="#">«</a></li>--}}
                    {{--<li><a href="#">1</a></li>--}}
                    {{--<li><a class="active" href="#">2</a></li>--}}
                    {{--<li><a href="#">3</a></li>--}}
                    {{--<li><a href="#">4</a></li>--}}
                    {{--<li><a href="#">5</a></li>--}}
                    {{--<li><a href="#">6</a></li>--}}
                    {{--<li><a href="#">7</a></li>--}}
                    {{--<li><a href="#">»</a></li>--}}
                </ul>
                <div style="margin-top:20px;min-height:40px;"><label>BANNER池：</label>
                    <div id="selectedxsbanner">

                    </div>

                    <div style="margin-bottom: 20px;height: 55px;">
                        <input type="button" style="margin-left: 0;" class="butt" value="完成" onclick="xsovers()">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reminder" tabindex="-1" role="dialog" aria-labelledby="myModalLabelEdit">
    <div class="modal-dialog" role="document" >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabelEdit">提示</h4>
            </div>
            <div class="modal-body">
                <div id="reminderbody"></div>
            </div>
        </div>
    </div>
</div>

<script>
bid=[];
btitle=[];
typeArr=[];
onebid = [];
onebtitle = [];
ckids=[];
cknames=[];
su=[];
function closereminder() {
    $("#reminder").modal('hide');
    $(".modal-backdrop").hide();
}
function saveall() {


    $.ajax({//先清空表数据
        type: "GET",
        url: "/novel/deldata",
        data: {},
        dataType: "json",
        success: function (data) {
            if(data.code == 1){

                //作者区域
                var artids=[];
                var artimgs=[];
                var ar=0;
                var artindex = $("#artindex").val();
                // if(artindex ==''||artindex==undefined){
                //     alert("作者区域数据值未填写");
                //     return;
                // }

                $("#part_art span").each(function(){
                    var artid=$("#artid"+ar).val();
                    var imgurl=$("#urlget"+ar).attr("imgurl");
                    artids.push(artid);
                    artimgs.push(imgurl);
                    ar++;
                });
                console.log('artids'+artids);
                console.log('artimgs'+artimgs);
                $.ajax({
                    type: "GET",
                    url: "/novel/saveart",
                    data: {artindex:artindex,artids:artids,imgurl:artimgs},
                    dataType: "json",
                    success: function (data) {
                        if(data.code == 1){

                            $("#reminderbody").html("数据保存成功");
                            $("#reminder").modal();
                            setTimeout("closereminder()", 1000)
                        }else{
                            alert("可能失败了，请重试下吧");
                        }

                    }
                })



                //banner部分的数据 begin
                var bannerid=[];
                var bannertitle=[];
                var bannertypeArr=[];
                $("#banner_id_list p").each(function () {
                    bannerid.push($(this).attr("bid"));
                    bannertitle.push($(this).attr("btitle"));
                    bannertypeArr.push($(this).attr("type"));
                })
                console.log("bannerid:"+bannerid);
                console.log("bannertitle:"+bannertitle);
                console.log("bannertypeArr:"+bannertypeArr);
                console.log("bannerid_length:"+bannertitle.length);

                $.ajax({
                    type: "GET",
                    url: "/novel/savebanner",
                    data: {bannerid:bannerid,bannertypeArr:bannertypeArr},
                    dataType: "json",
                    success: function (data) {
                        if(data.code == 1){
                            console.log(data);
                            // alert("banner区域数据保存成功");
                            // su.push(1);
                            $("#reminderbody").html("数据保存成功");
                            $("#reminder").modal();
                            setTimeout("closereminder()", 1000)
                        }else{
                            alert("可能失败了，请重试下吧");
                        }

                    }
                })


                //banner部分的数据 end

                //热推一本的数据 begin
                var onebookid=[];
                var onebooktitle=[];
                $("#onebookarea p").each(function () {
                    onebookid.push($(this).attr("bid"));
                    onebooktitle.push($(this).attr("btitle"));
                })
                var onebookhead = $("#onebookhead").val();
                console.log(onebookid);
                console.log(onebooktitle);
                $.ajax({
                    type: "GET",
                    url: "/novel/saveone",
                    data: {onebookid:onebookid,onebookhead:onebookhead},
                    dataType: "json",
                    success: function (data) {
                        if(data.code == 1){
                            $("#reminderbody").html("数据保存成功");
                            $("#reminder").modal();
                            setTimeout("closereminder()", 1000)
                        }else{
                            alert("可能失败了，请重试下吧");
                        }

                    }
                });

                //热推一本的数据 end

                //热推多本数据 begin
                var morebookid=[];
                var morebooktitle=[];
                $("#hotmorearea p").each(function () {
                    morebookid.push($(this).attr("bid"));
                    morebooktitle.push($(this).attr("btitle"));
                })
                console.log(morebookid);
                console.log(morebooktitle);

                $.ajax({
                    type: "GET",
                    url: "/novel/savemore",
                    data: {morebookid:morebookid},
                    dataType: "json",
                    success: function (data) {
                        if(data.code == 1){
                            console.log(data);
                            // alert("热推区域数据保存成功");
                            $("#reminderbody").html("数据保存成功");
                            $("#reminder").modal();
                            setTimeout("closereminder()", 1000)
                        }else{
                            alert("可能失败了，请重试下吧");
                        }

                    }
                });



                //热推多本数据 end


                //多块区域的数据 begin
                var i=0;
                var m=1;
                $("#parthree i").each(function(){
                    i++
                });
                var thridareaid=[];

                if(i>0){
                    for(var j=0;j<i;j++){//中间栏数据入库
                        $("#thridarea"+j+" p").each(function(){
                            thridareaid.push($(this).attr("bid"));
                        });
                        var title = $("#booktitle"+j).val();
                        var paixu = $("#paixu"+j).val();
                        var palie = $('#indextype'+j+' option:selected').val();

                        $.ajax({
                            type: "GET",
                            url: "/novel/savedata",
                            data: {thridareaid:thridareaid,title:title,paixu:paixu,palie:palie},
                            dataType: "json",
                            success: function (data) {
                                if(data.code == 1){
                                    thridareaid.length=0;
                                    // console.log(data);
                                    // alert("多书区域数据保存成功");
                                    $("#reminderbody").html("数据保存成功");
                                    $("#reminder").modal();
                                    setTimeout("closereminder()", 1000)
                                }else{
                                    alert("可能失败了，请重试下吧");
                                }

                            }
                        })
                        thridareaid.length=0;
                    }
                }

                //多块区域的数据 end

            }else{
                alert("可能失败了，请重试下吧");
            }

        }
    })
}
function uploadart(index) {
    var form = new FormData(document.getElementById("artmp"+index));
    $.ajax({
        url:"/novel/uploadart",
        type:"post",
        data:form,
        dataType:"json",
        processData:false,
        contentType:false,
        success:function(data){

            if(data.code == 1){
                alert("上传成功");
                $("#urlget"+index).attr("imgurl",data.url);
            }else{
                alert("上传失败，请重试");
            }
        },
    });
}
function addart() {
    var j=0;

    $("#part_art span").each(function(){
        j++
    });



    var _html = "";
    _html += '<span><div class="arteachpart"><form id="artmp'+j+'" class="" method="post" action="" enctype="multipart/form-data"  onsubmit="return false">';
    _html += '<div class="arteachpart_left" id="urlget'+j+'" ><input type="text" id="artid'+j+'" style="width: 80%;margin-left: 10%;margin-top: 10px;"/>';
    _html += '<input type="file" name="imgFile" value="" placeholder="作者名片" style="margin-left: 40%;margin-top:10px;"><input onclick="uploadart('+j+')" type="button"class="butts" value="上传作者名片" style="margin-left: 40%;"></div>';
    _html += ' <input type="hidden" name="_token" value="{{ csrf_token() }}"></form><div class="arteachpart_right"> <input type="button" class="opbtn" value="添加" onclick="addart()"><input type="button"  class="opbtn" value="删除" onclick="delart(this)">';
    _html += ' </div> </div></span>';
    // alert(j);
    $("#part_art").append(_html);
}
function delart(o) {
    var i=0;
    $("#part_art span").each(function(){
        i++
    });
    // alert(i);
    if(i < 2){
        alert("至少保留一块区域");return;
    }else{
        $(o).parent().parent().parent().remove();
    }
}
function thrideachovers() {
    var overid = $("#myModalthridbook").attr('overid');
    var ckids=[];
    var cknames=[];
    ckids.length =0;
    cknames.length =0;
    // $("#thridarea"+overid+"").html("");
    $("#thridarea"+overid).html("");
    $("#thridchi p").each(function(){
        ckids.push($(this).attr("bid"));
        cknames.push($(this).attr("btitle"));
    });
    var _line="";
    if(ckids.length > 0){
        for(var i=0;i < ckids.length;i++){
            _line += "<p bid="+ckids[i]+" btitle="+cknames[i]+"><span>id:"+ckids[i]+"</span>";
            _line += "   <span style='display: inline-block;width: 100px;'>"+cknames[i]+"</span>";
            _line += "<span><input type=\"button\" value=\"上移\" onclick='doing(this,\"up\","+ckids[i]+",\""+cknames[i]+"\")'><input type=\"button\" value=\"下移\" onclick='doing(this,\"down\","+ckids[i]+",\""+cknames[i]+"\")'><input type=\"button\" value=\"删除\" onclick='doing(this,\"remove\","+ckids[i]+",\""+cknames[i]+"\")'></span>";
            _line += "</p>";
        }

        // $("#thridarea"+overid+"").html("");
        // $("#thridarea"+overid+"").html(_line);
        // $("#thridarea0").html(_line);
        $("#thridarea"+overid).html("");
        $("#thridarea"+overid).html(_line);
    }
    $("#myModalthridbook").modal('hide');
}
function addbookarea() {
    var i=0;
    $("#parthree i").each(function(){
        i++
    });
    var _part = "";
    _part += '<div class="eachpart"><i  style="display: none"></i ><div id="morebook"><div style=""><input id="booktitle'+i+'" type="text" placeholder="请输入标题">';
    _part += '<input type="text" id="paixu'+i+'" placeholder="排序值" style="width: 40px;"><select id="indextype'+i+'"><option value="0">横向排列</option><option value="1">纵向排列</option></select></div>';
    _part += '<div><input type="button" class="butt" value="添加书籍" onclick="parthreeaddbook('+i+');"><div id="thridarea'+i+'">书名区域</div></div></div>';
    _part += '<div style="margin-bottom: 50px;"><input type="button" class="butts" value="增加一块" onclick="addbookarea()">';
    _part += '<input type="button" class="butts" value="删除该块" onclick="delbookarea(this)">';
    _part += '</div></div>';
    $("#parthree").append(_part);
}
function delbookarea(o) {
    var i=0;
    $("#parthree i").each(function(){
        i++
    });
    if(i < 2){
        alert("至少保留一块区域");return;
    }else{
        $(o).parent().parent().remove();
    }
}
function parthreeaddbook(n) {
    $("#thridchi").html("");
    $("#myModalthridbook").modal();
    $("#myModalthridbook").attr('overid',n);
    $.ajax({
        type: "GET",
        url: "/novel/catelist",
        data: {},
        dataType: "json",
        success: function (data) {

            var categorys = data.categorys;

            var _str = "";
            _str += "<select id='catesel"+n+"'>";
            _str += "<option id=''>请选择</option>";
            for(var i=0;i<categorys.length;i++){

                _str += "<option id="+categorys[i].cateid+">"+categorys[i].name+"</option>";


            }
            _str += "</select>";
            $("#addselectthridline").html(_str);

        }
    });
}
function thridsearch() {
    var n = $("#myModalthridbook").attr('overid');
    var hotbookrname = $("#thridbookrname").val();
    var optionval = $('#catesel'+n+' option:selected').attr('id');
    if(optionval ==0 && bookrel==''){
        alert("请输入查询条件");return;
    }
    var _html="";
    $.ajax({
        type: "GET",
        url: "/novel/novelsel",
        data: {bookrel:hotbookrname,optionval:optionval},
        dataType: "json",
        success: function (data) {
            if(data.code == 1){
                _html += "<table id='customers'>";
                _html += "<tr>" +
                    "                            <th><input onclick='tcheckattr()' type='checkbox' name='tbkd'></th><th>书ID</th>" +
                    "                            <th>书名</th>" +
                    "                            <th>作者</th>" +
                    "                            <th>种类</th>" +
                    "                            <th>操作</th>" +
                    "                        </tr>";
                for(var i = 0;i < data.novels.length;i++){

                    _html += "<tr><td><input tntitle=\""+data.novels[i].name+"\" tnid=\""+data.novels[i].novelid+"\" type='checkbox' name='tbkd' onclick='teachcheck(this)'></td><td>"+data.novels[i].novelid+"</td><td>"+data.novels[i].name+"</td><td>"+data.novels[i].author+"</td>";
                    _html += "<td>"+data.novels[i].catename+"</td>"
                    _html += "<td><a style='cursor: pointer;' onclick='tthridadd("+data.novels[i].novelid+",\""+data.novels[i].name+"\")'>添加</a></td></tr>";
                }
            }else{

                _html = "可能失败了，请重试下吧";
            }



            $("#thridconts").html(_html);
        }
    })
}
function tcheckattr() {
    var tckids=[];
    var tcknames=[];
    tckids.length =0;
    tcknames.length = 0;

    $("input[name='tbkd']").each(function(){
        if (this.checked) {
            var tckid = $(this).attr("tnid");
            var tckname = $(this).attr("tntitle");

            if(tckid != undefined && tckid!=''){
                tckids.push(tckid);
                tcknames.push(tckname);
            }

            $("[name=tbkd]:checkbox").prop("checked", true);

        }else {
            var tckid = $(this).attr("tnid");
            var tckname = $(this).attr("tntitle");
            var index = tckids.indexOf(tckid);
            var index1 = tcknames.indexOf(tckname);


            if (index > -1) {
                tckids.splice(index, 1);
                tcknames.splice(index1, 1);
            }
            $("[name=tbkd]:checkbox").prop("checked", false);
        }
    })
    console.log(tckids);
    var _line = "";$("#thridchi").html("");
    if(tckids.length>0){
        for(var i=0;i<tckids.length;i++){
            _line += "<p bid="+tckids[i]+" btitle="+tcknames[i]+"><span>id:"+tckids[i]+"</span>";
            _line += "   <span style='display: inline-block;width: 100px;'>"+tcknames[i]+"</span>";
            _line += "<span><input type=\"button\" value=\"上移\" onclick='doing(this,\"up\","+tckids[i]+",\""+tcknames[i]+"\")'><input type=\"button\" value=\"下移\" onclick='doing(this,\"down\","+tckids[i]+",\""+tcknames[i]+"\")'><input type=\"button\" value=\"删除\" onclick='doing(this,\"remove\","+tckids[i]+",\""+tcknames[i]+"\")'></span>";
            _line += "</p>";
        }
        $("#thridchi").html("");
        $("#thridchi").html(_line);

    }
}
function teachcheck(o){
    var ckids=[];
    var cknames=[];
    ckids.length =0;
    cknames.length = 0;

    $("input[name='tbkd']").each(function(){
        if (this.checked) {
            var ckid = $(this).attr("tnid");
            var ckname = $(this).attr("tntitle");

            if(ckid != undefined && ckname!=undefined){
                ckids.push(ckid);
                cknames.push(ckname);
            }

            // $("[name=bkd]:checkbox").prop("checked", true);

        }else {
            var ckid = $(this).attr("tnid");
            var ckname = $(this).attr("tntitle");
            var index = ckids.indexOf(ckid);
            var index1 = cknames.indexOf(ckname);


            if (index > -1) {
                ckids.splice(index, 1);
                cknames.splice(index1, 1);
            }


            // ckids.pop(ckid);
            // cknames.pop(ckname);
            // alert(ckid);
            // $("[name=bkd]:checkbox").prop("checked", false);
        }
    })
    console.log(ckids);
    var _line = "";$("#thridchi").html("");
    if(ckids.length>0){
        for(var i=0;i<ckids.length;i++){
            _line += "<p bid="+ckids[i]+" btitle="+cknames[i]+"><span>id:"+ckids[i]+"</span>";
            _line += "   <span style='display: inline-block;width: 100px;'>"+cknames[i]+"</span>";
            _line += "<span><input type=\"button\" value=\"上移\" onclick='doing(this,\"up\","+ckids[i]+",\""+cknames[i]+"\")'><input type=\"button\" value=\"下移\" onclick='doing(this,\"down\","+ckids[i]+",\""+cknames[i]+"\")'><input type=\"button\" value=\"删除\" onclick='doing(this,\"remove\","+ckids[i]+",\""+cknames[i]+"\")'></span>";
            _line += "</p>";
        }

        $("#thridchi").html(_line);

    }
}
function tthridadd(id,title) {
    var _line = "";
    _line += "<p bid="+id+" btitle="+title+"><span>id:"+id+"</span>";
    _line += "   <span style='display: inline-block;width: 100px;'>"+title+"</span>";
    _line += "<span><input type=\"button\" value=\"上移\" onclick='doing(this,\"up\","+id+",\""+title+"\")'><input type=\"button\" value=\"下移\" onclick='doing(this,\"down\","+id+",\""+title+"\")'><input type=\"button\" value=\"删除\" onclick='doing(this,\"remove\","+id+",\""+title+"\")'></span>";
    _line += "</p>";
    $("#thridchi").append(_line);
}
function morexsovers(){
    ckids.length =0;
    cknames.length =0;
    console.log(ckids);
    $("#hotmorearea").html("");
    $("#moreselectedxs p").each(function(){
        ckids.push($(this).attr("bid"));
        cknames.push($(this).attr("btitle"));
    });
    var _line="";
    if(ckids.length > 0){
        for(var i=0;i < ckids.length;i++){
            _line += "<p bid="+ckids[i]+" btitle="+cknames[i]+"><span>id:"+ckids[i]+"</span>";
            _line += "   <span style='display: inline-block;width: 100px;'>"+cknames[i]+"</span>";
            _line += "<span><input type=\"button\" value=\"上移\" onclick='doing(this,\"up\","+ckids[i]+",\""+cknames[i]+"\")'><input type=\"button\" value=\"下移\" onclick='doing(this,\"down\","+ckids[i]+",\""+cknames[i]+"\")'><input type=\"button\" value=\"删除\" onclick='doing(this,\"remove\","+ckids[i]+",\""+cknames[i]+"\")'></span>";
            _line += "</p>";
        }

        $("#hotmorearea").html("");
        $("#hotmorearea").html(_line);
    }
    $("#myModaladdmorehot").modal('hide');

}
function addall(){
    ckids.length =0;
    cknames.length = 0;
    $("input[name='bkd']").each(function(){
        if (this.checked) {
            var ckid = $(this).attr("nid");
            var ckname = $(this).attr("ntitle");

            if(ckid != undefined && ckid!=''){
                ckids.push(ckid);
                cknames.push(ckname);
            }

            // $("[name=bkd]:checkbox").prop("checked", true);

        }else {
            var ckid = $(this).attr("nid");
            var ckname = $(this).attr("ntitle");
            var index = ckids.indexOf(ckid);
            var index1 = cknames.indexOf(ckname);


            if (index > -1) {
                ckids.splice(index, 1);
                cknames.splice(index1, 1);
            }
            // $("[name=bkd]:checkbox").prop("checked", false);
        }
    })



    console.log(ckids);

    if(ckids.length == 10){

        var text = $('#catesel option:selected').val();
        var cateid = $('#catesel option:selected').attr('id');
        // alert(text);
        var _line = "";
        _line += "<p bid="+cateid+" btitle="+text+"><span>cateid:"+cateid+"</span>";
        _line += "   <span style='display: inline-block;width: 100px;'>"+text+"</span>";
        _line += "<span><input type=\"button\" value=\"上移\" onclick='doing(this,\"up\","+cateid+",\""+text+"\")'><input type=\"button\" value=\"下移\" onclick='doing(this,\"down\","+cateid+",\""+text+"\")'><input type=\"button\" value=\"删除\" onclick='doing(this,\"remove\","+cateid+",\""+text+"\")'></span>";
        _line += "</p>";
        $("#moreselectedxs").html(_line);
    }else{
        // alert(1);
        if(ckids.length>0){
            for(var i=0;i<ckids.length;i++){
                _line += "<p bid="+ckids[i]+" btitle="+cknames[i]+"><span>id:"+ckids[i]+"</span>";
                _line += "   <span style='display: inline-block;width: 100px;'>"+cknames[i]+"</span>";
                _line += "<span><input type=\"button\" value=\"上移\" onclick='doing(this,\"up\","+ckids[i]+",\""+cknames[i]+"\")'><input type=\"button\" value=\"下移\" onclick='doing(this,\"down\","+ckids[i]+",\""+cknames[i]+"\")'><input type=\"button\" value=\"删除\" onclick='doing(this,\"remove\","+ckids[i]+",\""+cknames[i]+"\")'></span>";
                _line += "</p>";
            }

            $("#moreselectedxs").html(_line);
        }

    }

}
function checkattr() {
    ckids.length =0;
    cknames.length = 0;

    $("input[name='bkd']").each(function(){
        if (this.checked) {
            var ckid = $(this).attr("nid");
            var ckname = $(this).attr("ntitle");

            if(ckid != undefined && ckid!=''){
                ckids.push(ckid);
                cknames.push(ckname);
            }

            $("[name=bkd]:checkbox").prop("checked", true);

        }else {
            var ckid = $(this).attr("nid");
            var ckname = $(this).attr("ntitle");
            var index = ckids.indexOf(ckid);
            var index1 = cknames.indexOf(ckname);


            if (index > -1) {
                ckids.splice(index, 1);
                cknames.splice(index1, 1);
            }
            $("[name=bkd]:checkbox").prop("checked", false);
        }
    })
    console.log(ckids);
    var _line = "";$("#moreselectedxs").html("");
    if(ckids.length>0){
        for(var i=0;i<ckids.length;i++){
            _line += "<p bid="+ckids[i]+" btitle="+cknames[i]+"><span>id:"+ckids[i]+"</span>";
            _line += "   <span style='display: inline-block;width: 100px;'>"+cknames[i]+"</span>";
            _line += "<span><input type=\"button\" value=\"上移\" onclick='doing(this,\"up\","+ckids[i]+",\""+cknames[i]+"\")'><input type=\"button\" value=\"下移\" onclick='doing(this,\"down\","+ckids[i]+",\""+cknames[i]+"\")'><input type=\"button\" value=\"删除\" onclick='doing(this,\"remove\","+ckids[i]+",\""+cknames[i]+"\")'></span>";
            _line += "</p>";
        }
        $("#moreselectedxs").html("");
        $("#moreselectedxs").html(_line);

    }

}
function eachcheck(o) {
    ckids.length =0;
    cknames.length = 0;

    $("input[name='bkd']").each(function(){
        if (this.checked) {
            var ckid = $(this).attr("nid");
            var ckname = $(this).attr("ntitle");

            if(ckid != undefined && ckname!=undefined){
                ckids.push(ckid);
                cknames.push(ckname);
            }

            // $("[name=bkd]:checkbox").prop("checked", true);

        }else {
            var ckid = $(this).attr("nid");
            var ckname = $(this).attr("ntitle");
            var index = ckids.indexOf(ckid);
            var index1 = cknames.indexOf(ckname);


            if (index > -1) {
                ckids.splice(index, 1);
                cknames.splice(index1, 1);
            }


            // ckids.pop(ckid);
            // cknames.pop(ckname);
            // alert(ckid);
            // $("[name=bkd]:checkbox").prop("checked", false);
        }
    })
    console.log(ckids);
    var _line = "";$("#moreselectedxs").html("");
    if(ckids.length>0){
        for(var i=0;i<ckids.length;i++){
            _line += "<p bid="+ckids[i]+" btitle="+cknames[i]+"><span>id:"+ckids[i]+"</span>";
            _line += "   <span style='display: inline-block;width: 100px;'>"+cknames[i]+"</span>";
            _line += "<span><input type=\"button\" value=\"上移\" onclick='doing(this,\"up\","+ckids[i]+",\""+cknames[i]+"\")'><input type=\"button\" value=\"下移\" onclick='doing(this,\"down\","+ckids[i]+",\""+cknames[i]+"\")'><input type=\"button\" value=\"删除\" onclick='doing(this,\"remove\","+ckids[i]+",\""+cknames[i]+"\")'></span>";
            _line += "</p>";
        }

        $("#moreselectedxs").html(_line);

    }
}
function searchmorehot() {
    var hotbookrname = $("#hotbookrname").val();
    var optionval = $('#cateselh option:selected').attr('id');
    if(optionval ==0 && bookrel==''){
        alert("请输入查询条件");return;
    }
    var _html="";
    $.ajax({
        type: "GET",
        url: "/novel/novelsel",
        data: {bookrel:hotbookrname,optionval:optionval},
        dataType: "json",
        success: function (data) {
            if(data.code == 1){
                _html += "<table id='customers'>";
                _html += "<tr>" +
                    "                            <th><input onclick='checkattr()' type='checkbox' name='bkd'></th><th>书ID</th>" +
                    "                            <th>书名</th>" +
                    "                            <th>作者</th>" +
                    "                            <th>种类</th>" +
                    "                            <th>操作</th>" +
                    "                        </tr>";
                for(var i = 0;i < data.novels.length;i++){

                    _html += "<tr><td><input ntitle=\""+data.novels[i].name+"\" nid=\""+data.novels[i].novelid+"\" type='checkbox' name='bkd' onclick='eachcheck(this)'></td><td>"+data.novels[i].novelid+"</td><td>"+data.novels[i].name+"</td><td>"+data.novels[i].author+"</td>";
                    _html += "<td>"+data.novels[i].catename+"</td>"
                    _html += "<td><a style='cursor: pointer;' onclick='hotmoreadd("+data.novels[i].novelid+",\""+data.novels[i].name+"\")'>添加</a></td></tr>";
                }
            }else{

                _html = "可能失败了，请重试下吧";
            }



            $("#addmorehotconts").html(_html);
        }
    })
}
function hotmoreadd(id,title) {
    for(var i=0;i<ckids.length;i++){
        if(ckids[i] == id){

            alert("已存在于小说池中，无需重复加入");
            return;
        }
    }


    var _line = "";
    _line += "<p bid="+id+" btitle="+title+"><span>id:"+id+"</span>";
    _line += "   <span style='display: inline-block;width: 100px;'>"+title+"</span>";
    // _line += "<span><input type=\"button\" value=\"删除\" onclick='doing(this,\"remove\","+id+",\""+title+"\")'></span>";
    _line += "<span><input type=\"button\" value=\"上移\" onclick='doing(this,\"up\","+id+",\""+title+"\")'><input type=\"button\" value=\"下移\" onclick='doing(this,\"down\","+id+",\""+title+"\")'><input type=\"button\" value=\"删除\" onclick='doing(this,\"remove\","+id+",\""+title+"\")'></span>";
    _line += "</p>";
    // $("#selectedbanner").append(_line);
    $("#moreselectedxs").append(_line);

}
function addhotmore() {
    $('#myModaladdmorehot').modal();
    $.ajax({
        type: "GET",
        url: "/novel/catelist",
        data: {},
        dataType: "json",
        success: function (data) {

            var categorys = data.categorys;

            var _str = "";
            _str += "<select id='cateselh'>";
            _str += "<option id=''>请选择</option>";
            for(var i=0;i<categorys.length;i++){

                _str += "<option id="+categorys[i].cateid+">"+categorys[i].name+"</option>";


            }
            _str += "</select>";
            $("#addselectmoreline").html(_str);

        }
    });
}
function onexsovers() {
    $("#onebookarea").html("");
    onebid.length =0;
    $("#oneselectedxs p").each(function(){
        onebid.push($(this).attr("bid"));
        onebtitle.push($(this).attr("btitle"));
    });
    if(onebid.length != 1){
        alert("该操作只能保持一本书籍，请重新选择");return;
    }
    console.log(onebid);
    var _line = "";
    _line += "<p bid="+onebid[0]+" btitle="+onebtitle[0]+"><span>id:"+onebid[0]+"</span>";
    _line += "   <span style='display: inline-block;width: 100px;'>"+onebtitle[0]+"</span>";
    _line += "<span><input type=\"button\" value=\"删除\" onclick='doing(this,\"remove\","+onebid[0]+",\""+onebtitle[0]+"\")'></span>";
    _line += "</p>";
    $("#onebookarea").html(_line);
    $("#myModalAddxs").modal('hide');
}
function addbookone(){
    $('#myModalAddxs').modal();
    $.ajax({
        type: "GET",
        url: "/novel/catelist",
        data: {},
        dataType: "json",
        success: function (data) {

            var categorys = data.categorys;

            var _str = "";
            _str += "<select id='catesel'>";
            _str += "<option id=''>请选择</option>";
            for(var i=0;i<categorys.length;i++){

                _str += "<option id="+categorys[i].cateid+">"+categorys[i].name+"</option>";


            }
            _str += "</select>";
            $("#addselectline").html(_str);

        }
    });
}
function searchgg() {
    var ggname = $("#ggname").val();
    if(ggname == ''){
        alert("请输入广告名称");return;
    }
    var _html="";
    $.ajax({
        type: "GET",
        url: "/novel/searchgg",
        data: {ggname:ggname},
        dataType: "json",
        success: function (data) {
            if(data.code == 1){
                _html += "<table id='customers'>";
                _html += "<tr>" +
                    "                            <th>ID</th>" +
                    "                            <th>广告名称</th>" +
                    "                            <th>链接</th>" +
                    "                            <th>图片</th>" +
                    "                            <th>操作</th>" +
                    "                        </tr>";
                for(var i = 0;i < data.adverts.length;i++){

                    _html += "<tr><td>"+data.adverts[i].advertid+"</td><td>"+data.adverts[i].title+"</td><td>"+data.adverts[i].url+"</td><td>"+data.adverts[i].img+"</td>";
                    _html += "<td><a style='cursor: pointer;' onclick='addline("+data.adverts[i].advertid+",\""+data.adverts[i].title+"\",1)'>添加</a></td></tr>";
                }
            }else{

                _html = "可能失败了，请重试下吧";
            }



            $("#conts").html(_html);

        }
    })
}
function selectbanner() {
    $('#myModalbanner').modal();
}
function selectgbanner() {
    $("#selectedbanner").html("");
    $('#myModalggAdd').modal();
    $("#conts").html('加载中，请稍后。。。');
    var _html="";
    $.ajax({
        type: "GET",
        url: "/novel/gg",
        data: {},
        dataType: "json",
        success: function(data){
            if(data.code == 1){
                _html += "<table id='customers'>";
                _html += "<tr>" +
                    "                            <th>ID</th>" +
                    "                            <th>广告名称</th>" +
                    "                            <th>链接</th>" +
                    "                            <th>图片</th>" +
                    "                            <th>操作</th>" +
                    "                        </tr>";
                for(var i = 0;i < data.adverts.length;i++){

                    _html += "<tr><td>"+data.adverts[i].advertid+"</td><td>"+data.adverts[i].title+"</td><td>"+data.adverts[i].url+"</td><td>"+data.adverts[i].img+"</td>";
                    _html += "<td><a style='cursor: pointer;' onclick='addline("+data.adverts[i].advertid+",\""+data.adverts[i].title+"\",1)'>添加</a></td></tr>";
                }
            }else{

                _html = "可能失败了，请重试下吧";
            }



            $("#conts").html(_html);


        }

    });
    if(bid.length > 0){
        var _line="";
        for(var i=0;i < bid.length;i++){
            _line += "<p type="+typeArr[i]+" bid="+bid[i]+" btitle="+btitle[i]+"><span>id:"+bid[i]+"</span>";
            _line += "   <span style='display: inline-block;width: 100px;'>"+btitle[i]+"</span>";
            _line += "<span><input type=\"button\" value=\"上移\" onclick='doing(this,\"up\","+bid[i]+",\""+btitle[i]+"\","+typeArr[i]+")'><input type=\"button\" value=\"下移\" onclick='doing(this,\"down\","+bid[i]+",\""+btitle[i]+"\","+typeArr[i]+")'><input type=\"button\" value=\"删除\" onclick='doing(this,\"remove\","+bid[i]+",\""+btitle[i]+"\","+typeArr[i]+")'></span>";
            _line += "</p>";
        }

        $("#selectedbanner").append(_line);
    }


}
function selectxbanner() {
    $("#selectedxsbanner").html("");
    $.ajax({
        type: "GET",
        url: "/novel/catelist",
        data: {},
        dataType: "json",
        success: function (data) {

            var categorys = data.categorys;

            var _str = "";
            _str += "<select id='catesel'>";
            _str += "<option id=''>请选择</option>";
            for(var i=0;i<categorys.length;i++){

                _str += "<option id="+categorys[i].cateid+">"+categorys[i].name+"</option>";


            }
            _str += "</select>";
            $("#selectline").html(_str);

        }
    });
    if(bid.length > 0){
        var _line="";
        for(var i=0;i < bid.length;i++){
            _line += "<p type="+typeArr[i]+" bid="+bid[i]+" btitle="+btitle[i]+"><span>id:"+bid[i]+"</span>";
            _line += "   <span style='display: inline-block;width: 100px;'>"+btitle[i]+"</span>";
            _line += "<span><input type=\"button\" value=\"上移\" onclick='doing(this,\"up\","+bid[i]+",\""+btitle[i]+"\","+typeArr[i]+")'><input type=\"button\" value=\"下移\" onclick='doing(this,\"down\","+bid[i]+",\""+btitle[i]+"\","+typeArr[i]+")'><input type=\"button\" value=\"删除\" onclick='doing(this,\"remove\","+bid[i]+",\""+btitle[i]+"\","+typeArr[i]+")'></span>";
            _line += "</p>";
        }

        $("#selectedxsbanner").append(_line);
    }


    $('#myModalxsAdd').modal();

}
function addsearchxs() {
    var bookrname = $("#bookrname").val();
    var optionval = $('#catesel option:selected').attr('id');
    if(optionval ==0 && bookrel==''){
        alert("请输入查询条件");return;
    }
    var _html="";
    $.ajax({
        type: "GET",
        url: "/novel/novelsel",
        data: {bookrel:bookrname,optionval:optionval},
        dataType: "json",
        success: function (data) {
            if(data.code == 1){
                _html += "<table id='customers'>";
                _html += "<tr>" +
                    "                            <th><input type='checkbox'></th><th>书ID</th>" +
                    "                            <th>书名</th>" +
                    "                            <th>作者</th>" +
                    "                            <th>种类</th>" +
                    "                            <th>操作</th>" +
                    "                        </tr>";
                for(var i = 0;i < data.novels.length;i++){

                    _html += "<tr><td><input type='checkbox'></td><td>"+data.novels[i].novelid+"</td><td>"+data.novels[i].name+"</td><td>"+data.novels[i].author+"</td>";
                    _html += "<td>"+data.novels[i].catename+"</td>"
                    _html += "<td><a style='cursor: pointer;' onclick='onexsaddline("+data.novels[i].novelid+",\""+data.novels[i].name+"\")'>添加</a></td></tr>";
                }
            }else{

                _html = "可能失败了，请重试下吧";
            }



            $("#addxsconts").html(_html);
        }
    })

}
function onexsaddline(id,title) {
    var _line = "";
    _line += "<p bid="+id+" btitle="+title+"><span>id:"+id+"</span>";
    _line += "   <span style='display: inline-block;width: 100px;'>"+title+"</span>";
    _line += "<span><input type=\"button\" value=\"删除\" onclick='doing(this,\"remove\","+id+",\""+title+"\")'></span>";
    // _line += "<span><input type=\"button\" value=\"上移\" onclick='doing(this,\"up\","+id+",\""+title+"\")'><input type=\"button\" value=\"下移\" onclick='doing(this,\"down\","+id+",\""+title+"\")'><input type=\"button\" value=\"删除\" onclick='doing(this,\"remove\","+id+",\""+title+"\")'></span>";
    _line += "</p>";
    // $("#selectedbanner").append(_line);
    $("#oneselectedxs").append(_line);
}
function searchxs() {
    var bookrel = $("#bookrel").val();
    var optionval = $('#catesel option:selected').attr('id');
    if(optionval ==0 && bookrel==''){
        alert("请输入查询条件");return;
    }
    var _html="";
    $.ajax({
        type: "GET",
        url: "/novel/novelsel",
        data: {bookrel:bookrel,optionval:optionval},
        dataType: "json",
        success: function (data) {
            if(data.code == 1){
                _html += "<table id='customers'>";
                _html += "<tr>" +
                    "                            <th>书ID</th>" +
                    "                            <th>书名</th>" +
                    "                            <th>作者</th>" +
                    "                            <th>种类</th>" +
                    "                            <th>操作</th>" +
                    "                        </tr>";
                for(var i = 0;i < data.novels.length;i++){

                    _html += "<tr><td>"+data.novels[i].novelid+"</td><td>"+data.novels[i].name+"</td><td>"+data.novels[i].author+"</td>";
                    _html += "<td>"+data.novels[i].catename+"</td>"
                    _html += "<td><a style='cursor: pointer;' onclick='xsaddline("+data.novels[i].novelid+",\""+data.novels[i].name+"\",2)'>添加</a></td></tr>";
                }



                // var novelnum = data.novelnum;
                // var novelnum = 10;
                // var now = data.page;
                // $("#xsbannerfy").show();
                // var str = "";
                // if(novelnum>10){
                //     // str += '<li><a href="#" onclick="novelselpage(0)">«</a></li>';
                //     // for(var q=0;q<3;q++){
                //     //     var j=q+1;
                //     //     if(now==j){
                //     //         str += '<li><a onclick="novelselpage('+j+')" p='+j+' class="active">'+j+'</a></li>';
                //     //     }else{
                //     //         str += '<li><a onclick="novelselpage('+j+')"  p='+j+'>'+j+'</a></li>';
                //     //     }
                //     // }
                //     // str += '<li><a >...</a></li>';
                //
                //     if(now>5){
                //
                //     }
                // }else{
                //
                //     str += '<li><a href="#" onclick="novelselpage(0)">«</a></li>';
                //     for(var q=0;q<novelnum;q++){
                //         var j=q+1;
                //         if(now==j){
                //             str += '<li><a onclick="novelselpage('+j+')" p='+j+' class="active">'+j+'</a></li>';
                //         }else{
                //             str += '<li><a onclick="novelselpage('+j+')"  p='+j+'>'+j+'</a></li>';
                //         }
                //
                //     }
                //     str += '<li><a onclick="novelselpage(-1)" href="#">»</a></li>';
                // }
                // $("#xsbannerfy").html(str);
            }else{

                _html = "可能失败了，请重试下吧";
            }



            $("#xsconts").html(_html);
        }
    })

}
function novelselpage(o) {
    var p='';
    if(o==0){
        $("#xsbannerfy a").each(function () {
            if($(this).attr('class') == 'active'){
                p=$(this).attr('p')-1;
            }
        })
    }else if(o==-1){
        $("#xsbannerfy a").each(function () {
            if($(this).attr('class') == 'active'){
                p=parseInt($(this).attr('p'))+1;
            }
        })
    }else{
        p = o;
    }
    alert(p);
}
function addline(id,title,type) {
    var _line = "";
    _line += "<p type="+type+" bid="+id+" btitle="+title+"><span>id:"+id+"</span>";
    _line += "   <span style='display: inline-block;width: 100px;'>"+title+"</span>";
    _line += "<span><input type=\"button\" value=\"上移\" onclick='doing(this,\"up\","+id+",\""+title+"\","+type+")'><input type=\"button\" value=\"下移\" onclick='doing(this,\"down\","+id+",\""+title+"\","+type+")'><input type=\"button\" value=\"删除\" onclick='doing(this,\"remove\","+id+",\""+title+"\","+type+")'></span>";
    _line += "</p>";
    $("#selectedbanner").append(_line);
    // $("#selectedxsbanner").append(_line);
// alert(1);
}
function xsaddline(id,title,type) {
    var _line = "";
    _line += "<p type="+type+" bid="+id+" btitle="+title+"><span>id:"+id+"</span>";
    _line += "   <span style='display: inline-block;width: 100px;'>"+title+"</span>";
    _line += "<span><input type=\"button\" value=\"上移\" onclick='doing(this,\"up\","+id+",\""+title+"\","+type+")'><input type=\"button\" value=\"下移\" onclick='doing(this,\"down\","+id+",\""+title+"\","+type+")'><input type=\"button\" value=\"删除\" onclick='doing(this,\"remove\","+id+",\""+title+"\","+type+")'></span>";
    _line += "</p>";
    // $("#selectedbanner").append(_line);
    $("#selectedxsbanner").append(_line);
// alert(1);
}
function doing(o,act,id,title,type) {
    bid.length=0;
    btitle.length=0;
    typeArr.length=0;
    var _line = "";
    _line += "<p type="+type+" bid="+id+" btitle="+title+"><span>id:"+id+"</span>";
    _line += "   <span style='display: inline-block;width: 100px;'>"+title+"</span>";
    _line += "<span><input type=\"button\" value=\"上移\" onclick='doing(this,\"up\","+id+",\""+title+"\","+type+")'><input type=\"button\" value=\"下移\" onclick='doing(this,\"down\","+id+",\""+title+"\","+type+")'><input type=\"button\" value=\"删除\" onclick='doing(this,\"remove\","+id+",\""+title+"\","+type+")'></span>";
    _line += "</p>";
 if(act == "remove"){

     $(o).parent().parent().remove();
 }else if(act == "up"){

     $(o).parent().parent().prev().before(_line);

     $(o).parent().parent().remove();
 }else {
     $(o).parent().parent().next().after(_line);

     $(o).parent().parent().remove();
 }
}

//广告banner选择 modal的完成按钮
function overs() {
    bid.length=0;
    btitle.length=0;
    typeArr.length=0;
    // var typeArr=[];
    $("#banner_id_list").html("");
    $("#selectedbanner p").each(function(){
        bid.push($(this).attr("bid"));
        btitle.push($(this).attr("btitle"));
        typeArr.push($(this).attr("type"));
    });


    console.log(bid);
    console.log(btitle);
    console.log(typeArr);

    // $('#myModalbanner').hide();
    // $('#myModalggAdd').hide();
    // $('.fade').hide();
    // $('.modal-backdrop').hide();


    var _line="";
    if(bid.length > 0){

        for(var i=0;i < bid.length;i++){
            _line += "<p type="+typeArr[i]+" bid="+bid[i]+" btitle="+btitle[i]+"><span>id:"+bid[i]+"</span>";
            _line += "   <span style='display: inline-block;width: 100px;'>"+btitle[i]+"</span>";
            _line += "<span><input type=\"button\" value=\"上移\" onclick='doing(this,\"up\","+bid[i]+",\""+btitle[i]+"\","+typeArr[i]+")'><input type=\"button\" value=\"下移\" onclick='doing(this,\"down\","+bid[i]+",\""+btitle[i]+"\","+typeArr[i]+")'><input type=\"button\" value=\"删除\" onclick='doing(this,\"remove\","+bid[i]+",\""+btitle[i]+"\","+typeArr[i]+")'></span>";
            _line += "</p>";
        }


        $("#banner_id_list").html(_line);
    }

    $("#myModalggAdd").modal('hide');


}
//小说banner选择 modal的完成按钮
function xsovers() {
    bid.length =0;
    btitle.length =0;
    typeArr.length =0;
    $("#banner_id_list").html("");
    $("#selectedxsbanner p").each(function(){
        bid.push($(this).attr("bid"));
        btitle.push($(this).attr("btitle"));
        typeArr.push($(this).attr("type"));
    });
    console.log(bid);
    console.log(btitle);
    console.log(typeArr);
    var _line="";
    if(bid.length > 0){
        for(var i=0;i < bid.length;i++){
            _line += "<p type="+typeArr[i]+" bid="+bid[i]+" btitle="+btitle[i]+"><span>id:"+bid[i]+"</span>";
            _line += "   <span style='display: inline-block;width: 100px;'>"+btitle[i]+"</span>";
            _line += "<span><input type=\"button\" value=\"上移\" onclick='doing(this,\"up\","+bid[i]+",\""+btitle[i]+"\","+typeArr[i]+")'><input type=\"button\" value=\"下移\" onclick='doing(this,\"down\","+bid[i]+",\""+btitle[i]+"\","+typeArr[i]+")'><input type=\"button\" value=\"删除\" onclick='doing(this,\"remove\","+bid[i]+",\""+btitle[i]+"\","+typeArr[i]+")'></span>";
            _line += "</p>";
        }

        $("#banner_id_list").html("");
        $("#banner_id_list").html(_line);
    }
    $("#myModalxsAdd").modal('hide');
}


$(document).ready(function () {

    $.ajax({//先清空表数据
        type: "GET",
        url: "/novel/alldata",
        data: {},
        dataType: "json",
        success: function (data) {

            var _line="";
            if(data.code==1){

                var banner = data.banner;
                if(banner.length>0){
                    for(var i=0;i<banner.length;i++){
                        _line += "<p type="+banner[i]['extra']+" bid="+banner[i]['content']+" btitle="+banner[i]['title']+"><span>id:"+banner[i]['content']+"</span>";
                        _line += "   <span style='display: inline-block;width: 100px;'>"+banner[i]['title']+"</span>";
                        _line += "<span><input type=\"button\" value=\"上移\" onclick='doing(this,\"up\","+banner[i]['content']+",\""+banner[i]['title']+"\","+banner[i]['extra']+")'><input type=\"button\" value=\"下移\" onclick='doing(this,\"down\","+banner[i]['content']+",\""+banner[i]['title']+"\","+banner[i]['extra']+")'><input type=\"button\" value=\"删除\" onclick='doing(this,\"remove\","+banner[i]['content']+",\""+banner[i]['title']+"\","+banner[i]['extra']+")'></span>";
                        _line += "</p>";
                    }

                    $("#banner_id_list").html(_line);
                }


                var hotone = data.hotone;

                if(hotone !=null ){
                    $("#onebookhead").val(hotone.name);
                    var _lineone = "";
                    _lineone += "<p bid="+hotone.content+" btitle="+hotone.title+"><span>id:"+hotone.content+"</span>";
                    _lineone += "   <span style='display: inline-block;width: 100px;'>"+hotone.title+"</span>";
                    _lineone += "<span><input type=\"button\" value=\"删除\" onclick='doing(this,\"remove\","+hotone.content+",\""+hotone.title+"\")'></span>";
                    _lineone += "</p>";
                    $("#onebookarea").html(_lineone);
                }





                var hotmore=data.hotmore;
                if(hotmore.length>0){
                    var _linemore="";
                    for(var i=0;i < hotmore.length;i++){
                        _linemore += "<p bid="+hotmore[i]['content']+" btitle="+hotmore[i]['title']+"><span>id:"+hotmore[i]['content']+"</span>";
                        _linemore += "   <span style='display: inline-block;width: 100px;'>"+hotmore[i]['title']+"</span>";
                        _linemore += "<span><input type=\"button\" value=\"上移\" onclick='doing(this,\"up\","+hotmore[i]['content']+",\""+hotmore[i]['title']+"\")'><input type=\"button\" value=\"下移\" onclick='doing(this,\"down\","+hotmore[i]['content']+",\""+hotmore[i]['title']+"\")'><input type=\"button\" value=\"删除\" onclick='doing(this,\"remove\","+hotmore[i]['content']+",\""+hotmore[i]['title']+"\")'></span>";
                        _linemore += "</p>";
                    }

                    $("#hotmorearea").html(_linemore);
                }



                var art=data.art;
                if(art.length>0){
                    var _html="";
                    $("#artindex").val(art[0]['index']);

                    $("#span0").remove();
                    for(var j=0;j<art.length;j++){
                        // alert(j);
                        _html += '<span><div class="arteachpart"><form id="artmp'+j+'" class="" method="post" action="" enctype="multipart/form-data"  onsubmit="return false">';
                        _html += '<div class="arteachpart_left" id="urlget'+j+'" imgurl="'+art[j]["extra"]+'"><input type="text" value="'+art[j]["content"]+'" id="artid'+j+'" style="width: 80%;margin-left: 10%;margin-top: 10px;"/>';
                        _html += '<input type="file" name="imgFile" value="'+art[j]["extra"]+'" placeholder="作者名片" style="margin-left: 40%;margin-top:10px;"><input onclick="uploadart('+j+')" type="button"class="butts" value="上传作者名片" style="margin-left: 40%;"></div>';
                        _html += ' <input type="hidden" name="_token" value="{{ csrf_token() }}"></form><div class="arteachpart_right"> <input type="button" class="opbtn" value="添加" onclick="addart()"><input type="button"  class="opbtn" value="删除" onclick="delart(this)">';
                        _html += ' </div> </div></span>';
                    }
                    $("#part_art").append(_html);
                }

                var morepart = data.morepart;
                if(morepart.length >0){
                    var _part = "";
                    $("#epart").remove();
                    for(var i=0;i<morepart.length;i++){

                        _part += '<div class="eachpart"><i  style="display: none"></i ><div id="morebook"><div style=""><input id="booktitle'+i+'" value="'+morepart[i].name+'" type="text" placeholder="请输入标题">';
                        _part += '<input type="text" value="'+morepart[i].index+'" id="paixu'+i+'" placeholder="排序值" style="width: 40px;"><select id="indextype'+i+'">';

                        if(morepart[i].showtype==0){
                            _part+='<option value="0" selected="selected">横向排列</option><option value="1">纵向排列</option></select></div>';
                        }else{
                            _part+='<option value="0">横向排列</option><option value="1" selected="selected">纵向排列</option></select></div>';
                        }

                        _part += '<div><input type="button" class="butt" value="添加书籍" onclick="parthreeaddbook('+i+');">';
                        _part +='<div id="thridarea'+i+'">';
                        for(var t=0;t<morepart[i].include.length;t++){
                            _part += "<p bid="+morepart[i].include[t].content+" btitle="+morepart[i].include[t].title+"><span>id:"+morepart[i].include[t].content+"</span>";
                            _part += "   <span style='display: inline-block;width: 100px;'>"+morepart[i].include[t].title+"</span>";
                            _part += "<span><input type=\"button\" value=\"上移\" onclick='doing(this,\"up\","+morepart[i].include[t].content+",\""+morepart[i].include[t].title+"\")'><input type=\"button\" value=\"下移\" onclick='doing(this,\"down\","+morepart[i].include[t].content+",\""+morepart[i].include[t].title+"\")'><input type=\"button\" value=\"删除\" onclick='doing(this,\"remove\","+morepart[i].include[t].content+",\""+morepart[i].include[t].title+"\")'></span>";
                            _part += "</p>";
                        }
                        _part +='</div></div></div>';
                        _part += '<div style="margin-bottom: 50px;"><input type="button" class="butts" value="增加一块" onclick="addbookarea()">';
                        _part += '<input type="button" class="butts" value="删除该块" onclick="delbookarea(this)">';
                        _part += '</div></div>';

                    }


                    $("#parthree").append(_part);
                }




            }else{

                alert("数据获取失败，请重试");
            }
        }
    })
})


</script>
</body>
</html>
