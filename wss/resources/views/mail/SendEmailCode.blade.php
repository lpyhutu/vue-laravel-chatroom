<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        * {
            padding: 0px;
            margin: 0px;
        }

        #app {
            padding: 20px;
            background: rgb(240, 240, 240);
            font-size: 14px;

        }

        .content {
            height: 460px;
            max-width: 800px;
            background: #fff;
            border-radius: 5px;
            padding: 10px;
            margin: auto;
            border: 1px solid #eee;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            height: 80px;
            padding: 20px;
        }

        .header img {
            height: 35px;
            margin-right: 10px;
        }

        .center {
            padding: 30px 140px;
            line-height: 26px;
            border-bottom: 1px dashed #999;
        }

        .code {
            color: red;
            font-size: 20px;
        }

        .footer {
            display: flex;
            flex-flow: column;
            align-items: flex-end;
            line-height: 26px;
            padding: 30px 50px;
        }
    </style>
</head>

<body>
    <div id="app">
        <div class="content">
            <div class="header">
                <img src="{{asset('img/logo.png')}}" />
                <div>
                    <h1>糊涂小菜鸟</h1>
                </div>
            </div>
            <div class="center">
                <p>您好！</p>
                <p>感谢你注册使用糊涂聊天室。</p>
                <p>你的登录账号为：{{$email}}，</p>
                <p>验证码：<span class="code">{{$code}}</span>，验证码3分钟内有效，请在规定时间内完成注册。</p>
                <br>
                <p>如无法注册，请联系邮箱：1048672466@qq.com，我会尽快处理问题！</p>
            </div>
            <div class="footer">
                <div class="user">糊涂</div>
            </div>
        </div>
    </div>
</body>

</html>