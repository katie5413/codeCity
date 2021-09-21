<?php
session_start();
include('../../pdoInc.php');

if (!isset($_SESSION['user']['email']) || $_SESSION['user']['email'] === '') {
    echo '<script>alert(\'You need to set up email forst\')</script>';
    echo '<meta http-equiv="refresh" content="0; url=../../Setting/index.php">';
} else if (isset($_POST['oldPassword']) && $_POST['oldPassword'] !== '' && isset($_POST['password1']) && $_POST['password1'] !== '' && isset($_SESSION['user']['name'])) {
    $oldPassword = preg_replace("/[^A-Za-z0-9]/", '', $_POST['oldPassword']);
    $password1 = preg_replace("/[^A-Za-z0-9]/", '', $_POST['password1']);
    $password2 = preg_replace("/[^A-Za-z0-9]/", '', $_POST['password2']);

    if ($oldPassword != $_POST['oldPassword'] || $password1 != $_POST['password1'] || $password2 != $_POST['password2']) {
        echo '<script>alert(\'Wrong password\')</script>';
        echo '<meta http-equiv="refresh" content="0; url=../../Setting/index.php">';
    } else {
        // 檢查新密碼是否相同
        if ($password1 === $password2) {


            $oldPassword = hash('sha256', $oldPassword); //加密
            $password1 = hash('sha256', $password1); //加密
            $password2 = hash('sha256', $password2); //加密

            // check teacher

            $findTeacher = $dbh->prepare('SELECT * FROM teacher WHERE email = ? AND password=? AND name=?');
            $findTeacher->execute(array($_SESSION['user']['email'], $oldPassword, $_SESSION['user']['name']));
            $teacher = $findTeacher->fetch(PDO::FETCH_ASSOC);
            if ($findTeacher->rowCount() >= 1) {
                $updateTeacher = $dbh->prepare('UPDATE teacher SET password = ? WHERE email = ? AND name=?');
                $updateTeacher->execute(array($password1, $_SESSION['user']['email'], $_SESSION['user']['name']));
                echo '<script>alert(\'Success! Please login again\')</script>';
                echo '<meta http-equiv="refresh" content="0; url=../../index.php">';
            } else {
                
                $findStudent = $dbh->prepare('SELECT * FROM student WHERE email = ? AND password=? AND name=?');
                $findStudent->execute(array($_SESSION['user']['email'], $oldPassword, $_SESSION['user']['name']));
                $student = $findStudent->fetch(PDO::FETCH_ASSOC);
                if ($findStudent->rowCount() >= 1) {
                    $updateStudent = $dbh->prepare('UPDATE student SET password = ? WHERE email = ? AND name=?');
                    $updateStudent->execute(array($password1, $_SESSION['user']['email'], $_SESSION['user']['name']));
                    echo '<script>alert(\'Success! Please login again\')</script>';
                    echo '<meta http-equiv="refresh" content="0; url=../../index.php">';
                }
            }
        } else {
            echo '<script>alert(\'Verify fail\')</script>';
            echo '<meta http-equiv="refresh" content="0; url=../../Setting/index.php">';
        }
    }
}
