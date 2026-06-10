<?php

include('../auth.php');
include('../db_connection.php');

// ======================
// FILTERS
// ======================

$where = " WHERE 1=1 ";

if (!empty($_GET['translator'])) {

    $translator = $_GET['translator'];

    $where .= "
    AND translated_books.translated_by='$translator'
    ";
}

if (!empty($_GET['department'])) {

    $department = $_GET['department'];

    $where .= "
    AND translated_books.Department='$department'
    ";
}

if (!empty($_GET['category'])) {

    $category = $_GET['category'];

    $where .= "
    AND translated_books.Category LIKE '%$category%'
    ";
}

if (!empty($_GET['date_from'])) {

    $date_from = $_GET['date_from'];

    $where .= "
    AND translated_books.Publish_Date >= '$date_from'
    ";
}

if (!empty($_GET['date_to'])) {

    $date_to = $_GET['date_to'];

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

?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>Translated Books Report</title>

    <link rel="stylesheet" href="../css/bootstrap.min.css">

    <style>
        .form-label {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 3px;
        }

        .form-control {
            font-size: 13px;
            height: 34px;
            padding: 4px 8px;
        }

        .btn {
            font-size: 13px;
            padding: 5px 12px;
        }

        .table {
            font-size: 13px;
        }

        .report-title {
            font-size: 24px;
        }

        h5 {
            font-size: 15px;
        }

        body {
            background: #f4f6f9;
            font-family: Segoe UI;
            overflow-y: auto !important;
            height: auto !important;
        }

        .report-container {
            width: 95%;
            margin: 20px auto;
        }

        .report-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .1);
        }

        .report-title {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }

        @media print {

            .no-print {
                display: none !important;
            }

            body {
                background: white !important;
            }

            .report-card {
                box-shadow: none !important;
                border: none !important;
            }

            .report-container {
                width: 100% !important;
                margin: 0 !important;
            }

            .report-title {
                text-align: center;
                margin-bottom: 20px;
            }
        }
    </style>

</head>

<body>
    <div class="no-print">
        <?php include('header.php'); ?>
    </div>


    <div class="report-container">

        <div class="report-card">

            <h2 class="report-title">
                Translated Books Report
            </h2>

            <div class="mb-3 no-print">


                <button
                    class="btn btn-success"
                    onclick="window.print()">
                    Print Report
                </button>

                <a href="translatedbooks_report_pdf.php?<?php echo http_build_query($_GET); ?>"
                    class="btn btn-danger">
                    Download PDF
                </a>
            </div>

            <form method="GET" class="row mb-3 no-print">

                <div class="col-md-3">

                    <label class="form-label">
                        Translator
                    </label>

                    <select name="translator" class="form-control">

                        <option value="">
                            All Translators
                        </option>

                        <?php

                        $teacher = $conn->query("SELECT * FROM teacher");

                        while ($t = $teacher->fetch_assoc()) {

                        ?>

                            <option value="<?php echo $t['ID']; ?>">

                                <?php echo $t['Name']; ?>

                            </option>

                        <?php } ?>

                    </select>

                </div>

                <div class="col-md-2">

                    <label class="form-label">
                        Category
                    </label>

                    <input type="text"
                        name="category"
                        class="form-control">

                </div>

                <div class="col-md-2">

                    <label class="form-label">
                        Department
                    </label>

                    <select name="department"
                        class="form-control">

                        <option value="">
                            All Departments
                        </option>

                        <?php

                        $dep = $conn->query("SELECT * FROM department");

                        while ($d = $dep->fetch_assoc()) {

                        ?>

                            <option value="<?php echo $d['ID']; ?>">
                                <?php echo $d['Name']; ?>
                            </option>

                        <?php } ?>

                    </select>

                </div>

                <div class="col-md-2">

                    <label class="form-label">
                        Date From
                    </label>

                    <input type="date"
                        name="date_from"
                        class="form-control">

                </div>

                <div class="col-md-2">

                    <label class="form-label">
                        Date To
                    </label>

                    <input type="date"
                        name="date_to"
                        class="form-control">

                </div>

                <div class="col-md-12 mt-2">

                    <button
                        type="submit"
                        class="btn btn-primary">

                        Filter Report

                    </button>

                </div>

            </form>

            <table class="table table-bordered table-striped">

                <thead>

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

                </thead>

                <tbody>

                    <?php while ($row = $book_result->fetch_assoc()) { ?>

                        <tr>

                            <td><?php echo $row['ID']; ?></td>

                            <td><?php echo $row['Title']; ?></td>

                            <td><?php echo $row['Author']; ?></td>

                            <td><?php echo $row['translator_name']; ?></td>

                            <td><?php echo $row['Category']; ?></td>

                            <td><?php echo $row['department_name']; ?></td>

                            <td><?php echo $row['Pages']; ?></td>

                            <td><?php echo $row['Publish_Date']; ?></td>

                        </tr>

                    <?php } ?>

                </tbody>

            </table>

            <h5>

                Total Translated Books :
                <?php echo $total; ?>

            </h5>

        </div>

    </div>

</body>

</html>