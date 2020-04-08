<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="{{asset('/newsty')}}/css/index.css" />
    <meta name="viewport" content="width=device-width,initial-scale=1, maximum-scale=1, user-scalable=no">
    <script src="{{asset('/newsty')}}/js/rem.js"></script>
    <script src="{{asset('/newsty')}}/js/mui.js"></script>
    <title></title>
</head>
<body>
<div class="body">
    <div style="overflow: auto;height: 100vh;">
        <div class="title1">
            <img src="{{asset('/newsty')}}/img/biaoti2.png" />
        </div>
        <div class="title2" id="zhuce">
            <img src="{{asset('/newsty')}}/img/zhuce.png" />
        </div>
        <div class="title3">
            <img src="{{asset('/newsty')}}/img/xiazai.png" />
        </div>

    </div>
    <div class="shuimo">
        <img src="{{asset('/newsty')}}/img/shuomo.png" />
    </div>
</div>
<div class="model">
    <div class="mask"></div>
    <div class="contetn">
        <a class="close" id="close">
            <img src="{{asset('/newsty')}}/img/close.png" />
        </a>
        <h1>
            <img src="{{asset('/newsty')}}/img/biaoti.png" />
        </h1>
        <div class="form">
            <input placeholder="请输入手机号码" name="phone" type="tel" />
        </div>
        <div class="form">
            <input class="min" name="code" placeholder="请输入验证码" type="tel" />
            <a id="send" style="font-size: 0.3rem;text-align: center;">
                <img src="{{asset('/newsty')}}/img/yanzhengma.png" />
            </a>
        </div>
        <div class="form">
            <input placeholder="密码设置(6~15位字母+数字组合)" name="password" type="password" />
        </div>
        <input type="hidden" value="{{$uuid}}" name="userfrom">
        <div class="button" id="submit">
            <img src="{{asset('/newsty')}}/img/zhuce.png" />
        </div>
    </div>
</div>
<script type="text/javascript">
    document.getElementById("zhuce").addEventListener('tap', function() {
        document.querySelector(".model").style.display = "block";
    });
    document.getElementById("close").addEventListener('tap', function() {
        document.querySelector(".model").style.display = "none";
    });
    var sendFlag = false;
    document.getElementById("send").addEventListener('tap', function() {
        if (sendFlag == false) {
            sendFlag = true;
            var data = {
                phone: document.querySelector("[name='phone']").value
            }
            if (!data.phone) {
                mui.toast('请输入正确手机号码！');
                return;
            }
            if (!/^[1][0-9]{10}$/.test(data.phone)) {
                mui.toast('请输入正确手机号码！');
                return false;
            }
            {{--$.ajax({--}}
            {{--type: "POST",--}}
            {{--url: "/novel/sendcode",--}}
            {{--data: {phone: data.phone, _token: "{{csrf_token()}}"},--}}
            {{--dataType: "json",--}}
            {{--success: function (data) {--}}
            {{--if (data.code == 1) {--}}
            {{--window.location.href = "{{url('/novel/choiceness')}}";--}}
            {{--}--}}
            {{--}--}}
            {{--})--}}
            mui.ajax({
                url: "/novel/sendcode",
                dataType: "json",
                type: "get",
                data: {phone: data.phone },
                success: function(data) {
                    mui.toast('短信发送成功！');
                    var img=document.getElementById('send').innerHTML;
                    sendFlag = true;
                    var Time=60;
                    var T=setInterval(function(){
                        document.getElementById('send').innerHTML=Time+"s";
                        Time--;
                        if(Time==0){
                            sendFlag = false;
                            clearInterval(T);
                            document.getElementById('send').innerHTML=img;
                        }
                    },1000);
                    // alert('短信发送成功');


                }
            })
        }
    });
    document.getElementById("submit").addEventListener('tap', function() {
        var data = {
            phone: document.querySelector("[name='phone']").value,
            code: document.querySelector("[name='code']").value,
            password: document.querySelector("[name='password']").value
        };
        if (!data.phone) {
            mui.toast('请输入正确手机号码！');
            return;
        }
        if (!/^[1][0-9]{10}$/.test(data.phone)) {
            mui.toast('请输入正确手机号码！');
            return false;
        }
        if (!data.code) {
            mui.toast('请输入验证码！');
            return;
        }
        if (!data.password) {
            mui.toast('请输入密码！');
            return;
        }
        if(data.password.length<6||data.password.length>15){
            mui.toast('请输入6-15位的密码！');
            return;
        }
        if (!/^(?![^a-zA-Z]+$)(?!\D+$)/.test(data.password)) {
            mui.toast('请输入6-15位数字字母的密码！');
            return;
        }
        var userfrom = document.querySelector("[name='userfrom']").value;
        mui.ajax({
            url: "/novel/register",
            dataType: "json",
            type: "get",
            data: {phone: data.phone ,code:data.code,password:data.password,userfrom:userfrom},
            success: function(data) {
                if(data.status==1){
                    mui.toast('注册成功！');
                    window.location.reload();
                }

            }
        })

    })
</script>
</body>
</html>
