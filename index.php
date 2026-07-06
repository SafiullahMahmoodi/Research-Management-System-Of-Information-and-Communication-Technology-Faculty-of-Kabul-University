<?php
session_start();

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
    setcookie('lang', $_GET['lang'], time() + (86400 * 30), "/");
}

// فقط از session یا cookie به ترتیب درست
$lang = $_SESSION['lang'] ?? $_COOKIE['lang'] ?? 'en';

$dir = ($lang == 'fa') ? 'rtl' : 'ltr';
?>


<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $dir ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Research Management System</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- Bootstrap Local CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <style>
        body {
            background-image: url("img/ictimage.jpeg");
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
    </style>

</head>

<body class="<?php echo ($lang == 'fa') ? 'rtl' : 'ltr'; ?>">

    <!-- Header -->

    <header class="main-header d-flex justify-content-between align-items-center">

        <div>
            <?php
            echo ($lang == 'fa')
                ? 'سیستم مدیریت تحقیقات پوهنځی تکنالوژی معلوماتی و مخابراتی'
                : 'Research Management System for Information & Communication Technology';
            ?>
        </div>
        <div>
            <a href="?lang=en" class="btn btn-light btn-sm">
                English
            </a>

            <a href="?lang=fa" class="btn btn-warning btn-sm">
                فارسی
            </a>
        </div>

    </header>

    <!-- Hero Section -->

    <section class="hero-section">

        <div class="hero-content">

            <h1 class="hero-title">
                <?php
                echo ($lang == 'fa')
                    ? 'به سیستم مدیریت تحقیقات خوش آمدید'
                    : 'Welcome to Research Management System';
                ?>
            </h1>

            <p class="hero-text">
                <?php
                echo ($lang == 'fa')
                    ? 'مدیریت تحقیقات، کاربران و اطلاعات علمی به آسانی'
                    : 'Manage Researches, Users and Academic Information Easily';
                ?>
            </p>
            <div class="d-flex flex-column flex-md-row justify-content-center align-items-center">

                <a href="login.php" class="btn btn-custom btn-signin">
                    <?php echo ($lang == 'fa') ? 'ورود' : 'Sign In'; ?>
                </a>

                <a href="signup.php" class="btn btn-custom btn-signup">
                    <?php echo ($lang == 'fa') ? 'ثبت نام' : 'Sign Up'; ?>
                </a>

            </div>
            <div class="d-flex flex-column flex-md-row justify-content-center align-items-center ">

                <a href="about.php" class="btn btn-custom btn-signin btn-info">
                    <?php echo ($lang == 'fa') ? 'درباره سیستم' : 'About System'; ?>
                </a>



            </div>

        </div>

    </section>

    <!-- Footer -->

    <footer class="main-footer">

        &copy; <?php echo date("Y"); ?>
        Information & Communication Technology Faculty of Kabul University

    </footer>

    <!-- Bootstrap Local JS -->

    <script src="css/bootstrap.bundle.min.js"></script>

</body>

</html>