<?php
include('../auth.php');
include('../db_connection.php');

$lang = $_SESSION['lang'] ?? 'en';
$isFa = ($lang === 'fa');

$title = $isFa ? 'راپور کتاب‌های ترجمه شده' : 'Translated Books Report';

$translator_text = $isFa ? 'مترجم' : 'Translator';
$category_text   = $isFa ? 'کتگوری' : 'Category';
$department_text = $isFa ? 'دیپارتمنت' : 'Department';
$date_from_text  = $isFa ? 'از تاریخ' : 'Date From';
$date_to_text    = $isFa ? 'تا تاریخ' : 'Date To';
$all_text        = $isFa ? 'همه' : 'All';

$th_id         = $isFa ? 'آی‌دی' : 'ID';
$th_title      = $isFa ? 'عنوان' : 'Title';
$th_author     = $isFa ? 'نویسنده' : 'Author';
$th_translator = $isFa ? 'مترجم' : 'Translator';
$th_category   = $isFa ? 'کتگوری' : 'Category';
$th_department = $isFa ? 'دیپارتمنت' : 'Department';
$th_pages      = $isFa ? 'صفحات' : 'Pages';
$th_date       = $isFa ? 'تاریخ نشر' : 'Publish Date';

$btn_print  = $isFa ? 'پرنت راپور' : 'Print Report';
$btn_pdf    = $isFa ? 'دانلود PDF' : 'Download PDF';
$btn_filter = $isFa ? 'فیلتر' : 'Filter Report';

$total_text = $isFa ? 'تعداد کل کتاب‌های ترجمه شده' : 'Total Translated Books';

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function selected($current, $expected)
{
    return ((string)$current === (string)$expected) ? 'selected' : '';
}

$filters = [
    'translator' => $_GET['translator'] ?? '',
    'department' => $_GET['department'] ?? '',
    'category' => $_GET['category'] ?? '',
    'date_from' => $_GET['date_from'] ?? '',
    'date_to' => $_GET['date_to'] ?? '',
];

$whereParts = ['1=1'];
$params = [];
$types = '';

if ($filters['translator'] !== '') {
    $whereParts[] = 'translated_books.translated_by = ?';
    $params[] = $filters['translator'];
    $types .= 'i';
}

if ($filters['department'] !== '') {
    $whereParts[] = 'translated_books.Department = ?';
    $params[] = $filters['department'];
    $types .= 'i';
}

if ($filters['category'] !== '') {
    $whereParts[] = 'translated_books.Category LIKE ?';
    $params[] = '%' . $filters['category'] . '%';
    $types .= 's';
}

if ($filters['date_from'] !== '') {
    $whereParts[] = 'translated_books.Publish_Date >= ?';
    $params[] = $filters['date_from'];
    $types .= 's';
}

if ($filters['date_to'] !== '') {
    $whereParts[] = 'translated_books.Publish_Date <= ?';
    $params[] = $filters['date_to'];
    $types .= 's';
}

$where = ' WHERE ' . implode(' AND ', $whereParts);

$bookSql = "
    SELECT translated_books.*,
           teacher.Name AS translator_name,
           department.Name AS department_name
    FROM translated_books
    LEFT JOIN teacher ON translated_books.translated_by = teacher.ID
    LEFT JOIN department ON translated_books.Department = department.ID
    $where
    ORDER BY translated_books.ID DESC
";

$bookStmt = $conn->prepare($bookSql);
if (!$bookStmt) {
    die('Query prepare failed: ' . e($conn->error));
}

if ($params) {
    $bookStmt->bind_param($types, ...$params);
}

$bookStmt->execute();
$book_result = $bookStmt->get_result();

$totalSql = "
    SELECT COUNT(*) AS total
    FROM translated_books
    LEFT JOIN teacher ON translated_books.translated_by = teacher.ID
    LEFT JOIN department ON translated_books.Department = department.ID
    $where
