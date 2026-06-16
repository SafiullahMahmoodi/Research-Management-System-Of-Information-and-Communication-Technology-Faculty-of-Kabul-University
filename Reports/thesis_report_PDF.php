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

if (!empty($_GET['student'])) {
    $where .= " AND thesis.Student_ID='" . $_GET['student'] . "'";
}

if (!empty($_GET['instructor'])) {
    $where .= " AND thesis.Instructor='" . $_GET['instructor'] . "'";
}

if (!empty($_GET['department'])) {
    $where .= " AND thesis.Department='" . $_GET['department'] . "'";
}

if (!empty($_GET['category'])) {
    $where .= " AND thesis.Category LIKE '%" . $_GET['category'] . "%'";
}

if (!empty($_GET['date_from'])) {
    $where .= " AND thesis.Publish_Date >= '" . $_GET['date_from'] . "'";
}

if (!empty($_GET['date_to'])) {
    $where .= " AND thesis.Publish_Date <= '" . $_GET['date_to'] . "'";
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

$total = $conn->query("

SELECT COUNT(*) AS total
FROM thesis
LEFT JOIN students ON thesis.Student_ID = students.ID
LEFT JOIN teacher ON thesis.Instructor = teacher.ID
LEFT JOIN department ON thesis.Department = department.ID

$where

")->fetch_assoc()['total'];

// ======================
// LANGUAGE
// ======================

$isFa = ($lang == 'fa');

$title = $isFa ? 'گزارش پایان‌نامه‌ها' : 'Thesis Report';
$generated = $isFa ? 'تاریخ تولید' : 'Generated Date';
$totalText = $isFa ? 'تعداد کل پایان‌نامه‌ها' : 'Total Thesis';

$th_id = $isFa ? 'آی‌دی' : 'ID';
$th_title = $isFa ? 'عنوان' : 'Title';
$th_category = $isFa ? 'کتگوری' : 'Category';
$th_student = $isFa ? 'محصل' : 'Student';
$th_instructor = $isFa ? 'استاد' : 'Instructor';
$th_department = $isFa ? 'دیپارتمنت' : 'Department';
$th_date = $isFa ? 'تاریخ' : 'Date';

// direction support
$dir = $isFa ? 'rtl' : 'ltr';

// ======================
// HTML
// ======================

$html = '

<style>

body{
    font-family: DejaVu Sans, sans-serif;
    direction: ' . $dir . ';
}

.report-title{
    text-align:center;
    font-size:22px;
    font-weight:bold;
    margin-bottom:15px;
}

.report-info{
    margin-bottom:10px;
    font-size:13px;
    text-align:center;
}

table{
    width:100%;
    border-collapse:collapse;
    font-size:12px;
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
    margin-top:15px;
    font-size:14px;
    font-weight:bold;
    text-align:center;
}

</style>

<div class="report-title">' . $title . '</div>

<div class="report-info">
' . $generated . ' : ' . date("Y-m-d") . '
</div>

<table border="1">

<tr>
<th>' . $th_id . '</th>
<th>' . $th_title . '</th>
<th>' . $th_category . '</th>
<th>' . $th_student . '</th>
<th>' . $th_instructor . '</th>
<th>' . $th_department . '</th>
<th>' . $th_date . '</th>
</tr>

';

while ($row = $thesis_result->fetch_assoc()) {

    $html .= '

<tr>
<td>' . $row['ID'] . '</td>
<td>' . $row['Title'] . '</td>
<td>' . $row['Category'] . '</td>
<td>' . $row['student_name'] . '</td>
<td>' . $row['instructor_name'] . '</td>
<td>' . $row['department_name'] . '</td>
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
// PDF OUTPUT
// ======================

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

$dompdf->stream(
    "Thesis_Report.pdf",
    array("Attachment" => true)
);

exit();
