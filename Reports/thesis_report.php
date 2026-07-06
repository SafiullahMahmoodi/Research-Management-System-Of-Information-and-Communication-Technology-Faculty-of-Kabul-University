<?php

include('../auth.php');
include('../db_connection.php');

$lang = $_SESSION['lang'] ?? 'en';

// ======================
// FILTERS
// ======================

$where = " WHERE 1=1 ";

if (!empty($_GET['student'])) {
    $where .= " AND thesis.Student_ID='" . $_GET['student'] . "' ";
}

if (!empty($_GET['instructor'])) {
    $where .= " AND thesis.Instructor='" . $_GET['instructor'] . "' ";
}

if (!empty($_GET['department'])) {
    $where .= " AND thesis.Department='" . $_GET['department'] . "' ";
}

if (!empty($_GET['category'])) {
    $where .= " AND thesis.Category LIKE '%" . $_GET['category'] . "%' ";
}

if (!empty($_GET['date_from'])) {
    $where .= " AND thesis.Publish_Date >= '" . $_GET['date_from'] . "' ";
}

if (!empty($_GET['date_to'])) {
    $where .= " AND thesis.Publish_Date <= '" . $_GET['date_to'] . "' ";
}

// ======================
// QUERY
// ======================

$thesis_result = $conn->query("

SELECT thesis.*,
students.Name AS student_name,
teacher.Name AS instructor_name,
department.Name AS department_name

FROM thesis

LEFT JOIN students ON thesis.Student_ID = students.ID
LEFT JOIN teacher ON thesis.Instructor = teacher.ID
LEFT JOIN department ON thesis.Department = department.ID

$where

ORDER BY thesis.ID DESC

");

// total
$total = $conn->query("

SELECT COUNT(*) AS total
FROM thesis
LEFT JOIN students ON thesis.Student_ID = students.ID
LEFT JOIN teacher ON thesis.Instructor = teacher.ID
LEFT JOIN department ON thesis.Department = department.ID
$where

")->fetch_assoc()['total'];

// ======================
// TEXTS
// ======================

$isFa = ($lang == 'fa');

$title = $isFa ? 'راپور پایان‌نامه‌ها' : 'Thesis Report';

$th_id = $isFa ? 'آی‌دی' : 'ID';
$th_title = $isFa ? 'عنوان' : 'Title';
$th_category = $isFa ? 'کتگوری' : 'Category';
$th_student = $isFa ? 'محصل' : 'Student';
$th_instructor = $isFa ? 'استاد' : 'Instructor';
$th_dep = $isFa ? 'دیپارتمنت' : 'Department';
$th_date = $isFa ? 'تاریخ نشر' : 'Publish Date';

$btn_print = $isFa ? 'پرنت راپور' : 'Print Report';
$btn_pdf = $isFa ? 'دانلود PDF' : 'Download PDF';
$btn_filter = $isFa ? 'فیلتر' : 'Filter Report';

$totalText = $isFa ? 'تعداد کل مونوگراف ها' : 'Total Thesis';

?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $title ?></title>

    <link rel="stylesheet" href="../css/bootstrap.min.css">

    <link rel="stylesheet" href="style.css">
    <style>
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

<body dir="<?= $isFa ? 'rtl' : 'ltr'; ?>">

    <div class="no-print">
        <?php include('header.php'); ?>
    </div>

    <div class="report-container">

        <div class="report-card">

            <h2 class="report-title"><?= $title ?></h2>



            <!-- PDF BUTTON -->
            <div class="no-print mb-3 text-end">

                <a href="thesis_report_PDF.php?<?= http_build_query($_GET); ?>"
                    class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i>
                    <?= ($lang == 'fa') ? 'دانلود PDF' : 'Download PDF'; ?>
                </a>

            </div>
            <!-- FILTER CARD -->
            <div class="filter-card no-print">

                <form method="GET">

                    <div class="row">

                        <!-- Student -->
                        <div class="col-md-4">
                            <label class="form-label"><?= $isFa ? 'محصل' : 'Student'; ?></label>
                            <select name="student" class="form-control">
                                <option value=""><?= $isFa ? 'همه' : 'All'; ?></option>
                                <?php
                                $s = $conn->query("SELECT * FROM students");
                                while ($r = $s->fetch_assoc()) {
                                    $sel = (($_GET['student'] ?? '') == $r['ID']) ? 'selected' : '';
                                    echo "<option value='{$r['ID']}' $sel>{$r['Name']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Instructor -->
                        <div class="col-md-4">
                            <label class="form-label"><?= $isFa ? 'استاد' : 'Instructor'; ?></label>
                            <select name="instructor" class="form-control">
                                <option value=""><?= $isFa ? 'همه' : 'All'; ?></option>
                                <?php
                                $t = $conn->query("SELECT * FROM teacher");
                                while ($r = $t->fetch_assoc()) {
                                    $sel = (($_GET['instructor'] ?? '') == $r['ID']) ? 'selected' : '';
                                    echo "<option value='{$r['ID']}' $sel>{$r['Name']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Category -->
                        <div class="col-md-4">
                            <label class="form-label"><?= $isFa ? 'کتگوری' : 'Category'; ?></label>
                            <input type="text" name="category"
                                class="form-control"
                                value="<?= $_GET['category'] ?? '' ?>">
                        </div>

                        <!-- Department -->
                        <div class="col-md-4">
                            <label class="form-label"><?= $isFa ? 'دیپارتمنت' : 'Department'; ?></label>
                            <select name="department" class="form-control">
                                <option value=""><?= $isFa ? 'همه' : 'All'; ?></option>
                                <?php
                                $d = $conn->query("SELECT * FROM department");
                                while ($r = $d->fetch_assoc()) {
                                    $sel = (($_GET['department'] ?? '') == $r['ID']) ? 'selected' : '';
                                    echo "<option value='{$r['ID']}' $sel>{$r['Name']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Date From -->
                        <div class="col-md-4">
                            <label class="form-label"><?= $isFa ? 'از تاریخ' : 'From Date'; ?></label>
                            <input type="date" name="date_from"
                                class="form-control"
                                value="<?= $_GET['date_from'] ?? '' ?>">
                        </div>

                        <!-- Date To -->
                        <div class="col-md-4">
                            <label class="form-label"><?= $isFa ? 'تا تاریخ' : 'To Date'; ?></label>
                            <input type="date" name="date_to"
                                class="form-control"
                                value="<?= $_GET['date_to'] ?? '' ?>">
                        </div>

                        <!-- Buttons -->
                        <div class="col-12 text-center mt-3">

                            <button type="submit" class="btn btn-success px-4">
                                <?= $isFa ? 'اعمال فیلتر' : 'Apply Filter'; ?>
                            </button>

                            <a href="thesis_report.php" class="btn btn-secondary px-4">
                                <?= $isFa ? 'پاک کردن' : 'Reset'; ?>
                            </a>

                        </div>

                    </div>

                </form>

            </div>

            <!-- TABLE -->
            <table class="table table-bordered table-striped">

                <thead>
                    <tr>
                        <th><?= $th_id ?></th>
                        <th><?= $th_title ?></th>
                        <th><?= $th_category ?></th>
                        <th><?= $th_student ?></th>
                        <th><?= $th_instructor ?></th>
                        <th><?= $th_dep ?></th>
                        <th><?= $th_date ?></th>
                    </tr>
                </thead>

                <tbody>

                    <?php while ($row = $thesis_result->fetch_assoc()) { ?>

                        <tr>
                            <td><?= $row['ID'] ?></td>
                            <td><?= $row['Title'] ?></td>
                            <td><?= $row['Category'] ?></td>
                            <td><?= $row['student_name'] ?></td>
                            <td><?= $row['instructor_name'] ?></td>
                            <td><?= $row['department_name'] ?></td>
                            <td><?= $row['Publish_Date'] ?></td>
                        </tr>

                    <?php } ?>

                </tbody>

            </table>

            <h5>
                <?= $totalText ?> : <?= $total ?>
            </h5>

        </div>

    </div>

</body>

</html>