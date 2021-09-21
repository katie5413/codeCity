<?php
session_start();
include('../../pdoInc.php');

if (isset($_GET['classID']) && $_GET['name'] !== '' && isset($_GET['name'])) {

    $findTeacher = $dbh->prepare('SELECT id from class WHERE teacherID=?');
    $findTeacher->execute(array($_SESSION['user']['id']));

    if ($findTeacher->rowCount() > 0) {
        // 是本班教師
        $newName =  htmlspecialchars($_GET['name']);
        $updateClassName = $dbh->prepare('UPDATE class SET name = ? WHERE id = ? AND teacherID=?');
        $updateClassName->execute(array($newName, $_GET['classID'], $_SESSION['user']['id']));

        echo '<meta http-equiv="refresh" content="0; url=../../Class/index.php?classID='.$_GET['classID'].'">';
    } else {
        // 非本班教師
        echo '<meta http-equiv="refresh" content="0; url=../../Class/index.php?classID='.$_GET['classID'].'">';
    }
} else {
    echo '<meta http-equiv="refresh" content="0; url=../../Class/index.php?classID='.$_GET['classID'].'">';
}
