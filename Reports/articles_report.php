<?php

include('../auth.php');
include('../db_connection.php');

// ======================
// FILTERS
// ======================

$where = " WHERE 1=1 ";

if (!empty($_GET['teacher'])) {

    $teacher = $_GET['teacher'];

    $where .= "
    AND articles.Teacher_ID='$teacher'
    ";
}

if (!empty($_GET['student'])) {

    $student = $_GET['student'];

    $where .= "
    AND articles.Student_ID='$student'
    ";
}

if (!empty($_GET['category'])) {

    $category = $_GET['category'];

    $where .= "
    AND articles.Category LIKE '%$category%'
    ";
}

if (!empty($_GET['department'])) {

    $department = $_GET['department'];

    $where .= "
    AND articles.Department='$department'
    ";
}

if (!empty($_GET['date_from'])) {

    $date_from = $_GET['date_from'];

    $where .= "
    AND articles.Date >= '$date_from'
    ";
}

if (!empty($_GET['date_to'])) {

    $date_to = $_GET['date_to'];

    $where .= "
    AND articles.Date <= '$date_to'
    ";
}

// ======================
// REPORT QUERY
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

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Articles Report</title>

    <link rel="stylesheet"
        href="../css/bootstrap.min.css">

    <style>
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
                display: none;
            }

            body {
                background: white;
            }

            .report-card {
                box-shadow: none;
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

                Articles Report

            </h2>

            <div class="col-md-12 mt-3 no-print">

                <button
                    class="btn btn-success me-2"
                    onclick="window.print()">

                    Print Report

                </button>

                <a href="articles_report_pdf.php?<?php echo http_build_query($_GET); ?>"
                    class="btn btn-danger">

                    Download PDF

                </a>

            </div>
            <form method="GET" class="row mb-3 no-print">

                <!-- Teacher -->

                <div class="col-md-2">
                    <label class="form-label">
                        Teacher
                    </label>
                    <select name="teacher" class="form-control">

                        <option value="">All Teachers</option>

                        <?php

                        $teacher = $conn->query("SELECT * FROM teacher");

                        while ($t = $teacher->fetch_assoc()) {

                            echo "<option value='{$t['ID']}'>
                        {$t['Name']}
                      </option>";
                        }

                        ?>

                    </select>
                </div>

                <!-- Student -->

                <div class="col-md-2">
                    <label class="form-label">
                        Student
                    </label>
                    <select name="student" class="form-control">

                        <option value="">All Students</option>

                        <?php

                        $student = $conn->query("SELECT * FROM students");

                        while ($s = $student->fetch_assoc()) {

                            echo "<option value='{$s['ID']}'>
                        {$s['Name']}
                      </option>";
                        }

                        ?>

                    </select>
                </div>

                <!-- Category -->

                <div class="col-md-2">
                    <label class="form-label">
                        Category
                    </label>
                    <input type="text"
                        name="category"
                        class="form-control"
                        placeholder="Category">
                </div>

                <!-- Department -->

                <div class="col-md-2">
                    <label class="form-label">
                        Department
                    </label>
                    <select name="department" class="form-control">

                        <option value="">All Departments</option>

                        <?php

                        $dep = $conn->query("SELECT * FROM department");

                        while ($d = $dep->fetch_assoc()) {

                            echo "<option value='{$d['ID']}'>
                        {$d['Name']}
                      </option>";
                        }

                        ?>

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
                    <button type="submit"
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
                        <th>Category</th>
                        <th>Teacher</th>
                        <th>Student</th>
                        <th>Department</th>
                        <th>Date</th>

                    </tr>

                </thead>

                <tbody>

                    <?php while ($row = $article_result->fetch_assoc()) { ?>

                        <tr>

                            <td><?php echo $row['ID']; ?></td>

                            <td><?php echo $row['Title']; ?></td>

                            <td><?php echo $row['Category']; ?></td>

                            <td><?php echo $row['teacher_name']; ?></td>

                            <td><?php echo $row['student_name']; ?></td>

                            <td><?php echo $row['department_name']; ?></td>

                            <td><?php echo $row['Date']; ?></td>

                        </tr>

                    <?php } ?>

                </tbody>

            </table>

            <br>

            <h5>

                Total Articles :

                <?php

                $total = $conn->query("
SELECT COUNT(*) AS total
FROM articles
")->fetch_assoc();

                echo $total['total'];

                ?>

            </h5>

        </div>

    </div>

</body>

</html>