<?php
session_start();
include('../../pdoInc.php');

if (isset($_SESSION['user']['id']) && isset($_SESSION['user']['email']) && isset($_SESSION['user']['identity']) && isset($_GET['name']) && $_GET['name'] != '') {

    if ($_SESSION['user']['identity'] === 'teacher') {
        $findTeacher = $dbh->prepare('SELECT id from teacher WHERE id=? and email=?');
        $findTeacher->execute(array($_SESSION['user']['id'], $_SESSION['user']['email']));

        if ($findTeacher->rowCount() > 0) {
            $newName =  htmlspecialchars($_GET['name']);

            $updateUserName = $dbh->prepare('UPDATE teacher SET name = ? WHERE id = ? AND email=?');
            $updateUserName->execute(array($newName, $_SESSION['user']['id'], $_SESSION['user']['email']));
            $_SESSION['user']['name'] = $newName;
            echo '<meta http-equiv="refresh" content="0; url=../../Setting/index.php">';
        }
    } else if ($_SESSION['user']['identity'] === 'student') {
        $findStudent = $dbh->prepare('SELECT id from student WHERE id=? and email=?');
        $findStudent->execute(array($_SESSION['user']['id'], $_SESSION['user']['email']));

        if ($findStudent->rowCount() > 0) {
            $newName =  htmlspecialchars($_GET['name']);
            $_SESSION['user']['name'] = $newName;
            $updateUserName = $dbh->prepare('UPDATE student SET name = ? WHERE id = ? AND email=?');
            $updateUserName->execute(array($newName, $_SESSION['user']['id'], $_SESSION['user']['email']));
            echo '<meta http-equiv="refresh" content="0; url=../../Setting/index.php">';
        }
    }
} else {
    echo '<meta http-equiv="refresh" content="0; url=../../Setting/index.php">';
}
