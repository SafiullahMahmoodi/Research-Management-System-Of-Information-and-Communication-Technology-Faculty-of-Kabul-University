<?php
include('../auth.php');
include('../db_connection.php');

$lang = $_SESSION['lang'] ?? 'en';
$isFa = ($lang === 'fa');

$title = $isFa ? 'گزارش کتاب‌های ترجمه شده' : 'Translated Books Report';

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

$btn_print  = $isFa ? 'پرنت گزارش' : 'Print Report';
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
</head>

<body dir="<?= $isFa ? 'rtl' : 'ltr'; ?>">

    <div class="no-print">
        <?php include('header.php'); ?>
    </div>

    <div class="report-container">
        <div class="report-card">

            <h2 class="report-title"><?= e($title); ?></h2>

            <div class="mb-3 no-print">
                <button class="btn btn-success" onclick="window.print()">
                    <?= e($btn_print); ?>
                </button>

                <a href="translatedbooks_report_pdf.php?<?= e(http_build_query($_GET)); ?>" class="btn btn-danger">
                    <?= e($btn_pdf); ?>
                </a>
            </div>

            <form method="GET" class="row mb-3 no-print">
                <div class="col-md-3">
                    <label class="form-label"><?= e($translator_text); ?></label>
                    <select name="translator" class="form-control">
                        <option value=""><?= e($all_text); ?></option>
                        <?php
                        $teacher = $conn->query("SELECT ID, Name FROM teacher ORDER BY Name");
                        while ($t = $teacher->fetch_assoc()) {
                            echo '<option value="' . e($t['ID']) . '" ' . selected($filters['translator'], $t['ID']) . '>' . e($t['Name']) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label"><?= e($category_text); ?></label>
                    <input type="text" name="category" class="form-control" value="<?= e($filters['category']); ?>">
                </div>

                <div class="col-md-2">
                    <label class="form-label"><?= e($department_text); ?></label>
                    <select name="department" class="form-control">
                        <option value=""><?= e($all_text); ?></option>
                        <?php
                        $dep = $conn->query("SELECT ID, Name FROM department ORDER BY Name");
                        while ($d = $dep->fetch_assoc()) {
                            echo '<option value="' . e($d['ID']) . '" ' . selected($filters['department'], $d['ID']) . '>' . e($d['Name']) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label"><?= e($date_from_text); ?></label>
                    <input type="date" name="date_from" class="form-control" value="<?= e($filters['date_from']); ?>">
                </div>

                <div class="col-md-2">
                    <label class="form-label"><?= e($date_to_text); ?></label>
                    <input type="date" name="date_to" class="form-control" value="<?= e($filters['date_to']); ?>">
                </div>

                <div class="col-md-12 mt-2">
                    <button class="btn btn-primary" type="submit">
                        <?= e($btn_filter); ?>
                    </button>
                </div>
            </form>

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