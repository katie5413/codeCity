<?php
session_start();
include "../pdoInc.php";

if (!isset($_SESSION['user']['email'])) {
    // 沒登入，滾出去
    die('<meta http-equiv="refresh" content="0; url=../index.php">');
}
?>

<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <script src="../src/library/jquery/jquery.min.js"></script>
    <script src="../src/library/moment-with-locales.min.js"></script>
    <script src="../src/library/datatables/datatables.min.js"></script>
    <script src="../src/library/datatables/dataTables.scrollResize.min.js"></script>
    <script src="../src/library/daterangepicker/daterangepicker.min.js"></script>
    <script src="../src/common/common.js"></script>
    <script src="../src/component/dropBox/index.js"></script>
    <link rel="stylesheet" type="text/css" href="../src/common/common.css">
    <link rel="stylesheet" type="text/css" href="../src/library/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" type="text/css" href="../src/library/datatables/datatables.css">
    <link rel="stylesheet" type="text/css" href="../src/component/missionCard/index.css">
    <link rel="stylesheet" type="text/css" href="../src/component/sideMenu/index.css">
    <link rel="stylesheet" type="text/css" href="../src/component/pop/index.css">
    <link rel="stylesheet" type="text/css" href="../src/component/messege/index.css">
    <link rel="stylesheet" type="text/css" href="../src/component/dropBox/index.css">
    <link rel="stylesheet" type="text/css" href="../src/component/datePicker/index.css">
    <link rel="stylesheet" type="text/css" href="../src/component/table/index.css">
    <link rel="stylesheet" type="text/css" href="index.css?v=<?php echo time(); ?>">

    <title>設定</title>


</head>

