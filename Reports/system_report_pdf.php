<?php

require_once '../dompdf/autoload.inc.php';

use Dompdf\Dompdf;

include('../auth.php');
include('../db_connection.php');

// Statistics

$users       = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$teachers    = $conn->query("SELECT COUNT(*) AS total FROM teacher")->fetch_assoc()['total'];
$students    = $conn->query("SELECT COUNT(*) AS total FROM students")->fetch_assoc()['total'];
$departments = $conn->query("SELECT COUNT(*) AS total FROM department")->fetch_assoc()['total'];
$articles    = $conn->query("SELECT COUNT(*) AS total FROM articles")->fetch_assoc()['total'];
$books       = $conn->query("SELECT COUNT(*) AS total FROM books")->fetch_assoc()['total'];
$translated  = $conn->query("SELECT COUNT(*) AS total FROM translated_books")->fetch_assoc()['total'];
$thesis      = $conn->query("SELECT COUNT(*) AS total FROM thesis")->fetch_assoc()['total'];

$total_records =
    $users +
    $departments +
    $teachers +
    $students +
    $articles +
    $books +
    $translated +
    $thesis;

$html = '

<h2 style="text-align:center;">
Research Management System Report
</h2>

<table border="1" width="100%" cellpadding="8" cellspacing="0">

<tr style="background-color:#dddddd;">
<th>Module</th>
<th>Total Records</th>
</tr>

<tr>
<td>Users</td>
<td>' . $users . '</td>
</tr>

<tr>
<td>Departments</td>
<td>' . $departments . '</td>
</tr>

<tr>
<td>Teachers</td>
<td>' . $teachers . '</td>
</tr>

<tr>
<td>Students</td>
<td>' . $students . '</td>
</tr>

<tr>
<td>Articles</td>
<td>' . $articles . '</td>
</tr>

<tr>
<td>Books</td>
<td>' . $books . '</td>
</tr>

<tr>
<td>Translated Books</td>
<td>' . $translated . '</td>
</tr>

<tr>
<td>Thesis</td>
<td>' . $thesis . '</td>
</tr>

</table>

<br><br>

<h3>
Total Records In System : ' . $total_records . '
</h3>

';

$dompdf = new Dompdf();

$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

$dompdf->stream(
    "System_Report.pdf",
    array("Attachment" => true)
);
