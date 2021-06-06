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

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
<div style="text-align: center; width: 100%; height: 30px; line-height: 40px"><b>聊天室</b></div>
<div class="flex-center position-ref full-height">
    <div style="width: 200px; height: 95%;  border: black 1px solid; position: relative">
        <div style="text-align: center; height: 60px; border-bottom: #636b6f 1px solid;">
            个人信息：<br>
            昵称：{{$userInfo['name']}}
        </div>
        <input type="hidden" id="username" value="{{$userInfo["name"]}}">
        <div style="text-align: center; height: 30px; border-bottom: #636b6f 1px solid; line-height: 30px">
            添加好友
        </div>
        <div style="text-align: center; height: 30px; border-bottom: #636b6f 1px solid; line-height: 30px">好友列表</div>
        <ul>
            @foreach($userFriend as $value)
                <li value="{{$value['id']}}" class="user" style="border: #636b6f 1px solid">{{$value['name']}}</li>
            @endforeach
        </ul>
        <div class="logout" style="border-top: #636b6f 1px solid; text-align: center; height: 60px; position: absolute; bottom: 0; width: 100%; line-height: 60px">
            退出
        </div>
    </div>
    <div style="width: 500px; height: 95%; border: black 1px solid; text-align: center; display:table;" class="content-parent">
        <div style="display:table-cell;vertical-align:middle;">请选择聊天对象</div>
    </div>
    <div style="width: 500px; height: 95%; border: black 1px solid; display: none" class="content">
        <div style="width: 100%; height: 357px; border-bottom: #636b6f 2px solid">
            <div style="border-bottom: black 1px solid; height: 30px; line-height: 30px">
                聊天记录：
            </div>
            <div>
                <div id="recv"></div>
            </div>
        </div>
        <div>
            <div>
                <textarea rows="5" style="width: 98.5%" id="rece"></textarea>
            </div>
            <input type="hidden" value="" id="receive_user">
            <button id="send" style="width: 100%;height: 60px">发送消息</button>
        </div>
    </div>
</div>
</body>
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<script type="text/javascript">
    var token = localStorage.getItem("userToken")
    var websocket = new WebSocket("ws://localhost:9502/?token=" + token);

    websocket.onopen = function () {
        console.log("websocket opend");
    }

    websocket.onclose = function () {
        console.log("websocket close");
    }

    websocket.onmessage = function (e) {
        var data = JSON.parse(e.data);
        var str = document.getElementById("recv").innerHTML;
        $("#recv").html("");
        $("#recv").append(str + "<br> 昵称：" + data["username"] + "<br>&nbsp;&nbsp;&nbsp;&nbsp;" + data['data'])
        $("#recv").after("")
    }

    document.getElementById("send").onclick = function () {
        var rece = document.getElementById("rece").value;
        var receive_user = $("#receive_user").attr("value");

        if (!rece) {
            alert("请选择输入聊天内容")
            return;
        }

        var data = {}
        data.data = rece;
        data.receive_user = receive_user;

        websocket.send(JSON.stringify(data));
        document.getElementById("rece").value = "";

        var username = $("#username").attr("value");
        var str = document.getElementById("recv").innerHTML;
        $("#recv").html("");
        $("#recv").append(str + "<br>昵称：" + username + "<br>&nbsp;&nbsp;&nbsp;&nbsp;" + rece) + "<br> "
        $("#recv").after("")
    }

    $(".logout").click(function(){
        $.ajax({
            "url":"/api/auth/logout",
            "type":"post",
            "success":function(e){
                if(e.code == 0) {
                    alert(e.msg);
                    window.location.href="/api/home/index";
                } else{
                    alert("退出失败");
                }
            }
        })
    });

    $(".user").click(function () {
        var userId = $(this).attr("value");
        $("#receive_user").attr("value", userId);
        $(".content").css("display", "table");
        $(".content-parent").css("display", "none");

        var content = $("#recv").text();
        if (!content) {
            $.ajax({
                url: "/api/home/chatRecordList",
                type: "post",
                data: {
                    "receive_id": userId,
                    "token": token,
                },
                success: function (result) {
                    if (result.code == 200) {
                        var data = result.data;
                        len = data.length;

                        var str = "";
                        for ($i = len-1; $i > -1; $i--) {
                            str += "昵称：" + data[$i]["uid_name"] + "<br>&nbsp;&nbsp;&nbsp;&nbsp;" + data[$i]['content'] + "<br>";
                        }

                        $("#recv").append(str)
                        $("#recv").after("")
                    } else {
                        alert(result.msg)
                    }
                }
            })
        }
    });
</script>
</html>
