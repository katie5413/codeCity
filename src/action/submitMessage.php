
<?php
session_start();
include('../../pdoInc.php');


if (isset($_FILES["upload_msg_img"]["name"]) || isset($_POST['msg_text'])) {
    if (isset($_FILES["upload_msg_img"]["name"])) {
        if ($_FILES["upload_msg_img"]["size"] / 1024 > 5 * 1024) {
            echo '<script>alert(\'Too Big! No more than 5MB\')</script>';
            die('<meta http-equiv="refresh" content="0; url=../../Mission/index.php?missionID=' . $_SESSION['missionID'] . '">');
        }
        $type = explode(".", $_FILES["upload_msg_img"]["name"]);
        $type =  strtolower(end($type));
        if (in_array($type, array('jpeg', 'jpg', 'png', 'svg'))) {
            $data = file_get_contents($_FILES["upload_msg_img"]["tmp_name"]); // 把整个文件读入一个字符串
            $submitImg = 'data:image/' . $type . ';base64,' . base64_encode($data);
            $submitImgType = $type;
        } else {
            $submitImg = null;
            $submitImgType = null;
        }
    } else {
        $submitImg = null;
        $submitImgType = null;
    }

    if (isset($_POST['msg_text'])) {
        $submitContent = htmlspecialchars($_POST['msg_text']);
    } else {
        $submitContent = null;
    }

    if ($_SESSION['user']['identity'] === 'teacher') {
        $isTeacher = 1;
    } else {
        $isTeacher = 0;
    }

    if($submitContent != null){
        $sth = $dbh->prepare('INSERT INTO message (img, imgType, content,ownerID, missionID, studentID, isTeacher, subMissionID) VALUES (?, ?, ?, ?, ?,?,?,?)');
        $sth->execute(array($submitImg, $submitImgType, $submitContent, $_SESSION['user']['id'], $_SESSION['missionID'], $_SESSION['homeworkOwner'], $isTeacher, $_SESSION['subMissionID']));
    }
    
    echo '<script>history.go(-1);</script>';
} else {
    die('<meta http-equiv="refresh" content="0; url=../../Mission/index.php?missionID=' . $_SESSION['missionID'] . '">');
}
