<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>
</head>
<div style="display: table; width: 100%; height: 100%;">
    <div style="display:table-cell;vertical-align:middle;">
        <div style="width: 20%; height: 30%; border: black 2px solid;  margin: 0 auto; text-align: center">
            <div style="height: 50px;"><h1>登录</h1></div>
            邮箱：<input type="text" name="name" id="name"><br><br>
            密码：<input type="password" name="password" id="password"><br><br>
            <button id="login" style="width: 200px;height: 30px">登录</button>
        </div>
    </div>
</div>

<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<script type="text/javascript">
    document.getElementById("login").onclick = function () {
        var name = document.getElementById("name").value;
        var password = document.getElementById("password").value;

        $.ajax({
            url: "/api/auth/login",
            type: "post",
            data: {
                "name": name,
                "password": password,
            },
            success: function (result) {
                if (result.code == 200) {
                    localStorage.setItem("userToken", result.data.token);
                    location.href = "/api/home/chat?token=" + result.data.token;
                } else {
                    alert(result.msg)
                }
            }
        })
    };
</script>
</html>