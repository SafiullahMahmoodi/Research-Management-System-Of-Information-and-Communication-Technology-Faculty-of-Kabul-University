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

$title     = $isFa ? 'راپور  مونوگراف ها' : 'Thesis Report';
$generated = $isFa ? 'تاریخ تولید' : 'Generated Date';
$totalText = $isFa ? 'تعداد کل مونوگراف ها' : 'Total Thesis';

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
    font-family: dejavusans;
    direction: ' . $dir . ';
    text-align: ' . $align . ';
}

/* ===== Header ===== */

.header-table{
    width:100%;
    border:none;
    margin-bottom:15px;
}

.header-table td{
    border:none;
    vertical-align:middle;
}

.uni-title{
    text-align:center;
    font-size:20px;
    font-weight:bold;
}

.faculty-title{
    text-align:center;
    font-size:16px;
    margin-top:5px;
}

.report-title{
    text-align:center;
    font-size:18px;
    font-weight:bold;
    margin-top:8px;
}

.info{
    text-align:center;
    font-size:12px;
    margin-bottom:15px;
}

/* ===== Table ===== */

table{
    width:100%;
    border-collapse:collapse;
    font-size:12px;
}

th{
    background:#198754;
    color:#fff;
    border:1px solid #000;
    padding:8px;
    text-align:center;
    font-weight:bold;
}

td{
    border:1px solid #000;
    padding:6px;
    text-align:center;
}

.footer{
    margin-top:18px;
    text-align:center;
    font-size:15px;
    font-weight:bold;
}

/* ===== Notes ===== */

.notes-title{
    margin-top:25px;
    font-size:14px;
    font-weight:bold;
}

.notes-box{
    margin-top:8px;
    border:1px solid #000;
    height:120px;
}

</style>

<table class="header-table">

<tr>

<td width="20%" align="left">
<img src="../img/new_logo_6.png" width="80">
</td>

<td width="60%">

<div class="uni-title">
' . ($isFa ? 'پوهنتون کابل' : 'Kabul University') . '
</div>

<div class="faculty-title">
' . ($isFa
    ? 'پوهنځی تکنالوژی معلوماتی و مخابراتی'
    : 'Faculty of Information and Communication Technology') . '
</div>

<div class="report-title">
' . $title . '
</div>

</td>

<td width="20%" align="right">
<img src="../img/ict_logo.jpeg" width="80">
</td>

</tr>

</table>

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

<div class="notes-title">
' . ($isFa ? 'یادداشت:' : 'Notes:') . '
</div>

<div class="notes-box"></div>


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
    $isFa ? "راپور _مونوگراف ها.pdf" : "Thesis_Report.pdf",
    "D"
);

exit();
