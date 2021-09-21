<?php
session_start();
include('../../pdoInc.php');

if (isset($_FILES["upload_user_img"]["name"])) {    //如果上傳頭貼
    if ($_FILES["upload_user_img"]["size"] / 1024 > 5 * 1024) {
        echo '<script>alert(\'Too Big! No more than 5MB\')</script>';
        die('<meta http-equiv="refresh" content="0; url=../../Setting/index.php">');
    }
    $type = explode(".", $_FILES["upload_user_img"]["name"]);
    $type =  strtolower(end($type));
    if (in_array($type, array('jpeg', 'jpg', 'png', 'svg'))) {
        $data = file_get_contents($_FILES["upload_user_img"]["tmp_name"]); // 把整个文件读入一个字符串
        $upload_user_img = 'data:image/' . $type . ';base64,' . base64_encode($data);

        if (isset($_SESSION['user']['email'])) {
            $sth = $dbh->prepare('SELECT email FROM student WHERE email = ?');
            $sth->execute(array($_SESSION['user']['email']));
            //如果是學生
            if ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $sth = $dbh->prepare('UPDATE student set img = ?, img_name=? WHERE email=?');
                $sth->execute(array($upload_user_img, $type, $_SESSION['user']['email']));
                $_SESSION['user']['img']=$upload_user_img;
                echo '<meta http-equiv="refresh" content="0; url=../../Setting/index.php">';
            } else {
                // 如果是老師
                $sth = $dbh->prepare('SELECT email FROM teacher WHERE email = ?');
                $sth->execute(array($_SESSION['user']['email']));
                if ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                    $sth = $dbh->prepare('UPDATE teacher set img = ?, img_name=? WHERE email=?');
                    $sth->execute(array($upload_user_img, $type, $_SESSION['user']['email']));
                    $_SESSION['user']['img']=$upload_user_img;
                    echo '<meta http-equiv="refresh" content="0; url=../../Setting/index.php">';
                } else {
                    // 沒有這個人
                    echo '<meta http-equiv="refresh" content="0; url=../../Setting/index.php">';
                }
            }
        }
    } else {
        echo '<script>alert(\'Wrong file type\')</script>';
        die('<meta http-equiv="refresh" content="0; url=../../Setting/index.php">');
    }
} else {
    echo '<script>alert(\'Fail upload\')</script>';
    die('<meta http-equiv="refresh" content="0; url=../../Setting/index.php">');
}
