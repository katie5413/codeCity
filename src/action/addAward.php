<?php
session_start();
include('../../pdoInc.php');
if ($_SESSION['user']['identity'] === 'teacher' && isset($_POST['award_name']) || isset($_FILES["upload_award_img"]["name"]) || isset($_POST['award_link'])) {
    $name =  htmlspecialchars($_POST['award_name']);

    if (isset($_FILES["upload_award_img"]["name"])) {
        if ($_FILES["upload_award_img"]["size"] / 1024 > 5 * 1024) {
            echo '<script>alert(\'Too Big! No more than 5MB\')</script>';
            die('<meta http-equiv="refresh" content="0; url=../../Award/index.php">');
        }
        $type = explode(".", $_FILES["upload_award_img"]["name"]);
        $type =  strtolower(end($type));
        if (in_array($type, array('jpeg', 'jpg', 'png', 'svg'))) {
            $data = file_get_contents($_FILES["upload_award_img"]["tmp_name"]); // 把整个文件读入一个字符串
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

    if (isset($_POST['award_link'])) {
        $link =  htmlspecialchars($_POST['award_link']);
    } else {
        $link = null;
    }

    $addMission = $dbh->prepare('INSERT INTO award (name, img_link, img) VALUES (?, ?, ?)');
    $addMission->execute(array($name, $link, $submitImg));

    echo '<meta http-equiv="refresh" content="0; url=../../Award/index.php">';
} else {
    die('<meta http-equiv="refresh" content="0; url=../../Award/index.php">');
}