<body>
    <div id="content">
        <div class="side-menu--container">
            <div class="side-menu">
                <img class="logo" src="https://picsum.photos/400/300" alt="logo">
                <div class="user">
                    <div class="avatar">
                        <?php
                        if ($_SESSION['user']['img'] == 1) {
                            echo '<img src="../src/img/3Dcity.svg" alt="avatar" />';
                        } else {
                            echo '<img src="' . $_SESSION['user']['img'] . '" alt="avatar" />';
                        }
                        ?> </div>
                    <h1 class="username">
                        <?php echo $_SESSION['user']['name']; ?>
                    </h1>
                </div>
                <div class="bar"></div>
                <nav>
                    <!--
                    <li class="active">
                        <a href="#">
                            <div class="item" aria-hidden="true">
                                <img class="icon" src="../src/img/icon/home-dark.svg" alt="icon">
                                <span class="text">首頁</span>
                            </div>
                        </a>
                    </li>
                    
                    <li>
                        <a href="#">
                            <div class="item" aria-hidden="true">
                                <img class="icon" src="../src/img/icon/bell-dark.svg" alt="icon">
                                <span class="text">通知</span>
                            </div>
                        </a>
                    </li>
                    -->
                    <li>
                        <a href="../Game" target="_blank">
                            <div class="item" aria-hidden="true">
                                <img class="icon" src="../src/img/icon/game.svg" alt="icon">
                                <span class="text">遊戲</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="../main.php">
                            <div class="item" aria-hidden="true">
                                <img class="icon" src="../src/img/icon/mission-dark.svg" alt="icon">
                                <span class="text">任務</span>
                            </div>
                        </a>
                    </li>

                    <?php
                    if ($_SESSION['user']['identity'] === 'teacher') {
                        echo "<li>
                        <a href='../Class/index.php'>
                            <div class='item' aria-hidden='true'>
                                <img class='icon' src='../src/img/icon/class-dark.svg' alt='icon'>
                                <span class='text'>班級</span>
                            </div>
                        </a>
                    </li>";
                    }
                    ?>

                    <li class="active">
                        <a href="../Setting/index.php">
                            <div class="item" aria-hidden="true">
                                <img class="icon" src="../src/img/icon/setting-dark.svg" alt="icon">
                                <span class="text">設定</span>
                            </div>
                        </a>
                    </li>
                </nav>

                <div class="status">
                    <a class="item" href="../src/action/logout.php">
                        <img class="icon" src="../src/img/icon/logout.svg" alt="icon">
                        <div class="text">登出</div>
                    </a>
                </div>
            </div>
        </div>
        <!-- sideMenu end-->
        <div class="tab">
            <div class="tab-content">
                <div class="tab-content-top">
                    <h1 class="page-title">
                        設定
                    </h1>
                </div>
                <div class="tab-content-bottom">
                    <div class="top">
                        <div class="user">
                            <form class="avatar" action="../src/action/changeAvatar.php" method="post" enctype="multipart/form-data">
                                <input type="file" name="upload_user_img" id="upload_user_img" accept=".jpg, .jpeg, .png, .svg" hidden />
                                <label for="upload_user_img">
                                    <div id="user__img_area" class="img">


                                        <img class="user_img" src="<?php if ($_SESSION['user']['img'] == 1) {
                                                                        echo '../src/img/3Dcity.svg';
                                                                    } else {
                                                                        echo $_SESSION['user']['img'];
                                                                    } ?>" alt="avatar" />
                                    </div>
                                    <img class="icon" src="../src/img/icon/setting.svg" alt="setting" />
                                </label>
                                <button id="changeAvatar" type=submit hidden></button>
                            </form>

                        </div>
                        <div class="user-data">
                            <div class="username"><?php echo $_SESSION['user']['name']; ?></div>
                            <!--<div class="data school">學校：NTNU</div>-->
                            <div class="data email">信箱：<?php echo $_SESSION['user']['email']; ?></div>
                            <div class="data class">班級：
                                <?php
                                $findClass = $dbh->prepare('SELECT * FROM class WHERE id = ?');
                                $findClass->execute(array($_SESSION['user']['classID']));

                                if ($classData = $findClass->fetch(PDO::FETCH_ASSOC)) {
                                    echo $classData['name'];
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="session">
                        <div class="function">
                            <div class="top">
                                <div class="title">更改密碼</div>
                            </div>
                            <form class="form" action="<?php echo "../src/action/changePassword.php"; ?>" method="post" enctype="multipart/form-data">
                                <div class="form__input password">
                                    <img src="../src/img/icon/lock.svg" />
                                    <input class="input" name="oldPassword" type="text" onkeyup="value=value.replace(/[^\w_-.]/g,'')" placeholder="請輸入舊密碼" />
                                </div>
                                <div class="form__input password">
                                    <img src="../src/img/icon/lock.svg" />
                                    <input class="input" name="password1" type="text" onkeyup="value=value.replace(/[^\w_-.]/g,'')" placeholder="請輸入新密碼" />
                                </div>
                                <div class="form__input password">
                                    <img src="../src/img/icon/lock.svg" />
                                    <input class="input" name="password2" type="text" onkeyup="value=value.replace(/[^\w_-.]/g,'')" placeholder="再輸入新密碼" />
                                </div>
                                <button type="submit" class="button button-pink">更改密碼</button>
                            </form>
                        </div>
                        <div class="function">
                            <div class="top">
                                <div class="title">更改信箱</div>
                            </div>
                            <form class="form" action="<?php echo "../src/action/changeEmail.php"; ?>" method="post">
                                <div class="form__input ">
                                    <img src="../src/img/icon/email.svg" />
                                    <input class="input" name="update_email" type="text" onkeyup="value=value.replace(/[^\w_-.]/g,'')" name="password" placeholder="請輸入信箱" />
                                </div>
                                <button type="submit" class="button button-pink">更改信箱</button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- tab end-->
    </div>
    <!-- content end-->
</body>

<script src="../src/library/jquery.min.js"></script>
<script src="../src/library/datatables/datatables.min.js"></script>
<script src="../src/library/moment-with-locales.min.js"></script>
<script src="../src/library/daterangepicker/daterangepicker.min.js"></script>
<script src="../src/component/pop/index.js"></script>
<script src="../src/component/sideMenu/index.js"></script>
<script src="../src/common/common.js"></script>
<script src="index.js"></script>

</html>