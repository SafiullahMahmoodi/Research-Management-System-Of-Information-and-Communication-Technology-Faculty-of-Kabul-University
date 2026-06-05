<?php

include('../auth.php');
include('../db_connection.php');

require_once '../dompdf/autoload.inc.php';

use Dompdf\Dompdf;

// ======================
// FILTERS
// ======================

$where = " WHERE 1=1 ";

if (!empty($_GET['translator'])) {

    $translator = mysqli_real_escape_string($conn, $_GET['translator']);

    $where .= "
    AND translated_books.translated_by='$translator'
    ";
}

if (!empty($_GET['department'])) {

    $department = mysqli_real_escape_string($conn, $_GET['department']);

    $where .= "
    AND translated_books.Department='$department'
    ";
}

if (!empty($_GET['category'])) {

    $category = mysqli_real_escape_string($conn, $_GET['category']);

    $where .= "
    AND translated_books.Category LIKE '%$category%'
    ";
}

if (!empty($_GET['date_from'])) {

    $date_from = mysqli_real_escape_string($conn, $_GET['date_from']);

    $where .= "
    AND translated_books.Publish_Date >= '$date_from'
    ";
}

if (!empty($_GET['date_to'])) {

    $date_to = mysqli_real_escape_string($conn, $_GET['date_to']);

    $where .= "
    AND translated_books.Publish_Date <= '$date_to'
    ";
}

// ======================
// QUERY
// ======================

$book_result = $conn->query("

SELECT translated_books.*,
teacher.Name AS translator_name,
department.Name AS department_name

FROM translated_books

LEFT JOIN teacher
ON translated_books.translated_by = teacher.ID

LEFT JOIN department
ON translated_books.Department = department.ID

$where

ORDER BY translated_books.ID DESC

");

// ======================
// TOTAL
// ======================

$total = $conn->query("

SELECT COUNT(*) AS total

FROM translated_books

LEFT JOIN teacher
ON translated_books.translated_by = teacher.ID

LEFT JOIN department
ON translated_books.Department = department.ID

$where

")->fetch_assoc()['total'];

// ======================
// PDF DESIGN
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
Translated Books Report
</div>

<div class="report-info">
Generated Date : ' . date("Y-m-d") . '
</div>

<table border="1">

<tr>

<th>ID</th>
<th>Title</th>
<th>Author</th>
<th>Translator</th>
<th>Category</th>
<th>Department</th>
<th>Pages</th>
<th>Publish Date</th>

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

Total Translated Books : ' . $total . '

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
