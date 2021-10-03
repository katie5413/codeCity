<?php
session_start();
include('../../pdoInc.php');
if ($_SESSION['user']['identity'] === 'teacher' && isset($_SESSION['user']['schoolID']) &&isset($_SESSION['user']['id'])) {

    $addClass = $dbh->prepare('INSERT INTO class (teacherID, schoolID, status) VALUES (?, ?, ?)');
    $addClass->execute(array($_SESSION['user']['id'], $_SESSION['user']['schoolID'],1));

    echo '<script>history.go(-1);</script>';
}else{
    echo '<script>history.go(-1);</script>';
}