";

$totalStmt = $conn->prepare($totalSql);
if (!$totalStmt) {
    die('Total query prepare failed: ' . e($conn->error));
}

if ($params) {
    $totalStmt->bind_param($types, ...$params);
}

$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$total = $totalResult->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="<?= e($lang); ?>">

<head>
    <meta charset="UTF-8">
    <title><?= e($title); ?></title>
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

            <h2 class="report-title"><?= e($title); ?></h2>

            <!-- PDF BUTTON -->
            <div class="no-print mb-3 text-end">

                <a href="translatedbooks_report_PDF.php?<?= http_build_query($_GET); ?>"
                    class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i>
                    <?= ($lang == 'fa') ? 'دانلود PDF' : 'Download PDF'; ?>
                </a>

            </div>

            <!-- FILTER CARD START -->
            <div class="filter-card no-print">
                <form method="GET">

                    <div class="row">

                        <div class="col-md-3">
                            <label class="form-label"><?= e($translator_text); ?></label>
                            <select name="translator" class="form-control">
                                <option value=""><?= e($all_text); ?></option>
                                <?php
                                $teacher = $conn->query("SELECT ID, Name FROM teacher ORDER BY Name");
                                while ($t = $teacher->fetch_assoc()) {
                                    echo '<option value="' . e($t['ID']) . '" ' .
                                        selected($filters['translator'], $t['ID']) . '>' .
                                        e($t['Name']) .
                                        '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label"><?= e($category_text); ?></label>
                            <input type="text" name="category" class="form-control"
                                value="<?= e($filters['category']); ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label"><?= e($department_text); ?></label>
                            <select name="department" class="form-control">
                                <option value=""><?= e($all_text); ?></option>
                                <?php
                                $dep = $conn->query("SELECT ID, Name FROM department ORDER BY Name");
                                while ($d = $dep->fetch_assoc()) {
                                    echo '<option value="' . e($d['ID']) . '" ' .
                                        selected($filters['department'], $d['ID']) . '>' .
                                        e($d['Name']) .
                                        '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label"><?= e($date_from_text); ?></label>
                            <input type="date" name="date_from" class="form-control"
                                value="<?= e($filters['date_from']); ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label"><?= e($date_to_text); ?></label>
                            <input type="date" name="date_to" class="form-control"
                                value="<?= e($filters['date_to']); ?>">
                        </div>

                        <!-- BUTTONS -->
                        <div class="col-12 text-center mt-3">
                            <button type="submit" class="btn btn-success px-4">
                                <?= ($lang == 'fa') ? 'اعمال فیلتر' : 'Apply Filter'; ?>
                            </button>

                            <a href="translatedbooks_report.php" class="btn btn-secondary px-4">
                                <?= ($lang == 'fa') ? 'پاک کردن' : 'Reset'; ?>
                            </a>
                        </div>

                    </div>
                </form>
            </div>
            <!-- FILTER CARD END -->

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th><?= e($th_id); ?></th>
                        <th><?= e($th_title); ?></th>
                        <th><?= e($th_author); ?></th>
                        <th><?= e($th_translator); ?></th>
                        <th><?= e($th_category); ?></th>
                        <th><?= e($th_department); ?></th>
                        <th><?= e($th_pages); ?></th>
                        <th><?= e($th_date); ?></th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($row = $book_result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= e($row['ID']); ?></td>
                            <td><?= e($row['Title']); ?></td>
                            <td><?= e($row['Author']); ?></td>
                            <td><?= e($row['translator_name']); ?></td>
                            <td><?= e($row['Category']); ?></td>
                            <td><?= e($row['department_name']); ?></td>
                            <td><?= e($row['Pages']); ?></td>
                            <td><?= e($row['Publish_Date']); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <h5><?= e($total_text); ?> : <?= e($total); ?></h5>

        </div>
    </div>

</body>

</html>