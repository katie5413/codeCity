<?php

session_start();
include "pdoInc.php";

?>

<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <script src="src/library/jquery.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" type="text/css" href="src/common/common.css">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="index.css?v=<?php echo time(); ?>">
</head>

<body>
    <video autoplay muted loop id="myVideo">
        <source src="src/img/background-move.mp4" type="video/mp4">
    </video>
    <div id="content">
        <div class="left">
            <div class="tab">
                <div class="logo">
                    <img src="src/img/3Dcity.svg" />
                </div>
                <form class="form" action="src/action/login.php" method="post">
                    <div class="form__input account">
                        <img src="src/img/icon/email.svg" />
                        <input id="email" class="input" type="text" name="email" placeholder="帳號（信箱）" onkeyup="value=value.replace(/[^\w_.@!#$%&'*+-/=?^_`{|}~]/g,'')" />
                    </div>
                    <div class="form__input password">
                        <img src="src/img/icon/lock.svg" />
                        <input class="input" type="password" name="password" placeholder="密碼" onkeyup="value=value.replace(/[^\w_.]/g,'')"/>
                    </div>
                    <div class="buttons">
                        <a href="signup.php"><button class="signup-btn button-hollow">註冊</button></a>
                        <button class="login-btn button-fill" type="submit">登入</button>
                        <!--<a href="forgetPWD.php"><button class="forget-btn button-pink">忘記密碼</button></a>-->
                    </div>
                </form>
            </div>
        </div>
        <div class="banner">
            <img src="src/img/3Dcity.svg" />
        </div>

    </div>
</body>

<script src="src/library/datatables/datatables.min.js"></script>
<script src="src/library/moment-with-locales.min.js"></script>
<script src="src/library/daterangepicker/daterangepicker.min.js"></script>
<script src="src/common/common.js"></script>

</html>