
<?php
session_start();
include('../../pdoInc.php');

if (isset($_GET['selectID'])) {
    $findMissionData = $dbh->prepare('SELECT * FROM mission WHERE id = ? and teacherID=?');
    $findMissionData->execute(array($_SESSION['missionID'], $_SESSION['user']['id']));
    $missionData = $findMissionData->fetch(PDO::FETCH_ASSOC);

    if ($findMissionData->rowCount() < 1) {
        // 沒有這個任務或非本班教師
        echo 1;
        die('<meta http-equiv="refresh" content="0; url=../main.php">');
    }else{
        $sth = $dbh->prepare('DELETE FROM missionGoal WHERE id=? and missionID=?');
        $sth->execute(array($_GET['selectID'], $_SESSION['missionID']));
        echo '<script>history.go(-1);</script>';
    }

    echo '<script>history.go(-1);</script>';
} else {
    die('<script>history.go(-1);</script>');
}
