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

if (!empty($_GET['teacher'])) {
    $teacher = mysqli_real_escape_string($conn, $_GET['teacher']);
    $where .= " AND articles.Teacher_ID='$teacher' ";
}

if (!empty($_GET['student'])) {
    $student = mysqli_real_escape_string($conn, $_GET['student']);
    $where .= " AND articles.Student_ID='$student' ";
}

if (!empty($_GET['category'])) {
    $category = mysqli_real_escape_string($conn, $_GET['category']);
    $where .= " AND articles.Category LIKE '%$category%' ";
}

if (!empty($_GET['department'])) {
    $department = mysqli_real_escape_string($conn, $_GET['department']);
    $where .= " AND articles.Department='$department' ";
}

if (!empty($_GET['date_from'])) {
    $date_from = mysqli_real_escape_string($conn, $_GET['date_from']);
    $where .= " AND articles.Date >= '$date_from' ";
}

if (!empty($_GET['date_to'])) {
    $date_to = mysqli_real_escape_string($conn, $_GET['date_to']);
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

$total_query = $conn->query("

SELECT COUNT(*) AS total

FROM articles

LEFT JOIN teacher
ON articles.Teacher_ID = teacher.ID

LEFT JOIN students
ON articles.Student_ID = students.ID

LEFT JOIN department
ON articles.Department = department.ID

$where

");

$total = $total_query->fetch_assoc()['total'];

// ======================
// TEXTS
// ======================

$titleText = ($lang == 'fa') ? 'گزارش مقالات' : 'Articles Report';
$dateText  = ($lang == 'fa') ? 'تاریخ تولید' : 'Generated Date';
$totalText = ($lang == 'fa') ? 'تعداد کل مقالات' : 'Total Filtered Articles';

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
    ' . $titleText . '
</div>

<div class="report-info">
    ' . $dateText . ': ' . date("Y-m-d") . '
</div>

<table border="1">

<tr>

<th>' . ($lang == 'fa' ? 'آی‌دی' : 'ID') . '</th>
<th>' . ($lang == 'fa' ? 'عنوان' : 'Title') . '</th>
<th>' . ($lang == 'fa' ? 'کتگوری' : 'Category') . '</th>
<th>' . ($lang == 'fa' ? 'استاد' : 'Teacher') . '</th>
<th>' . ($lang == 'fa' ? 'محصل' : 'Student') . '</th>
<th>' . ($lang == 'fa' ? 'دیپارتمنت' : 'Department') . '</th>
<th>' . ($lang == 'fa' ? 'تاریخ' : 'Date') . '</th>

</tr>

';

while ($row = $article_result->fetch_assoc()) {

    $html .= '

    <tr>

        <td>' . $row['ID'] . '</td>
        <td>' . $row['Title'] . '</td>
        <td>' . $row['Category'] . '</td>
        <td>' . $row['teacher_name'] . '</td>
        <td>' . $row['student_name'] . '</td>
        <td>' . $row['department_name'] . '</td>
        <td>' . $row['Date'] . '</td>

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
// PDF OUTPUT
// ======================

$dompdf = new Dompdf();

$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'landscape');

$dompdf->render();

$dompdf->stream(
    ($lang == 'fa') ? "گزارش_مقالات.pdf" : "Articles_Report.pdf",
    array("Attachment" => true)
);

exit();
