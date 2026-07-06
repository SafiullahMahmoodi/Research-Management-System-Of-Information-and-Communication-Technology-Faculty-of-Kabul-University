<?php

include('../auth.php');
include('../db_connection.php');

$lang = $_SESSION['lang'] ?? 'en';

// ======================
// FILTERS
// ======================

$where = " WHERE 1=1 ";

if (!empty($_GET['teacher'])) {
    $teacher = $_GET['teacher'];
    $where .= " AND articles.Teacher_ID='$teacher' ";
}

if (!empty($_GET['student'])) {
    $student = $_GET['student'];
    $where .= " AND articles.Student_ID='$student' ";
}

if (!empty($_GET['category'])) {
    $category = $_GET['category'];
    $where .= " AND articles.Category LIKE '%$category%' ";
}

if (!empty($_GET['department'])) {
    $department = $_GET['department'];
    $where .= " AND articles.Department='$department' ";
}

if (!empty($_GET['date_from'])) {
    $date_from = $_GET['date_from'];
    $where .= " AND articles.Date >= '$date_from' ";
}

if (!empty($_GET['date_to'])) {
    $date_to = $_GET['date_to'];
    $where .= " AND articles.Date <= '$date_to' ";
}

// ======================
// QUERY
// ======================

$article_result = $conn->query("

SELECT articles.*,
teacher.Name AS teacher_name,
students.Name AS student_name,
department.Name AS department_name

FROM articles

LEFT JOIN teacher
ON articles.Teacher_ID = teacher.ID

LEFT JOIN students
ON articles.Student_ID = students.ID

LEFT JOIN department
ON articles.Department = department.ID

$where

ORDER BY articles.ID DESC

");

// ======================
// TOTAL
// ======================

$total = $conn->query("SELECT COUNT(*) AS total FROM articles")
    ->fetch_assoc()['total'];

?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= ($lang == 'fa') ? 'گزارش مقالات' : 'Articles Report'; ?>
    </title>

    <link rel="stylesheet" href="../css/bootstrap.min.css">

    <link rel="stylesheet" href="style.css">

    <style>
        body {
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* ==========================
   FILTER CARD
========================== */
        .filter-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 5px 18px rgba(0, 0, 0, .08);
        }

        .filter-card .row {
            row-gap: 18px;
        }

        .filter-card .form-label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
        }

        .filter-card .form-control,
        .filter-card .form-select {
            width: 100%;
            height: 45px;
            font-size: 13px;
            border-radius: 8px;
            border: 1px solid #ced4da;
        }

        .filter-card .form-control:focus,
        .filter-card .form-select:focus {
            border-color: #0f9d58;
            box-shadow: 0 0 0 .15rem rgba(15, 157, 88, .18);
        }

        .filter-card .btn {
            min-width: 150px;
            height: 42px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
        }

        html[dir="rtl"] .filter-card {
            direction: rtl;
            text-align: right;
        }

        html[dir="ltr"] .filter-card {
            direction: ltr;
            text-align: left;
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
                <?= ($lang == 'fa') ? 'راپور مقالات' : 'Articles Report'; ?>
            </h2>


            <!-- PDF BUTTON -->
            <div class="no-print mb-3 text-end">

                <a href="articles_report_pdf.php?<?= http_build_query($_GET); ?>"
                    class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i>
                    <?= ($lang == 'fa') ? 'دانلود PDF' : 'Download PDF'; ?>
                </a>

            </div>

            <div class="filter-card no-print">

                <form method="GET" class="row g-3">

                    <div class="col-lg-4 col-md-6">
                        <label class="form-label"><?= ($lang == 'fa') ? 'استاد' : 'Teacher'; ?></label>
                        <select name="teacher" class="form-select">
                            <option value="">
                                <?= ($lang == 'fa') ? 'همه استادان' : 'All Teachers'; ?>
                            </option>
                            <?php
                            $teacher = $conn->query("SELECT * FROM teacher");
                            while ($t = $teacher->fetch_assoc()) {
                                echo "<option value='{$t['ID']}'>{$t['Name']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label class="form-label"><?= ($lang == 'fa') ? 'محصل' : 'Student'; ?></label>
                        <select name="student" class="form-select">
                            <option value="">
                                <?= ($lang == 'fa') ? 'همه محصلین' : 'All Students'; ?>
                            </option>
                            <?php
                            $student = $conn->query("SELECT * FROM students");
                            while ($s = $student->fetch_assoc()) {
                                echo "<option value='{$s['ID']}'>{$s['Name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label"><?= ($lang == 'fa') ? 'کتگوری' : 'Category'; ?></label>
                        <input type="text" name="category" class="form-control">
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label class="form-label"><?= ($lang == 'fa') ? 'دیپارتمنت' : 'Department'; ?></label>
                        <select name="department" class="form-select">
                            <option value="">
                                <?= ($lang == 'fa') ? 'همه دیپارتمنت‌ها' : 'All Departments'; ?>
                            </option>
                            <?php
                            $dep = $conn->query("SELECT * FROM department");
                            while ($d = $dep->fetch_assoc()) {
                                echo "<option value='{$d['ID']}'>{$d['Name']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label class="form-label"><?= ($lang == 'fa') ? 'تا تاریخ' : 'Date To'; ?></label>
                        <input type="date" name="date_to" class="form-control">
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label class="form-label"><?= ($lang == 'fa') ? 'از تاریخ' : 'Date From'; ?></label>
                        <input type="date" name="date_from" class="form-control">
                    </div>



                    <div class="col-12 text-center mt-3">
                        <button type="submit" class="btn btn-success px-4">
                            <?= ($lang == 'fa') ? 'اعمال فیلتر' : 'Apply Filter'; ?>
                        </button>

                        <a href="articles_report.php" class="btn btn-secondary px-4">
                            <?= ($lang == 'fa') ? 'پاک کردن' : 'Reset'; ?>
                        </a>
                    </div>

                </form>

            </div>

            <!-- TABLE -->
            <table class="table table-bordered table-striped table-scrollable">

                <thead>
                    <tr>
                        <th><?= ($lang == 'fa') ? 'آی‌دی' : 'ID'; ?></th>
                        <th><?= ($lang == 'fa') ? 'عنوان' : 'Title'; ?></th>
                        <th><?= ($lang == 'fa') ? 'کتگوری' : 'Category'; ?></th>
                        <th><?= ($lang == 'fa') ? 'استاد' : 'Teacher'; ?></th>
                        <th><?= ($lang == 'fa') ? 'محصل' : 'Student'; ?></th>
                        <th><?= ($lang == 'fa') ? 'دیپارتمنت' : 'Department'; ?></th>
                        <th><?= ($lang == 'fa') ? 'تاریخ' : 'Date'; ?></th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($row = $article_result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $row['ID']; ?></td>
                            <td><?= $row['Title']; ?></td>
                            <td><?= $row['Category']; ?></td>
                            <td><?= $row['teacher_name']; ?></td>
                            <td><?= $row['student_name']; ?></td>
                            <td><?= $row['department_name']; ?></td>
                            <td><?= $row['Date']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>

            </table>

            <!-- TOTAL -->
            <h5>
                <?= ($lang == 'fa') ? 'تعداد کل مقالات' : 'Total Articles'; ?> :
                <?= $total; ?>
            </h5>

        </div>

    </div>

</body>

</html>