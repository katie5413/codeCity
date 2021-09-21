<?php
session_start();
include('../../pdoInc.php');


$sth = $dbh->prepare('SELECT missionID, studentID, score FROM homework');
$sth->execute();

if ($sth->rowCount() > 0) {
    while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
        $findaward = $dbh->prepare('SELECT awardID FROM mission WHERE id=?');
        $findaward->execute(array($row["missionID"]));

        if ($findaward->rowCount() > 0) {

            while ($award = $findaward->fetch(PDO::FETCH_ASSOC)) {

                if ($award['awardID'] == null) {
                    echo
                    '0,' . $row["studentID"] . ',' . $row["score"] . '\n';
                } else {
                    echo
                    $award['awardID'] . "," . $row["studentID"] . "," . $row["score"] . "\n";
                }
            }
        }
    }
} else {
    echo "0 results";
}
