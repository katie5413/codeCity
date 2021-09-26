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
            display: flex;
            align-items: center;
            gap: 16px;
            width: 100%;
        }

        .checkbox {
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--function);
            padding: 6px 12px;
            border-radius: 8px;
            margin-right: 8px;
        }

        .checkbox input {
            margin-right: 8px;
        }

        .checkbox label {
            color: var(--function);
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
        <form class="left form" action="src/action/signup.php" method="post" enctype="multipart/form-data">
            <div class="tab">
                <div class="user">
                    <input type="file" name="reg_file" id="reg_file" accept=".jpg, .jpeg, .png, .svg" hidden />
                    <label for="reg_file">
                        <div class="avatar" id="previewDiv">
                            <img class="user_img" src="src/img/icon/uploadImg.svg" alt="avatar" />
                        </div>
                    </label>

                </div>
                <div>
                    <div class="form__input">
                        <div class="checkbox">
                            <input type="radio" id="student" name="reg_identity" value="student" checked>
                            <label for="student">學生</label>
                        </div>
                        <div class="checkbox">
                            <input type="radio" id="teacher" name="reg_identity" value="teacher">
                            <label for="teacher">教師</label>
                        </div>

                    </div>

                    <div class="form__input account">
                        <img src="src/img/icon/user-dark.svg" />
                        <input class="input" type="text" name="reg_name" placeholder="請輸入姓名" onkeyup="value=value.replace(/[^\w\u3400-\u4DBF\u3100-\u312F\u2E80-\u2FDF\u4E00-\u9FFF\uF900-\uFAFF\u02C9\u02CA\u02C7\u02CB\u02D9]/g, '')" />
                    </div>
                    <div class="form__input school">
                        <img src="src/img/icon/class-dark.svg" />
                        <div class="drop__container">
                            <input class="select-selected" type="text" placeholder="請選擇學校" name="reg_school" onkeyup="value=value.replace(/[^\w\u3400-\u4DBF\u3100-\u312F\u2E80-\u2FDF\u4E00-\u9FFF\uF900-\uFAFF\u02C9\u02CA\u02C7\u02CB\u02D9]/g, '')" />
                            <img src="src/img/icon/right-dark.svg" alt="icon" class="icon">
                            <div class="line"></div>
                            <div class="select-items">
                                <?php
                                $sth = $dbh->prepare('SELECT * FROM school');
                                $sth->execute();
                                while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<div class="option" value=' . $row['id'] . '>' . $row['name'] . '</div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form__input email">
                        <img src="src/img/icon/email.svg" />
                        <input class="input" type="mail" name="reg_email" placeholder="請輸入信箱作為綁定帳號" onkeyup="value=value.replace(/[^\w_.@!#$%&'*+-/=?^_`{|}~]/g,'')" />
                    </div>
                    <div class="form__input password">
                        <img src="src/img/icon/lock.svg" />
                        <input class="input" type="password" name="reg_password" placeholder="請輸入密碼" onkeyup="value=value.replace(/[^\w_.]/g,'')" />
                    </div>
                    <div class="form__input password">
                        <img src="src/img/icon/lock.svg" />
                        <input class="input" type="password" name="reg_password2" placeholder="再次輸入密碼" onkeyup="value=value.replace(/[^\w_.]/g,'')" />
                    </div>
                    <div class="buttons">
                        <a href="index.php"><button class="signup-btn button-hollow">已有帳號</button></a>
                        <button class="login-btn button-fill" type="submit">
                            提交
                        </button>
                    </div>
                </div>
            </div>
        </form>
        <div class="banner">
            <img src="src/img/3Dcity.svg" />
        </div>

    </div>
</body>
<script>
    $("#reg_file").change(function() {

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