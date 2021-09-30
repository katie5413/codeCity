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
        $findHomework = $dbh->prepare('SELECT * FROM homework WHERE studentID = ? and missionID=?');
        $findHomework->execute(array($_SESSION['user']['id'], $_SESSION['missionID']));

        if ($homework = $findHomework->fetch(PDO::FETCH_ASSOC)) {
            $homeworkStatus = (int)$homework['score'];

            if ($homeworkStatus === 0) {
                $homeworkStatusText = '<div class="no-score">評分中</div>';
            } else {
                $status = '';
                for ($i = 1; $i < 6; $i++) {
                    $star = $i <= $homeworkStatus ? '<img class="star ' . $i . '" src="../src/img/icon/star-active.svg" />' : '<img class="star ' . $i . '" src="../src/img/icon/star-disable.svg" />';
                    $status .= $star;
                }
                $homeworkStatusText = $status;
            }
        } else {
            $homeworkStatusText = '<div class="not-submit">未繳交</div>'; // 未找到，未繳交
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
        $findClass->execute(array($_GET['studentID']));

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
        $findHomework = $dbh->prepare('SELECT * FROM homework WHERE studentID = ? and missionID=?');
        $findHomework->execute(array($_SESSION['homeworkOwner'], $_SESSION['missionID']));

        if ($homework = $findHomework->fetch(PDO::FETCH_ASSOC)) {
            $homeworkStatus = (int)$homework['score'];

            if ($homeworkStatus === 0) {
                $status = '';
                for ($i = 1; $i < 6; $i++) {
                    $star = $i <= $homeworkStatus ? '<a href="../src/action/submitScore.php?studentID=' . $_SESSION['homeworkOwner'] . '&&score=' . $i . '"><img class="star ' . $i . '" src="../src/img/icon/star-active.svg" /></a>' : '<a href="../src/action/submitScore.php?studentID=' . $_SESSION['homeworkOwner'] . '&&score=' . $i . '"><img class="star ' . $i . '" src="../src/img/icon/star-disable.svg" /></a>';
                    $status .= $star;
                }
                $homeworkStatusText = $status;
            } else {
                $status = '';
                for ($i = 1; $i < 6; $i++) {
                    $star = $i <= $homeworkStatus ? '<a href="../src/action/submitScore.php?studentID=' . $_SESSION['homeworkOwner'] . '&&score=' . $i . '"><img class="star ' . $i . '" src="../src/img/icon/star-active.svg" /></a>' : '<a href="../src/action/submitScore.php?studentID=' . $_SESSION['homeworkOwner'] . '&&score=' . $i . '"><img class="star ' . $i . '" src="../src/img/icon/star-disable.svg" /></a>';
                    $status .= $star;
                }
                $homeworkStatusText = $status;
            }
        } else {
            $homeworkStatusText = '<div class="not-submit">未繳交</div>'; // 未找到，未繳交
        }
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
    <link rel="stylesheet" type="text/css" href="index.css?v=<?php echo time(); ?>">
    <title>任務</title>
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
                </div>
                <div class="mission-session">
                    <!-- start end not-start -->
                    <h2 class="mission_time <?php echo $period; ?>"><?php echo $endTime; ?></h2>
                    <!-- star no-score not-submit -->
                    <div class="mission-card-score">
                        <?php echo $homeworkStatusText; ?>
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
                                    任務說明
                                </h2>
                            </div>
                            <div class="mission-card-detail"><?php echo $missionData['detail']; ?></div>
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
                            if (!isset($homework['id'])) {
                                echo '<button class="submit-homework-btn button-fill">繳交作業</button>';
                            } else {
                                echo '<button class="edit-homework-btn button-hollow">編輯作業</button>';
                                echo '<button class="delete-homework-btn button-pink">刪除作業</button>';
                            }
                        }

                        ?>

                    </div>
                    <div class="messege-board">
                        <?php
                        // 如果登入者是學生
                        if ($_SESSION['user']['identity'] === 'student') {
                            $findMessage = $dbh->prepare('SELECT * FROM message WHERE missionID = ? and studentID =? ORDER BY time ASC');
                            $findMessage->execute(array($_SESSION['missionID'], $_SESSION['user']['id']));
                        } else if ($_SESSION['user']['identity'] === 'teacher' && isset($_SESSION['homeworkOwner'])) {
                            $findMessage = $dbh->prepare('SELECT * FROM message WHERE missionID = ? and studentID =? ORDER BY time ASC');
                            $findMessage->execute(array($_SESSION['missionID'], $_SESSION['homeworkOwner']));
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


                        ?>

                        <div class="functions">
                            <button class="submit-msg-btn button-fill">留言</button>
                        </div>

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
                        <div class="submit-top">
                            <input type="file" name="upload_mission_img" id="upload_mission_img" accept=".jpg, .jpeg, .png, .svg" hidden />
                            <label for="upload_mission_img">
                                <div id="mission__img_area" class="img">
                                    <img class="mission_submit" src="../src/img/3Dcity.svg" alt="mission_submit">
                                </div>
                            </label>

                        </div>
                        <div class="submit-bottom">
                            <div class="form__input mission_link">
                                <div class="title">連結</div>
                                <input class="input group_at_least_one" type="text" name="submit_mission_link" placeholder="請輸入連結"></input>
                            </div>
                        </div>
                    </div>
                    <div class="notice">
                        需提交至少一個圖片或連結
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
                                        echo '<img class="mission_submit" src="../src/img/3Dcity.svg" alt="mission_submit">';
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
                        <span>新增留言</span>
                    </div>
                    <div class="close">x</div>
                </div>
                <div class="content">
                    <div class="mission">
                        <div class="submit-top">
                            <input type="file" name="upload_msg_img" id="upload_msg_img" accept=".jpg, .jpeg, .png, .svg" hidden />
                            <label for="upload_msg_img">
                                <div id="msg__img_area" class="img">
                                    <img class="msg_img" src="../src/img/icon/image-dark.svg" alt="msg">
                                </div>
                            </label>
                        </div>
                        <div class="setting-bottom">
                            <div class="form__input msg_text">
                                <div class="title">留言<span class="must__fill-label">必填</span></div>
                                <textarea class="input" type="text" name="msg_text" placeholder="請輸入留言文字"></textarea>
                            </div>
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
                        <span>編輯留言</span>
                    </div>
                    <div class="close">x</div>
                </div>
                <div class="content">
                    <div class="mission">
                        <div class="submit-top">
                            <input type="file" name="upload_msg_img_update" id="upload_msg_img_update" accept=".jpg, .jpeg, .png, .svg" hidden />
                            <label for="upload_msg_img_update">
                                <div id="msg__img_area_update" class="img">
                                    <img class="msg_img" src="../src/img/icon/image-dark.svg" alt="msg">
                                </div>
                            </label>
                        </div>
                        <div class="setting-bottom">
                            <div class="form__input msg_text">
                                <div class="title">留言<span class="must__fill-label">必填</span></div>
                                <textarea class="input" type="text" id="msg_text_update" name="msg_text_update" placeholder="請輸入留言文字"></textarea>
                            </div>
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
                        <span>刪除留言</span>
                    </div>
                    <div class="close">x</div>
                </div>
                <div class="content">
                    <div class="alert">
                        確定要刪除留言嗎？
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
<script>
    function mark() {
        $('#markdownResult').remove();
        $('#mark').append(`<div id="markdownResult" class="codeCity-markdown border">${marked($('#editor').val())}</div>`);
    }

    const markResult = marked($('.mission-submit-area .mission-card-detail').html());
    $('.mission-submit-area .mission-card-detail').remove();
    $('.mission-submit-area .mission-card-content').append(`<div class="mission-card-detail codeCity-markdown">${markResult}</div>`);
</script>
<script src="../src/library/jquery.min.js"></script>
<script src="../src/library/datatables/datatables.min.js"></script>
<script src="../src/library/moment-with-locales.min.js"></script>
<script src="../src/library/daterangepicker/daterangepicker.min.js"></script>
<script src="../src/component/pop/index.js"></script>
<script src="../src/component/sideMenu/index.js"></script>
<script src="../src/common/common.js"></script>
<script src="index.js"></script>

</html>