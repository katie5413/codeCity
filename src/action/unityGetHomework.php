<?php
session_start();
include('../../pdoInc.php');

// 列出所有作業的 任務 ID 、 學生 ID 、分數
$sth = $dbh->prepare('SELECT missionID, studentID, score FROM homework');
$sth->execute();

if ($sth->rowCount() > 0) {
    while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
        
        // 找出每個主題作業的子任務作業
        $findSub = $dbh->prepare('SELECT AVG(score) FROM homework WHERE missionID=? and studentID=?');
        $findSub->execute(array($row["missionID"],$row["studentID"]));
        $subScore = $findSub->fetch(PDO::FETCH_ASSOC);
        $homeworkScore = ceil($subScore['AVG(score)']);

        // 找出每個任務的獎勵
        $findaward = $dbh->prepare('SELECT awardID FROM mission WHERE id=?');
        $findaward->execute(array($row["missionID"]));

        if ($findaward->rowCount() > 0) {

            while ($award = $findaward->fetch(PDO::FETCH_ASSOC)) {

                if ($award['awardID'] == null) {
                    echo
                    '0,' . $row["studentID"] . ',' . $homeworkScore . '\n';
                } else {
                    echo
                    $award['awardID'] . "," . $row["studentID"] . "," . $homeworkScore . "\n";
                }
            }
        }
    }
} else {
    echo "0 results";
}
