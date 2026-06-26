<?php

include('../auth.php');
include('../db_connection.php');

require_once '../vendor/autoload.php';

use Mpdf\Mpdf;

// ======================
// LANGUAGE
// ======================

$lang = $_SESSION['lang'] ?? 'en';
$isFa = ($lang === 'fa');

$dir = $isFa ? 'rtl' : 'ltr';

// ======================
// FILTERS
// ======================

$where = " WHERE 1=1 ";

if (!empty($_GET['translator'])) {
    $translator = mysqli_real_escape_string($conn, $_GET['translator']);
    $where .= " AND translated_books.translated_by='$translator' ";
}

if (!empty($_GET['department'])) {
    $department = mysqli_real_escape_string($conn, $_GET['department']);
    $where .= " AND translated_books.Department='$department' ";
}

if (!empty($_GET['category'])) {
    $category = mysqli_real_escape_string($conn, $_GET['category']);
    $where .= " AND translated_books.Category LIKE '%$category%' ";
}

if (!empty($_GET['date_from'])) {
    $date_from = mysqli_real_escape_string($conn, $_GET['date_from']);
    $where .= " AND translated_books.Publish_Date >= '$date_from' ";
}

if (!empty($_GET['date_to'])) {
    $date_to = mysqli_real_escape_string($conn, $_GET['date_to']);
    $where .= " AND translated_books.Publish_Date <= '$date_to' ";
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

// ======================
// TEXTS
// ======================

$title      = $isFa ? 'گزارش کتاب‌های ترجمه شده' : 'Translated Books Report';
$date_text  = $isFa ? 'تاریخ تولید' : 'Generated Date';
$total_text = $isFa ? 'مجموع کتاب‌های ترجمه شده' : 'Total Translated Books';

// ======================
// COLUMN HEADERS (IMPORTANT FIX)
// ======================

$th = [
    'id'         => $isFa ? 'آی‌دی' : 'ID',
    'title'      => $isFa ? 'عنوان' : 'Title',
    'author'     => $isFa ? 'نویسنده' : 'Author',
    'translator' => $isFa ? 'مترجم' : 'Translator',
    'category'   => $isFa ? 'کتگوری' : 'Category',
    'department' => $isFa ? 'دیپارتمنت' : 'Department',
    'pages'      => $isFa ? 'صفحات' : 'Pages',
    'date'       => $isFa ? 'تاریخ نشر' : 'Date'
];

// ======================
// HTML
// ======================

$html = "
<style>

body {
    font-family: dejavusans;
    direction: $dir;
}

table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
    direction: $dir;
}

th, td {
    border: 1px solid #ddd;
    padding: 6px;
    text-align: center;
}

th {
    background: #198754;
    color: #fff;
}

.title {
    text-align: center;
    font-size: 22px;
    font-weight: bold;
    margin-bottom: 15px;
}

.info {
    text-align: center;
    font-size: 13px;
    margin-bottom: 10px;
}

.footer {
    margin-top: 15px;
    text-align: center;
    font-size: 14px;
    font-weight: bold;
}

</style>

<div class='title'>$title</div>

<div class='info'>
$date_text : " . date("Y-m-d") . "
</div>

<table>

<tr>
";

// ======================
// HEADER (RTL / LTR FIX)
// ======================

if ($isFa) {
    echo "
        <th>{$th['date']}</th>
        <th>{$th['pages']}</th>
        <th>{$th['department']}</th>
        <th>{$th['category']}</th>
        <th>{$th['translator']}</th>
        <th>{$th['author']}</th>
        <th>{$th['title']}</th>
        <th>{$th['id']}</th>
    ";
} else {
    echo "
        <th>{$th['id']}</th>
        <th>{$th['title']}</th>
        <th>{$th['author']}</th>
        <th>{$th['translator']}</th>
        <th>{$th['category']}</th>
        <th>{$th['department']}</th>
        <th>{$th['pages']}</th>
        <th>{$th['date']}</th>
    ";
}

$html .= "</tr>";

// ======================
// ROWS
// ======================

while ($row = $book_result->fetch_assoc()) {

    if ($isFa) {
        $html .= "
        <tr>
            <td>{$row['Publish_Date']}</td>
            <td>{$row['Pages']}</td>
            <td>{$row['department_name']}</td>
            <td>{$row['Category']}</td>
            <td>{$row['translator_name']}</td>
            <td>{$row['Author']}</td>
            <td>{$row['Title']}</td>
            <td>{$row['ID']}</td>
        </tr>";
    } else {
        $html .= "
        <tr>
            <td>{$row['ID']}</td>
            <td>{$row['Title']}</td>
            <td>{$row['Author']}</td>
            <td>{$row['translator_name']}</td>
            <td>{$row['Category']}</td>
            <td>{$row['department_name']}</td>
            <td>{$row['Pages']}</td>
            <td>{$row['Publish_Date']}</td>
        </tr>";
    }
}

$html .= "
</table>

<div class='footer'>
$total_text : " . $book_result->num_rows . "
</div>
";

// ======================
// MPDF (RTL FIX)
// ======================

$mpdf = new Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4-L'
]);

$mpdf->SetDirectionality($dir);
$mpdf->autoScriptToLang = true;
$mpdf->autoLangToFont = true;

$mpdf->WriteHTML($html);

$mpdf->Output(
    $isFa ? "گزارش_کتاب‌های_ترجمه_شده.pdf" : "Translated_Books_Report.pdf",
    "D"
);

exit();
