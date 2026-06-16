<?php
$lang = $_SESSION['lang'] ?? 'en';
?>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: #eef2f7;
        font-family: Segoe UI;
        overflow: hidden;
    }

    .main-header {
        background: #0f9d58;
        height: 65px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 22px;
    }

    .header-menu {
        display: flex;
        gap: 18px;
    }

    .header-menu a {
        color: #d1fae5;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        padding: 8px 14px;
        border-radius: 8px;
        transition: 0.3s;
    }

    .header-menu a:hover {
        background: white;
        color: #0f9d58;
    }

    .header-menu a.active {
        background: white;
        color: #0f9d58;
        font-weight: 700;
    }

    .header-buttons {
        display: flex;
        gap: 10px;
    }

    .header-btn {
        background: white;
        color: #0f9d58;
        padding: 7px 16px;
        border-radius: 10px;
        text-decoration: none;
        font-size: 12px;
        font-weight: 700;
    }

    @media(max-width:992px) {
        body {
            overflow: auto;
        }

        .main-header {
            flex-direction: column;
            height: auto;
            padding: 15px;
        }

        .header-menu {
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 12px;
        }
    }
</style>

<header class="main-header">

    <div class="header-menu">

        <a href="systemreports.php"
            class="<?= basename($_SERVER['PHP_SELF']) == 'systemreports.php' ? 'active' : ''; ?>">

            <?= ($lang == 'fa') ? 'گزارش سیستم' : 'System Report'; ?>

        </a>

        <a href="articles_report.php"
            class="<?= basename($_SERVER['PHP_SELF']) == 'articles_report.php' ? 'active' : ''; ?>">

            <?= ($lang == 'fa') ? 'گزارش مقالات' : 'Articles Report'; ?>

        </a>

        <a href="books_report.php"
            class="<?= basename($_SERVER['PHP_SELF']) == 'books_report.php' ? 'active' : ''; ?>">

            <?= ($lang == 'fa') ? 'گزارش کتاب‌ها' : 'Books Report'; ?>

        </a>

        <a href="translatedbooks_report.php"
            class="<?= basename($_SERVER['PHP_SELF']) == 'translatedbooks_report.php' ? 'active' : ''; ?>">

            <?= ($lang == 'fa') ? 'کتاب‌های ترجمه شده' : 'Translated Books Report'; ?>

        </a>

        <a href="thesis_report.php"
            class="<?= basename($_SERVER['PHP_SELF']) == 'thesis_report.php' ? 'active' : ''; ?>">

            <?= ($lang == 'fa') ? 'پایان‌نامه‌ها' : 'Thesis Report'; ?>

        </a>

    </div>

    <div class="header-buttons">

        <a href="../admin/dashboard.php" class="header-btn">
            <?= ($lang == 'fa') ? 'داشبورد' : 'Dashboard'; ?>
        </a>

        <a href="../logout.php" class="header-btn">
            <?= ($lang == 'fa') ? 'خروج' : 'Logout'; ?>
        </a>

    </div>

</header>