<?php
session_start();
include('../../pdoInc.php');

if ($_SESSION['user']['identity'] === 'teacher' && isset($_SESSION['user']['id']) && isset($_SESSION['classID'])  && isset($_SESSION['missionID']) && isset($_POST['missionName_update']) && isset($_POST['missionPeriod_update']) && isset($_POST['missionDetail_update'])) {
    // 檢查


    // 找 classID
    $class = $_SESSION['classID'];
    // 拆 start, end
    $time = explode(" - ", $_POST['missionPeriod_update']);
    $startTime = $time[0];
    $endTime = $time[1];

    // 拿到awardID
    if (isset($_POST['imgName_update'])) {
        $findAward = $dbh->prepare('SELECT * FROM award WHERE name = ?');
        $findAward->execute(array($_POST['imgName_update']));
        $awardID = $findAward->fetch(PDO::FETCH_ASSOC);
        if ($findAward->rowCount() >= 1) {
            $awardImg = $awardID['id'];

            $updateMission = $dbh->prepare('UPDATE mission SET name = ?, startTime = ?, endTime = ?,awardID = ?, detail = ? WHERE id=? AND teacherID=?');
            $updateMission->execute(array($_POST['missionName_update'], $startTime, $endTime, (int)$awardImg, $_POST['missionDetail_update'], $_SESSION['missionID'], $_SESSION['user']['id']));
            echo '<meta http-equiv="refresh" content="0; url=../../MissionManage/index.php?missionID=' . $_SESSION['missionID'] . '&classID=' . $_SESSION['classID'] . '">';
        }
    } else {

        $updateMission = $dbh->prepare('UPDATE mission SET name = ?, startTime = ?, endTime = ?, detail = ? WHERE id=? AND teacherID=?');
        $updateMission->execute(array($_POST['missionName_update'], $startTime, $endTime, $_POST['missionDetail_update'], $_SESSION['missionID'], $_SESSION['user']['id']));
        echo '<meta http-equiv="refresh" content="0; url=../../MissionManage/index.php?missionID=' . $_SESSION['missionID'] . '&classID=' . $_SESSION['classID'] . '">';
    }


    echo '<meta http-equiv="refresh" content="0; url=../../MissionManage/index.php?missionID=' . $_SESSION['missionID'] . '&classID=' . $_SESSION['classID'] . '">';
} else {
    echo '<meta http-equiv="refresh" content="0; url=../../MissionManage/index.php?missionID=' . $_SESSION['missionID'] . '&classID=' . $_SESSION['classID'] . '">';
}
