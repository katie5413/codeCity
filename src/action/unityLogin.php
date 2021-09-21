<?php
session_start();
include('../../pdoInc.php');


if (isset($_SESSION["user"]['id']) && isset($_SESSION["user"]['name']) && isset($_SESSION["user"]['email']) && isset($_SESSION["user"]['classID'])) {
    if ($_SESSION["user"]['id'] != '' && $_SESSION["user"]['name'] != '' && $_SESSION["user"]['email'] != '' && $_SESSION["user"]['classID'] != '') {
        echo $_SESSION["user"]['id'] . "," . $_SESSION["user"]['name'] . "," . $_SESSION["user"]["email"] . "," . $_SESSION["user"]["classID"];
    } else {
        echo "at lest one empty";
    }
} else {
    echo "0 results";
}
