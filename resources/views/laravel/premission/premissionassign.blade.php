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
    <script src="{{ asset("/bower_components/confirm/jquery-confirm.min.js")}}"></script>
    <link rel="stylesheet" href="{{ asset("/bower_components/confirm/jquery-confirm.min.css")}}">
    <script type="text/javascript" src="{{asset('/newsty')}}/comm.js"></script>
    <style type="text/css">
        .pagination li {
            padding-left: 5px;
        }
        .jconfirm.jconfirm-white .jconfirm-box .buttons button.btn-default:hover {
            background: #ddd;
        }

        .jconfirm.jconfirm-white .jconfirm-box .buttons button.btn-default {
            box-shadow: none;
            color: #333;
        }
        .jconfirm.jconfirm-white .jconfirm-box .buttons button {
            border: none;
            background-image: none;
            text-transform: uppercase;
            font-size: 14px;
            font-weight: bold;
            text-shadow: none;
            -webkit-transition: background .1s;
            transition: background .1s;
            color: #fff;
        }
        .col-md-offset-4 {
            margin-left: 33.33333333%;
        }

        .col-md-4 {
            width: 33.33333333%;
        }

        .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9 {
            float: left;
        }

        .col-sm-offset-3 {
            margin-left: 25%;
        }

        .col-sm-6 {
            width: 50%;
        }

        .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9 {
            float: left;
        }

        .col-xs-offset-1 {
            margin-left: 8.33333333%;
        }

        .col-xs-10 {
            width: 83.33333333%;
        }

        .col-xs-1, .col-xs-10, .col-xs-11, .col-xs-12, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9 {
            float: left;
        }

        .col-lg-1, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-xs-1, .col-xs-10, .col-xs-11, .col-xs-12, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9 {
            position: relative;
            min-height: 1px;
            padding-right: 32%;
            padding-left: 32%;
        }
        .jconfirm .jconfirm-box div.title-c .title {
            font-size: inherit;
            font-family: inherit;
            display: inline-block;
            vertical-align: middle;
            padding-bottom: 15px;
        }
        .jconfirm .jconfirm-box div.content-pane .content {
            position: absolute;
            top: 0;
            left: 0;
            -webkit-transition: all .2s ease-in;
            transition: all .2s ease-in;
            right: 0;
        }
        .content {
            clip: rect(0px 170px 250px -100px)!important;
        }
        .content {
            min-height: 30px;
            padding: 15px;
            margin-right: auto;
            margin-left: auto;
            padding-left: 15px;
            padding-right: 15px;
        }
        .jconfirm.jconfirm-white .jconfirm-box .buttons {
            float: right;
        }
        .jconfirm .jconfirm-box .buttons {
            padding-bottom: 15px;
        }

        * {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        .jconfirm.jconfirm-white .jconfirm-box {
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
        }

        .jconfirm .jconfirm-box {
            opacity: 1;
            -webkit-transition-property: -webkit-transform, opacity, box-shadow;
            transition-property: transform, opacity, box-shadow;
        }

        .jconfirm .jconfirm-box {
            background: #fff;
            border-radius: 4px;
            position: relative;
            outline: none;
            padding: 15px 15px 0;
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
            你当前位置：系统用户管理 - 权限分配
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
        @foreach($PremissinList as $key=>$value)
            <div class="panel panel-success">
                <div class="control panel-heading">
                    <h3 class="panel-title">控制器：{{$key}}
                        <div class="has-error">
                            <div class="checkbox">
                                <label class="checkbox-inline">
                                    <input type="checkbox" class="checkboxError" value="option1">
                                    全选
                                </label>
                            </div>
                        </div>
                    </h3>
                </div>
                <div class="cksel panel-body">
                    <span>方法：</span>
                    @if(!empty($value))
                        @foreach($value as $fun)
                            <label class="checkbox-inline">
                                <div class="has-warning">
                                    <input type="checkbox"
                                           @if($role->id==1 || in_array($fun['id'],$rolePremission)) checked
                                           @endif class="checkboxWarning" value="{{$fun['id']}}">
                                    {{$fun['label']}}
                                </div>
                            </label>
                        @endforeach
                    @else
                        无
                    @endif
                </div>
            </div>
        @endforeach
        @if($role->id!=1)
            <div class="panel panel-default">
                <div class="panel-footer">
                    <h3 class="panel-title">操作</h3>
                </div>
                <div class="panel-body">
                    <p>
                        <button class="btn btn-primary" id="submit">更新</button>
                        <a class="btn btn-default" href="{{url('role')}}"><
                            <返回
                        </a>
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    $(function () {
        $('#submit').click(function () {
            var chk_value = [];
            $('.checkboxWarning:checked').each(function () {
                chk_value.push($(this).val());
            });
            $.post("{{url('premission/premission-update')}}", {
                roleid: "{{$role->id}}",
                pressionids: chk_value,
                _token: "{{ csrf_token() }}"
            }, function (data) {
                $.alert({
                    title: '消息提示!',
                    content: '权限更新成功!',
                    confirm: function () {
                        window.location.href = window.location.href;
                    }
                });
            }, 'json');
        });

        $('.cksel').each(function () {
            var $isck = true;
            var obj = $(this).find('.checkboxWarning');
            obj.each(function (e) {
                if (!$(this).is(':checked')) {
                    $isck = false;
                }
            });
            if ($isck) {
                $(this).prev().find('.checkboxError').attr('checked', 'checked');
            }
        });
        $('.checkboxError').click(function () {
            if (!$(this).is(':checked')) {
                $(this).parents('.control').next().find('.checkboxWarning').prop("checked", this.checked);
            } else {
                console.log($(this).parent('div .control'));
                $(this).parents('.control').next().find('.checkboxWarning').prop("checked", this.checked);
            }
        });
        $('.checkboxWarning').click(function () {
            $(this).parents('.cksel').find('.checkboxWarning').each(function () {
                $isc = true;
                if (!$(this).is(':checked')) {
                    $isc = false;
                    return false;
                }
            });
            $(this).parents('.cksel').prev().find('.checkboxError').prop("checked", $isc);

            if (!$(this).is(':checked')) {
                $(this).parents('.control').next().find('.checkboxWarning').prop("checked", this.checked);
            } else {
                console.log($(this).parent('div .control'));
                $(this).parents('.control').next().find('.checkboxWarning').prop("checked", this.checked);
            }
        });
    });
</script>