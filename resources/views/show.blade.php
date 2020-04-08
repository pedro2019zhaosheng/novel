<!DOCTYPE html>
<html>
    <head>
        <title>Be right back.</title>

        

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                color: #B0BEC5;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 72px;
                margin-bottom: 40px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="title">@if (isset($title)){{$title}}@endif</div>
                <div id="ShowMag" style="padding-bottom:10px;font-size: x-large;font-weight: bold;color:blue;">
                @if (isset($message)){{$message}}@endif
                </div>
                <div id="ShowDiv"></div>
            </div>
        </div>
    </body>
    <script language="javascript">
        var secs = 3; //倒计时的秒数
        var URL ;
        function Load(){
            for(var i=secs;i>=0;i--)
            {
               window.setTimeout('doUpdate(' + i + ')', (secs-i) * 1000);
            }
        }
        function doUpdate(num)
        {
            document.getElementById('ShowDiv').innerHTML = '将在'+num+'秒后自动跳转' ;
            if(num == 0) { window.location.href=document.referrer; }//window.location = URL; }
        }
        Load();
    </script>
</html>
