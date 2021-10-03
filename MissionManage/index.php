<?php
session_start();
include "../pdoInc.php";
if (!isset($_SESSION['user']['email'])) {
    // 沒登入，滾出去
    die('<meta http-equiv="refresh" content="0; url=../index.php">');
}
if (isset($_GET['missionID'])) {
    $_SESSION['missionID'] = $_GET['missionID'];

    // 登入身份為學生
    if ($_SESSION['user']['identity'] === 'student') {
        die('<meta http-equiv="refresh" content="0; url=../main.php">');
    } else {
        // 登入身份為老師
        $_SESSION['missionID'] = $_GET['missionID'];
        $_SESSION['classID'] = $_GET['classID'];

        // 留言用
        $_SESSION['homeworkOwner'] = $_GET['studentID'];

        $findMissionData = $dbh->prepare('SELECT * FROM mission WHERE id = ? and teacherID=? and classID=?');
        $findMissionData->execute(array($_SESSION['missionID'], $_SESSION['user']['id'], $_GET['classID']));
        $missionData = $findMissionData->fetch(PDO::FETCH_ASSOC);

        if ($findMissionData->rowCount() < 1) {
            // 沒有這個任務或非本班學生
            die('<meta http-equiv="refresh" content="0; url=../main.php">');
        }

        // 作業開放時間
        $endTime = substr($missionData['endTime'], 0, -3);

        $end = strtotime($endTime);
        $now = time();
        $period = '';
        if ($now > $end && $end!=null) {
            $period = 'end';
        } else {
            $period = 'start';
        }


        $award = '../src/img/3Dcity.svg';
        // 獎勵
        if ($missionData['awardID'] !== NULL) {
            $findAward = $dbh->prepare('SELECT * FROM award WHERE id = ?');
            $findAward->execute(array($missionData['awardID']));
            $awardItem = $findAward->fetch(PDO::FETCH_ASSOC);
            $award = $awardItem['img'] !== NULL ? $awardItem['img'] : $awardItem['img_link'];
        }
    }
}
?>

