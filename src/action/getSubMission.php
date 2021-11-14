<?php
session_start();
include('../../pdoInc.php');
if (isset($_GET['selectID'])) {
    $_SESSION['currentSubMissionID'] = $_GET['selectID'];
    $findSubMission = $dbh->prepare('SELECT * FROM missionGoal WHERE id = ?');
    $findSubMission->execute(array($_GET['selectID']));
    if ($subMissionItem = $findSubMission->fetch(PDO::FETCH_ASSOC)) {
        $subMissionData = array();
        $subMissionData["0"] = array("id" => $subMissionItem['id'], "missionID" => $subMissionItem['missionID'], "content" => $subMissionItem['content'],'title'=>$subMissionItem['title']);
        echo json_encode($subMissionData);
    } else {
        echo null;
        // 沒有找到資料，轉預設
    }
} else {
    echo null;
}
