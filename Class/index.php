<?php
session_start();
include "../pdoInc.php";

if (!isset($_SESSION['user']['email'])) {
    // 沒登入，滾出去
    die('<meta http-equiv="refresh" content="0; url=../index.php">');
}

if ($_SESSION['user']['identity'] === 'teacher') {
} else {
    die('<meta http-equiv="refresh" content="0; url=../main.php">');
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
    <title>班級</title>
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
                        ?>
                    </div>
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
                        echo '<li class="active">
                        <a href="../Class/index.php">
                            <div class="item" aria-hidden="true">
                                <img class="icon" src="../src/img/icon/class-dark.svg" alt="icon">
                                <span class="text">班級</span>
                            </div>
                        </a>
                    </li>';
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
                        <a href="index.php">班級列表</a>
                    </h1>
                    <?php if ($_GET['classID'] == null && $_GET['studentID'] == null) {
                        echo '<form action="../src/action/addClass.php" class="addClass-btn"><button class="button-hollow">新增班級</button></form>';
                    } ?>
                    <?php if (isset($_GET['classID'])) {
                        $getClassName = $dbh->prepare('SELECT name FROM class WHERE id =?');
                        $getClassName->execute(array($_GET['classID']));
                        if ($className = $getClassName->fetch(PDO::FETCH_ASSOC)) {
                            echo '
                            <img class="arrow" src="../src/img/icon/right-dark.svg" />
                            <div class="sub-title active form__input">
                                <img class="edit-className" src="../src/img/icon/edit.svg" />
                                <input id="changeClassName" classID="' . $_GET['classID'] . '" name="className" value="' . $className['name'] . '" disabled/>
                            </div>';

                            echo '<div class="addStudent-btn"><button class="button-hollow">新增學生</button></div>';
                        }
                    } ?>
                    <?php if (isset($_GET['studentID'])) {
                        $getStudent = $dbh->prepare('SELECT name,classID FROM student WHERE id =?');
                        $getStudent->execute(array($_GET['studentID']));
                        if ($student = $getStudent->fetch(PDO::FETCH_ASSOC)) {
                            $getClassName = $dbh->prepare('SELECT name FROM class WHERE id =?');
                            $getClassName->execute(array($student['classID']));

                            if ($className = $getClassName->fetch(PDO::FETCH_ASSOC)) {
                                echo '
                            <img class="arrow" src="../src/img/icon/right-dark.svg" />
                            <a href="index.php?classID=' . $student['classID'] . '"><h1 class="sub-title">
                            ' . $className['name'] . '
                        </h1></a>';
                            }
                            echo '
                            <img class="arrow" src="../src/img/icon/right-dark.svg" />
                            <a href="index.php?studentID=' . $_GET['studentID'] . '"><h1 class="sub-title active">
                            ' . $student['name'] . '
                        </h1></a>';
                        }
                    } ?>
                </div>
                <div class="tab-content-bottom">
                    <div class="table__container class">
                        <table id="classTable" class="stripe" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>班級名稱</th>
                                    <th>人數</th>
                                    <th>主題總數</th>
                                    <th>查看</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $findClass = $dbh->prepare('SELECT * FROM class WHERE teacherID =?');
                                $findClass->execute(array($_SESSION["user"]['id']));
                                $index = 0;
                                while ($classData = $findClass->fetch(PDO::FETCH_ASSOC)) {
                                    $index++;
                                    $findStudent = $dbh->prepare('SELECT id FROM student WHERE classID =?');
                                    $findStudent->execute(array($classData['id']));
                                    $studentSum = (int)0;
                                    while ($studentData = $findStudent->fetch(PDO::FETCH_ASSOC)) {
                                        $studentSum++;
                                    }

                                    $findMission = $dbh->prepare('SELECT id FROM mission WHERE classID =?');
                                    $findMission->execute(array($classData['id']));
                                    $missionSum = (int)0;
                                    while ($missionData = $findMission->fetch(PDO::FETCH_ASSOC)) {
                                        $missionSum++;
                                    }

                                    echo '<tr><td>' . $index . '</td><td>' . $classData['name'] . '</td><td>' . $studentSum . '</td><td>' . $missionSum . '</td><td><a href="index.php?classID=' . $classData['id'] . '"><button class="button-fill">查看</button></a></td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="table__container student">
                        <table id="studentTable" class="stripe" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>學生頭貼</th>
                                    <th>學生名稱</th>
                                    <th>學生信箱</th>
                                    <th>學生金幣數</th>
                                    <th>查看</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $findStudent = $dbh->prepare('SELECT id,img,email,name,coins FROM student WHERE classID =?');
                                $findStudent->execute(array($_GET["classID"]));
                                $index = 0;
                                while ($studentData = $findStudent->fetch(PDO::FETCH_ASSOC)) {
                                    $index++;
                                    if ($studentData['img'] == 1) {
                                        $studentData['img'] = '../src/img/3Dcity.svg';
                                    }
                                    echo '<tr><td>' . $index . '</td><td><div class="avatar">
                                    <img src="' . $studentData['img'] . '" alt="avatar" />
                                </div></td><td>' . $studentData['name'] . '</td><td>' . $studentData['email'] . '</td><td>' . $studentData['coins'] . '</td><td><a href="
                                    ?studentID=' . $studentData['id'] . '"><button class="button-fill">查看</button></a></td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="table__container person">
                        <table id="personTable" class="stripe" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>主題名稱</th>
                                    <th>截止時間</th>
                                    <th>作業狀態</th>
                                    <th>作業平均星等</th>
                                    <th>作業平均分數</th>
                                    <th>學生留言數</th>
                                    <th>查看</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $findMission = $dbh->prepare('SELECT id, name, endTime FROM mission WHERE classID =?');
                                $findMission->execute(array($student['classID']));
                                $index = 0;
                                while ($missionData = $findMission->fetch(PDO::FETCH_ASSOC)) {
                                    $index++;

                                    // 先獲取該主題下，所有子任務的總數
                                    $findMissionGoalCount = $dbh->prepare('SELECT id FROM missionGoal WHERE missionID=?');
                                    $findMissionGoalCount->execute(array($missionData['id']));
                                    // 子任務總數
                                    $missionGoalCount = 0;
                                    while ($missionGoalDataCount = $findMissionGoalCount->fetch(PDO::FETCH_ASSOC)) {
                                        $missionGoalCount++;
                                    }

                                    // 列出該生所有的作業
                                    $findHomeworkCount = $dbh->prepare('SELECT id,score FROM homework WHERE studentID = ? and missionID=? and subMissionID IS NOT NULL');
                                    $findHomeworkCount->execute(array($_GET['studentID'], $missionData['id']));

                                    $waitToScore = 0; // 待評分
                                    $submitHomeworkCount = 0; // 已繳交
                                    $submitHomeworkScoreTotal = 0; // 已繳交作業總分
                                    while ($homeworkCount  = $findHomeworkCount->fetch(PDO::FETCH_ASSOC)) {
                                        $submitHomeworkCount++;
                                        if ($homeworkCount['score'] == 0) {
                                            $waitToScore++;
                                        } else {
                                            $submitHomeworkScoreTotal += $homeworkCount['score'];
                                        }
                                    }

                                    // 未繳交數量
                                    $missionNotSubmitCount = $missionGoalCount - $submitHomeworkCount;
                                    // 未繳交
                                    if ($submitHomeworkCount == 0) {
                                        $homeworkStatusText = '<span class="alert">未繳交</span>';
                                        $homeworkStatus = 0;

                                        if($missionGoalCount == 0){
                                            $homeworkStatusText = '<span>無任務</span>';
                                        }
                                    }else if ($missionGoalCount > $submitHomeworkCount) {
                                        // 有缺
                                        $homeworkStatusText = '<span class="alert">尚缺 ' . $missionNotSubmitCount . ' 待評 ' . $waitToScore . '</span>';
                                        $homeworkStatus = ceil($submitHomeworkScoreTotal / ($submitHomeworkCount - $waitToScore));
                                    }
                                    else if ($missionGoalCount == $submitHomeworkCount) {
                                        if ($submitHomeworkCount - $waitToScore == 0) {
                                            $homeworkStatus = 0;
                                            $homeworkStatusText = '<span class="alert">尚缺 ' . $missionNotSubmitCount . ' 待評 ' . $waitToScore . '</span>';

                                        } else {
                                            $homeworkStatus = ceil($submitHomeworkScoreTotal / ($submitHomeworkCount - $waitToScore));
                                            $homeworkStatusText = '<span>已完成</span>';
                                        }
                                    } 


                                    // 留言數
                                    $findMessage = $dbh->prepare('SELECT * FROM message WHERE missionID =? and studentID = ? and ownerID=? and isTeacher=? and subMissionID IS NOT NULL');
                                    $findMessage->execute(array($missionData['id'], $_GET['studentID'], $_GET['studentID'], 0));
                                    $messageNum = 0;

                                    while ($messageData = $findMessage->fetch(PDO::FETCH_ASSOC)) {
                                        $messageNum++;
                                    }

                                    $switchScore = 0;
                                    if ($homeworkStatus == 0) {
                                        $switchScore = 0;
                                    } else if ($homeworkStatus <= 33) {
                                        $switchScore = 1;
                                    } else if ($homeworkStatus <= 66) {
                                        $switchScore = 2;
                                    } else {
                                        $switchScore = 3;
                                    }

                                    $status = '';
                                    for ($i = 1; $i < 4; $i++) {
                                        $star = $i <= $switchScore ? '<img class="star ' . $i . '" src="../src/img/icon/star-active.svg" />' : '<img class="star ' . $i . '" src="../src/img/icon/star-disable.svg" />';
                                        $status .= $star;
                                    }
                                    $homeworkScore = $status;


                                    echo '<tr><td>' . $index . '</td><td>' . $missionData['name'] . '</td><td>' . $missionData['endTime'] . '</td><td>' . $homeworkStatusText  . '</td><td>' . $homeworkScore . '</td><td>' . $homeworkStatus . '</td><td>' . $messageNum . '</td><td><a href="../Mission/index.php?missionID=' . $missionData['id'] . '&&studentID=' . $_GET['studentID'] . '"><button class="button-fill">查看</button></a></td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
        <!-- tab end-->

        <div id="viewImg" class="pop close">
            <div class="inner">
            </div>
        </div>

        <div id="addStudent" class="pop close">
            <form class="inner" action="<?php echo '../src/action/addStudent.php' ?>" method="post" enctype="multipart/form-data">
                <div class="top">
                    <div class="title">
                        <img class="header__icon" src="../src/img/icon/mission-dark.svg" alt="icon">
                        <span>新增學生</span>
                    </div>
                    <div class="close">x</div>
                </div>
                <div class="content">
                    <div class="form__input mission_title">
                        <div class="title">學生名單</div>
                        <div class="drop__container" id="selectAwardArea">
                            <input id="selectStudent" name="student" class="select-selected" type="text" placeholder="請選擇" autocomplete="off" value="" />
                            <img src="../src/img/icon/right-dark.svg" alt="icon" class="icon">
                            <div class="line"></div>
                            <div class="select-items">
                                <?php
                                $sth = $dbh->prepare('SELECT id,name,email FROM student WHERE schoolID=?');
                                $sth->execute(array($_SESSION['user']['schoolID']));
                                while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<div class="option" value=' . $row['id'] . '>' . $row['name'] . ' - [' . $row['email'] . ']</div>';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="notice">註：一次僅能一筆</div>
                    </div>
                </div>
                <div class="buttons">
                    <button class="cancel button button-pink">取消</button>
                    <button type="submit" class="add-student__confirm button button-fill">確定</button>
                </div>
            </form>
        </div>
    </div>
    <!-- content end-->
</body>

<?php

if (isset($_GET['classID'])) {
    echo '<script>$(".table__container").hide();$(".table__container.student").show();</script>';
}

if (isset($_GET['studentID'])) {
    echo '<script>$(".table__container").hide();$(".table__container.person").show();</script>';
}

if (!isset($_GET['studentID']) && !isset($_GET['classID'])) {
    echo '<script>$(".table__container").hide();$(".table__container.class").show();</script>';
}
?>
<script src="../src/library/jquery.min.js"></script>
<script src="../src/library/datatables/datatables.min.js"></script>
<script src="../src/library/moment-with-locales.min.js"></script>
<script src="../src/library/daterangepicker/daterangepicker.min.js"></script>
<script src="../src/component/pop/index.js"></script>
<script src="../src/component/sideMenu/index.js"></script>
<script src="../src/common/common.js"></script>
<script src="index.js?v=<?php echo time(); ?>"></script>

</html>