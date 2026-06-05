<?php

include('../auth.php');
include('../db_connection.php');

require_once '../dompdf/autoload.inc.php';

use Dompdf\Dompdf;

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

$thesis_result = $conn->query("

SELECT thesis.*,
students.Name AS student_name,
teacher.Name AS instructor_name,
department.Name AS department_name

FROM thesis

LEFT JOIN students
ON thesis.Student_ID = students.ID

LEFT JOIN teacher
ON thesis.Instructor = teacher.ID

LEFT JOIN department
ON thesis.Department = department.ID

$where

ORDER BY thesis.ID DESC

");

$total = $conn->query("

SELECT COUNT(*) AS total

FROM thesis

LEFT JOIN students
ON thesis.Student_ID = students.ID

LEFT JOIN teacher
ON thesis.Instructor = teacher.ID

LEFT JOIN department
ON thesis.Department = department.ID

$where

")->fetch_assoc()['total'];

$html = '

<style>

body{
font-family:DejaVu Sans,sans-serif;
}

.report-title{
text-align:center;
font-size:24px;
font-weight:bold;
margin-bottom:20px;
}

.report-info{
margin-bottom:15px;
}

table{
width:100%;
border-collapse:collapse;
}

th{
background:#198754;
color:white;
padding:8px;
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
Thesis Report
</div>

<div class="report-info">
Generated Date : ' . date('Y-m-d') . '
</div>

<table border="1">

<tr>

<th>ID</th>
<th>Title</th>
<th>Category</th>
<th>Student</th>
<th>Instructor</th>
<th>Department</th>
<th>Date</th>

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

Total Thesis : ' . $total . '

</div>

';

$dompdf = new Dompdf();

$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'landscape');

$dompdf->render();

$dompdf->stream(
    "Thesis_Report.pdf",
    array("Attachment" => true)
);

exit();
