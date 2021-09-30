<?php
session_start();
include('../../pdoInc.php');
if ($_SESSION['user']['identity'] === 'teacher' && isset($_POST['missionName']) && isset($_POST['missionPeriod']) && isset($_POST['missionDetail'])) {

    $missionName = htmlspecialchars($_POST['missionName']);
    $missionDetail = htmlspecialchars($_POST['missionDetail']);
    if ($missionName != "" && $missionDetail != '') {
        // 拿到awardID
        $findAward = $dbh->prepare('SELECT * FROM award WHERE name = ?');
        $findAward->execute(array($_POST['imgName']));
        $awardID = $findAward->fetch(PDO::FETCH_ASSOC);
        if ($findAward->rowCount() >= 1) {
            $awardImg = $awardID['id'];
        }
        // 找 classID，目前只有一個班！！
        $findClassID = $dbh->prepare('SELECT id FROM class WHERE teacherID = ?');
        $findClassID->execute(array($_SESSION['user']['id']));
        $classID = $findClassID->fetch(PDO::FETCH_ASSOC);
        if ($findClassID->rowCount() >= 1) {
            $class = $classID['id'];
        }
        // 拆 start, end
        $endTime = $_POST['missionPeriod'];

        $addMission = $dbh->prepare('INSERT INTO mission (classID ,name, endTime, teacherID, awardID, detail) VALUES (?, ?, ?, ?, ?, ?)');
        $addMission->execute(array($class, $missionName, $endTime, $_SESSION['user']['id'], $awardImg, $missionDetail));

    }

    echo '<meta http-equiv="refresh" content="0; url=../../main.php">';
}else{
    echo '<meta http-equiv="refresh" content="0; url=../../main.php">';
}
