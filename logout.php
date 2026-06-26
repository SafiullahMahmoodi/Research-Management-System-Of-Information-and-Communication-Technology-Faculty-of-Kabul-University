<?php

session_start();

// ✅ اول از session یا cookie بخوان
$lang = $_SESSION['lang'] ?? $_COOKIE['lang'] ?? 'en';

// ذخیره در cookie
setcookie('lang', $lang, time() + (86400 * 30), "/");

// پاک کردن session
$_SESSION = [];

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

session_destroy();

// redirect
header("Location: login.php");
exit();
