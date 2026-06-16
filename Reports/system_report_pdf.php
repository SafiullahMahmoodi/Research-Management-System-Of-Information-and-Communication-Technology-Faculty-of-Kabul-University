<?php

require_once '../dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

include('../auth.php');
include('../db_connection.php');

$lang = $_SESSION['lang'] ?? 'en';

$dir = ($lang == 'fa') ? 'rtl' : 'ltr';

// ======================
// STATISTICS
// ======================

$users       = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$teachers    = $conn->query("SELECT COUNT(*) AS total FROM teacher")->fetch_assoc()['total'];
$students    = $conn->query("SELECT COUNT(*) AS total FROM students")->fetch_assoc()['total'];
$departments = $conn->query("SELECT COUNT(*) AS total FROM department")->fetch_assoc()['total'];
$articles    = $conn->query("SELECT COUNT(*) AS total FROM articles")->fetch_assoc()['total'];
$books       = $conn->query("SELECT COUNT(*) AS total FROM books")->fetch_assoc()['total'];
$translated  = $conn->query("SELECT COUNT(*) AS total FROM translated_books")->fetch_assoc()['total'];
$thesis      = $conn->query("SELECT COUNT(*) AS total FROM thesis")->fetch_assoc()['total'];

$total_records =
    $users + $teachers + $students + $departments +
    $articles + $books + $translated + $thesis;

// ======================
// TEXTS
// ======================

$titleText = ($lang == 'fa')
    ? 'گزارش سیستم مدیریت تحقیقات'
    : 'Research Management System Report';

$moduleText = ($lang == 'fa') ? 'ماژول' : 'Module';
$totalText  = ($lang == 'fa') ? 'تعداد ریکاردها' : 'Total Records';
$finalText  = ($lang == 'fa') ? 'مجموع کل سیستم' : 'Total System Records';

// ======================
// HTML
// ======================

$html = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">

<style>
body {
    font-family: DejaVu Sans, sans-serif;
    direction: ' . $dir . ';
}

.container {
    width: 100%;
}

h2 {
    text-align: center;
    margin-bottom: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background-color: #198754;
    color: white;
    padding: 8px;
    text-align: center;
}

td {
    padding: 8px;
    text-align: center;
}

.footer {
    margin-top: 20px;
    font-size: 16px;
    font-weight: bold;
    text-align: center;
}
</style>

</head>

<body>

<div class="container">

<h2>' . $titleText . '</h2>

<table border="1">

<tr>
<th>' . $moduleText . '</th>
<th>' . $totalText . '</th>
</tr>

<tr><td>' . ($lang == 'fa' ? 'کاربران' : 'Users') . '</td><td>' . $users . '</td></tr>
<tr><td>' . ($lang == 'fa' ? 'دیپارتمنت‌ها' : 'Departments') . '</td><td>' . $departments . '</td></tr>
<tr><td>' . ($lang == 'fa' ? 'استادان' : 'Teachers') . '</td><td>' . $teachers . '</td></tr>
<tr><td>' . ($lang == 'fa' ? 'محصلین' : 'Students') . '</td><td>' . $students . '</td></tr>
<tr><td>' . ($lang == 'fa' ? 'مقالات' : 'Articles') . '</td><td>' . $articles . '</td></tr>
<tr><td>' . ($lang == 'fa' ? 'کتاب‌ها' : 'Books') . '</td><td>' . $books . '</td></tr>
<tr><td>' . ($lang == 'fa' ? 'کتاب‌های ترجمه شده' : 'Translated Books') . '</td><td>' . $translated . '</td></tr>
<tr><td>' . ($lang == 'fa' ? 'پایان‌نامه‌ها' : 'Thesis') . '</td><td>' . $thesis . '</td></tr>

</table>

<div class="footer">
' . $finalText . ' : ' . $total_records . '
</div>

</div>

</body>
</html>
';

// ======================
// DOMPDF CONFIG
// ======================

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');

$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream(
    ($lang == 'fa') ? "گزارش_سیستم.pdf" : "System_Report.pdf",
    ["Attachment" => true]
);
