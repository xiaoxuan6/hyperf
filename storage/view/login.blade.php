<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>
</head>
{{--<form action="post" url="auth/login">--}}
邮箱：<input type="text" name="name" id="name">
密码：<input type="password" name="password" id="password">
<button id="login">登录</button>
{{--</form>--}}

<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<script type="text/javascript">
    document.getElementById("login").onclick = function () {
        var name = document.getElementById("name").value;
        var password = document.getElementById("password").value;

        $.ajax({
            url: "/auth/login",
            type: "post",
            data: {
                "name": name,
                "password": password,
            },
            success: function (result) {
                if (result.code == 200) {
                    localStorage.setItem("userId", result.data.id);
                    localStorage.setItem("userToken", result.data.token);
                    location.href = "/chat/message/index";
                } else {
                    alert(result.msg)
                }
            }
        })
    };
</script>
</html>