<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <script src="../src/library/jquery/jquery.min.js"></script>
    <script src="../src/library/moment-with-locales.min.js"></script>
    <script src="../src/library/daterangepicker/daterangepicker.min.js"></script>
    <script src="../src/common/common.js"></script>
    <script src="../src/component/dropBox/index.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../src/common/common.css">
    <link rel="stylesheet" type="text/css" href="../src/library/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" type="text/css" href="../src/component/missionCard/index.css">
    <link rel="stylesheet" type="text/css" href="../src/component/sideMenu/index.css">
    <link rel="stylesheet" type="text/css" href="../src/component/pop/index.css">
    <link rel="stylesheet" type="text/css" href="../src/component/submitCard/index.css?">
    <link rel="stylesheet" type="text/css" href="../src/component/messege/index.css">
    <link rel="stylesheet" type="text/css" href="../src/component/dropBox/index.css">
    <link rel="stylesheet" type="text/css" href="../src/component/datePicker/index.css">
    <link rel="stylesheet" type="text/css" href="../src/component/markdown/index.css">
    <link rel="stylesheet" type="text/css" href="index.css?v=<?php echo time(); ?>">
    <title>任務管理</title>
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
                    <?php
                    if ($_SESSION['user']['identity'] !== 'teacher') {
                        echo '<li>
                        <a href="../Game" target="_blank">
                            <div class="item" aria-hidden="true">
                                <img class="icon" src="../src/img/icon/game.svg" alt="icon">
                                <span class="text">遊戲</span>
                            </div>
                        </a>
                    </li>';
                    }
                    ?>
                    <li class="active">
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

                    <li>
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
                        <?php echo $missionData['name']; ?>
                    </h1>
                    <div class="functions">
                        <button class="edit-mission-btn button-fill">編輯</button>
                    </div>
                </div>
                <div class="mission-session">
                    <!-- start end not-start -->
                    <h2 class="mission_time <?php echo $period; ?>"><?php echo $endTime; ?></h2>
                    <!-- star no-score not-submit -->
                </div>
                <div class="mission-submit-area">
                    <div class="mission-infomation">
                        <div class="mission-card-img">
                            <img src="<?php echo $award ?>" />
                        </div>
                        <div class="mission-card-content">
                            <div class="top">
                                <h2 class="mission-card-title">
                                    任務說明
                                </h2>
                            </div>
                            <div class="mission-card-detail"><?php echo $missionData['detail']; ?></div>
                        </div>
                    </div>

                    <div class="submit-status-board">
                        <div class="left">
                            <div class="status submit">
                                <div class="status-tag">
                                    已繳交
                                </div>
                                <div class="status-number">
                                    <?php
                                    $findSubmitStudent = $dbh->prepare('SELECT * FROM student LEFT JOIN homework on student.id = homework.studentID where student.classID = ? and missionID = ? ORDER by homework.score');
                                    $findSubmitStudent->execute(array($_GET['classID'], $_SESSION['missionID']));
                                    $submitSum = (int)0;
                                    while ($submitStudentList = $findSubmitStudent->fetch(PDO::FETCH_ASSOC)) {
                                        if ($submitStudentList['score'] !== NULL) {
                                            $submitSum = $submitSum + 1;
                                        }
                                    }
                                    echo $submitSum;
                                    ?>
                                </div>
                            </div>
                            <div id="submitList" class="list-area">
                                <?php
                                $findSubmitStudent = $dbh->prepare('SELECT * FROM student LEFT JOIN homework on student.id = homework.studentID where student.classID = ? and missionID = ? ORDER by homework.score');
                                $findSubmitStudent->execute(array($_GET['classID'], $_SESSION['missionID']));
                                while ($submitStudentList = $findSubmitStudent->fetch(PDO::FETCH_ASSOC)) {
                                    $submitStudentList['score'];
                                    $homeworkStatus = (int)$submitStudentList['score'];

                                    if ($homeworkStatus === 0) {
                                        $homeworkStatusText = '<div class="no-score">待評分</div>';
                                    } else {
                                        $status = '';
                                        for ($i = 1; $i < 4; $i++) {
                                            $star = $i <= $homeworkStatus ? '<img class="star ' . $i . '" src="../src/img/icon/star-active.svg" />' : '<img class="star ' . $i . '" src="../src/img/icon/star-disable.svg" />';
                                            $status .= $star;
                                        }
                                        $homeworkStatusText = $status;
                                    }

                                    if ($submitStudentList['img'] == 1) {
                                        $submitStudentList['img'] = '../src/img/3Dcity.svg';
                                    }

                                    echo '<a class="submitCard" href="../Mission/index.php?missionID=' . $submitStudentList['missionID'] . '&&studentID=' . $submitStudentList['studentID'] . '">
                                            <div class="user">
                                                <div class="user_img">
                                                    <img src="' . $submitStudentList['img'] . '" alt="' . $submitStudentList['name'] . '" />
                                                </div>
                                                <div class="name">' . $submitStudentList['name'] . '</div>
                                            </div>
                                            <div class="submit-mission-score">' . $homeworkStatusText . '</div>
                                            </a>';
                                }
                                ?>
                            </div>
                        </div>

                        <div class="right">
                            <div class="status not-submit">
                                <div class="status-tag">
                                    未繳交
                                </div>
                                <div class="status-number">
                                    <?php
                                    $findNotSubmitStudent = $dbh->prepare('SELECT id,img,name,classID from student where classID = ? and id NOT IN (SELECT student.id FROM student LEFT JOIN homework on student.id = homework.studentID where missionID = ?)');
                                    $findNotSubmitStudent->execute(array($_GET['classID'],$_SESSION['missionID']));
                                    $notSubmitSum = (int)0;
                                    while ($notSubmitStudentList = $findNotSubmitStudent->fetch(PDO::FETCH_ASSOC)) {
                                        $notSubmitSum = $notSubmitSum + 1;
                                    }
                                    echo $notSubmitSum;
                                    ?>
                                </div>
                            </div>
                            <div id="not-submitList" class="list-area">
                                <?php
                                $findNotSubmitStudent = $dbh->prepare('SELECT id,img,name from student where classID = ? and id NOT IN (SELECT student.id FROM student LEFT JOIN homework on student.id = homework.studentID where missionID = ?)');
                                $findNotSubmitStudent->execute(array($_GET['classID'],$_SESSION['missionID']));
                                while ($notSubmitStudentList = $findNotSubmitStudent->fetch(PDO::FETCH_ASSOC)) {
                                    if ($notSubmitStudentList['img'] == 1) {
                                        $notSubmitStudentList['img'] = '../src/img/3Dcity.svg';
                                    }
                                    echo '<a class="submitCard" href="../Mission/index.php?missionID=' . $_SESSION['missionID'] . '&&studentID=' . $notSubmitStudentList['id'] . '">
                                        <div class="user">
                                            <div class="user_img">
                                                <img src="' . $notSubmitStudentList['img'] . '" alt="' . $notSubmitStudentList['name'] . '" />
                                            </div>
                                            <div class="name">' . $notSubmitStudentList['name'] . '</div>
                                        </div>
                                    </a>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- tab end-->

    <div id="editMission" class="pop close">
        <form class="inner" action="../src/action/updateMission.php" method="post">
            <div class="top">
                <div class="title">
                    <img class="header__icon" src="../src/img/icon/mission-dark.svg" alt="icon">
                    <span>編輯任務</span>
                </div>
                <div class="close">x</div>
            </div>
            <div class="content">
                <div class="mission">
                    <div class="setting-top">
                        <div id="building_img_area" class="img">
                            <img class="mission_building" src="<?php echo $award; ?>" alt="mission_building">
                        </div>
                        <div class="mission_detail">
                            <div class="form__input mission_title">
                                <div class="title">任務獎勵</div>
                                <div class="drop__container" id="selectAwardArea">

                                    <input id="selectAward" name="imgName_update" class="select-selected" type="text" placeholder="請選擇" autocomplete="off" value="<?php
                                                                                                                                                                    $sth = $dbh->prepare('SELECT name FROM award where id=?');
                                                                                                                                                                    $sth->execute(array($missionData['awardID']));
                                                                                                                                                                    while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                                                                                                                                                                        echo $row['name'];
                                                                                                                                                                    }
                                                                                                                                                                    ?>" />
                                    <img src="../src/img/icon/right-dark.svg" alt="icon" class="icon">
                                    <div class="line"></div>
                                    <div class="select-items">
                                        <?php
                                        $sth = $dbh->prepare('SELECT * FROM award');
                                        $sth->execute();
                                        while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                                            echo '<div class="option" value=' . $row['id'] . '>' . $row['name'] . '</div>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form__input mission_title">
                                <div class="title">任務名稱<span class="must__fill-label">必填</span></div>
                                <input class="input input__must_fill" type="text" name="missionName_update" placeholder="請輸入任務名稱" value="<?php echo $missionData['name']; ?>" />
                            </div>
                            <div class="form__input">
                                <div class="title">啟動區間<span class="must__fill-label">必填</span></div>
                                <input type="text" name="missionPeriod_update" class="input input__must_fill calendar" value="<?php echo $endTime; ?>" autocomplete="off" />
                            </div>
                        </div>
                    </div>
                    <div class="setting-bottom">
                        <div class="form__input mission_info">
                            <div class="title">任務說明<span class="must__fill-label">必填</span></div>
                            <div id="mark">
                                <textarea id="editor" class="input input__must_fill" type="text" name="missionDetail_update" placeholder="請輸入任務說明" onkeyup="mark()"><?php echo $missionData['detail']; ?></textarea>
                                <div id="markdownResult" class="codeCity-markdown border">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="buttons">
                <button class="cancel button button-pink">取消</button>
                <button type="submit" class="edit-mission__confirm button button-fill">確定</button>
            </div>
        </form>
    </div>
    <!-- content end-->
</body>
<script>
    function mark() {
        $('#markdownResult').remove();
        $('#mark').append(`<div id="markdownResult" class="codeCity-markdown border">${marked($('#editor').val())}</div>`);
    }

    const markResult = marked($('.mission-submit-area .mission-card-detail').html());
    $('.mission-submit-area .mission-card-detail').remove();
    $('.mission-submit-area .mission-card-content').append(`<div class="mission-card-detail codeCity-markdown">${markResult}</div>`);

    // date picker
    moment.locale('zh-TW');
    $('.calendar').daterangepicker({
        singleDatePicker: true,
        timePicker: true,
        timePicker24Hour: true,
        showDropdowns: true,
        autoApply: false,
        locale: {
            format: 'YYYY-MM-DD HH:mm'
        }
    });
</script>

<script src="../src/library/jquery.min.js"></script>
<script src="../src/library/datatables/datatables.min.js"></script>
<script src="../src/library/moment-with-locales.min.js"></script>
<script src="../src/library/daterangepicker/daterangepicker.min.js"></script>
<script src="../src/component/pop/index.js"></script>
<script src="../src/component/sideMenu/index.js"></script>
<script src="../src/common/common.js"></script>
<script src="index.js?v=<?php echo time(); ?>"></script>

</html>