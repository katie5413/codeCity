<?php
session_start();
include('../../pdoInc.php');

if ($_SESSION['user']['identity'] === 'teacher' && isset($_SESSION['user']['id']) && isset($_SESSION['classID'])  && isset($_SESSION['missionID']) && isset($_POST['missionName_update']) && isset($_POST['missionPeriod_update']) && isset($_POST['missionDetail_update'])) {
    // 找 classID
    $class = $_SESSION['classID'];
    // end
    $endTime = $_POST['missionPeriod_update'];


    // 拿到awardID
    if (isset($_POST['imgName_update'])) {
        $findAward = $dbh->prepare('SELECT * FROM award WHERE name = ?');
        $findAward->execute(array($_POST['imgName_update']));
        $awardID = $findAward->fetch(PDO::FETCH_ASSOC);
        if ($findAward->rowCount() >= 1) {
            $awardImg = $awardID['id'];

            $updateMission = $dbh->prepare('UPDATE mission SET name = ?, endTime = ?,awardID = ?, detail = ? WHERE id=? AND teacherID=?');
            $updateMission->execute(array($_POST['missionName_update'], $endTime, (int)$awardImg, $_POST['missionDetail_update'], $_SESSION['missionID'], $_SESSION['user']['id']));
            echo '<meta http-equiv="refresh" content="0; url=../../MissionManage/index.php?missionID=' . $_SESSION['missionID'] . '&classID=' . $_SESSION['classID'] . '">';
        }
    } else {

        //更新主題內容
        $updateMission = $dbh->prepare('UPDATE mission SET name = ?, endTime = ?, detail = ? WHERE id=? AND teacherID=?');
        $updateMission->execute(array($_POST['missionName_update'], $endTime, $_POST['missionDetail_update'], $_SESSION['missionID'], $_SESSION['user']['id']));
        
        echo '<meta http-equiv="refresh" content="0; url=../../MissionManage/index.php?missionID=' . $_SESSION['missionID'] . '&classID=' . $_SESSION['classID'] . '">';
    }

    echo '<meta http-equiv="refresh" content="0; url=../../MissionManage/index.php?missionID=' . $_SESSION['missionID'] . '&classID=' . $_SESSION['classID'] . '">';
} else {
    echo '<meta http-equiv="refresh" content="0; url=../../MissionManage/index.php?missionID=' . $_SESSION['missionID'] . '&classID=' . $_SESSION['classID'] . '">';
}
