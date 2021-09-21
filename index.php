<?php

session_start();
include "pdoInc.php";

?>

<html>

<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
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
                    <img src="https://picsum.photos/400/300" />
                </div>
                <form class="form" action="src/action/login.php" method="post">
                    <div class="form__input account">
                        <img src="src/img/icon/email.svg" />
                        <input class="input" type="text" name="email" placeholder="信箱" />
                    </div>
                    <div class="form__input password">
                        <img src="src/img/icon/lock.svg" />
                        <input class="input" type="password" name="password" placeholder="密碼" />
                    </div>
                    <div class="buttons">
                        <a href="signup.php"><button class="signup-btn button-hollow">註冊</button></a>
                        <button class="login-btn button-fill" type="submit">登入</button>
                        <a href="forgetPWD.php"><button class="forget-btn button-pink">忘記密碼</button></a>
                    </div>
                </form>
            </div>
        </div>
        <div class="banner">
            <img src="src/img/3Dcity.svg" />
        </div>

    </div>
    <!--
<table border="1">
        <tr>
            <th>資料序號</th>
            <th>當日排名</th>
            <th>前日排名</th>
            <th>歌曲名稱</th>
            <th>演 唱 者</th>
        </tr>
        <?php
        /*$sql = "SELECT * from songrank";
        $sth = $dbh->query($sql);
        while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr><td>" . $row['id'] . "</td>";
            echo "<td>" . $row['this_rank'] . "</td>";
            echo "<td>" . $row['prev_rank'] . "</td>";
            echo "<td>" . $row['song_name'] . "</td>";
            echo "<td>" . $row['singer_name'] . "</td><tr>";
        }*/
        ?>
    </table>
-->




</body>

<script src="src/library/jquery.min.js"></script>
<script src="src/library/datatables/datatables.min.js"></script>
<script src="src/library/moment-with-locales.min.js"></script>
<script src="src/library/daterangepicker/daterangepicker.min.js"></script>
<script src="src/common/common.js"></script>

</html>