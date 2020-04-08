<html xmlns="http://www.w3.org/1999/xhtml">
<head><meta http-equiv="Content-Type" content="text/html; charset=gb2312" /><title>
        欢迎页
    </title><link rel="stylesheet" type="text/css" href="/styles/layout.css" />
    <style>
        .welcome-bd{ padding: 10px;}
        .welcome-box{ border: 1px solid #dcdcdc; background: url({{asset('newsty')}}/images/welcome_i.jpg);position: relative;padding:40px 40px 133px;}
        .welcome-i{
            position: absolute;bottom: -1px;right: -1px; height: 133px; width: 248px; background: url({{asset('newsty')}}/images/welcome_bj.jpg);
        }
        .welcome-box h2{ font-size: 14px; margin-bottom: 15px;}
        .welcome-box h2 strong{ color: red;}
        .welcome-list{ height: 30px; font-size:15px;line-height: 30px; display:block;}
        .welcome-list label{ width: 110px; text-align: right;float: left; margin-right: 5px;}
    </style>
</head>

<body>
<div class="welcome-bd">
    <div class="welcome-box">
        <i class="welcome-i"></i>
        <h2>尊敬的用户 <strong>{{ Auth::user()->name }}</strong>  【{{$role}}】，欢迎登录游戏管理后台！</h2>
        <div class="welcome-list"><label>系统版本：</label>1.0.0</div>
        <div class="welcome-list"><label>上次登录时间：</label>{{$lastlogin->created_at}}</div>
        <div class="welcome-list"><label>上次登录地址：</label>{{$lastlogin->ipaddr}}</div>
        <div class="welcome-list"><label>登录次数：</label>{{$logincount}} 次</div>
    </div>
</div>
</body>
</html>

