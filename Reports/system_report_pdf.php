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

$titleText = $isFa
    ? 'گزارش سیستم مدیریت تحقیقات'
    : 'Research Management System Report';

$moduleText = $isFa ? 'ماژول' : 'Module';
$totalText  = $isFa ? 'تعداد ریکاردها' : 'Total Records';
$finalText  = $isFa ? 'مجموع کل سیستم' : 'Total System Records';

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
    margin-bottom:20px;
}

table{
    width:100%;
    border-collapse:collapse;
    font-size:13px;
}

th{
    background:#198754;
    color:#fff;
    padding:10px;
    text-align:center;
}

td{
    padding:8px;
    border:1px solid #ddd;
    text-align:center;
}

.footer{
    margin-top:20px;
    font-size:16px;
    font-weight:bold;
    text-align:center;
}

</style>

<div class="title">' . $titleText . '</div>

<table>

<tr>
    <th>' . $moduleText . '</th>
    <th>' . $totalText . '</th>
</tr>

<tr><td>' . ($isFa ? 'کاربران' : 'Users') . '</td><td>' . $users . '</td></tr>
<tr><td>' . ($isFa ? 'دیپارتمنت‌ها' : 'Departments') . '</td><td>' . $departments . '</td></tr>
<tr><td>' . ($isFa ? 'استادان' : 'Teachers') . '</td><td>' . $teachers . '</td></tr>
<tr><td>' . ($isFa ? 'محصلین' : 'Students') . '</td><td>' . $students . '</td></tr>
<tr><td>' . ($isFa ? 'مقالات' : 'Articles') . '</td><td>' . $articles . '</td></tr>
<tr><td>' . ($isFa ? 'کتاب‌ها' : 'Books') . '</td><td>' . $books . '</td></tr>
<tr><td>' . ($isFa ? 'کتاب‌های ترجمه شده' : 'Translated Books') . '</td><td>' . $translated . '</td></tr>
<tr><td>' . ($isFa ? 'پایان‌نامه‌ها' : 'Thesis') . '</td><td>' . $thesis . '</td></tr>

</table>

<div class="footer">
' . $finalText . ' : ' . $total_records . '
</div>
';

// ======================
// MPDF CONFIG
// ======================

$mpdf = new Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'orientation' => 'P'
]);

$mpdf->SetDirectionality($dir);

// برای بهتر شدن فارسی
$mpdf->autoScriptToLang = true;
$mpdf->autoLangToFont   = true;

// ======================
// OUTPUT
// ======================

$mpdf->WriteHTML($html);

$mpdf->Output(
    $isFa ? "گزارش_سیستم.pdf" : "System_Report.pdf",
    "D"
);

exit();
