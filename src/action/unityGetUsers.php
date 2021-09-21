<?php
session_start();
include('../../pdoInc.php');

$sth = $dbh->prepare('SELECT name,id,classID FROM student');
$sth->execute();

if ($sth->rowCount() > 0) {
    while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
        echo
        $row["id"] . "," . $row["name"] . "," . $row["classID"] . "\n";
    }
} else {
    echo "0 results";
}
