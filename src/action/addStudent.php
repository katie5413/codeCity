<?php
session_start();
include('../../pdoInc.php');
if ($_SESSION['user']['identity'] === 'teacher' && isset($_POST['student'])) {

    // 找 classID
    $findClassID = $dbh->prepare('SELECT id FROM class WHERE teacherID = ?');
    $findClassID->execute(array($_SESSION['user']['id']));
    $classID = $findClassID->fetch(PDO::FETCH_ASSOC);
    if ($findClassID->rowCount() >= 1) {
        $class = $classID['id'];
    }

    // 拆 start, end
    $student = explode(" - ", $_POST['student']);
    $studentName=$student[0];
    $studentEmail=substr($student[1], 1, -1);


    $addStudent = $dbh->prepare('UPDATE student SET classID =? WHERE name=? AND email=?');
    $addStudent->execute(array($class,$studentName,$studentEmail));

    echo '<meta http-equiv="refresh" content="0; url=../../Class/index.php?classID='.$class.'">';
}else{
    echo '<meta http-equiv="refresh" content="0; url=../../Class/index.php?classID='.$class.'">';
}
