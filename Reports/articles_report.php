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

    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white !important;
            }
        }

        body {
            background: #f4f6f9;
            font-family: Segoe UI;
        }

        .report-container {
            width: 95%;
            margin: 20px auto;
        }

        .report-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
        }

        .report-title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .form-label {
            font-size: 13px;
        }

        .table {
            font-size: 13px;
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
                <?= ($lang == 'fa') ? 'گزارش مقالات' : 'Articles Report'; ?>
            </h2>

            <!-- BUTTONS -->
            <div class="no-print mb-3">

                <button class="btn btn-success" onclick="window.print()">
                    <?= ($lang == 'fa') ? 'پرنت گزارش' : 'Print Report'; ?>
                </button>

                <a href="articles_report_pdf.php?<?= http_build_query($_GET); ?>"
                    class="btn btn-danger">
                    <?= ($lang == 'fa') ? 'دانلود PDF' : 'Download PDF'; ?>
                </a>

            </div>

            <!-- FILTER FORM -->
            <form method="GET" class="row mb-3 no-print">

                <!-- Teacher -->
                <div class="col-md-2">
                    <label class="form-label">
                        <?= ($lang == 'fa') ? 'استاد' : 'Teacher'; ?>
                    </label>
                    <select name="teacher" class="form-control">
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

                <!-- Student -->
                <div class="col-md-2">
                    <label class="form-label">
                        <?= ($lang == 'fa') ? 'محصل' : 'Student'; ?>
                    </label>
                    <select name="student" class="form-control">
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

                <!-- Category -->
                <div class="col-md-2">
                    <label class="form-label">
                        <?= ($lang == 'fa') ? 'کتگوری' : 'Category'; ?>
                    </label>
                    <input type="text" name="category" class="form-control"
                        placeholder="<?= ($lang == 'fa') ? 'کتگوری' : 'Category'; ?>">
                </div>

                <!-- Department -->
                <div class="col-md-2">
                    <label class="form-label">
                        <?= ($lang == 'fa') ? 'دیپارتمنت' : 'Department'; ?>
                    </label>
                    <select name="department" class="form-control">
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

                <!-- Date From -->
                <div class="col-md-2">
                    <label class="form-label">
                        <?= ($lang == 'fa') ? 'از تاریخ' : 'Date From'; ?>
                    </label>
                    <input type="date" name="date_from" class="form-control">
                </div>

                <!-- Date To -->
                <div class="col-md-2">
                    <label class="form-label">
                        <?= ($lang == 'fa') ? 'تا تاریخ' : 'Date To'; ?>
                    </label>
                    <input type="date" name="date_to" class="form-control">
                </div>

                <div class="col-md-12 mt-2">
                    <button type="submit" class="btn btn-primary">
                        <?= ($lang == 'fa') ? 'فیلتر گزارش' : 'Filter Report'; ?>
                    </button>
                </div>

            </form>

            <!-- TABLE -->
            <table class="table table-bordered table-striped">

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