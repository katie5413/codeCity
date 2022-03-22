<?php
session_start();
include "pdoInc.php";

if (!isset($_SESSION['user']['email'])) {
    // 沒登入，滾出去
    die('<meta http-equiv="refresh" content="0; url=index.php">');
}

// 如果是老師，找 teacherID
$findTeacherID = $dbh->prepare('SELECT id, schoolID FROM  teacher WHERE email = ?');
$findTeacherID->execute(array($_SESSION['user']['email']));
$teacher = $findTeacherID->fetch(PDO::FETCH_ASSOC);
if ($findTeacherID->rowCount() >= 1) {
    $_SESSION['user']['id'] = $teacher['id'];
    $_SESSION['user']['identity'] = 'teacher';
    $_SESSION['user']['schoolID'] = $teacher['schoolID'];
}

// 如果是學生
$findStudentData = $dbh->prepare('SELECT id, classID,coins,schoolID FROM  student WHERE email = ?');
$findStudentData->execute(array($_SESSION['user']['email']));
$studentData = $findStudentData->fetch(PDO::FETCH_ASSOC);
if ($findStudentData->rowCount() >= 1) {
    $_SESSION['user']['id'] = $studentData['id'];
    $_SESSION['user']['identity'] = 'student';
    $_SESSION['user']['classID'] = $studentData['classID'];
    $_SESSION['user']['coins'] = $studentData['coins'];
    $_SESSION['user']['schoolID'] = $studentData['schoolID'];
}

?>

<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <script src="src/library/jquery/jquery.min.js"></script>
    <script src="src/library/moment-with-locales.min.js"></script>
    <script src="src/library/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="src/common/common.js"></script>
    <link rel="stylesheet" type="text/css" href="src/common/common.css">
    <link rel="stylesheet" type="text/css" href="src/library/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" type="text/css" href="src/component/missionCard/index.css">
    <link rel="stylesheet" type="text/css" href="src/component/sideMenu/index.css">
    <link rel="stylesheet" type="text/css" href="src/component/pop/index.css">
    <link rel="stylesheet" type="text/css" href="src/component/dropBox/index.css">
    <link rel="stylesheet" type="text/css" href="src/component/datePicker/index.css">
    <link rel="stylesheet" type="text/css" href="main.css?v=<?php echo time(); ?>">
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

</head>

