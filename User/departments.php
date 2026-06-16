<?php
// departments.php

include('../auth.php');

$lang = $_SESSION['lang'] ?? 'en';


include('../db_connection.php');

// ===========================
// Insert Department
// ===========================

if (isset($_POST['save_department'])) {

    $id         = $_POST['id'];
    $department = $_POST['department'];

    $insert_query = "INSERT INTO department
    (ID, Name)

    VALUES

    ('$id','$department')";

    $conn->query($insert_query);

    header("Location: departments.php");
    exit();
}


// ===========================
// Search
// ===========================

$search = "";

if (isset($_GET['search'])) {

    $search = $_GET['search'];

    $department_query = "SELECT * FROM department

    WHERE

    ID LIKE '%$search%'
    OR Name LIKE '%$search%'";
} else {

    $department_query = "SELECT * FROM department";
}

$department_result = $conn->query($department_query);

?>

<!DOCTYPE html>
<html lang="<?= ($lang == 'fa') ? 'fa' : 'en'; ?>"
    dir="<?= ($lang == 'fa') ? 'rtl' : 'ltr'; ?>">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title><?= ($lang == 'fa') ? 'دیپارتمنت‌ها' : 'Departments'; ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet"
        href="../css/bootstrap.min.css">

    <script src="../js/bootstrap.bundle.min.js"></script>
    <style>
        html[dir="rtl"] {
            direction: rtl;
        }

        html[dir="rtl"] .table-card,
        html[dir="rtl"] .form-card,
        html[dir="rtl"] .table,
        html[dir="rtl"] .search-form,
        html[dir="rtl"] .form-label,
        html[dir="rtl"] .form-title {
            text-align: right;
        }

        html[dir="rtl"] .table th,
        html[dir="rtl"] .table td {
            text-align: right;
        }

        html[dir="rtl"] .form-control,
        html[dir="rtl"] .search-input {
            text-align: right;
        }
    </style>

</head>

<body>
    <?php include('header.php'); ?>
    <div class="main-wrapper">

        <!-- TABLE SECTION -->

        <div class="table-section">

            <div class="search-wrapper"
                dir="<?= ($lang == 'fa') ? 'rtl' : 'ltr'; ?>">

                <form method="GET" class="search-form">

                    <input type="text"
                        name="search"
                        class="search-input"
                        placeholder="<?= ($lang == 'fa')
                                            ? 'جستجوی دیپارتمنت‌ها...'
                                            : 'Search departments...'; ?>"
                        value="<?= $search; ?>">

                    <button type="submit" class="search-btn">

                        <?= ($lang == 'fa')
                            ? 'جستجو'
                            : 'Search'; ?>

                    </button>

                </form>

            </div>

            <div class="table-card">

                <table class="table table-hover">
                    <thead>

                        <tr>

                            <th width="100">
                                <?= ($lang == 'fa') ? 'شناسه' : 'ID'; ?>
                            </th>

                            <th>
                                <?= ($lang == 'fa')
                                    ? 'نام دیپارتمنت'
                                    : 'Department Name'; ?>
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($row = $department_result->fetch_assoc()) { ?>

                            <tr>

                                <td><?php echo $row['ID']; ?></td>

                                <td><?php echo $row['Name']; ?></td>


                            </tr>

                        <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>
        <!-- FORM SECTION -->

        <div class="form-section">

            <div class="form-card">

                <div class="form-title">

                    <?= ($lang == 'fa')
                        ? 'افزودن دیپارتمنت'
                        : 'Add Department'; ?>

                </div>

                <form method="POST">

                    <!-- ID -->

                    <div class="mb-3">

                        <label class="form-label">

                            <?= ($lang == 'fa') ? 'شناسه' : 'ID'; ?>

                        </label>

                        <input type="text"

                            name="id"

                            class="form-control"

                            placeholder="<?= ($lang == 'fa')
                                                ? 'وارد کردن شناسه دیپارتمنت...'
                                                : 'Enter department ID...'; ?>"

                            required>

                    </div>

                    <!-- Department Name -->

                    <div class="mb-4">

                        <label class="form-label">

                            <?= ($lang == 'fa')
                                ? 'نام دیپارتمنت'
                                : 'Department Name'; ?>

                        </label>

                        <input type="text"

                            name="department"

                            class="form-control"

                            placeholder="<?= ($lang == 'fa')
                                                ? 'وارد کردن نام دیپارتمنت...'
                                                : 'Enter department name...'; ?>"

                            required>

                    </div>

                    <!-- Save Button -->

                    <button type="submit"

                        class="save-btn"

                        name="save_department">

                        <?= ($lang == 'fa')
                            ? 'ذخیره دیپارتمنت'
                            : 'Save Department'; ?>

                    </button>

                </form>

            </div>

        </div>

    </div>

</body>

</html>