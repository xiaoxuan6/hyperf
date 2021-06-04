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
                <div class="title m-b-md" id="send">
                    Hyperf
                </div>
                <div id="recv"></div>
            </div>
        </div>
    </body>
<script type="text/javascript">
    var websocket = new WebSocket("ws://localhost:9502/");

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
        var val = this.innerHTML;
        websocket.send(val);
    }
</script>
</html>
