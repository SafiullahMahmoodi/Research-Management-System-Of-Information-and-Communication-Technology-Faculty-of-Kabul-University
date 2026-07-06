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
LEFT JOIN teacher ON articles.Teacher_ID = teacher.ID
LEFT JOIN students ON articles.Student_ID = students.ID
LEFT JOIN department ON articles.Department = department.ID
$where
ORDER BY articles.ID DESC
");

$total = $conn->query("
SELECT COUNT(*) AS total
FROM articles
LEFT JOIN teacher ON articles.Teacher_ID = teacher.ID
LEFT JOIN students ON articles.Student_ID = students.ID
LEFT JOIN department ON articles.Department = department.ID
$where
")->fetch_assoc()['total'];

// ======================
// TEXTS (MULTI LANGUAGE)
// ======================

$titleText = $isFa ? 'راپور مقالات' : 'Articles Report';
$dateText  = $isFa ? 'تاریخ تولید' : 'Generated Date';
$totalText = $isFa ? 'تعداد کل مقالات' : 'Total Articles';

// Table headers
$th_id         = $isFa ? 'آی‌دی' : 'ID';
$th_title      = $isFa ? 'عنوان' : 'Title';
$th_category   = $isFa ? 'کتگوری' : 'Category';
$th_teacher    = $isFa ? 'استاد' : 'Teacher';
$th_student    = $isFa ? 'محصل' : 'Student';
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
}

</style>

<div class="title">' . $titleText . '</div>

<div class="info">
' . $dateText . ' : ' . date("Y-m-d") . '
</div>


<table style="width:100%; border:none; margin-bottom:15px;">
<tr>

<td style="width:20%; border:none; text-align:left;">
    <img src="../img/new_logo_6.png" width="90">
</td>

<td style="width:60%; border:none; text-align:center;">

    <div style="font-size:20px;font-weight:bold;">
        ' . ($isFa ? 'پوهنتون کابل' : 'Kabul University') . '
    </div>

    <div style="font-size:17px;margin-top:6px;">
        ' . ($isFa
    ? 'پوهنځی تکنالوژی معلوماتی و مخابراتی'
    : 'Faculty of Information and Communication Technology') . '
    </div>

    <div style="margin-top:8px;font-size:18px;font-weight:bold;">
        ' . $titleText . '
    </div>

</td>

<td style="width:20%; border:none; text-align:right;">
    <img src="../img/ict_logo.jpeg" width="90">
</td>

</tr>
</table>


<table>

<tr>
    <th>' . $th_id . '</th>
    <th>' . $th_title . '</th>
    <th>' . $th_category . '</th>
    <th>' . $th_teacher . '</th>
    <th>' . $th_student . '</th>
    <th>' . $th_department . '</th>
    <th>' . $th_date . '</th>
</tr>
';

// ======================
// ROWS
// ======================

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
    </tr>';
}

$html .= '
</table>


<br><br>

<div style="
font-size:15px;
font-weight:bold;
margin-bottom:8px;
">

' . ($isFa ? 'یادداشت:' : 'Notes:') . '

</div>

<div style="
border:1px solid #000;
height:120px;
border-radius:4px;
">

</div>



<div class="footer">
' . $totalText . ' : ' . $total . '
</div>
';

// ======================
// MPDF SETTINGS
// ======================

$mpdf = new Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4-L'
]);

$mpdf->SetDirectionality($dir);

// فارسی بهتر
$mpdf->autoScriptToLang = true;
$mpdf->autoLangToFont   = true;

// ======================
// OUTPUT
// ======================

$mpdf->WriteHTML($html);

$mpdf->Output(
    $isFa ? "گزارش_مقالات.pdf" : "Articles_Report.pdf",
    "D"
);

exit();
