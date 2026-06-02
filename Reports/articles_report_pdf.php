<?php

include('../auth.php');
include('../db_connection.php');

require_once '../dompdf/autoload.inc.php';

use Dompdf\Dompdf;

// ======================
// FILTERS
// ======================

$where = " WHERE 1=1 ";

if (!empty($_GET['teacher'])) {

    $teacher = mysqli_real_escape_string($conn, $_GET['teacher']);

    $where .= "
    AND articles.Teacher_ID='$teacher'
    ";
}

if (!empty($_GET['student'])) {

    $student = mysqli_real_escape_string($conn, $_GET['student']);

    $where .= "
    AND articles.Student_ID='$student'
    ";
}

if (!empty($_GET['category'])) {

    $category = mysqli_real_escape_string($conn, $_GET['category']);

    $where .= "
    AND articles.Category LIKE '%$category%'
    ";
}

if (!empty($_GET['department'])) {

    $department = mysqli_real_escape_string($conn, $_GET['department']);

    $where .= "
    AND articles.Department='$department'
    ";
}

if (!empty($_GET['date_from'])) {

    $date_from = mysqli_real_escape_string($conn, $_GET['date_from']);

    $where .= "
    AND articles.Date >= '$date_from'
    ";
}

if (!empty($_GET['date_to'])) {

    $date_to = mysqli_real_escape_string($conn, $_GET['date_to']);

    $where .= "
    AND articles.Date <= '$date_to'
    ";
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
// TOTAL FILTERED RECORDS
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
    Articles Report
</div>

<div class="report-info">
    Generated Date: ' . date("Y-m-d") . '
</div>

<table border="1">

<tr>

<th>ID</th>
<th>Title</th>
<th>Category</th>
<th>Teacher</th>
<th>Student</th>
<th>Department</th>
<th>Date</th>

</tr>

';

// ======================
// HTML
// ======================

$html = '

<h2 style="text-align:center;">
Articles Report
</h2>

<table border="1"
cellspacing="0"
cellpadding="5"
width="100%">

<tr style="background:#dddddd;">

<th>ID</th>
<th>Title</th>
<th>Category</th>
<th>Teacher</th>
<th>Student</th>
<th>Department</th>
<th>Date</th>

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

<br><br>

<h3>
Total Filtered Articles : ' . $total . '
</h3>

';

// ======================
// PDF
// ======================

$dompdf = new Dompdf();

$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'landscape');

$dompdf->render();

$dompdf->stream(
    "Articles_Report.pdf",
    array("Attachment" => true)
);

exit();
