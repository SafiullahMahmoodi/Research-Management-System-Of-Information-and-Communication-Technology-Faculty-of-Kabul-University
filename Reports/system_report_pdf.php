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
    font-family: dejavusans;
    direction: ' . $dir . ';
    text-align: ' . $align . ';
}

/* ---------- Header ---------- */

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

/* ---------- Table ---------- */

table{
    width:100%;
    border-collapse:collapse;
    font-size:13px;
}

th{
    background:#198754;
    color:#ffffff;
    border:1px solid #000;
    padding:9px;
    text-align:center;
    font-weight:bold;
}

td{
    border:1px solid #000;
    padding:8px;
    text-align:center;
}

.footer{
    margin-top:20px;
    text-align:center;
    font-size:15px;
    font-weight:bold;
}

/* ---------- Notes ---------- */

.notes-title{
    margin-top:30px;
    font-size:14px;
    font-weight:bold;
}

.notes-box{
    border:1px solid #000;
    height:120px;
    margin-top:8px;
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
' . $titleText . '
</div>

</td>

<td width="20%" align="right">
<img src="../img/ict_logo.jpeg" width="80">
</td>

</tr>

</table>

<div class="info">
' . ($isFa ? 'تاریخ تولید' : 'Generated Date') . ' : ' . date("Y-m-d") . '
</div>

<table>

<tr>
<th>' . $moduleText . '</th>
<th>' . $totalText . '</th>
</tr>

<tr>
<td>' . ($isFa ? 'استفاده کننده گان' : 'Users') . '</td>
<td>' . $users . '</td>
</tr>

<tr>
<td>' . ($isFa ? 'دیپارتمنت‌ها' : 'Departments') . '</td>
<td>' . $departments . '</td>
</tr>

<tr>
<td>' . ($isFa ? 'استادان' : 'Teachers') . '</td>
<td>' . $teachers . '</td>
</tr>

<tr>
<td>' . ($isFa ? 'محصلین' : 'Students') . '</td>
<td>' . $students . '</td>
</tr>

<tr>
<td>' . ($isFa ? 'مقالات' : 'Articles') . '</td>
<td>' . $articles . '</td>
</tr>

<tr>
<td>' . ($isFa ? 'کتاب‌ها' : 'Books') . '</td>
<td>' . $books . '</td>
</tr>

<tr>
<td>' . ($isFa ? 'کتاب‌های ترجمه‌شده' : 'Translated Books') . '</td>
<td>' . $translated . '</td>
</tr>

<tr>
<td>' . ($isFa ? 'مونوگراف‌ها' : 'Thesis') . '</td>
<td>' . $thesis . '</td>
</tr>

</table>

<div class="footer">
' . $finalText . ' : ' . $total_records . '
</div>

<div class="notes-title">
' . ($isFa ? 'یادداشت:' : 'Notes:') . '
</div>

<div class="notes-box"></div>
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
