<?php
session_start();
include "pdoInc.php";
?>

<html>

<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <script src="src/library/jquery/jquery.min.js"></script>
    <script src="src/library/moment-with-locales.min.js"></script>
    <script src="src/library/daterangepicker/daterangepicker.min.js"></script>
    <script src="src/common/common.js"></script>
    <script src="src/component/dropBox/index.js"></script>
    <link rel="stylesheet" type="text/css" href="src/common/common.css">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="src/component/dropBox/index.css">

    <style type="text/css">
        body {
            background-color: var(--background);
        }

        .button-hollow {
            width: 80px;
            height: 40px;
            border-radius: 8px;
            border: 1px solid var(--function);
            font-size: 12px;
            font-weight: 500;
            text-align: center;
            color: var(--function);
            cursor: pointer;
        }

        .button-hollow:hover {
            border: 1px solid var(--secondary-green);
            color: var(--secondary-green);
            box-shadow: 0 0 4px 0 rgba(0, 0, 0, 0.08);
        }

        .button-fill {
            width: 100%;
            height: 40px;
            border-radius: 8px;
            background-color: var(--function) !important;
            border: 1px solid var(--function);
            font-size: 12px;
            font-weight: 500;
            text-align: center;
            color: #fff;
            cursor: pointer;
        }

        .button-fill:hover {
            background-color: var(--secondary-green) !important;
            border: 1px solid var(--secondary-green);
            box-shadow: 0 0 4px 0 rgba(0, 0, 0, 0.08);
        }

        .button-pink {
            width: 80px;
            height: 40px;
            border-radius: 8px;
            border: 1px solid var(--alert);
            font-size: 12px;
            font-weight: 500;
            text-align: center;
            color: var(--alert);
            cursor: pointer;
        }

        .button-pink:hover {
            color: var(--alert-dark) !important;
            border: 1px solid var(--alert-dark);
            box-shadow: 0 0 4px 0 rgba(0, 0, 0, 0.08);
        }

        #content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            max-width: 1280px;
            padding: 60px;
            margin: auto;
            height: -webkit-fill-available;
        }

        #content .left {
            width: 500px;
            height: fit-content;
        }

        #content .left .title {
            font-weight: 900;
            font-size: 80px;
            margin-bottom: 20px;
            text-align: center;
            color: var(--primary-green);
        }

        #content .left .tab {
            display: flex;
            flex-direction: column;
            padding: 30px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 12px 0 rgba(0, 0, 0, 0.08);
        }

        #content .left .tab .logo {
            width: 100%;
            margin-bottom: 30px;
        }

        #content .left .tab .logo img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }

        #content .left .tab .form {
            width: 100%;
        }

        #content .left .tab .form__input {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }

        #content .left .tab .form__input>img {
            width: 30px;
            height: 30px;
            margin-right: 12px;
        }

        #content .left .tab .form__input .input {
            padding: 9px 16px;
            box-sizing: border-box;
            border-radius: 8px;
            border: 1px solid #f2f2f2;
            width: 100%;
        }

        #content .left .tab .form__input .input:focus {
            border-color: var(--function);
        }

        .buttons {
            display: grid;
            grid-template-columns: 80px 1fr 80px;
            gap: 16px;
            width: 100%;
        }

        #content .banner {
            display: flex;
            justify-content: center;
            width: 60vw;
            position: absolute;
            right: -60px;
            z-index: -1;
            bottom: 50%;
            transform: translateY(60%);
        }

        #content .banner img {
            width: 100%;
            object-fit: cover;
        }

        .user {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .user .avatar {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background-color: #d4e2e2;
            margin-bottom: 10px;
        }

        .user .avatar .user_img {
            display: block;
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>

<body>
    <div id="content">
        <div class="left">
            <div class="tab">
                <div class="user">
                    <div class="avatar" id="previewDiv">
                        <img class="user_img" src="src/img/icon/girl.svg" alt="avatar" />
                    </div>
                </div>
                <form class="form" action="src/action/login.php" method="post" enctype="multipart/form-data">
                    <div class="form__input">
                        <input type="radio" id="student" name="reg_identity" value="student" checked>
                        <label for="student">學生</label>
                        <input type="radio" id="teacher" name="reg_identity" value="teacher">
                        <label for="teacher">教師</label>
                    </div>

                    <input type="file" name="reg_file" id="imagesfile" accept=".jpg, .jpeg, .png, .svg" />
                    <div class="form__input account">
                        <img src="src/img/icon/user-dark.svg" />
                        <input class="input" type="text" name="reg_name" placeholder="請輸入帳號名稱" required />
                    </div>
                    <!--
                    <div class="form__input school">
                        <img src="src/img/icon/class-dark.svg" />
                        <div class="drop__container">
                            <input class="select-selected" type="text" placeholder="請選擇學校" name="school"/>
                            <img src="src/img/icon/right-dark.svg" alt="icon" class="icon">
                            <div class="line"></div>
                            <div class="select-items">
                                <div class="option" value="1">學校1</div>
                                <div class="option" value="2">學校2</div>
                                <div class="option" value="3">學校3</div>
                                <div class="option" value="4">學校4</div>
                            </div>
                        </div>
                    </div>
                    -->
                    <div class="form__input email">
                        <img src="src/img/icon/email.svg" />
                        <input class="input" type="mail" name="reg_email" placeholder="請輸入信箱" required />
                    </div>
                    <div class="form__input password">
                        <img src="src/img/icon/lock.svg" />
                        <input class="input" type="password" name="reg_password" placeholder="請輸入密碼" required />
                    </div>
                    <div class="form__input password">
                        <img src="src/img/icon/lock.svg" />
                        <input class="input" type="password" name="reg_password2" placeholder="再輸入密碼" required />
                    </div>
                    <div class="buttons">
                        <button class="login-btn button-fill">
                            <input type="submit" name="submit" value="提交">
                        </button>
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
<script>
    $("#imagesfile").change(function() {

        var file = this.files[0];
        console.log(file);
        //用size属性判断文件大小不能超过5M ，前端直接判断的好处，免去服务器的压力。
        if (file.size > 5 * 1024 * 1024) {
            alert("Too Big! No more than 5MB")
        }

        var reader = new FileReader();
        reader.onload = function() {
            // 通过 reader.result 来访问生成的 base64 DataURL
            var base64 = reader.result;
            showPreviewImage(base64);
        }
        reader.readAsDataURL(file);
    });

    function showPreviewImage(src) {
        $("#previewDiv .user_img").remove();
        $("#previewDiv").append(`<img class="user_img" src="${src}" alt="avatar" />`);
    }
</script>
<script src="src/library/jquery.min.js"></script>
<script src="src/library/datatables/datatables.min.js"></script>
<script src="src/library/moment-with-locales.min.js"></script>
<script src="src/library/daterangepicker/daterangepicker.min.js"></script>
<script src="src/common/common.js"></script>

</html>