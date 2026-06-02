<?php

include('../auth.php');
include('../db_connection.php');

require_once '../dompdf/autoload.inc.php';

use Dompdf\Dompdf;

// ======================
// FILTERS
// ======================

$where = " WHERE 1=1 ";

if (!empty($_GET['author'])) {

    $author = $_GET['author'];

    $where .= "
    AND books.Author='$author'
    ";
}

if (!empty($_GET['department'])) {

    $department = $_GET['department'];

    $where .= "
    AND books.Department='$department'
    ";
}

if (!empty($_GET['category'])) {

    $category = $_GET['category'];

    $where .= "
    AND books.Category LIKE '%$category%'
    ";
}

if (!empty($_GET['date_from'])) {

    $date_from = $_GET['date_from'];

    $where .= "
    AND books.Publish_Date >= '$date_from'
    ";
}

if (!empty($_GET['date_to'])) {

    $date_to = $_GET['date_to'];

    $where .= "
    AND books.Publish_Date <= '$date_to'
    ";
}

// ======================
// QUERY
// ======================

$book_result = $conn->query("

SELECT books.*,
teacher.Name AS author_name,
department.Name AS department_name

FROM books

LEFT JOIN teacher
ON books.Author = teacher.ID

LEFT JOIN department
ON books.Department = department.ID

$where

ORDER BY books.ID DESC

");

$total = $conn->query("

SELECT COUNT(*) AS total

FROM books

LEFT JOIN teacher
ON books.Author = teacher.ID

LEFT JOIN department
ON books.Department = department.ID

$where

")->fetch_assoc()['total'];

// ======================
// HTML DESIGN
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
    Books Report
</div>

<div class="report-info">
    Generated Date: ' . date("Y-m-d") . '
</div>

<table border="1">

<tr>
<th>ID</th>
<th>Title</th>
<th>Category</th>
<th>Author</th>
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
    Total Books : ' . $total . '
</div>

';

// ======================
// PDF
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
