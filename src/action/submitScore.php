
<?php
session_start();
include('../../pdoInc.php');

// 拿到資料
if (isset($_GET["studentID"]) && isset($_GET["score"]) && isset($_SESSION["missionID"]) && isset($_GET["subMissionID"])) {
    $findTeacherEmail = $dbh->prepare('SELECT email FROM teacher WHERE id IN (SELECT teacherID FROM mission WHERE id=?)');
    $findTeacherEmail->execute(array($_SESSION["missionID"]));
    $teacherEmailData = $findTeacherEmail->fetch(PDO::FETCH_ASSOC);

    if ($findTeacherEmail->rowCount() >= 1) {
        $checkEmail = $teacherEmailData['email'];
    }

    if ($_SESSION["user"]['identity'] === 'teacher' && $_SESSION["user"]['email'] === $checkEmail) {
        $sth = $dbh->prepare('UPDATE homework SET score=? WHERE studentID=? AND missionID =? AND subMissionID =?');
        $sth->execute(array($_GET["score"], $_GET["studentID"], $_SESSION['missionID'],$_GET["subMissionID"]));
        
        //echo '<meta http-equiv="refresh" content="0; url=../../Mission/index.php?missionID=' . $_SESSION['missionID'] . '&&studentID=' . $_GET['studentID'] . '">';

    } else {
        die('<meta http-equiv="refresh" content="0; url=../../Mission/index.php?missionID=' . $_SESSION['missionID'] . '&&studentID=' . $_GET['studentID'] . '">');
    }

    echo '<meta http-equiv="refresh" content="0; url=../../Mission/index.php?missionID=' . $_SESSION['missionID'] . '&&studentID=' . $_GET['studentID'] . '">';
} else {
    die('<meta http-equiv="refresh" content="0; url=../../Mission/index.php?missionID=' . $_SESSION['missionID'] . '&&studentID=' . $_GET['studentID'] . '">');
}
