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
        if ($now > $end && $end != null) {
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

        // 處理子任務
        if (isset($_GET['subMissionID'])) {
            $findSubMissionData = $dbh->prepare('SELECT * FROM missionGoal WHERE id = ? and missionID=?');
            $findSubMissionData->execute(array($_GET['subMissionID'], $_SESSION['missionID']));
            $subMissionData = $findSubMissionData->fetch(PDO::FETCH_ASSOC);

            if ($findSubMissionData->rowCount() < 1) {
                // 沒有這個子任務
                die('<meta http-equiv="refresh" content="0; url=../main.php">');
            }
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
    <script src="../src/library/datatables/datatables.min.js"></script>
    <script src="../src/library/datatables/dataTables.scrollResize.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../src/library/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" type="text/css" href="../src/component/missionCard/index.css">
    <link rel="stylesheet" type="text/css" href="../src/component/sideMenu/index.css">
    <link rel="stylesheet" type="text/css" href="../src/library/datatables/datatables.css">
    <link rel="stylesheet" type="text/css" href="../src/component/pop/index.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" type="text/css" href="../src/component/submitCard/index.css?">
    <link rel="stylesheet" type="text/css" href="../src/component/messege/index.css">
    <link rel="stylesheet" type="text/css" href="../src/component/dropBox/index.css">
    <link rel="stylesheet" type="text/css" href="../src/component/datePicker/index.css">
    <link rel="stylesheet" type="text/css" href="../src/component/markdown/index.css">
    <link rel="stylesheet" type="text/css" href="../src/component/table/index.css">
    <link rel="stylesheet" type="text/css" href="index.css?v=<?php echo time(); ?>">
    <title>任務管理</title>
</head>

<body>
    <div id="content">
        <div class="side-menu--container">
            <div class="side-menu">
                <img class="logo" src="../src/img/3Dcity.svg" alt="logo">
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
                        <?php if (!isset($_GET['subMissionID'])) echo '<a href="javascript:history.back()" class="link">返回</a><img class="arrow" src="../src/img/icon/right-dark.svg" />' . $missionData['name'] . '' ?>
                        <?php if (isset($_GET['subMissionID'])) echo '<a href="javascript:history.back()" class="link">返回</a><img class="arrow" src="../src/img/icon/right-dark.svg" /><a href="?missionID=' . $_SESSION['missionID'] . '&&classID=' . $_SESSION['classID'] . '" class="link">' . $missionData['name'] . '</a><img class="arrow" src="../src/img/icon/right-dark.svg" />' . $subMissionData['title'] . '' ?>
                    </h1>
                    <div class="functions">
                        <button class="edit-mission-btn button-fill">編輯</button>
                        <button class="add-subMission-btn button-fill">新增子任務</button>
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
                                    主題說明
                                </h2>
                            </div>
                            <div class="mission-card-detail"><?php echo $missionData['detail']; ?></div>
                            <br>
                            <div class="top">
                                <h2 class="mission-card-title">
                                    任務說明
                                </h2>
                            </div>
                            <div class="table__container">
                                <table id="subMissionTable" class="stripe" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>任務標題</th>
                                            <th>任務說明</th>
                                            <th style="text-align:center">編輯/刪除</th>
                                            <th>查看</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                        // 所有子任務分數佔比總和
                                        /*
                                        $findMissionGoalPercentSum = $dbh->prepare('SELECT SUM(percent) FROM missionGoal WHERE missionID=?');
                                        $findMissionGoalPercentSum->execute(array($_SESSION['missionID']));
                                        $missionGoalDataPercentSum = $findMissionGoalPercentSum->fetch(PDO::FETCH_ASSOC);
                                        */

                                        // 單個任務
                                        $findMissionGoal = $dbh->prepare('SELECT * FROM missionGoal WHERE missionID=?');
                                        $findMissionGoal->execute(array($_SESSION['missionID']));
                                        $index = 0;

                                        while ($missionGoalData = $findMissionGoal->fetch(PDO::FETCH_ASSOC)) {
                                            $index++;
                                            echo '<tr>
                                            <td>' . $index . '</td>
                                            <td>
                                            ' . $missionGoalData['title'] . '
                                            </td>
                                            <td>
                                            ' . $missionGoalData['content'] . '
                                            </td>
                                            <td>
                                            <div class="function"><img class="icon editSubMission edit-subMission-btn" id="' . $missionGoalData['id'] . '" src="../src/img/icon/edit.svg" alt="edit" /><img class="icon deleteSubMission delete-subMission-btn" id="' . $missionGoalData['id'] . '" src="../src/img/icon/trash.svg" alt="delete" /></div>
                                            </td>
                                            <td>
                                                <a href="?missionID=' . $_SESSION['missionID'] . '&&classID=' . $_SESSION['classID'] . '&&subMissionID=' . $missionGoalData['id'] . '"><button class="button-fill">查看</button></a>
                                            </td>
                                        </tr>';
                                        }
                                        ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                    <!--處理主題繳交狀況-->
                    <div class="submit-status-board <?php if (isset($_GET['subMissionID'])) {
                                                        echo 'hide';
                                                    } else {
                                                        echo 'all';
                                                    }  ?>">
                        <div class="all">
                            <div class="list-area">
                                <?php
                                // 先獲取該主題下，所有子任務的總數
                                $findMissionGoalCount = $dbh->prepare('SELECT Count(id) FROM missionGoal WHERE missionID=?');
                                $findMissionGoalCount->execute(array($_SESSION['missionID']));
                                $missionGoalDataCount = $findMissionGoalCount->fetch(PDO::FETCH_ASSOC); // 子任務總數

                                $findStudent = $dbh->prepare('SELECT id, img,name FROM student where classID = ?');
                                $findStudent->execute(array($_GET['classID']));
                                while ($studentList = $findStudent->fetch(PDO::FETCH_ASSOC)) {
                                    // 列出該生所有的作業
                                    $findHomeworkCount = $dbh->prepare('SELECT Count(id),AVG(score),score FROM homework WHERE studentID = ? and missionID=? and subMissionID IS NOT NULL');
                                    $findHomeworkCount->execute(array($studentList['id'], $_SESSION['missionID']));
                                    $homeworkCount = $findHomeworkCount->fetch(PDO::FETCH_ASSOC); // 該學生繳交作業總數與分數平均

                                    $waitToScore = 0;
                                    while ($findHomeworkCount->fetch(PDO::FETCH_ASSOC)) {
                                        if ($homeworkCount['score'] == 0) {
                                            $waitToScore++;
                                        }
                                    }

                                    if ($homeworkCount['Count(id)'] == 0) {
                                        $homeworkStatusText = '<div class="not-submit">未繳交</div>';
                                    } else if ($missionGoalDataCount['Count(id)'] > $homeworkCount['Count(id)']) {
                                        $homeworkStatusText = '<div class="not-submit">尚缺 ' . $missionGoalDataCount['Count(id)'] - $homeworkCount['Count(id)'] . ' 待評 ' . $waitToScore . '</div>';
                                    }

                                    $homeworkStatus = ceil($homeworkCount['AVG(score)']);
                                    $status = '';
                                    for ($i = 1; $i < 4; $i++) {
                                        $star = $i <= $homeworkStatus ? '<img class="star ' . $i . '" src="../src/img/icon/star-active.svg" />' : '<img class="star ' . $i . '" src="../src/img/icon/star-disable.svg" />';
                                        $status .= $star;
                                    }
                                    $homeworkScore = $status;

                                    if ($studentList['img'] == 1) {
                                        $studentList['img'] = '../src/img/3Dcity.svg';
                                    }

                                    echo '<a class="submitCard" href="../Mission/index.php?missionID=' . $_SESSION['missionID'] . '&&studentID=' . $studentList['id'] . '">
                                            <div class="user">
                                                <div class="user_img">
                                                    <img src="' . $studentList['img'] . '" alt="' . $studentList['name'] . '" />
                                                </div>
                                                <div class="name">' . $studentList['name'] . '</div>
                                            </div>
                                            <div class="submit-mission-score">' . $homeworkStatusText . '' . $homeworkScore . '</div>
                                            </a>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <!--處理子任務繳交狀況-->
                    <div class="submit-status-board <?php if (!isset($_GET['subMissionID'])) echo 'hide' ?>">
                        <div class="left">
                            <div class="status submit">
                                <div class="status-tag">
                                    已繳交
                                </div>
                                <div class="status-number">
                                    <?php
                                    $findSubmitStudent = $dbh->prepare('SELECT * FROM student LEFT JOIN homework on student.id = homework.studentID where student.classID = ? and missionID = ?and subMissionID = ? ORDER by homework.score');
                                    $findSubmitStudent->execute(array($_GET['classID'], $_SESSION['missionID'], $_GET['subMissionID']));
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
                                $findSubmitStudent = $dbh->prepare('SELECT student.id, student.img,student.name,homework.score,homework.studentID,homework.missionID, homework.subMissionID FROM student LEFT JOIN homework on student.id = homework.studentID where student.classID = ? and missionID = ? and subMissionID = ? ORDER by homework.score');
                                $findSubmitStudent->execute(array($_GET['classID'], $_SESSION['missionID'], $_GET['subMissionID']));
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
                                    $findNotSubmitStudent = $dbh->prepare('SELECT id,img,name,classID from student where classID = ? and id NOT IN (SELECT student.id FROM student LEFT JOIN homework on student.id = homework.studentID where missionID = ? and subMissionID = ?)');
                                    $findNotSubmitStudent->execute(array($_GET['classID'], $_SESSION['missionID'], $_GET['subMissionID']));
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
                                $findNotSubmitStudent = $dbh->prepare('SELECT id,img,name from student where classID = ? and id NOT IN (SELECT student.id FROM student LEFT JOIN homework on student.id = homework.studentID where missionID = ? and subMissionID = ?)');
                                $findNotSubmitStudent->execute(array($_GET['classID'], $_SESSION['missionID'],$_GET['subMissionID']));
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
                    <span>編輯主題</span>
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
                            <div class="inline">
                                <div class="form__input mission_title">
                                    <div class="title">主題獎勵</div>
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
                                    <div class="title">主題名稱<span class="must__fill-label">必填</span></div>
                                    <input class="input input__must_fill" type="text" name="missionName_update" placeholder="請輸入主題名稱" value="<?php echo $missionData['name']; ?>" />
                                </div>
                                <div class="form__input mission_end">
                                    <div class="title">截止日期<span class="must__fill-label">必填</span></div>
                                    <input type="text" name="missionPeriod_update" class="input input__must_fill calendar" value="<?php echo $endTime; ?>" autocomplete="off" />
                                </div>
                            </div>

                            <div class="form__input mission_info">
                                <div class="title">主題說明<span class="must__fill-label">必填</span></div>
                                <div id="mark">
                                    <textarea id="editor" class="input input__must_fill" type="text" name="missionDetail_update" placeholder="請輸入主題說明"><?php echo $missionData['detail']; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="buttons">
                <button type="submit" class="edit-mission__confirm button button-fill">確定</button>
            </div>
        </form>
    </div>
    <div id="editSubMission" class="pop close">
        <form class="inner" action="../src/action/updateSubMission.php" method="post">
            <div class="top">
                <div class="title">
                    <img class="header__icon" src="../src/img/icon/mission-dark.svg" alt="icon">
                    <span>編輯任務</span>
                </div>
                <div class="close">x</div>
            </div>
            <div class="content">
                <div class="mission">
                    <div class="mission_detail">
                        <div class="form__input missionGoal_title">
                            <div class="title">任務標題<span class="must__fill-label">必填</span></div>
                            <input class="input input__must_fill" type="text" name="missionGoal_title_update" id="missionGoal_title_update" placeholder="請輸入任務標題" value="<?php echo $missionGoalData['title']; ?>" />
                        </div>
                        <div class="form__input missionGoal_content">
                            <div class="title">任務說明<span class="must__fill-label">必填</span></div>
                            <input class="input input__must_fill" type="text" name="missionGoal_content_update" id="missionGoal_content_update" placeholder="請輸入任務說明" value="<?php echo $missionGoalData['content']; ?>" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="buttons">
                <button type="submit" class="edit-subMission__confirm button button-fill">確定</button>
            </div>
        </form>
    </div>

    <div id="addSubMission" class="pop close">
        <form class="inner" action="../src/action/addSubMission.php" method="post">
            <div class="top">
                <div class="title">
                    <img class="header__icon" src="../src/img/icon/mission-dark.svg" alt="icon">
                    <span>新增子任務</span>
                </div>
                <div class="close">x</div>
            </div>
            <div class="content">
                <div class="mission">
                    <div class="mission_detail">
                        <div class="form__input missionGoal_title">
                            <div class="title">子任務標題<span class="must__fill-label">必填</span></div>
                            <input class="input input__must_fill" type="text" name="missionGoal_title" id="missionGoal_title" placeholder="請輸入子任務標題" value="" />
                        </div>
                        <div class="form__input missionGoal_content">
                            <div class="title">子任務說明<span class="must__fill-label">必填</span></div>
                            <input class="input input__must_fill" type="text" name="missionGoal_content" id="missionGoal_content" placeholder="請輸入子任務說明" value="" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="buttons">
                <button type="submit" class="add-subMission__confirm button button-fill">確定</button>
            </div>
        </form>
    </div>

    <div id="deleteSubMission" class="pop close">
        <form class="inner" action="../src/action/deleteSubMission.php" method="post" enctype="multipart/form-data">
            <div class="top">
                <div class="title">
                    <img class="header__icon" src="../src/img/icon/trash.svg" alt="icon">
                    <span>刪除任務說明</span>
                </div>
                <div class="close">x</div>
            </div>
            <div class="content">
                <div class="alert">
                    確定要刪除子任務說明嗎？
                </div>
            </div>
            <div class="buttons">
                <button class="cancel button button-pink">取消</button>
                <button type="submit" class="delete-subMission__confirm button button-fill">確定</button>
            </div>
        </form>
    </div>
    <!-- content end-->
</body>
<script>
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