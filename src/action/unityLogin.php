<?php
session_start();
include('../../pdoInc.php');
/*
if (isset($_SESSION["user"]['id']) && isset($_SESSION["user"]['name']) && isset($_SESSION["user"]['email']) && isset($_SESSION["user"]['classID']) && isset($_SESSION["user"]['coins'])) {
    if ($_SESSION["user"]['id'] != '' && $_SESSION["user"]['name'] != '' && $_SESSION["user"]['email'] != '' && $_SESSION["user"]['classID'] != '' && $_SESSION["user"]['coins'] != '') {
        echo $_SESSION["user"]['id'] . "," . $_SESSION["user"]['name'] . "," . $_SESSION["user"]["email"] . "," . $_SESSION["user"]["classID"] . "," . $_SESSION["user"]["coins"];
    } else {
        echo "at lest one empty";
    }
} else {
    echo "0 results";
}
*/

if (isset($_SESSION["user"]['id']) && isset($_SESSION["user"]['name']) && isset($_SESSION["user"]['email'])  && isset($_SESSION["user"]['coins'])) {
    if ($_SESSION["user"]['id'] != '' && $_SESSION["user"]['name'] != '' && $_SESSION["user"]['email'] != '' && $_SESSION["user"]['coins'] != '') {
        echo $_SESSION["user"]['id'] . "," . $_SESSION["user"]['name'] . "," . $_SESSION["user"]["email"] . "," . $_SESSION["user"]["classID"] . "," . $_SESSION["user"]["coins"];
    } else {
        echo "at lest one empty";
    }
} else {
    echo "0 results";
}