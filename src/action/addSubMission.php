<?php
session_start();
include('../../pdoInc.php');
if ($_SESSION['user']['identity'] === 'teacher' && isset($_POST['missionGoal_title']) && isset($_POST['missionGoal_content']) && isset($_SESSION['missionID'])) {

    $missionName = htmlspecialchars($_POST['missionGoal_title']);
    $missionDetail = htmlspecialchars($_POST['missionGoal_content']);
    if ($missionName != "" && $missionDetail != '') {

        $addSubMission = $dbh->prepare('INSERT INTO missionGoal (title, content, missionID) VALUES (?, ?,?)');
        $addSubMission->execute(array($missionName, $missionDetail, $_SESSION['missionID']));
    }

    echo '<meta http-equiv="refresh" content="0; url=../../MissionManage/index.php?missionID=' . $_SESSION['missionID'] . '&classID=' . $_SESSION['classID'] . '">';
} else {
    die('<meta http-equiv="refresh" content="0; url=../../MissionManage/index.php?missionID=' . $_SESSION['missionID'] . '&classID=' . $_SESSION['classID'] . '">');
}
