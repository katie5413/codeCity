<?php
session_start();
include('../../pdoInc.php');
if ($_SESSION['user']['identity'] == 'teacher') {
    echo 'teacher,' . $_SESSION["user"]['name'] . ',' . $_SESSION["user"]["img"];
} else if ($_SESSION['user']['identity'] == 'student') {
    if (isset($_SESSION["user"]['id']) && isset($_SESSION["user"]['name']) && isset($_SESSION["user"]['email'])  && isset($_SESSION["user"]['coins'])) {
        if ($_SESSION["user"]['id'] != '' && $_SESSION["user"]['name'] != '' && $_SESSION["user"]['email'] != '' && $_SESSION["user"]['coins'] != '') {
            echo $_SESSION["user"]['id'] . "," . $_SESSION["user"]['name'] . "," . $_SESSION["user"]["email"] . "," . $_SESSION["user"]["classID"] . "," . $_SESSION["user"]["coins"] . "," . $_SESSION["user"]["img"];
        } else {
            echo "at lest one empty";
        }
    } else {
        echo "0 results";
    }
}
