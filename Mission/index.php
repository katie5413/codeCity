<?php
session_start();
include "../pdoInc.php";

if (!isset($_SESSION['user']['email'])) {
    // 沒登入，滾出去
    die('<meta http-equiv="refresh" content="0; url=../index.php">');
}

if (isset($_GET['missionID'])) {
    $_SESSION['missionID'] = $_GET['missionID'];
    $_SESSION['subMissionID'] = $_GET['subMissionID'];

    // 登入身份為學生
    if ($_SESSION['user']['identity'] === 'student') {
        $findMissionData = $dbh->prepare('SELECT * FROM mission WHERE id = ? and classID=?');
        $findMissionData->execute(array($_SESSION['missionID'], $_SESSION['user']['classID']));
        $missionData = $findMissionData->fetch(PDO::FETCH_ASSOC);
        if ($findMissionData->rowCount() < 1) {
            // 沒有這個任務或非本班學生
            die('<meta http-equiv="refresh" content="0; url=../main.php">');
        }

        // 留言用
        $_SESSION['homeworkOwner'] = $_SESSION['user']['id']; // 目前學生只能看自己的作業

        // 作業繳交狀態
        // 有主題 id 也有 子任務 id 顯示各子任務的繳交狀況
        if (isset($_GET['missionID']) && isset($_GET['subMissionID'])) {
            $findHomework = $dbh->prepare('SELECT * FROM homework WHERE studentID = ? and missionID=? and subMissionID=?');
            $findHomework->execute(array($_SESSION['user']['id'], $_SESSION['missionID'], $_SESSION['subMissionID']));

            if ($homework = $findHomework->fetch(PDO::FETCH_ASSOC)) {
                $homeworkStatus = (int)$homework['score'];

                if ($homeworkStatus === 0) {
                    $homeworkStatusText = '<div class="no-score">評分中</div>';
                } else {
                    $status = '';
                    for ($i = 1; $i < 4; $i++) {
                        $star = $i <= $homeworkStatus ? '<img class="star ' . $i . '" src="../src/img/icon/star-active.svg" />' : '<img class="star ' . $i . '" src="../src/img/icon/star-disable.svg" />';
                        $status .= $star;
                    }
                    $homeworkStatusText = $status;
                }
            } else {
                $homeworkStatusText = '<div class="not-submit">未繳交</div>'; // 未找到，未繳交
            }
        } else if (isset($_GET['missionID']) && !isset($_GET['subMissionID'])) {
            //如果只 GET 到主題 id ，但沒有子任務 id，則列出該主題中子任務的平均分數

            // 先獲取該主題下，所有子任務的總數
            $findMissionGoalCount = $dbh->prepare('SELECT id FROM missionGoal WHERE missionID=?');
            $findMissionGoalCount->execute(array($_SESSION['missionID']));
            // 子任務總數
            $missionGoalCount = 0;
            while ($missionGoalDataCount = $findMissionGoalCount->fetch(PDO::FETCH_ASSOC)) {
                $missionGoalCount++;
            }

            // 無任務
            if ($missionGoalCount == 0) {
                $homeworkStatusText = '<div class="no-score">尚無任務</div>';
            } else {
                //有任務
                // 列出該生所有的作業
                $findHomeworkCount = $dbh->prepare('SELECT id,score FROM homework WHERE studentID = ? and missionID=? and subMissionID IS NOT NULL');
                $findHomeworkCount->execute(array($_SESSION['user']['id'], $_SESSION['missionID']));

                $waitToScore = 0;
                $submitHomeworkCount = 0;
                $submitHomeworkScoreTotal = 0;
                while ($homeworkCount = $findHomeworkCount->fetch(PDO::FETCH_ASSOC)) {
                    $submitHomeworkCount++;
                    if ($homeworkCount['score'] == 0) {
                        $waitToScore++;
                    } else {
                        $submitHomeworkScoreTotal += $homeworkCount['score'];
                    }
                }

                // 該學生繳交作業總數與分數平均
                $missionNotSubmitCount = $missionGoalCount  - $submitHomeworkCount;

                // 未繳交
                if ($submitHomeworkCount == 0) {
                    $homeworkStatusText = '<div class="not-submit">未繳交</div>';
                } else if ($missionGoalCount > $submitHomeworkCount) {
                    // 有缺
                    $homeworkStatusText = '<div class="not-submit">尚缺 ' . $missionNotSubmitCount . '</div>';
                } else if ($missionGoalCount == $submitHomeworkCount) {
                    // 若作業都有繳交則開始計算分數
                    if ($submitHomeworkCount - $waitToScore == 0) {
                        $homeworkStatus = 0;
                    } else {
                        $homeworkStatus = ceil($submitHomeworkScoreTotal / ($submitHomeworkCount - $waitToScore));
                    }

                    if ($waitToScore > 0) {
                        $homeworkStatusText = '<div class="no-score">評分中</div>';
                    } else {
                        // 學生看星星
                        $switchScore = 0;
                        if ($homeworkStatus <= 33) {
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
                        $homeworkStatusText = $status;
                    }
                }
            }
        }
    } else if ($_SESSION['user']['identity'] === 'teacher') {
        // 登入身份為老師
        // 留言用
        if (isset($_GET['studentID'])) {
            $_SESSION['homeworkOwner'] = $_GET['studentID'];
        };

        //檢查是否本班老師
        $findClassTeacher = $dbh->prepare('SELECT teacherID FROM mission WHERE id=?');
        $findClassTeacher->execute(array($_GET['missionID']));
        $findClassTeacherData = $findClassTeacher->fetch(PDO::FETCH_ASSOC);

        $findClass = $dbh->prepare('SELECT classID FROM student WHERE id=?');
        $findClass->execute(array($_SESSION['homeworkOwner']));

        if ($class = $findClass->fetch(PDO::FETCH_ASSOC)) {
            $classID = $class['classID'];
        }

        if ($findClassTeacher->rowCount() >= 1) {

            // 非本班教師
            if ($findClassTeacherData['teacherID'] == $_SESSION['user']['id']) {
                $findMissionData = $dbh->prepare('SELECT * FROM mission WHERE id = ? and classID=?');
                $findMissionData->execute(array($_GET['missionID'], $classID));
                $missionData = $findMissionData->fetch(PDO::FETCH_ASSOC);

                if ($findMissionData->rowCount() < 1) {
                    // 沒有這個任務
                    die('<meta http-equiv="refresh" content="0; url=../main.php">');
                }
            } else {
                die('<meta http-equiv="refresh" content="0; url=../main.php">');
            }
        }

        // 拿學生作業
        // 作業繳交狀態
        // 有主題 id 也有 子任務 id
        if (isset($_GET['missionID']) && isset($_GET['subMissionID'])) {
            $findHomework = $dbh->prepare('SELECT * FROM homework WHERE studentID = ? and missionID=? and subMissionID=?');
            $findHomework->execute(array($_SESSION['homeworkOwner'], $_SESSION['missionID'], $_SESSION['subMissionID']));

            if ($homework = $findHomework->fetch(PDO::FETCH_ASSOC)) {
                $homeworkStatus = (int)$homework['score'];

                if ($homeworkStatus === 0) {
                    $homeworkStatus = '';
                }

                $status = '';
                for ($i = 1; $i <= 100; $i++) {
                    $star = '<a class="option" style="display:block;" href="../src/action/submitScore.php?studentID=' . $_SESSION['homeworkOwner'] . '&subMissionID=' . $_SESSION['subMissionID'] . '&score=' . $i . '">' . $i . '</a>';
                    $status .= $star;
                }

                $template =
                    '<div class="drop__container">
                    <input name="imgName" class="select-selected" type="text" placeholder="請選擇分數" autocomplete="off" value="' . $homeworkStatus . '" />
                    <img src="../src/img/icon/right-dark.svg" alt="icon" class="icon">
                    <div class="line"></div>
                    <div class="select-items">
                        ' . $status . '
                    </div>
                </div>';

                $homeworkStatusText = $template;
            } else {
                $homeworkStatusText = '<div class="not-submit">未繳交</div>'; // 未找到，未繳交
            }
        } else if (isset($_GET['missionID']) && !isset($_GET['subMissionID'])) {
            //如果只 GET 到主題 id ，但沒有子任務 id，則列出該主題中子任務的平均分數

            // 顯示作業繳交狀態
            // 先獲取該主題下，所有子任務的總數
            $findMissionGoalCount = $dbh->prepare('SELECT id FROM missionGoal WHERE missionID=?');
            $findMissionGoalCount->execute(array($_SESSION['missionID']));
            // 子任務總數
            $missionGoalCount = 0;
            while ($missionGoalDataCount = $findMissionGoalCount->fetch(PDO::FETCH_ASSOC)) {
                $missionGoalCount++;
            }

            // 無任務
            if ($missionGoalCount == 0) {
                $homeworkStatusText = '<div class="no-score">尚無任務</div>';
            } else {
                // 有任務
                // 列出該生所有的作業
                $findHomeworkCount = $dbh->prepare('SELECT id,score FROM homework WHERE studentID = ? and missionID=? and subMissionID IS NOT NULL');
                $findHomeworkCount->execute(array($_SESSION['homeworkOwner'], $_SESSION['missionID']));

                $waitToScore = 0;
                $submitHomeworkCount = 0;
                $submitHomeworkScoreTotal = 0;
                while ($homeworkCount = $findHomeworkCount->fetch(PDO::FETCH_ASSOC)) {
                    $submitHomeworkCount++;
                    if ($homeworkCount['score'] == 0) {
                        $waitToScore++;
                    } else {
                        $submitHomeworkScoreTotal += $homeworkCount['score'];
                    }
                }

                // 該學生繳交作業總數與分數平均
                $missionNotSubmitCount = $missionGoalCount  - $submitHomeworkCount;

                // 未繳交
                if ($submitHomeworkCount == 0) {
                    $homeworkStatusText = '<div class="not-submit">未繳交</div>';
                } else {
                    // 有繳交任何一個則開始計算分數
                    if ($submitHomeworkCount - $waitToScore == 0) {
                        $homeworkStatus = 0;
                        $homeworkStatusText = '<div class="no-score">未評分</div>';
                    } else {
                        $homeworkStatusText = ceil($submitHomeworkScoreTotal / ($submitHomeworkCount - $waitToScore));
                    }
                }
            }
        }
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

    $submitStatusClass = '';
    $submitTime = strtotime($homework['submitTime']);
    // 如果遲交
    if ($submitTime > $end) {
        $submitStatusClass = 'overtime';
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
    <script src="../src/library/datatables/datatables.min.js"></script>
    <script src="../src/library/datatables/dataTables.scrollResize.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../src/common/common.css">
    <link rel="stylesheet" type="text/css" href="../src/library/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" type="text/css" href="../src/component/missionCard/index.css">
    <link rel="stylesheet" type="text/css" href="../src/component/sideMenu/index.css">
    <link rel="stylesheet" type="text/css" href="../src/component/pop/index.css">
    <link rel="stylesheet" type="text/css" href="../src/component/messege/index.css">
    <link rel="stylesheet" type="text/css" href="../src/component/dropBox/index.css">
    <link rel="stylesheet" type="text/css" href="../src/component/datePicker/index.css">
    <link rel="stylesheet" type="text/css" href="../src/component/markdown/index.css">
    <link rel="stylesheet" type="text/css" href="../src/library/datatables/datatables.css">
    <link rel="stylesheet" type="text/css" href="../src/component/table/index.css">
    <link rel="stylesheet" type="text/css" href="index.css?v=<?php echo time(); ?>">
    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-TWGQMN8');
    </script>
    <!-- End Google Tag Manager -->
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-CMZ45H5BZ4"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-CMZ45H5BZ4');
    </script>
    <title>任務</title>
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
                    <li>
                        <a href="../Game" target="_blank">
                            <div class="item" aria-hidden="true">
                                <img class="icon" src="../src/img/icon/game.svg" alt="icon">
                                <span class="text">遊戲</span>
                            </div>
                        </a>
                    </li>
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


                        <?php

                        if (!isset($_SESSION['subMissionID'])) {
                            // 如果有學生 ID
                            if (isset($_SESSION['homeworkOwner'])) {
                                // 顯示子任務標題
                                $findStudent = $dbh->prepare('SELECT name FROM student WHERE id=?');
                                $findStudent->execute(array($_SESSION['homeworkOwner']));

                                while ($studentName = $findStudent->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<a href="../Class/index.php?studentID=' . $_SESSION['homeworkOwner'] . '" class="link">' . $studentName['name'] . '</a>';
                                    echo '<img class="arrow" src="../src/img/icon/right-dark.svg" />';
                                }
                            }
                            // 如果沒有子任務 ID 就只顯示主題標題
                            echo $missionData['name'];
                        } else {
                            // 如果有學生 ID
                            if (isset($_SESSION['homeworkOwner'])) {
                                // 顯示子任務標題
                                $findStudent = $dbh->prepare('SELECT name FROM student WHERE id=?');
                                $findStudent->execute(array($_SESSION['homeworkOwner']));

                                while ($studentName = $findStudent->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<a href="../Class/index.php?studentID=' . $_SESSION['homeworkOwner'] . '" class="link">' . $studentName['name'] . '</a>';
                                    echo '<img class="arrow" src="../src/img/icon/right-dark.svg" />';
                                }
                            }

                            // 如果有子任務 ID 
                            // 顯示主題連結
                            echo '<a href="?missionID=' . $_SESSION['missionID'] . '" class="link">' . $missionData['name'] . '</a>';
                            // 顯示箭頭
                            echo '<img class="arrow" src="../src/img/icon/right-dark.svg" />';

                            // 顯示子任務標題
                            $findMissionGoal = $dbh->prepare('SELECT * FROM missionGoal WHERE id=?');
                            $findMissionGoal->execute(array($_SESSION['subMissionID']));

                            while ($missionGoalData = $findMissionGoal->fetch(PDO::FETCH_ASSOC)) {
                                echo $missionGoalData['title'];
                            }
                        }
                        ?>
                    </h1>
                </div>
                <div class="mission-session">
                    <!-- start end not-start -->
                    <h2 class="mission_time <?php echo $period; ?>"><?php echo $endTime; ?></h2>
                    <!-- star no-score not-submit -->
                    <div class="mission-card-score">
                        <?php
                        if (!isset($_GET['subMissionID'])) {
                            echo '平均分數：';
                        } else {
                            echo '子任務分數：';
                        }
                        echo $homeworkStatusText; ?>
                    </div>
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
                                <table id="subMissionTable2" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>任務標題</th>
                                            <th>任務說明</th>
                                            <th>分數/等地</th>
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
                                            // 列出該生的作業
                                            $findSubHomework = $dbh->prepare('SELECT * FROM homework WHERE studentID = ? and missionID=? and subMissionID=?');
                                            $findSubHomework->execute(array($_SESSION['homeworkOwner'], $_SESSION['missionID'], $missionGoalData['id']));
                                            $subHomeworkData = $findSubHomework->fetch(PDO::FETCH_ASSOC); // 該學生繳交作業總數與分數平均

                                            if ($subHomeworkData['score'] == Null) {
                                                // 無值＝未繳交
                                                $subHomeworkStatusText = '未繳交';
                                            } else if ($subHomeworkData['score'] == 0) {
                                                // 有值為 0 ＝ 已交待評分 
                                                $subHomeworkStatusText = '待評分';
                                            } else {
                                                // 若作業有繳交則開始計算分數
                                                if ($_SESSION['user']['identity'] === 'student') {
                                                    //學生看星星
                                                    $subHomeworkStatus = '';
                                                    for ($i = 1; $i < 4; $i++) {

                                                        $switchScore = 0;
                                                        if ($subHomeworkData['score'] <= 33) {
                                                            $switchScore = 1;
                                                        } else if ($subHomeworkData['score'] <= 66) {
                                                            $switchScore = 2;
                                                        } else {
                                                            $switchScore = 3;
                                                        }

                                                        $star = $i <= $switchScore ? '<img class="star ' . $i . '" src="../src/img/icon/star-active.svg" />' : '<img class="star ' . $i . '" src="../src/img/icon/star-disable.svg" />';
                                                        $subHomeworkStatus .= $star;
                                                    }
                                                    $subHomeworkStatusText = $subHomeworkStatus;
                                                } else if ($_SESSION['user']['identity'] === 'teacher') {
                                                    $subHomeworkStatus = '';
                                                    for ($i = 1; $i < 4; $i++) {

                                                        $switchScore = 0;
                                                        if ($subHomeworkData['score'] <= 33) {
                                                            $switchScore = 1;
                                                        } else if ($subHomeworkData['score'] <= 66) {
                                                            $switchScore = 2;
                                                        } else {
                                                            $switchScore = 3;
                                                        }

                                                        $star = $i <= $switchScore ? '<img class="star ' . $i . '" src="../src/img/icon/star-active.svg" />' : '<img class="star ' . $i . '" src="../src/img/icon/star-disable.svg" />';
                                                        $subHomeworkStatus .= $star;
                                                    }
                                                    // 老師看分數
                                                    $subHomeworkStatusText = $subHomeworkData['score'] . '｜' . $subHomeworkStatus;
                                                }
                                            }
                                            $index++;
                                            $active = $missionGoalData['id'] == $_GET['subMissionID'] ? 'active' : '';
                                            echo '<tr class="' . $active . '">
                                            <td>' . $index . '</td>
                                            <td>
                                            ' . $missionGoalData['title'] . '
                                            </td>
                                            <td>
                                            ' . $missionGoalData['content'] . '
                                            </td>
                                            <td>
                                                <div class="score">' . $subHomeworkStatusText . '</div>
                                            </td>
                                            <td>
                                                <a href="?missionID=' . $_SESSION['missionID'] . '&subMissionID=' . $missionGoalData['id'] . '"><button class="button-fill">查看</button></a>
                                            </td>
                                        </tr>';
                                        }
                                        ?>

                                    </tbody>
                                </table>
                            </div>

                        </div>

                    </div>

                    <?php
                    if (isset($homework['img']) && isset($homework['imgType'])) {
                        echo '<div id="mission_img_area" class="img">
                        <img class="img" src="' . $homework['img'] . '">
                    </div>';
                    }
                    ?>

                    <?php
                    if (isset($homework['imgLink']) && $homework['imgLink'] != null) {
                        echo '<div class="submit-link"><a target="_blank" href="' . $homework['imgLink'] . '">作業連結</a></div>';
                    }
                    ?>

                    <?php
                    if (isset($homework['submitTime'])) {
                        echo '<div class="submit-time">
                        繳交時間：<span class="' . $submitStatusClass . '">' . $homework['submitTime'] . '</span>
                    </div>';
                    }
                    ?>


                    <div class="functions">
                        <?php
                        if ($_SESSION['user']['identity'] === 'student') {
                            // 檢查是否在子任務
                            if (isset($_GET['subMissionID'])) {
                                // 檢查是否有交作業
                                if (!isset($homework['id'])) {
                                    echo '<button class="submit-homework-btn button-fill">繳交作業</button>';
                                } else {
                                    // 若未超過截止時間，則開放編輯以及刪除
                                    if ($submitStatusClass != 'overtime') {
                                        echo '<button class="edit-homework-btn button-hollow">編輯作業</button>';
                                        echo '<button class="delete-homework-btn button-pink">刪除作業</button>';
                                    }
                                }
                            }
                        }

                        ?>

                    </div>
                    <div class="messege-board">
                        <?php

                        if (isset($_GET['subMissionID'])) {
                            // 如果登入者是學生
                            if ($_SESSION['user']['identity'] === 'student') {
                                $findMessage = $dbh->prepare('SELECT * FROM message WHERE missionID = ? and studentID =? and subMissionID=? ORDER BY time ASC');
                                $findMessage->execute(array($_SESSION['missionID'], $_SESSION['user']['id'], $_SESSION['subMissionID']));
                            } else if ($_SESSION['user']['identity'] === 'teacher' && isset($_SESSION['homeworkOwner'])) {
                                $findMessage = $dbh->prepare('SELECT * FROM message WHERE missionID = ? and studentID =? and subMissionID=? ORDER BY time ASC');
                                $findMessage->execute(array($_SESSION['missionID'], $_SESSION['homeworkOwner'], $_SESSION['subMissionID']));
                            }


                            while ($message = $findMessage->fetch(PDO::FETCH_ASSOC)) {
                                if ($message['isTeacher'] === '0') {
                                    $findOwner = $dbh->prepare('SELECT id,name, img, img_name FROM student WHERE id = ? ');
                                    $findOwner->execute(array($message['ownerID']));
                                    $owner = $findOwner->fetch(PDO::FETCH_ASSOC);
                                } else {
                                    $findOwner = $dbh->prepare('SELECT id,name, img, img_name FROM teacher WHERE id = ? ');
                                    $findOwner->execute(array($message['ownerID']));
                                    $owner = $findOwner->fetch(PDO::FETCH_ASSOC);
                                }

                                if ($owner['img'] == 1) {
                                    $owner['img'] = "../src/img/3Dcity.svg";
                                }

                                echo '<div class="messege">
                                <div class="user">
                                    <img src="' . $owner['img'] . '" alt="' . $owner['name'] . '" />
                                </div>
                                <div class="messege-content">
                                    <div class="top">
                                        <div class="left">
                                            <div class="name">' . $owner['name'] . '</div>
                                            <div class="bar"></div>
                                            <div class="messege-time">' . substr($message['time'], 0, -3) . '</div>
                                        </div>';

                                if ($_SESSION['user']['id'] == $owner['id']) {
                                    // 因為老師跟學生不同表， id 有可能會撞到
                                    if ($_SESSION['user']['identity'] === 'student' && $message['isTeacher'] === '0') {
                                        echo '<div class="function"><img class="icon editMsg edit-msg-btn" id="' . $message['id'] . '" src="../src/img/icon/edit.svg" alt="edit" /><img class="icon deleteMsg delete-msg-btn" id="' . $message['id'] . '" src="../src/img/icon/trash.svg" alt="delete" /></div>';
                                    }
                                    if ($_SESSION['user']['identity'] === 'teacher' && $message['isTeacher'] === '1') {
                                        echo '<div class="function"><img class="icon editMsg edit-msg-btn" id="' . $message['id'] . '" src="../src/img/icon/edit.svg" alt="edit" /><img class="icon deleteMsg delete-msg-btn" id="' . $message['id'] . '" src="../src/img/icon/trash.svg" alt="delete" /></div>';
                                    }
                                }

                                echo '</div>
                                    <div class="bottom">
                                        <div class="messege-img">
                                            ';
                                if ($message['img'] !== null) {
                                    echo '<img src="' . $message['img'] . '" alt="img" />';
                                }

                                echo '
                                        </div>
                                        <div class="messege-text">
                                        ' . $message['content'] . '
                                        </div>
                                    </div>
                                </div>
                            </div>';
                            };

                            $findStudentList = $dbh->prepare('SELECT id,name FROM student WHERE classID =?');
                            $findStudentList->execute(array($classID));


                            $studentIDList = [];
                            $studentNameList = [];
                            $studentListIndex = 0;
                            $currentStudentIndex = 0;
                            while ($studentList = $findStudentList->fetch(PDO::FETCH_ASSOC)) {
                                // 每次都會把目前的放到 tmp, 如果 tmp 不是作業擁有者的 id 則繼續
                                array_push($studentIDList, $studentList['id']);
                                array_push($studentNameList, $studentList['name']);
                                if ($studentList['id'] == $_SESSION['homeworkOwner']) {

                                    $currentStudentIndex = $studentListIndex;
                                }

                                $studentListIndex++;
                            }

                            $lastStudentIndex = $currentStudentIndex - 1;
                            $nextStudentIndex = $currentStudentIndex + 1;
                            if ($currentStudentIndex == 0) {
                                $lastStudentIndex = $studentListIndex - 1;
                            } elseif ($currentStudentIndex == $studentListIndex - 1) {
                                $nextStudentIndex = 0;
                            }


                            echo '<div class="functions">
                            <a href="../Mission/index.php?studentID=' . $studentIDList[$lastStudentIndex] . '&&missionID=' . $_GET['missionID'] . '&&subMissionID=' . $_GET['subMissionID'] . '" class="link"><img class="arrow" src="../src/img/icon/right-dark.svg" style="transform: rotate(180deg);"/>' .  $studentNameList[$lastStudentIndex] . '</a>
                            <button class="submit-msg-btn button-fill">留言</button>
                            <a href="../Mission/index.php?studentID=' . $studentIDList[$nextStudentIndex] . '&&missionID=' . $_GET['missionID'] . '&&subMissionID=' . $_GET['subMissionID'] . '" class="link">' .  $studentNameList[$nextStudentIndex] . '<img class="arrow" src="../src/img/icon/right-dark.svg"/></a>
                        </div>';
                        }
                        ?>
                    </div>
                </div>

            </div>
        </div>
        <!-- tab end-->

        <div id="viewImg" class="pop close">
            <div class="inner">
            </div>
        </div>

        <div id="submitHomework" class="pop close">
            <form class="inner" action="../src/action/submitHomework.php" method="post" enctype="multipart/form-data">
                <div class="top">
                    <div class="title">
                        <img class="header__icon" src="../src/img/icon/mission-dark.svg" alt="icon">
                        <span>提交作業</span>
                    </div>
                    <div class="close">x</div>
                </div>

                <div class="content">
                    <div class="mission">
                        <div class="submit-bottom">
                            <div class="form__input mission_link">
                                <div class="title">連結</div>
                                <input class="input group_at_least_one" type="text" name="submit_mission_link" placeholder="請輸入連結"></input>
                            </div>
                            <div class="notice">
                                需提交至少一個圖片或連結
                            </div>
                        </div>
                        <div class="submit-top">
                            <input type="file" name="upload_mission_img" id="upload_mission_img" accept=".jpg, .jpeg, .png, .svg" hidden />
                            <label for="upload_mission_img">
                                <div id="mission__img_area" class="img">
                                    <img class="mission_submit" src="../src/img/icon/uploadImg.svg" alt="mission_submit">
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="buttons">
                    <button class="cancel button button-pink">取消</button>
                    <button type="submit" class="submit-homework__confirm button button-fill">確定</button>
                </div>
            </form>
        </div>
        <div id="editHomework" class="pop close">
            <form class="inner" action="../src/action/updateHomework.php" method="post" enctype="multipart/form-data">
                <div class="top">
                    <div class="title">
                        <img class="header__icon" src="../src/img/icon/mission-dark.svg" alt="icon">
                        <span>編輯作業</span>
                    </div>
                    <div class="close">x</div>
                </div>

                <div class="content">
                    <div class="mission">
                        <div class="submit-top">
                            <input type="file" name="upload_mission_img_update" id="upload_mission_img_update" accept=".jpg, .jpeg, .png, .svg" hidden />
                            <label for="upload_mission_img_update">
                                <div id="mission__img_area_update" class="img">
                                    <?php
                                    if (isset($homework['img']) && isset($homework['imgType'])) {

                                        echo '<img class="mission_submit" src="' . $homework['img'] . '" alt="mission_submit">';
                                    } else {
                                        echo '<img class="mission_submit" src="../src/img/icon/uploadImg.svg" alt="mission_submit">';
                                    }
                                    ?>
                                </div>
                            </label>
                        </div>
                        <div class="submit-bottom">
                            <div class="form__input mission_link">
                                <div class="title">連結</div>
                                <?php
                                if (isset($homework['imgLink'])) {
                                    echo '<input class="input group_at_least_one" type="text" name="submit_mission_link_update" placeholder="請輸入連結" value="' . $homework['imgLink'] . '"></input>';
                                } else {
                                    echo '<input class="input group_at_least_one" type="text" name="submit_mission_link_update" placeholder="請輸入連結"></input>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="notice">
                        需提交至少一個圖片或連結
                    </div>
                </div>
                <div class="buttons">
                    <button class="cancel button button-pink">取消</button>
                    <button type="submit" class="edit-homework__confirm button button-fill">確定</button>
                </div>
            </form>
        </div>
        <div id="deleteHomework" class="pop close">
            <form class="inner" action="../src/action/deleteHomework.php" method="post">
                <div class="top">
                    <div class="title">
                        <img class="header__icon" src="../src/img/icon/trash.svg" alt="icon">
                        <span>刪除作業</span>
                    </div>
                    <div class="close">x</div>
                </div>
                <div class="content">
                    <div class="alert">
                        確定要刪除作業嗎？
                    </div>
                </div>
                <div class="buttons">
                    <button class="cancel button button-pink">取消</button>
                    <button type="submit" class="delete-homework__confirm button button-fill">確定</button>
                </div>
            </form>
        </div>

        <div id="submitMsg" class="pop close">
            <form class="inner" action="<?php echo '../src/action/submitMessage.php' ?>" method="post" enctype="multipart/form-data">
                <div class="top">
                    <div class="title">
                        <img class="header__icon" src="../src/img/icon/mission-dark.svg" alt="icon">
                        <span>新增作業說明</span>
                    </div>
                    <div class="close">x</div>
                </div>
                <div class="content">
                    <div class="mission">
                        <div class="setting-bottom">
                            <div class="form__input msg_text">
                                <div class="title">說明內容<span class="must__fill-label">必填</span></div>
                                <textarea class="input" type="text" name="msg_text" placeholder="請輸入留言文字"></textarea>
                            </div>
                        </div>
                        <div class="submit-top">
                            <input type="file" name="upload_msg_img" id="upload_msg_img" accept=".jpg, .jpeg, .png, .svg" hidden />
                            <label for="upload_msg_img">
                                <div id="msg__img_area" class="img">
                                    <img class="msg_img" src="../src/img/icon/uploadImg.svg" alt="msg">
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="buttons">
                    <button class="cancel button button-pink">取消</button>
                    <button type="submit" class="submit-msg__confirm button button-fill">確定</button>
                </div>
            </form>
        </div>
        <div id="editMsg" class="pop close">
            <form class="inner" action="../src/action/updateMessage.php>" method="post" enctype="multipart/form-data">
                <div class="top">
                    <div class="title">
                        <img class="header__icon" src="../src/img/icon/mission-dark.svg" alt="icon">
                        <span>編輯作業說明</span>
                    </div>
                    <div class="close">x</div>
                </div>
                <div class="content">
                    <div class="mission">
                        <div class="setting-bottom">
                            <div class="form__input msg_text">
                                <div class="title">說明內容<span class="must__fill-label">必填</span></div>
                                <textarea class="input" type="text" id="msg_text_update" name="msg_text_update" placeholder="請輸入留言文字"></textarea>
                            </div>
                        </div>
                        <div class="submit-top">
                            <input type="file" name="upload_msg_img_update" id="upload_msg_img_update" accept=".jpg, .jpeg, .png, .svg" hidden />
                            <label for="upload_msg_img_update">
                                <div id="msg__img_area_update" class="img">
                                    <img class="msg_img" src="../src/img/icon/uploadImg.svg" alt="msg">
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="buttons">
                    <button class="cancel button button-pink">取消</button>
                    <button type="submit" class="edit-msg__confirm button button-fill">確定</button>
                </div>
            </form>
        </div>'
        <div id="deleteMsg" class="pop close">
            <form class="inner" action="../src/action/deleteMessage.php" method="post" enctype="multipart/form-data">
                <div class="top">
                    <div class="title">
                        <img class="header__icon" src="../src/img/icon/trash.svg" alt="icon">
                        <span>刪除作業說明</span>
                    </div>
                    <div class="close">x</div>
                </div>
                <div class="content">
                    <div class="alert">
                        確定要刪除作業說明嗎？
                    </div>
                </div>
                <div class="buttons">
                    <button class="cancel button button-pink">取消</button>
                    <button type="submit" class="delete-msg__confirm button button-fill">確定</button>
                </div>
            </form>
        </div>

    </div>
    <!-- content end-->
</body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TWGQMN8" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<script>
    for (var i = 0; i < $('#subMissionTable2 tr td').length; i++) {

        if (i % 5 == 2) {
            var elm = $('#subMissionTable2 tr td').eq(i);

            // 清除前後空格，不然會跑版
            var str = elm.html().replace(/(^\s*)|(\s*$)/g, "");

            elm.html(`${marked.parse(str)}`);
        }
    }

    const markResult = marked.parse($('.mission-submit-area .mission-card-detail').html());
    $('.mission-submit-area .mission-card-detail').html(`<div class="mission-card-detail codeCity-markdown">${markResult}</div>`);
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