<?php
// ===========================
// HEADER FILE
// FILE NAME: header.php
// ===========================
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

    /* HEADER */

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


        <a href="departments_report.php"
            class="<?php echo basename($_SERVER['PHP_SELF']) == 'departments_report.php' ? 'active' : ''; ?>">

            Departments

        </a>

        <a href="teachers_report.php"
            class="<?php echo basename($_SERVER['PHP_SELF']) == 'teachers_report.php' ? 'active' : ''; ?>">

            Teachers

        </a>

        <a href="students_report.php"
            class="<?php echo basename($_SERVER['PHP_SELF']) == 'students_report.php' ? 'active' : ''; ?>">

            Students

        </a>

        <a href="articles_report.php"
            class="<?php echo basename($_SERVER['PHP_SELF']) == 'articles_report.php' ? 'active' : ''; ?>">

            Articles

        </a>

        <a href="books_report.php"
            class="<?php echo basename($_SERVER['PHP_SELF']) == 'books_report.php' ? 'active' : ''; ?>">

            Books

        </a>

        <a href="translatedbooks_report.php"
            class="<?php echo basename($_SERVER['PHP_SELF']) == 'translatedbooks_report.php' ? 'active' : ''; ?>">

            Translated Books

        </a>

        <a href="thesises_report.php"
            class="<?php echo basename($_SERVER['PHP_SELF']) == 'thesises_report.php' ? 'active' : ''; ?>">

            Thesises

        </a>

    </div>

    <div class="header-buttons">

        <a href="dashboard.php"
            class="header-btn">

            Dashboard

        </a>

        <a href="../logout.php"
            class="header-btn">

            Logout

        </a>

    </div>

</header>