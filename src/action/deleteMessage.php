
<?php
session_start();
include('../../pdoInc.php');

if (isset($_GET['selectID'])) {

    $sth = $dbh->prepare('DELETE FROM message WHERE id=? and ownerID=?');
    $sth->execute(array($_GET['selectID'],$_SESSION['user']['id']));

    echo '<meta http-equiv="refresh" content="0; url=../../Mission/index.php?missionID=' . $_SESSION['missionID'] . '">';
} else {
    die('<meta http-equiv="refresh" content="0; url=../../Mission/index.php?missionID=' . $_SESSION['missionID'] . '">');
}