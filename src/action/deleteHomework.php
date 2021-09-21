
<?php
session_start();
include('../../pdoInc.php');

if (isset($_SESSION['missionID']) && isset($_SESSION['user']['id'])) {

    $sth = $dbh->prepare('DELETE FROM homework WHERE studentID=? and missionID=?');
    $sth->execute(array($_SESSION['user']['id'], $_SESSION['missionID']));

    echo '<meta http-equiv="refresh" content="0; url=../../Mission/index.php?missionID=' . $_SESSION['missionID'] . '">';
} else {
    die('<meta http-equiv="refresh" content="0; url=../../Mission/index.php?missionID=' . $_SESSION['missionID'] . '">');
}
