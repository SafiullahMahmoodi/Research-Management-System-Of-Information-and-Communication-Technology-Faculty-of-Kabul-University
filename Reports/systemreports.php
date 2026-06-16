<?php

include('../auth.php');
include('../db_connection.php');

$lang = $_SESSION['lang'] ?? 'en';

// ======================
// STATISTICS
// ======================

$users       = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$teachers    = $conn->query("SELECT COUNT(*) AS total FROM teacher")->fetch_assoc()['total'];
$students    = $conn->query("SELECT COUNT(*) AS total FROM students")->fetch_assoc()['total'];
$departments = $conn->query("SELECT COUNT(*) AS total FROM department")->fetch_assoc()['total'];
$articles    = $conn->query("SELECT COUNT(*) AS total FROM articles")->fetch_assoc()['total'];
$books       = $conn->query("SELECT COUNT(*) AS total FROM books")->fetch_assoc()['total'];
$translated  = $conn->query("SELECT COUNT(*) AS total FROM translated_books")->fetch_assoc()['total'];
$thesis      = $conn->query("SELECT COUNT(*) AS total FROM thesis")->fetch_assoc()['total'];

$total_all = $users + $teachers + $students + $departments + $articles + $books + $translated + $thesis;

?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= ($lang == 'fa') ? 'گزارش سیستم' : 'System Report'; ?>
    </title>

    <link rel="stylesheet" href="../css/bootstrap.min.css">

    <style>
        body {
            background: #f4f6f9;
            font-family: Segoe UI;
            overflow-y: auto !important;
            height: auto !important;
        }

        .report-container {
            width: 90%;
            margin: 30px auto;
        }

        .report-title {
            text-align: center;
            margin-bottom: 25px;
            font-weight: bold;
        }

        .report-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
        }

        table {
            width: 100%;
        }

        th {
            background: #198754;
            color: white;
            text-align: center;
        }

        td {
            text-align: center;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white !important;
            }

            .report-card {
                box-shadow: none !important;
                border: none !important;
            }
        }
    </style>

</head>

<body dir="<?= ($lang == 'fa') ? 'rtl' : 'ltr'; ?>">

    <div class="no-print">
        <?php include('header.php'); ?>
    </div>

    <div class="report-container">

        <div class="report-card">

            <!-- TITLE -->
            <h2 class="report-title">
                <?= ($lang == 'fa')
                    ? 'گزارش سیستم مدیریت تحقیقات'
                    : 'Research Management System Report';
                ?>
            </h2>

            <!-- BUTTONS -->
            <button class="btn btn-success" onclick="window.print()">
                <?= ($lang == 'fa') ? 'پرنت گزارش' : 'Print Report'; ?>
            </button>

            <a href="system_report_pdf.php" class="btn btn-danger">
                <?= ($lang == 'fa') ? 'دانلود PDF' : 'Download PDF'; ?>
            </a>

            <!-- TABLE -->
            <table class="table table-bordered mt-3">

                <thead>
                    <tr>
                        <th><?= ($lang == 'fa') ? 'ماژول' : 'Module'; ?></th>
                        <th><?= ($lang == 'fa') ? 'تعداد' : 'Total Records'; ?></th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <td><?= ($lang == 'fa') ? 'کاربران' : 'Users'; ?></td>
                        <td><?= $users; ?></td>
                    </tr>

                    <tr>
                        <td><?= ($lang == 'fa') ? 'دیپارتمنت‌ها' : 'Departments'; ?></td>
                        <td><?= $departments; ?></td>
                    </tr>

                    <tr>
                        <td><?= ($lang == 'fa') ? 'استادان' : 'Teachers'; ?></td>
                        <td><?= $teachers; ?></td>
                    </tr>

                    <tr>
                        <td><?= ($lang == 'fa') ? 'محصلین' : 'Students'; ?></td>
                        <td><?= $students; ?></td>
                    </tr>

                    <tr>
                        <td><?= ($lang == 'fa') ? 'مقالات' : 'Articles'; ?></td>
                        <td><?= $articles; ?></td>
                    </tr>

                    <tr>
                        <td><?= ($lang == 'fa') ? 'کتاب‌ها' : 'Books'; ?></td>
                        <td><?= $books; ?></td>
                    </tr>

                    <tr>
                        <td><?= ($lang == 'fa') ? 'کتاب‌های ترجمه شده' : 'Translated Books'; ?></td>
                        <td><?= $translated; ?></td>
                    </tr>

                    <tr>
                        <td><?= ($lang == 'fa') ? 'پایان‌نامه‌ها' : 'Thesis'; ?></td>
                        <td><?= $thesis; ?></td>
                    </tr>

                </tbody>

            </table>

            <br>

            <!-- TOTAL -->
            <h5>
                <?= ($lang == 'fa') ? 'مجموع کل سیستم' : 'Total Records in System'; ?> :
                <?= $total_all; ?>
            </h5>

        </div>

    </div>

</body>

</html>