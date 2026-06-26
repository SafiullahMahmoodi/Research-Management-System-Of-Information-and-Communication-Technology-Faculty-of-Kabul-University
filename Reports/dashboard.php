<?php

session_start();

if (!isset($_SESSION['usertype'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['usertype'] == "admin") {

    header("Location: ../admin/dashboard.php");
    exit();
} else {

    header("Location: ../user/dashboard.php");
    exit();
}
