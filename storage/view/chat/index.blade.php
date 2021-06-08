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
        <div class="friend-apply-notice friend"
             style="text-align: center; height: 30px; border-bottom: #636b6f 1px solid; line-height: 30px;">
            消息通知
        </div>
        <div class="friend-list friend"
             style="text-align: center; height: 30px; border-bottom: #636b6f 1px solid; line-height: 30px">好友列表
        </div>
        <div class="chat-list friend"
             style="text-align: center; height: 30px; border-bottom: #636b6f 1px solid; line-height: 30px">消息记录
        </div>
        <div class="logout"
             style="border-top: #636b6f 1px solid; text-align: center; height: 60px; position: absolute; bottom: 0; width: 100%; line-height: 60px">
            退出
        </div>
    </div>
    {{--<div style="width: 500px; height: 95%; border: black 1px solid; text-align: center; display:table;" class="content-parent">--}}
    {{--<div style="display:table-cell;vertical-align:middle;">请选择聊天对象</div>--}}
    {{--</div>--}}
    <div style="width: 500px; height: 95%; border: black 1px solid; display:none;text-align: center;" class="user-list">
        <div style="height: 52px; margin-top: 40px; border-bottom: black 1px solid;">
            <span style="height: 30px; width: 15%">好友列表</span>
            <div style="width: 100%; height: 30px;line-height: 30px">
                <div style="float: left; width: 120px;font-weight: 700">我的好友（{{count($userFriend)}}）</div>
                <div class="friend-apply"
                     style="float: right; width: 100px; border-radius: 5px; border: #636b6f 1px solid;margin-right: 10px">
                    添加好友
                </div>
            </div>
        </div>
        <div>
            <ul>
                @foreach($userFriend as $value)
                    <li style="border: #636b6f 1px solid; list-style: none; height: 30px; width:100%;  line-height: 30px; border-radius: 5px; padding: 10px; margin-left: -30px; margin-top: 5px">
                        <div style="float: left" class="user-left">
                            <span style="font-weight: 900">{{$value["name"]}}</span>
                        </div>
                        <div style="float: right; width: 30%;" value="{{$value["id"]}}" class="user-right">
                            <div class="send-message" style="float: left">发送消息</div>
                            <div style="float:left;margin-left: 10px">删除好友</div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div style="width: 500px; height: 95%; border: black 1px solid; display:table;text-align: center;" class="chat-log">
        <div style="height: 52px; margin-top: 40px; border-bottom: black 1px solid;">
            <span style="height: 30px; width: 15%">消息记录</span>
        </div>
        <div>
            <ul class="notice-log">
                @foreach($userChatList as $value)
                    <li style="border: #636b6f 1px solid; list-style: none; height: 30px; width:100%;  line-height: 30px; border-radius: 5px; padding: 10px; margin-left: -30px; margin-top: 5px"
                        class="chat-log-user" value="{{$value["friend_id"]}}">
                        <div style="float: left">
                            <span style="font-weight: 900">{{$value["friend_user_info"]["name"]}}：</span>
                            {{$value["content"]}}
                        </div>
                        <div style="float: right">{{$value["updated_at"]}}</div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div style="width: 500px; height: 95%; border: black 1px solid; display:none;" class="notice">
        <div style="height: 52px; margin-top: 40px; border-bottom: black 1px solid;text-align: center; ">
            <span style="height: 30px; width: 15%" class="apply-search">消息通知</span>
        </div>
        <div style="margin: 0;">
            <div style="height: 50px; line-height: 50px; padding-left: 15px">
                <div style="text-align: center; width: 450px;" id="apply-notice-list">
                    {{--@foreach($applyUser as $value)--}}
                    {{--<div style="float: left"><storage>{{$value["oauth"]["name"]}}</storage></div><div style="float: right"><button value="{{$value["id"]}}" @if($value["status"] == 0) class="apply-user-agree"@endif>@if($value["status"] == 1)已同意@else同意@endif</button></div>--}}
                    {{--<br>--}}
                    {{--@endforeach--}}
                </div>
            </div>
        </div>
    </div>
    <div style="width: 500px; height: 95%; border: black 1px solid; display:none;" class="friend-search">
        <div style="height: 63px; margin-top: 60px; border-bottom: black 1px solid;text-align: center; ">
            <input type="text" name="apply" class="apply" value="" style="width: 70%; height: 30px" placeholder="好友账号">&nbsp;&nbsp;<button
                    style="height: 30px; width: 15%" class="apply-search">搜索
            </button>
        </div>
        <div class="apply-list" style="margin: 0; display: none">
            <div style="height: 50px; line-height: 50px; padding-left: 15px">
                搜索结果：
                <div style="text-align: center; width: 450px;" class="apply-user-list"></div>
            </div>
        </div>
    </div>
    <div style="width: 500px; height: 95%; border: black 1px solid; display: none" class="content">
        <div style="border-bottom: black 1px solid; height: 30px; line-height: 30px; text-align: center" class="username"></div>
        <div id="recv" style="overflow: auto;height: 310px;"></div>
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

        if (data['event'] == "talk_event") {
            var str = document.getElementById("recv").innerHTML;
            $("#recv").html("");
            $("#recv").append(str + "<br> 昵称：" + data["username"] + "<br>&nbsp;&nbsp;&nbsp;&nbsp;" + data['data'])
            $("#recv").after("")

            // 聊天记录
            chatLog()
        }

        if (data['event'] == "friend_apply_event") {
            var str = "<div style=\"float: left\"><storage>" + data['name'] + " 申请添加您为好友</storage></div><div style=\"float: right\"><button value=" + data['id'] + " class=\"apply-user-agree\">同意</button></div><br>";
            $("#apply-notice-list").append(str)
        }
    }

    document.getElementById("send").onclick = function () {
        var rece = document.getElementById("rece").value;
        var receive_user = $("#receive_user").attr("value");

        if (!receive_user) {
            alert("请选择聊天对象")
            return;
        }
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
        $("#recv").append(str + "<br>昵称：" + username + "<br>&nbsp;&nbsp;&nbsp;&nbsp;" + rece)
        // $("#recv").after("")

        // 窗口滚动到底部
        var scrollTop = $("#recv")[0].scrollHeight;
        var num = $("#recv").css("height")
        var height = num.replace("px", "") - 250;
        if(scrollTop > height) {
            $("#recv").scrollTop(scrollTop);
        }
    }

    $(".logout").click(function () {
        $.ajax({
            "url": "/api/auth/logout",
            "type": "post",
            "success": function (e) {
                if (e.code == 0) {
                    alert(e.msg);
                    window.location.href = "/api/home/index";
                } else {
                    alert("退出失败");
                }
            }
        })
    });

    $(".friend-apply-notice").click(function () {
        $(this).parent().find(".friend").css("background", "white")
        $(this).parent().find(".friend").css("color", "black")

        $(this).css("background", "#636b6f")
        $(this).css("color", "white")

        $(".content").css("display", "none")
        $(".friend-search").css("display", "none")
        $(".notice").css("display", "table")
        $(".chat-log").css("display", "none")
        $(".user-list").css("display", "none");
    });

    $(".friend-apply").click(function () {
        $(".friend-search").css("display", "table")
        $(".user-list").css("display", "none");
    });

    $(".friend-list").click(function () {
        $(this).parent().find(".friend").css("background", "white")
        $(this).parent().find(".friend").css("color", "black")

        $(this).css("background", "#636b6f")
        $(this).css("color", "white")

        $(".content").css("display", "none")
        $('.apply-list').css("display", "none")
        $(".friend-search").css("display", "none")
        $(".notice").css("display", "none")
        $(".user-list").css("display", "table");
        $(".chat-log").css("display", "none")
    });

    $(".chat-list").click(function () {
        $(this).parent().find(".friend").css("background", "white")
        $(this).parent().find(".friend").css("color", "black")

        $(this).css("background", "#636b6f")
        $(this).css("color", "white")

        $(".content").css("display", "none")
        $('.apply-list').css("display", "none")
        $(".friend-search").css("display", "none")
        $(".notice").css("display", "none")
        $(".chat-log").css("display", "table")
        $(".user-list").css("display", "none");

        // 聊天记录
        chatLog()
    });

    $(document).on("click", ".chat-log-user", function () {
        var name = $(this).find("span").text()
        var id = $(this).attr("value")

        $(".chat-log").css("display", "none")
        $(".content").css("display", "table")

        $(".username").css("font-weight", 900)
        $(".username").html(name)
        $("#receive_user").attr("value", id);
        $("#recv").html("");
        chat(id)

    })

    $(".apply-search").click(function () {
        var friendId = $(".apply").val();

        $('.apply-list').css("display", "table")

        if (!friendId) {
            $(".apply-user-list").html("没有搜索到相关结果")
            return;
        }

        $.ajax({
            url: "/api/user/apply",
            type: "post",
            data: {
                "friendId": friendId,
                "token": token
            },
            success: function (e) {
                if (e.code == 0) {
                    var str = "<div style=\"float: left\"><storage>" + e.data.name + "</storage></div><div style=\"float: right\">";

                    if (e.data.isfriend == 0) {
                        str += "<button value=" + e.data.id + " class=\"apply-user\">添加</button></div>";
                    }

                    if (e.data.isfriend == 1) {
                        str += "<button value=" + e.data.id + ">已申请</button></div>";
                    }

                    if (e.data.isfriend == 2) {
                        str += "<button value=" + e.data.id + ">已同意</button></div>";
                    }

                    $(".apply").val("")
                    $(".apply-user-list").html(str)
                } else {
                    $(".apply-user-list").html(e.msg)
                }
            }
        })
    });

    $(document).on("click", ".apply-user", function () {
        // $(".apply-user").click(function(){ // jq动态生成HTML元素时，点击事件无效
        var userId = $(this).attr("value")

        $.ajax({
            url: "/api/user/applyFriend",
            type: "post",
            data: {
                "userId": userId,
                "token": token
            },
            success: function (e) {
                if (e.code == 0) {
                    $(".apply-user").html("已申请");
                    $(".apply-user").removeAttr("class");
                }
                alert(e.msg)
            }
        })
    });

    $(".apply-user-agree").click(function () {
        var id = $(this).attr("value");

        $.ajax({
            url: "/api/user/applyFriendAgree",
            type: "post",
            data: {
                "id": id,
                "token": token
            },
            success: function (e) {
                if (e.code == 0) {
                    $(".apply-user-agree").html("已同意")
                    $(".apply-user").removeAttr("class");
                }
                alert(e.msg)
            }
        })
    });

    $(document).on("click", ".send-message", function () {
        $(".user-list").css("display", "none")
        $(".content").css("display", "table")

        var id = $(this).parent(".user-right").attr("value")
        var username = $(this).parent(".user-right").prev(".user-left").text()
        $("#receive_user").val(id)
        $(".username").css("font-weight", 900)
        $(".username").html(username)

        // 聊天记录
        $("#recv").html("");
        chat(id)

        $.ajax({
            url: "/api/home/addTalk",
            type: "post",
            data: {
                receive_id: id,
                token: token,
            },
            success: function (e) {
                if (e.code != 0) {
                    alert("新增对话列表失败");
                }
            }
        })
    });

    $(".user").click(function () {
        var userId = $(this).attr("value");
        $("#receive_user").attr("value", userId);
        $(".content").css("display", "table");
        $('.apply-list').css("display", "none")
        $(".friend-search").css("display", "none")
        $(".notice").css("display", "none")
        $(this).parent().find(".user").css("font-weight", "")
        $(this).css("font-weight", 900)

        $("#recv").html("");
        var content = $("#recv").text();
        if (!content) {
            chat(userId)
        }
    });

    function chat(userId) {
        if(!userId) {
            alert("请选择聊天对象");
            return ;
        }
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
                    for ($i = len - 1; $i > -1; $i--) {
                        str += "昵称：" + data[$i]["uid_name"] + "<br>&nbsp;&nbsp;&nbsp;&nbsp;" + data[$i]['content'] + "<br>";
                    }

                    $("#recv").append(str)
                    $("#recv").after("")
                } else {
                    alert(result.msg)
                }
            }
        })
    };

    function chatLog() {
        $.ajax({
            url: "/api/home/userChatList",
            type: "post",
            data: {
                token: token,
            },
            success: function (e) {
                if (e.code == 0) {
                    var data = e.data.userChatList
                    len = data.length

                    var str = ""
                    for ($i = 0; $i < len; $i++) {
                        str += "<li style=\"border: #636b6f 1px solid; list-style: none; height: 30px; width:100%;  line-height: 30px; border-radius: 5px; padding: 10px; margin-left: -30px; margin-top: 5px\" class=\"chat-log-user\" value=" + data[$i]["friend_id"] + ">\n" +
                            "                        <div style=\"float: left\">\n" +
                            "                            <span style=\"font-weight: 900\">" + data[$i]["friend_user_info"]["name"] + "：</span>\n" + data[$i]["content"] +
                            "                        </div>\n" +
                            "                        <div style=\"float: right\">" + data[$i]["updated_at"] + "</div>\n" +
                            "                    </li>";
                    }

                    $(".notice-log").html(str)
                }
            }
        })
    }
</script>
</html>
