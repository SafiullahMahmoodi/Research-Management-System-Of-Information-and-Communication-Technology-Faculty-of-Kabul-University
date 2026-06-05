<?php

include('../auth.php');
include('../db_connection.php');

// Statistics

$users      = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$teachers   = $conn->query("SELECT COUNT(*) AS total FROM teacher")->fetch_assoc()['total'];
$students   = $conn->query("SELECT COUNT(*) AS total FROM students")->fetch_assoc()['total'];
$departments = $conn->query("SELECT COUNT(*) AS total FROM department")->fetch_assoc()['total'];
$articles   = $conn->query("SELECT COUNT(*) AS total FROM articles")->fetch_assoc()['total'];
$books      = $conn->query("SELECT COUNT(*) AS total FROM books")->fetch_assoc()['total'];
$translated = $conn->query("SELECT COUNT(*) AS total FROM translated_books")->fetch_assoc()['total'];
$thesis     = $conn->query("SELECT COUNT(*) AS total FROM thesis")->fetch_assoc()['total'];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>System Report</title>

    <link rel="stylesheet" href="../css/bootstrap.min.css">

    <style>
        body {
            background: #f4f6f9;
            font-family: Segoe UI;

            overflow-y: auto !important;
            height: auto !important;
        }


        .report-container {
            width: 90%;
            margin: 30px auto;
        }

        .report-title {
            text-align: center;
            margin-bottom: 25px;
            font-weight: bold;
        }

        .report-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .1);
        }

        table {
            width: 100%;
        }

        th {
            background: #198754;
            color: white;
            text-align: center;
        }

        td {
            text-align: center;
        }

        .print-btn {
            margin-bottom: 15px;
        }

        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>


</head>

<body>
    <?php include('header.php'); ?>

    <div class="report-container">

        <div class="report-card">

            <h2 class="report-title">
                Research Management System Report
            </h2>

            <button class="btn btn-success print-btn"
                onclick="window.print()">
                Print Report
            </button>

            <a href="system_report_pdf.php"
                class="btn btn-danger print-btn">

                Download PDF

            </a>
            <table class="table table-bordered">

                <thead>

                    <tr>
                        <th>Module</th>
                        <th>Total Records</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td>Users</td>
                        <td><?php echo $users; ?></td>
                    </tr>

                    <tr>
                        <td>Departments</td>
                        <td><?php echo $departments; ?></td>
                    </tr>

                    <tr>
                        <td>Teachers</td>
                        <td><?php echo $teachers; ?></td>
                    </tr>

                    <tr>
                        <td>Students</td>
                        <td><?php echo $students; ?></td>
                    </tr>

                    <tr>
                        <td>Articles</td>
                        <td><?php echo $articles; ?></td>
                    </tr>

                    <tr>
                        <td>Books</td>
                        <td><?php echo $books; ?></td>
                    </tr>

                    <tr>
                        <td>Translated Books</td>
                        <td><?php echo $translated; ?></td>
                    </tr>

                    <tr>
                        <td>Thesis</td>
                        <td><?php echo $thesis; ?></td>
                    </tr>

                </tbody>

            </table>

            <br>

            <h5>Total Records In System:
                <?php
                echo $users +
                    $departments +
                    $teachers +
                    $students +
                    $articles +
                    $books +
                    $translated +
                    $thesis;
                ?>
            </h5>

        </div>

    </div>

</body>

</html>