<body>
    <div id="content">
        <div class="side-menu--container">
            <div class="side-menu">
                <img class="logo" src="src/img/3Dcity.svg" alt="logo">
                <div class="user">
                    <div class="avatar">
                        <?php
                        if ($_SESSION['user']['img'] == 1) {
                            echo '<img src="src/img/3Dcity.svg" alt="avatar" />';
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
                                <img class="icon" src="src/img/icon/home-dark.svg" alt="icon">
                                <span class="text">首頁</span>
                            </div>
                        </a>
                    </li>
                    
                    <li>
                        <a href="#">
                            <div class="item" aria-hidden="true">
                                <img class="icon" src="src/img/icon/bell-dark.svg" alt="icon">
                                <span class="text">通知</span>
                            </div>
                        </a>
                    </li>
                    -->

                    <li>
                        <a href="Game/" target="_blank">
                            <div class="item" aria-hidden="true">
                                <img class="icon" src="src/img/icon/game.svg" alt="icon">
                                <span class="text">遊戲</span>
                            </div>
                        </a>
                    </li>
                    <li class="active">
                        <a href="main.php">
                            <div class="item" aria-hidden="true">
                                <img class="icon" src="src/img/icon/mission-dark.svg" alt="icon">
                                <span class="text">任務</span>
                            </div>
                        </a>
                    </li>

                    <?php
                    if ($_SESSION['user']['identity'] === 'teacher') {
                        echo "<li>
                        <a href='Class/index.php'>
                            <div class='item' aria-hidden='true'>
                                <img class='icon' src='src/img/icon/class-dark.svg' alt='icon'>
                                <span class='text'>班級</span>
                            </div>
                        </a>
                    </li>";
                    }
                    ?>

                    <li>
                        <a href="Setting/index.php">
                            <div class="item" aria-hidden="true">
                                <img class="icon" src="src/img/icon/setting-dark.svg" alt="icon">
                                <span class="text">設定</span>
                            </div>
                        </a>
                    </li>
                </nav>

                <div class="status">
                    <a class="item" href="src/action/logout.php">
                        <img class="icon" src="src/img/icon/logout.svg" alt="icon">
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
                        主題列表
                    </h1>
                    <?php
                    if ($_SESSION['user']['identity'] === 'teacher') {
                        echo "<div class='functions'>
                        <button class='add-mission-btn button-fill'>新增</button>
                    </div>";
                    }
                    ?>

                </div>
                <div class="mission-board">
                    <div class="mission-board-function">
                        <div class="search">
                            <img src="src/img/icon/search.svg" />
                            <input class="input" type="text" onkeyup="value=value.replace(/[^\w_-.]/g,'')" name="search-mission" placeholder="篩選" />
                        </div>
                        <div class="filter">
                            <img src="src/img/icon/filter.svg" />
                            <button class="mission-board-button filter-btn all active">全部</button>
                            <div class="bar"></div>
                            <button class="mission-board-button filter-btn start">已開始</button>
                            <div class="bar"></div>
                            <button class="mission-board-button filter-btn end">已結束</button>
                        </div>
                    </div>
                    <div class="mission-board-card-area">
                        <?php
                        if ($_SESSION['user']['identity'] === 'teacher') {
                            $sth = $dbh->prepare('SELECT * FROM mission WHERE teacherID = ? ORDER BY id ASC');
                            $sth->execute(array($_SESSION['user']['id']));
                            $missionIndex = 0;

                            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                                $missionID = $row['id'];
                                $missionName = $row['name'];
                                $missionIndex++;

                                $endTime = substr($row['endTime'], 0, -3);
                                $classID = $row['classID'];

                                //  預設圖片
                                $award = 'src/img/3Dcity.svg';

                                // award 如果從後台來
                                if ($row['awardID'] !== NULL) {
                                    $findAward = $dbh->prepare('SELECT * FROM award WHERE id = ?');
                                    $findAward->execute(array($row['awardID']));
                                    $awardItem = $findAward->fetch(PDO::FETCH_ASSOC);
                                    $award = $awardItem['img'] !== NULL ? $awardItem['img'] : $awardItem['img_link'];
                                    $award = NULL ? 'src/img/3Dcity.svg' : $award;
                                }
                                $periodClass = checkPeriod($endTime);
                                $detail = $row['detail'];
                                $url = 'MissionManage/index.php?missionID=' . $missionID . '&classID=' . $classID . '';

                                echo '<a href="' . $url . '"><div class="mission-card ' . $periodClass . '">
                                <div class="mission-card-img">
                                    <img src="' . $award . '" />
                                </div>
                                <div class="mission-card-content">
                                    <div class="top">
                                        <h2 class="mission-card-title">
                                        #' . $missionIndex . ' ' . $missionName . '
                                        </h2>
                                    </div>
                                    <div class="mission-card-time">' . $endTime . '</div>
                                    <div class="mission-card-detail">' . $detail . '</div>
                                </div>
                            </div></a>';
                            }
                        } elseif ($_SESSION['user']['identity'] === 'student') {
                            $sth = $dbh->prepare('SELECT * FROM mission WHERE classID = ? ORDER BY id ASC');
                            $sth->execute(array($_SESSION['user']['classID']));
                            $missionIndex = 0;

                            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                                $missionIndex++;
                                $missionID = $row['id'];
                                $missionName = $row['name'];
                                $award = '<img src="src/img/3Dcity.svg" />';
                                $endTime = substr($row['endTime'], 0, -3);
                                $classID = $row['classID'];

                                //  預設圖片
                                $award = 'src/img/3Dcity.svg';

                                // award 如果從後台來
                                if ($row['awardID'] !== NULL) {
                                    $findAward = $dbh->prepare('SELECT * FROM award WHERE id = ?');
                                    $findAward->execute(array($row['awardID']));
                                    $awardItem = $findAward->fetch(PDO::FETCH_ASSOC);
                                    $award = $awardItem['img'] !== NULL ? $awardItem['img'] : $awardItem['img_link'];
                                    $award = NULL ? 'src/img/3Dcity.svg' : $award;
                                }

                                // 顯示課程期間狀態
                                $periodClass = checkPeriod($endTime);

                                $detail = $row['detail'];
                                $url = 'Mission/index.php?missionID=' . $missionID . '';

                                // 顯示作業繳交狀態
                                // 先獲取該主題下，所有子任務的總數
                                $findMissionGoalCount = $dbh->prepare('SELECT id FROM missionGoal WHERE missionID=?');
                                $findMissionGoalCount->execute(array($missionID));
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
                                    $findHomeworkCount->execute(array($_SESSION['user']['id'], $missionID));

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

                                        // 有交但沒有評分
                                        if ($homeworkStatus === 0) {
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
                                                $star = $i <= $switchScore ? '<img class="star ' . $i . '" src="src/img/icon/star-active.svg" />' : '<img class="star ' . $i . '" src="src/img/icon/star-disable.svg" />';
                                                $status .= $star;
                                            }
                                            $homeworkStatusText = $status;
                                        }
                                    }
                                }

                                echo '<a href="' . $url . '"><div class="mission-card ' . $periodClass . '">
                                <div class="mission-card-img">
                                    <img src="' . $award . '" />
                                </div>
                                <div class="mission-card-content">
                                    <div class="top">
                                        <h2 class="mission-card-title">
                                        #' . $missionIndex . ' ' . $missionName . '
                                        </h2>
                                        <div class="mission-card-score">
                                            ' . $homeworkStatusText . '
                                        </div>
                                    </div>
                                    <div class="mission-card-time">' . $endTime . '</div>
                                    <div class="mission-card-detail">' . $detail . '</div>
                                </div>
                            </div></a>';
                            }
                        }

                        function checkPeriod($end)
                        {
                            $end = strtotime($end);
                            $now = time();

                            $period = '';
                            if ($now > $end && $end != null) {
                                $period = 'end';
                            } else {
                                $period = 'start';
                            }

                            return $period;
                        };
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- tab end-->

        <div id="addMission" class="pop close">
            <form class="inner" action="src/action/addMission.php" method="post">
                <div class="top">
                    <div class="title">
                        <img class="header__icon" src="src/img/icon/mission-dark.svg" alt="icon">
                        <span>新增主題</span>
                    </div>
                    <div class="close">x</div>
                </div>
                <div class="content">
                    <div class="mission">
                        <div class="setting-top">
                            <div id="building_img_area" class="img">
                                <img class="mission_building" src="src/img/3Dcity.svg" alt="mission_building">
                            </div>
                            <div class="mission_detail">
                                <div class="form__input mission_title">
                                    <div class="title">主題獎勵</div>
                                    <div class="drop__container" id="selectAwardArea">
                                        <input id="selectAward" name="imgName" class="select-selected" type="text" placeholder="請選擇" autocomplete="off" value="" />
                                        <img src="src/img/icon/right-dark.svg" alt="icon" class="icon">
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
                                    <input class="input input__must_fill" name="missionName" type="text" name="mission_title" placeholder="請輸入主題名稱" value="" />
                                </div>
                                <div class="form__input">
                                    <div class="title">截止日期<span class="must__fill-label">必填</span></div>
                                    <input type="text" name="missionPeriod" class="input input__must_fill calendar" value="" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                        <div class="setting-bottom">
                            <div class="form__input mission_info">
                                <div class="title">主題說明<span class="must__fill-label">必填</span></div>
                                <div id="mark">
                                    <textarea id="editor" class="input input__must_fill" name="missionDetail" type="text" name="mission_info" placeholder="請輸入主題說明" onkeyup="mark()"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="buttons">
                    <button class="cancel button button-pink">取消</button>
                    <button type="submit" class="add-mission__confirm button button-fill">確定</button>
                </div>
            </form>
        </div>

        <div id="notification" class="pop">
            <form class="inner" action="src/action/addMission.php" method="post">
                <div class="top">
                    <div class="title">
                        <img class="header__icon" src="src/img/icon/mission-dark.svg" alt="icon">
                        <span>市長公告</span>
                    </div>
                    <div class="close">x</div>
                </div>
                <div class="content">
                    商城促銷中，快到你的城市看看吧！
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
    // 換圖片
    $('#selectAwardArea .select-items .option').on('click', function() {
        var selectAwardID = $(this).attr('value');
        $.ajax({
            type: "GET",
            url: `src/action/getAwardList.php?selectAward=${selectAwardID}`,
            success: function(data) {
                // do things
                $('#building_img_area img').remove();
                $('#building_img_area').append(data);;
            }
        })
        $('#selectAward').attr('value', $(this)[0].innerHTML);
    });
    $('.mission-board .mission-board-function .filter .mission-board-button').click(function() {
        $('.mission-board .mission-board-function .filter .mission-board-button').removeClass('active');
        $(this).addClass('active');
        var filter = this.classList[2];

        if (filter == 'all') {
            $(".mission-board-card-area .mission-card").fadeIn();
        } else if (filter == 'start') {
            $(".mission-board-card-area .mission-card").hide();
            $(".mission-board-card-area .mission-card.start").fadeIn();
        } else if (filter == 'end') {
            $(".mission-board-card-area .mission-card").hide();
            $(".mission-board-card-area .mission-card.end").fadeIn();
        }
    })

    // pop
    $('.add-mission-btn').click(function() {
        $('#addMission').removeClass('close').addClass('open');
        $('body').addClass('fixed');
    })

    $('.add-mission__confirm').click(function() {
        var allFill = true;
        $('#addMission .input__must_fill').each(function(index) {
            if ($(this).val().trim() == '') {
                alert($(this).prev().text());
                $(this).focus();
                allFill = false;
                return false;
            }
        });

        if (allFill) {
            const imgId = $('#selectAward')[0].getAttribute('select-id');


            $('#addMission').removeClass('open').addClass('close');
            $('body').removeClass('fixed');
        }

    })


    // date picker
    moment.locale('zh-TW');
    $('.calendar').daterangepicker({
        timePicker: true,
        singleDatePicker: true,
        timePicker24Hour: true,
        showDropdowns: true,
        autoApply: true,
        startDate: moment().startOf('hour'),
        locale: {
            format: 'YYYY/MM/DD HH:mm'
        }
    });
</script>
<script src="src/component/dropBox/index.js"></script>
<script src="src/library/jquery.min.js"></script>
<script src="src/library/datatables/datatables.min.js"></script>
<script src="src/library/moment-with-locales.min.js"></script>
<script src="src/library/daterangepicker/daterangepicker.min.js"></script>
<script src="src/component/pop/index.js"></script>
<script src="src/component/sideMenu/index.js"></script>
<script src="src/common/common.js"></script>

</html>