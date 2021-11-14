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

    echo '<script>history.go(-1);</script>';
} else {
    echo '<script>history.go(-1);</script>';
}
