<?php
session_start();
include('../../pdoInc.php');

if ($_SESSION['user']['identity'] === 'teacher' && isset($_SESSION['user']['id']) && isset($_GET['selectID'])  && isset($_SESSION['missionID']) && isset($_POST['missionGoal_title_update']) && isset($_POST['missionGoal_content_update'])) {
    $missionName = htmlspecialchars($_POST['missionGoal_title_update']);
    $missionDetail = htmlspecialchars($_POST['missionGoal_content_update']);
    if ($missionName != "" && $missionDetail != '') {

        //更新主題內容
        $updateMission = $dbh->prepare('UPDATE missionGoal SET title = ?, content = ? WHERE missionID=? and id=?');
        $updateMission->execute(array($missionName, $missionDetail, $_SESSION['missionID'], $_GET['selectID']));
        echo '<script>history.go(-1);</script>';
    }
} else {
    echo '<script>history.go(-1);</script>';
}
