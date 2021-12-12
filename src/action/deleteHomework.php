
<?php
session_start();
include('../../pdoInc.php');

if (isset($_SESSION['subMissionID']) && isset($_SESSION['missionID']) && isset($_SESSION['user']['id'])) {

    $sth = $dbh->prepare('DELETE FROM homework WHERE studentID=? and missionID=? and subMissionID=?');
    $sth->execute(array($_SESSION['user']['id'], $_SESSION['missionID'],$_SESSION['subMissionID']));

    echo '<meta http-equiv="refresh" content="0; url=../../Mission/index.php?missionID=' . $_SESSION['missionID'] . '&&subMissionID=' . $_SESSION['subMissionID'] . '">';
} else {
    die('<meta http-equiv="refresh" content="0; url=../../Mission/index.php?missionID=' . $_SESSION['missionID'] . '&&subMissionID=' . $_SESSION['subMissionID'] . '">');
}
