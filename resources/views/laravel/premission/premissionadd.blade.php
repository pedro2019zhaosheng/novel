<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head id="Head1">
    <title></title>
    <link href="{{asset('/newsty')}}/layout.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href='{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.css")}}'>
    <script src="{{ asset("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js")}}"></script>
    <script type="text/javascript" src="{{asset('/newsty')}}/common.js"></script>
    <script src="{{ asset("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js")}}"></script>
    <script type="text/javascript" src="{{asset('/newsty')}}/comm.js"></script>
    <style type="text/css">
        .pagination li {
            padding-left: 5px;
        }
    </style>
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
            你当前位置：系统用户管理 - 权限添加
        </td>
    </tr>
</table>
<!-- 头部菜单 End -->
<script src='{{ asset("/bower_components/AdminLTE/plugins/my97date/WdatePicker.js")}}'></script>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{{$actview}}</h3>
    </div>
    <div class="panel-body">
        <form class="form-horizontal" method="post" action="{{url('premission/add-premission')}}">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">权限名称</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="name"  placeholder="权限名称">
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">标签</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="label" placeholder="标签">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">描述</label>
                <div class="col-sm-4">
                    <textarea class="form-control" name="description" rows="3"></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">是否记录系统日志</label>
                <div class="col-sm-4">
                    <label class="radio-inline">
                        <input type="radio" name="status" checked  value="1"> 是
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="status"  value="0"> 否
                    </label>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-default">添加</button>
                </div>
            </div>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
        </form>
    </div>
</div>
<script>
    $(function () {


    });
</script>
</body>
</html>
