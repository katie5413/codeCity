<?php
session_start();
include('../../pdoInc.php');

if (isset($_SESSION["user"]['classID'])) {

    $sth = $dbh->prepare('SELECT name,id,classID,img,coins FROM student WHERE classID=?');
    $sth->execute(array($_SESSION["user"]['classID']));

    if ($sth->rowCount() > 0) {
        while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
            echo
            $row["id"] . "," . $row["name"] . "," . $row["classID"] . "," . $row["img"] . ",". $row["coins"] . "\n";
        }
    } else {
        echo "0 results";
    }
} else {
    echo "0 results";
}
