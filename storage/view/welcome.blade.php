<!doctype html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Raleway', sans-serif;
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<div class="flex-center position-ref full-height">
    <div class="content">
        <div class="title m-b-md">
            Hyperf
        </div>
        {{--<div id="recv"></div>--}}
        <div>所有在线用户</div>
        <div>
            @foreach($userids as $userid)
                <div id="receive_user" name="{{$userid}}">用户：{{$userid}}</div>
            @endforeach
        </div>
        <br>
        <br>
        <div>
            <div id="talk">选择和谁对话</div>
            <input type="-hidden" value="" id="receive_user_id"> <br>
            <br>
            <br>
            <div>聊天记录</div>
            <div id="recv"></div>
            <br>
            <br>
            <div>对话框</div>
            <textarea rows="10" cols="30" id="rece">
            </textarea>
            <button id="send">发送消息</button>
        </div>
    </div>
</div>
</body>
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<script type="text/javascript">
    var id = localStorage.getItem("userId")
    var token = localStorage.getItem("userToken")
    var websocket = new WebSocket("ws://localhost:9502/?id=" + id + "&token=" + token);

    websocket.onopen = function () {
        console.log("websocket opend");
        document.getElementById("recv").innerHTML = "websocket";
    }

    websocket.onclose = function () {
        console.log("websocket close");
    }

    websocket.onmessage = function (e) {
        console.log(e.data);
        document.getElementById("recv").innerHTML = e.data;
    }

    document.getElementById("send").onclick = function () {
        var data = {}
        var receive_user_id = document.getElementById("receive_user_id").value;
        var rece = document.getElementById("rece").value;
        data.receive_user = receive_user_id
        data.data = rece
        websocket.send(JSON.stringify(data));
    }

    document.getElementById("receive_user").onclick = function () {
        var val = $(this).attr("name");
        document.getElementById("receive_user_id").value = val;
    }
</script>
</html>
