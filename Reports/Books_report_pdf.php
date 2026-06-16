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

if (!empty($_GET['author'])) {
    $author = $_GET['author'];
    $where .= " AND books.Author='$author' ";
}

if (!empty($_GET['department'])) {
    $department = $_GET['department'];
    $where .= " AND books.Department='$department' ";
}

if (!empty($_GET['category'])) {
    $category = $_GET['category'];
    $where .= " AND books.Category LIKE '%$category%' ";
}

if (!empty($_GET['date_from'])) {
    $date_from = $_GET['date_from'];
    $where .= " AND books.Publish_Date >= '$date_from' ";
}

if (!empty($_GET['date_to'])) {
    $date_to = $_GET['date_to'];
    $where .= " AND books.Publish_Date <= '$date_to' ";
}

// ======================
// QUERY
// ======================

$book_result = $conn->query("

SELECT books.*,
teacher.Name AS author_name,
department.Name AS department_name

FROM books

LEFT JOIN teacher ON books.Author = teacher.ID
LEFT JOIN department ON books.Department = department.ID

$where

ORDER BY books.ID DESC

");

$total = $conn->query("

SELECT COUNT(*) AS total
FROM books
LEFT JOIN teacher ON books.Author = teacher.ID
LEFT JOIN department ON books.Department = department.ID
$where

")->fetch_assoc()['total'];

// ======================
// TEXT VARIABLES
// ======================

$title = ($lang == 'fa') ? 'گزارش کتاب‌ها' : 'Books Report';
$generated = ($lang == 'fa') ? 'تاریخ تولید' : 'Generated Date';
$totalText = ($lang == 'fa') ? 'تعداد کل کتاب‌ها' : 'Total Books';

$th_id = ($lang == 'fa') ? 'آی‌دی' : 'ID';
$th_title = ($lang == 'fa') ? 'عنوان' : 'Title';
$th_category = ($lang == 'fa') ? 'کتگوری' : 'Category';
$th_author = ($lang == 'fa') ? 'نویسنده' : 'Author';
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
    ' . $generated . ': ' . date("Y-m-d") . '
</div>

<table border="1">

<tr>
<th>' . $th_id . '</th>
<th>' . $th_title . '</th>
<th>' . $th_category . '</th>
<th>' . $th_author . '</th>
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
        <td>' . $row['Category'] . '</td>
        <td>' . $row['author_name'] . '</td>
        <td>' . $row['department_name'] . '</td>
        <td>' . $row['Pages'] . '</td>
        <td>' . $row['Publish_Date'] . '</td>
    </tr>

    ';
}

$html .= '

</table>

<div class="footer">
    ' . $totalText . ' : ' . $total . '
</div>

';

// ======================
// PDF GENERATE
// ======================

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

$dompdf->stream(
    "Books_Report.pdf",
    array("Attachment" => true)
);

exit();
