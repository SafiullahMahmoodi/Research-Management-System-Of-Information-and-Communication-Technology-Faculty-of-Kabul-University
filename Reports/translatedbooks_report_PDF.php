<?php

include('../auth.php');
include('../db_connection.php');

require_once '../dompdf/autoload.inc.php';

use Dompdf\Dompdf;

$lang = $_SESSION['lang'] ?? 'en';

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

// ======================
// TEXTS (MULTI LANGUAGE)
// ======================

$title = ($lang == 'fa') ? 'گزارش کتاب‌های ترجمه شده' : 'Translated Books Report';
$date_text = ($lang == 'fa') ? 'تاریخ تولید' : 'Generated Date';
$total_text = ($lang == 'fa') ? 'کتاب‌های ترجمه شده' : 'Total Translated Books';

$th_id = ($lang == 'fa') ? 'آی‌دی' : 'ID';
$th_title = ($lang == 'fa') ? 'عنوان' : 'Title';
$th_author = ($lang == 'fa') ? 'نویسنده' : 'Author';
$th_translator = ($lang == 'fa') ? 'مترجم' : 'Translator';
$th_category = ($lang == 'fa') ? 'کتگوری' : 'Category';
$th_department = ($lang == 'fa') ? 'دیپارتمنت' : 'Department';
$th_pages = ($lang == 'fa') ? 'صفحات' : 'Pages';
$th_date = ($lang == 'fa') ? 'تاریخ نشر' : 'Publish Date';

// ======================
// HTML
// ======================

$html = '

<style>

body{
    font-family: DejaVu Sans, sans-serif;
}

.report-title{
    text-align:center;
    font-size:24px;
    font-weight:bold;
    margin-bottom:20px;
}

.report-info{
    margin-bottom:15px;
    font-size:14px;
}

table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:#198754;
    color:white;
    padding:8px;
    text-align:center;
}

td{
    padding:6px;
    text-align:center;
}

.footer{
    margin-top:20px;
    font-size:16px;
    font-weight:bold;
}

</style>

<div class="report-title">
' . $title . '
</div>

<div class="report-info">
' . $date_text . ' : ' . date("Y-m-d") . '
</div>

<table border="1">

<tr>
<th>' . $th_id . '</th>
<th>' . $th_title . '</th>
<th>' . $th_author . '</th>
<th>' . $th_translator . '</th>
<th>' . $th_category . '</th>
<th>' . $th_department . '</th>
<th>' . $th_pages . '</th>
<th>' . $th_date . '</th>
</tr>

';

while ($row = $book_result->fetch_assoc()) {

    $html .= '

    <tr>
        <td>' . $row['ID'] . '</td>
        <td>' . $row['Title'] . '</td>
        <td>' . $row['Author'] . '</td>
        <td>' . $row['translator_name'] . '</td>
        <td>' . $row['Category'] . '</td>
        <td>' . $row['department_name'] . '</td>
        <td>' . $row['Pages'] . '</td>
        <td>' . $row['Publish_Date'] . '</td>
    </tr>

    ';
}

$html .= '

</table>

<div class="footer">
' . $total_text . ' : ' . $total . '
</div>

';

// ======================
// GENERATE PDF
// ======================

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

$dompdf->stream(
    "Translated_Books_Report.pdf",
    array("Attachment" => true)
);

exit();
