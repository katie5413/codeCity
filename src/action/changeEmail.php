<?php
session_start();
include('../../pdoInc.php');

if (isset($_POST['update_email']) && $_POST['update_email'] !== '' && isset($_SESSION['user']['name']) && isset($_SESSION['user']['id'])) {
    $email =  htmlspecialchars($_POST['update_email']);

    if ($email != $_POST['update_email']) {
        echo '<script>alert(\'Email only\')</script>';
        echo '<meta http-equiv="refresh" content="0; url=../../Setting/index.php">';
    } else {
        //檢查變更後的信項是否已使用
        $findEmail = $dbh->prepare('SELECT id from teacher WHERE email=? UNION SELECT id from teacher WHERE email=?');
        $findEmail->execute(array($email,$email));

        if($findEmail->rowCount() > 0) {
            echo '<script>alert(\'Already used\')</script>';
            echo '<meta http-equiv="refresh" content="0; url=../../Setting/index.php">';
        }

        // 檢查是否本人
        $updateTeacher = $dbh->prepare('UPDATE teacher SET email = ? WHERE id = ? AND name=?');
        $updateTeacher->execute(array($email, $_SESSION['user']['id'], $_SESSION['user']['name']));

        $updateStudent = $dbh->prepare('UPDATE student SET email = ? WHERE id = ? AND name=?');
        $updateStudent->execute(array($email, $_SESSION['user']['id'], $_SESSION['user']['name']));
        $_SESSION['user']['email'] = $email;
        echo '<meta http-equiv="refresh" content="0; url=../../Setting/index.php">';
    }
}else{
    echo '<meta http-equiv="refresh" content="0; url=../../Setting/index.php">';
}
