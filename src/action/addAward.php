<?php
session_start();
include('../../pdoInc.php');
if ($_SESSION['user']['identity'] === 'teacher' && isset($_POST['award_name']) && isset($_FILES["upload_award_img"]["name"])) {
    $name =  htmlspecialchars($_POST['award_name']);

    // 未輸入獎勵名稱
    if($name == ''){
        echo '<script>alert(\'Must fill award name\')</script>';
        die('<meta http-equiv="refresh" content="0; url=../../Award/index.php">');
    }

    // 未上傳圖片
    if ($_FILES["upload_award_img"]["name"] == '') {
        echo '<script>alert(\'Must upload award Img\')</script>';
        die('<meta http-equiv="refresh" content="0; url=../../Award/index.php">');
    } else {
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
    }

    $addMission = $dbh->prepare('INSERT INTO award (name img) VALUES (?, ?)');
    $addMission->execute(array($name, $submitImg));

    echo '<meta http-equiv="refresh" content="0; url=../../Award/index.php">';
} else {
    die('<meta http-equiv="refresh" content="0; url=../../Award/index.php">');
}
