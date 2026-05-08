<?php

if (session_status() == PHP_SESSION_NONE) {

    session_start();
}

// جلوگیری از cache

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// چک login

if (!isset($_SESSION['user'])) {

    header("Location: ../login.php");
    exit();
}
