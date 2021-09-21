
<?php
session_start();
include('../../pdoInc.php');


if (isset($_FILES["upload_mission_img_update"]["name"]) || isset($_POST['submit_mission_link_update'])) {


    if (isset($_FILES["upload_mission_img_update"]["name"])) {
        if ($_FILES["upload_mission_img_update"]["size"] / 1024 > 5 * 1024) {
            echo '<script>alert(\'Too Big! No more than 5MB\')</script>';
            die('<meta http-equiv="refresh" content="0; url=../../Mission/index.php?missionID=' . $_SESSION['missionID'] . '">');
        }
        $type = explode(".", $_FILES["upload_mission_img_update"]["name"]);
        $type =  strtolower(end($type));
        if (in_array($type, array('jpeg', 'jpg', 'png', 'svg'))) {
            $data = file_get_contents($_FILES["upload_mission_img_update"]["tmp_name"]); // 把整个文件读入一个字符串
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
    
    
    if (isset($_POST['submit_mission_link_update'])) {
        $submitLink = htmlspecialchars($_POST['submit_mission_link_update']);
    } else {
        $submitLink = null;
    }

    $sth = $dbh->prepare('UPDATE homework SET img =?, imgType=?, imgLink=? where studentID=? and missionID=?');
    $sth->execute(array($submitImg, $submitImgType, $submitLink, $_SESSION['user']['id'], $_SESSION['missionID']));

    echo '<meta http-equiv="refresh" content="0; url=../../Mission/index.php?missionID=' . $_SESSION['missionID'] . '">';
    
} else {
    die('<meta http-equiv="refresh" content="0; url=../../Mission/index.php?missionID=' . $_SESSION['missionID'] . '">');
}
