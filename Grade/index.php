<?php
session_start();
include "../pdoInc.php";

if (!isset($_SESSION['user']['email'])) {
    // 沒登入，滾出去
    die('<meta http-equiv="refresh" content="0; url=../index.php">');
}

if ($_SESSION['user']['identity'] === 'teacher') {
    $scoreList = [];
    if ($_GET['classID']) {
        // 檢查是否為該班教師
        $getClassTeacher = $dbh->prepare('SELECT teacherID FROM class WHERE id =?');
        $getClassTeacher->execute(array($_GET['classID']));
        if ($classTeacher = $getClassTeacher->fetch(PDO::FETCH_ASSOC)) {
            // 列出該班所有的學生與作業

            $findStudent = $dbh->prepare('SELECT id, name FROM student WHERE classID=?');
            $findStudent->execute(array($_GET['classID']));
            while ($studentData = $findStudent->fetch(PDO::FETCH_ASSOC)) {
                $findMission = $dbh->prepare('SELECT id, name, endTime FROM mission WHERE classID =?');
                $findMission->execute(array($_GET['classID']));
                $missionCount = 0;
                while ($missionData = $findMission->fetch(PDO::FETCH_ASSOC)) {
                    $missionCount++;

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
                    $findHomeworkCount->execute(array($studentData['id'], $missionData['id']));

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
                        $homeworkStatus = 0;
                    } else if ($missionGoalCount > $submitHomeworkCount) {
                        // 有缺
                        $homeworkStatus = ceil($submitHomeworkScoreTotal / ($submitHomeworkCount - $waitToScore));
                    } else if ($missionGoalCount == $submitHomeworkCount) {
                        if ($submitHomeworkCount - $waitToScore == 0) {
                            $homeworkStatus = 0;
                        } else {
                            $homeworkStatus = ceil($submitHomeworkScoreTotal / ($submitHomeworkCount - $waitToScore));
                        }
                    }

                    array_push($scoreList, $homeworkStatus);
                }
            }
        }
    }
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
    <script src="../src/library/datatables/datatables.min.js"></script>
    <script src="../src/library/datatables/dataTables.scrollResize.min.js"></script>
    <script src="../src/common/common.js"></script>
    <script src="../src/component/dropBox/index.js"></script>
    <link rel="stylesheet" type="text/css" href="../src/common/common.css">
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
    <title>成績綜覽</title>
</head>

<body>
    <div id="content">
        <button id="downloadGrade" class="button-fill">下載成績單</button>
        <div class="table__container">
            <table id="studentTable" class="stripe" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>學生名稱</th>
                        <th>學生信箱</th>
                        <?php
                        for ($i = 1; $i < $missionCount + 1; $i++) {
                            echo '<th>主題' . $i . '</th>';
                        }
                        ?>
                        <th>學生金幣數</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $findStudent = $dbh->prepare('SELECT id,email,name,coins FROM student WHERE classID =?');
                    $findStudent->execute(array($_GET["classID"]));
                    $index = 0;
                    while ($studentData = $findStudent->fetch(PDO::FETCH_ASSOC)) {
                        $index++;
                        $scoreTextForTable = '';

                        for ($k = 0; $k < $missionCount; $k++) {
                            $scoreTextForTable .= '<td>' . $scoreList[$k + ($missionCount * ($index - 1))] . '</td>';
                        }

                        echo '<tr><td>' . $index . '</td><td>' . $studentData['name'] . '</td><td>' . $studentData['email'] . '</td>' . $scoreTextForTable . '<td>' . $studentData['coins'] . '</td></tr>';
                    }
                    ?>

                </tbody>
            </table>
        </div>
    </div>


    <script src="../src/library/jquery.min.js"></script>
    <script src="../src/library/datatables/datatables.min.js"></script>
    <script src="../src/library/table2excel.js"></script>

    <script src="../src/common/common.js"></script>
    <script src="index.js?v=<?php echo time(); ?>"></script>

</body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TWGQMN8" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

</html>