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
            你当前位置：系统用户管理 - 用户编辑
        </td>
    </tr>
</table>
<!-- 头部菜单 End -->
<script src='{{ asset("/bower_components/AdminLTE/plugins/my97date/WdatePicker.js")}}'></script>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{{$actview}}
            <a type="button" href="{{url('sysuser')}}" class="btn btn-default" style="margin-left: 2%"><<返回</a>
        </h3>

    </div>
    <div class="panel-body">
        <form class="form-horizontal" action="{{url('sysuser/edit-user')}}" method="post" autocomplete="off">
            <input type="hidden" name="id" value="{{$user->id}}">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">用户名</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" value="{{$user->name}}" autocomplete="off" name="name" placeholder="用户名称">
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Email</label>
                <div class="col-sm-4">
                    <input type="email" class="form-control" value="{{$user->email}}" autocomplete="off" name="email" placeholder="Email">
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">密码</label>
                <div class="col-sm-4">
                    <input type="password" class="form-control" value="" autocomplete="off" name="password" placeholder="密码:默认为空不修改">
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">确认密码</label>
                <div class="col-sm-4">
                    <input type="password" class="form-control" value="" autocomplete="off" name="repassword" placeholder="确认密码:默认为空不修改">
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">用户角色</label>
                <div class="col-sm-4">
                    <select name="roleid" class="form-control">
                        <option value="">--未分配--</option>
                        @foreach($rolelist as $rl)
                            <option value="{{$rl->id}}" @if($user->role==$rl->id) selected="selected" @endif>{{$rl->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <?php
            $pro=\App\models\BaseModel::factory('product')->get()->toArray();
            $res=array();
            foreach($pro as $k=>$v){
                $res[$v['id']]=$v;
            }
            ?>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">可查看产品</label>
                <div class="col-sm-4">
                    @foreach($res as $km=>$gh)
                        <input type="checkbox" @if(in_array($km,explode(',',$user->productids))) checked="checked" @endif  name="pid[]" value="{{$km}}">{{$gh['name']}}&nbsp;&nbsp;
                    @endforeach
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-default">更新</button>
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
