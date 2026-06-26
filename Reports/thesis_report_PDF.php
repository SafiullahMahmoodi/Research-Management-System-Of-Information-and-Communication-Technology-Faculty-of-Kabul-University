<?php

include('../auth.php');
include('../db_connection.php');

require_once '../vendor/autoload.php';

use Mpdf\Mpdf;

// ======================
// LANGUAGE
// ======================

$lang = $_SESSION['lang'] ?? 'en';
$isFa = ($lang == 'fa');

$dir   = $isFa ? 'rtl' : 'ltr';
$align = $isFa ? 'right' : 'left';

// ======================
// FILTERS
// ======================

$where = " WHERE 1=1 ";

if (!empty($_GET['student'])) {
    $student = mysqli_real_escape_string($conn, $_GET['student']);
    $where .= " AND thesis.Student_ID='$student'";
}

if (!empty($_GET['instructor'])) {
    $instructor = mysqli_real_escape_string($conn, $_GET['instructor']);
    $where .= " AND thesis.Instructor='$instructor'";
}

if (!empty($_GET['department'])) {
    $department = mysqli_real_escape_string($conn, $_GET['department']);
    $where .= " AND thesis.Department='$department'";
}

if (!empty($_GET['category'])) {
    $category = mysqli_real_escape_string($conn, $_GET['category']);
    $where .= " AND thesis.Category LIKE '%$category%'";
}

if (!empty($_GET['date_from'])) {
    $date_from = mysqli_real_escape_string($conn, $_GET['date_from']);
    $where .= " AND thesis.Publish_Date >= '$date_from'";
}

if (!empty($_GET['date_to'])) {
    $date_to = mysqli_real_escape_string($conn, $_GET['date_to']);
    $where .= " AND thesis.Publish_Date <= '$date_to'";
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
// TEXTS (MULTI LANGUAGE)
// ======================

$title     = $isFa ? 'گزارش پایان‌نامه‌ها' : 'Thesis Report';
$generated = $isFa ? 'تاریخ تولید' : 'Generated Date';
$totalText = $isFa ? 'تعداد کل پایان‌نامه‌ها' : 'Total Thesis';

// Table headers
$th_id         = $isFa ? 'آی‌دی' : 'ID';
$th_title      = $isFa ? 'عنوان' : 'Title';
$th_category   = $isFa ? 'کتگوری' : 'Category';
$th_student    = $isFa ? 'محصل' : 'Student';
$th_instructor = $isFa ? 'استاد' : 'Instructor';
$th_department = $isFa ? 'دیپارتمنت' : 'Department';
$th_date       = $isFa ? 'تاریخ' : 'Date';

// ======================
// HTML
// ======================

$html = '
<style>

body{
    font-family: dejavu sans;
    direction: ' . $dir . ';
    text-align: ' . $align . ';
}

.title{
    text-align:center;
    font-size:22px;
    font-weight:bold;
    margin-bottom:10px;
}

.info{
    text-align:center;
    font-size:13px;
    margin-bottom:10px;
}

table{
    width:100%;
    border-collapse:collapse;
    font-size:12px;
}

th{
    background:#198754;
    color:#fff;
    padding:8px;
    text-align:center;
}

td{
    padding:6px;
    border:1px solid #ddd;
    text-align:center;
}

.footer{
    margin-top:15px;
    text-align:center;
    font-weight:bold;
    font-size:14px;
}

</style>

<div class="title">' . $title . '</div>

<div class="info">
' . $generated . ' : ' . date("Y-m-d") . '
</div>

<table>

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

// ======================
// ROWS
// ======================

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
    </tr>';
}

$html .= '
</table>

<div class="footer">
' . $totalText . ' : ' . $total . '
</div>
';

// ======================
// MPDF CONFIG
// ======================

$mpdf = new Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4-L'
]);

$mpdf->SetDirectionality($dir);

// بهتر شدن فارسی
$mpdf->autoScriptToLang = true;
$mpdf->autoLangToFont   = true;

// ======================
// OUTPUT
// ======================

$mpdf->WriteHTML($html);

$mpdf->Output(
    $isFa ? "گزارش_پایان‌نامه‌ها.pdf" : "Thesis_Report.pdf",
    "D"
);

exit();
