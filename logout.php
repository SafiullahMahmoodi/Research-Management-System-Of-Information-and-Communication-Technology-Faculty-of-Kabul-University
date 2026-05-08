<?php
// logout.php

session_start();

// ======================
// حذف تمام متغیرهای سیشن
// ======================

$_SESSION = array();

// ======================
// حذف کوکی سیشن از مرورگر
// ======================

if (ini_get("session.use_cookies")) {

    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// ======================
// نابود کردن کامل سیشن
// ======================

session_destroy();

// ======================
// جلوگیری از cache
// ======================

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// ======================
// انتقال به login
// ======================

header("Location: login.php");
exit();
?>