<?php

include('../auth.php');
include('../db_connection.php');

$lang = $_SESSION['lang'] ?? 'en';

// ======================
// TEXTS
// ======================

$title = ($lang == 'fa') ? 'گزارش کتاب‌های ترجمه شده' : 'Translated Books Report';
$translator_text = ($lang == 'fa') ? 'مترجم' : 'Translator';
$category_text = ($lang == 'fa') ? 'کتگوری' : 'Category';
$department_text = ($lang == 'fa') ? 'دیپارتمنت' : 'Department';
$date_from_text = ($lang == 'fa') ? 'از تاریخ' : 'Date From';
$date_to_text = ($lang == 'fa') ? 'تا تاریخ' : 'Date To';

$print_text = ($lang == 'fa') ? 'چاپ گزارش' : 'Print Report';
$pdf_text = ($lang == 'fa') ? 'دانلود PDF' : 'Download PDF';
$filter_text = ($lang == 'fa') ? 'فیلتر گزارش' : 'Filter Report';

$th_id = ($lang == 'fa') ? 'آی‌دی' : 'ID';
$th_title = ($lang == 'fa') ? 'عنوان' : 'Title';
$th_author = ($lang == 'fa') ? 'نویسنده' : 'Author';
$th_translator = ($lang == 'fa') ? 'مترجم' : 'Translator';
$th_category = ($lang == 'fa') ? 'کتگوری' : 'Category';
$th_department = ($lang == 'fa') ? 'دیپارتمنت' : 'Department';
$th_pages = ($lang == 'fa') ? 'صفحات' : 'Pages';
$th_date = ($lang == 'fa') ? 'تاریخ نشر' : 'Publish Date';

$total_text = ($lang == 'fa') ? 'کتاب‌های ترجمه شده' : 'Translated Books';

// ======================
// FILTERS
// ======================

$where = " WHERE 1=1 ";

if (!empty($_GET['translator'])) {
    $where .= " AND translated_books.translated_by='" . $_GET['translator'] . "' ";
}

if (!empty($_GET['department'])) {
    $where .= " AND translated_books.Department='" . $_GET['department'] . "' ";
}

if (!empty($_GET['category'])) {
    $where .= " AND translated_books.Category LIKE '%" . $_GET['category'] . "%' ";
}

if (!empty($_GET['date_from'])) {
    $where .= " AND translated_books.Publish_Date >= '" . $_GET['date_from'] . "' ";
}

if (!empty($_GET['date_to'])) {
    $where .= " AND translated_books.Publish_Date <= '" . $_GET['date_to'] . "' ";
}

// ======================
// QUERY
// ======================

$book_result = $conn->query("

SELECT translated_books.*,
teacher.Name AS translator_name,
department.Name AS department_name

FROM translated_books

LEFT JOIN teacher ON translated_books.translated_by = teacher.ID
LEFT JOIN department ON translated_books.Department = department.ID

$where

ORDER BY translated_books.ID DESC

");

// TOTAL

$total = $conn->query("

SELECT COUNT(*) AS total
FROM translated_books

LEFT JOIN teacher ON translated_books.translated_by = teacher.ID
LEFT JOIN department ON translated_books.Department = department.ID

$where

")->fetch_assoc()['total'];

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?></title>

    <link rel="stylesheet" href="../css/bootstrap.min.css">

    <style>
        body {
            background: #f4f6f9;
            font-family: Segoe UI;
            overflow-y: auto !important;
            height: auto !important;
        }

        .report-container {
            width: 95%;
            margin: 20px auto;
        }

        .report-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .1);
        }

        .report-title {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 3px;
        }

        .form-control {
            font-size: 13px;
            height: 34px;
            padding: 4px 8px;
        }

        .btn {
            font-size: 13px;
            padding: 5px 12px;
        }

        .table {
            font-size: 13px;
        }

        h5 {
            font-size: 15px;
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

<body>

    <div class="no-print">
        <?php include('header.php'); ?>
    </div>

    <div class="report-container">

        <div class="report-card">

            <h2 class="report-title">
                <?php echo $title; ?>
            </h2>

            <div class="mb-3 no-print">

                <button class="btn btn-success" onclick="window.print()">
                    <?php echo $print_text; ?>
                </button>

                <a href="translatedbooks_report_pdf.php?<?php echo http_build_query($_GET); ?>"
                    class="btn btn-danger">
                    <?php echo $pdf_text; ?>
                </a>

            </div>

            <!-- FILTER -->
            <form method="GET" class="row mb-3 no-print">

                <div class="col-md-3">
                    <label class="form-label"><?php echo $translator_text; ?></label>
                    <select name="translator" class="form-control">
                        <option value="">All</option>
                        <?php
                        $teacher = $conn->query("SELECT * FROM teacher");
                        while ($t = $teacher->fetch_assoc()) {
                            echo "<option value='{$t['ID']}'>{$t['Name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label"><?php echo $category_text; ?></label>
                    <input type="text" name="category" class="form-control">
                </div>

                <div class="col-md-2">
                    <label class="form-label"><?php echo $department_text; ?></label>
                    <select name="department" class="form-control">
                        <option value="">All</option>
                        <?php
                        $dep = $conn->query("SELECT * FROM department");
                        while ($d = $dep->fetch_assoc()) {
                            echo "<option value='{$d['ID']}'>{$d['Name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label"><?php echo $date_from_text; ?></label>
                    <input type="date" name="date_from" class="form-control">
                </div>

                <div class="col-md-2">
                    <label class="form-label"><?php echo $date_to_text; ?></label>
                    <input type="date" name="date_to" class="form-control">
                </div>

                <div class="col-md-12 mt-2">
                    <button class="btn btn-primary" type="submit">
                        <?php echo $filter_text; ?>
                    </button>
                </div>

            </form>

            <!-- TABLE -->
            <table class="table table-bordered table-striped">

                <thead>
                    <tr>
                        <th><?php echo $th_id; ?></th>
                        <th><?php echo $th_title; ?></th>
                        <th><?php echo $th_author; ?></th>
                        <th><?php echo $th_translator; ?></th>
                        <th><?php echo $th_category; ?></th>
                        <th><?php echo $th_department; ?></th>
                        <th><?php echo $th_pages; ?></th>
                        <th><?php echo $th_date; ?></th>
                    </tr>
                </thead>

                <tbody>

                    <?php while ($row = $book_result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['ID']; ?></td>
                            <td><?php echo $row['Title']; ?></td>
                            <td><?php echo $row['Author']; ?></td>
                            <td><?php echo $row['translator_name']; ?></td>
                            <td><?php echo $row['Category']; ?></td>
                            <td><?php echo $row['department_name']; ?></td>
                            <td><?php echo $row['Pages']; ?></td>
                            <td><?php echo $row['Publish_Date']; ?></td>
                        </tr>
                    <?php } ?>

                </tbody>

            </table>

            <h5>
                <?php echo $total_text; ?> : <?php echo $total; ?>
            </h5>

        </div>

    </div>

</body>

</html>