<?php
session_start();
include('../../pdoInc.php');
if (isset($_GET['selectAward'])) {
    $award = NULL;
    // query here
    // and you can return the result if you want to do some things cool ;)
    $findAward = $dbh->prepare('SELECT * FROM award WHERE id = ?');
    $findAward->execute(array($_GET['selectAward']));
    $awardItem = $findAward->fetch(PDO::FETCH_ASSOC);
    if($findAward->rowCount() >= 1){
        // 有上傳圖
        if($awardItem['img'] !== NULL){
            echo '<img class="mission_building" src="'. $awardItem['img'] .'" alt="'. $awardItem['name'] .'">';
        }else if($awardItem['img_link'] !== NULL){
            //沒上傳圖，但有圖片連結
            echo '<img class="mission_building" src="'. $awardItem['img_link'] .'" alt="'. $awardItem['name'] .'">';
        }else{
            // 沒上傳也沒連結
            echo '<img class="mission_building" src="src/img/3Dcity.svg" alt="mission_building">';
        }
    }else{
        // 沒有找到資料，轉預設
        echo '<img class="mission_building" src="src/img/3Dcity.svg" alt="mission_building">';
    }
}else{
    echo '<img class="mission_building" src="src/img/3Dcity.svg" alt="mission_building">';
}
