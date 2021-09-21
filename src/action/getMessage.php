<?php
session_start();
include('../../pdoInc.php');
if (isset($_GET['selectID'])) {
    $_SESSION['currentMsgID'] = $_GET['selectID'];
    $findMsg = $dbh->prepare('SELECT * FROM message WHERE id = ?');
    $findMsg->execute(array($_GET['selectID']));
    if ($msgItem = $findMsg->fetch(PDO::FETCH_ASSOC)) {
        $msgData = array();
        if ($msgItem['img'] === null) {
            $submitImg = "../src/img/icon/image-dark.svg";
        } else {
            $submitImg = $msgItem['img'];
        }
        $msgData["0"] = array("id" => $msgItem['id'], "img" => $submitImg, "content" => $msgItem['content'],'full'=>$_SESSION['currentMsgID']);
        echo json_encode($msgData);
    } else {
        echo null;
        // 沒有找到資料，轉預設
    }
} else {
    echo null;
}
