<?php
include('../auth.php');
$lang = $_SESSION['lang'] ?? 'en';

include('../db_connection.php');

// ===========================
// Insert Student
// ===========================

if (isset($_POST['save_student'])) {
    $id = $_POST['id'];
    $name        = $_POST['name'];
    $lastname    = $_POST['lastname'];
    $email       = $_POST['email'];
    $contact     = $_POST['contact'];
    $department  = $_POST['department'];

    $insert_query = "INSERT INTO students
(
    ID,
    Name,
    Last_Name,
    Email,
    Contact,
    Department
)

VALUES

(
    '$id',
    '$name',
    '$lastname',
    '$email',
    '$contact',
    '$department'
)";

    $conn->query($insert_query);

    header("Location: students.php");
    exit();
}


// ===========================
// Search
// ===========================

$search = "";

if (isset($_GET['search'])) {

    $search = $_GET['search'];

    $student_query = "SELECT students.*, department.Name AS department_name

    FROM students

    LEFT JOIN department
    ON students.Department = department.ID

    WHERE

    students.ID LIKE '%$search%'
    OR students.Name LIKE '%$search%'
    OR students.Last_Name LIKE '%$search%'
    OR students.Email LIKE '%$search%'
    OR students.Contact LIKE '%$search%'
    OR department.Name LIKE '%$search%'";
} else {

    $student_query = "SELECT students.*, department.Name AS department_name

    FROM students

    LEFT JOIN department
    ON students.Department = department.ID";
}

$student_result = $conn->query($student_query);

?>

<!DOCTYPE html>
<html lang="<?= ($lang == 'fa') ? 'fa' : 'en'; ?>"
    dir="<?= ($lang == 'fa') ? 'rtl' : 'ltr'; ?>">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title><?= ($lang == 'fa') ? 'محصلان' : 'Students'; ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet"
        href="../css/bootstrap.min.css">

    <script src="../js/bootstrap.bundle.min.js"></script>
    <Style>
        html[dir="rtl"] .form-label {
            display: block;
            width: 100%;
            text-align: right !important;
        }

        html[dir="rtl"] .form-control,
        html[dir="rtl"] .custom-select,
        html[dir="rtl"] .search-input {
            text-align: right;
            direction: rtl;
        }

        html[dir="rtl"] .table th,
        html[dir="rtl"] .table td {
            text-align: right;
        }

        html[dir="rtl"] .form-title {
            text-align: right;
        }
    </Style>

</head>

<body>
    <?php include('header.php'); ?>

    <div class="main-wrapper">

        <!-- TABLE SECTION -->

        <div class="table-section">

            <!-- SEARCH -->
            <div class="search-wrapper"
                dir="<?= ($lang == 'fa') ? 'rtl' : 'ltr'; ?>">

                <form method="GET" class="search-form">

                    <input type="text"
                        name="search"
                        class="search-input"
                        placeholder="<?= ($lang == 'fa')
                                            ? 'جستجوی محصلان...'
                                            : 'Search students...'; ?>"
                        value="<?= $search; ?>">

                    <button type="submit" class="search-btn">

                        <?= ($lang == 'fa')
                            ? 'جستجو'
                            : 'Search'; ?>

                    </button>

                </form>

            </div>

            <!-- TABLE -->

            <div class="table-card">

                <table class="table table-hover">

                    <thead>

                        <tr>
                            <th><?= ($lang == 'fa') ? 'شناسه' : 'ID'; ?></th>

                            <th><?= ($lang == 'fa') ? 'نام' : 'Name'; ?></th>

                            <th><?= ($lang == 'fa') ? 'تخلص' : 'Last Name'; ?></th>

                            <th><?= ($lang == 'fa') ? 'ایمیل' : 'Email'; ?></th>

                            <th><?= ($lang == 'fa') ? 'تماس' : 'Contact'; ?></th>

                            <th><?= ($lang == 'fa') ? 'دیپارتمنت' : 'Department'; ?></th>



                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($row = $student_result->fetch_assoc()) { ?>

                            <tr>

                                <td><?php echo $row['ID']; ?></td>

                                <td><?php echo $row['Name']; ?></td>

                                <td><?php echo $row['Last_Name']; ?></td>

                                <td><?php echo $row['Email']; ?></td>

                                <td><?php echo $row['Contact']; ?></td>

                                <td><?php echo $row['department_name']; ?></td>

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
                        ? 'افزودن محصل'
                        : 'Add Student'; ?>

                </div>

                <form method="POST">
                    <div class="mb-3">

                        <label class="form-label">

                            <?= ($lang == 'fa')
                                ? 'آی‌دی'
                                : 'ID'; ?>

                        </label>

                        <input type="text"
                            name="id"
                            class="form-control"
                            placeholder="<?= ($lang == 'fa')
                                                ? 'شناسه محصل را وارد کنید'
                                                : 'Enter student ID'; ?>"
                            required>

                    </div>

                    <!-- Name -->

                    <div class="mb-3">

                        <label class="form-label">

                            <?= ($lang == 'fa')
                                ? 'نام'
                                : 'Name'; ?>

                        </label>

                        <input type="text"

                            name="name"

                            class="form-control"

                            placeholder="<?= ($lang == 'fa')
                                                ? 'نام محصل را وارد کنید'
                                                : 'Enter student name'; ?>"

                            required>

                    </div>

                    <!-- Last Name -->

                    <div class="mb-3">

                        <label class="form-label">

                            <?= ($lang == 'fa')
                                ? 'تخلص'
                                : 'Last Name'; ?>

                        </label>

                        <input type="text"

                            name="lastname"

                            class="form-control"

                            placeholder="<?= ($lang == 'fa')
                                                ? 'تخلص محصل را وارد کنید'
                                                : 'Enter last name'; ?>"

                            required>

                    </div>

                    <!-- Email -->

                    <div class="mb-3">

                        <label class="form-label">

                            <?= ($lang == 'fa')
                                ? 'ایمیل'
                                : 'Email'; ?>

                        </label>

                        <input type="email"

                            name="email"

                            class="form-control"

                            placeholder="<?= ($lang == 'fa')
                                                ? 'ایمیل محصل را وارد کنید'
                                                : 'Enter email'; ?>"

                            required>

                    </div>

                    <!-- Contact -->

                    <div class="mb-3">

                        <label class="form-label">

                            <?= ($lang == 'fa')
                                ? 'نمبر تماس'
                                : 'Contact'; ?>

                        </label>

                        <input type="text"

                            name="contact"

                            class="form-control"

                            placeholder="<?= ($lang == 'fa')
                                                ? 'شماره تماس محصل را وارد کنید'
                                                : 'Enter contact'; ?>"

                            required>

                    </div>

                    <!-- Department -->

                    <div class="mb-4">

                        <label class="form-label">

                            <?= ($lang == 'fa')
                                ? 'دیپارتمنت'
                                : 'Department'; ?>

                        </label>

                        <select name="department"
                            class="custom-select"
                            required>

                            <option value="">

                                <?= ($lang == 'fa')
                                    ? 'انتخاب دیپارتمنت'
                                    : 'Select Department'; ?>

                            </option>

                            <?php

                            $department_query = "SELECT * FROM department";
                            $department_result = $conn->query($department_query);

                            while ($department = $department_result->fetch_assoc()) {

                            ?>

                                <option value="<?= $department['ID']; ?>">

                                    <?= $department['Name']; ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <!-- BUTTON -->

                    <button class="save-btn"
                        name="save_student">

                        <?= ($lang == 'fa')
                            ? 'ذخیره محصل'
                            : 'Save Student'; ?>

                    </button>

                </form>

            </div>

        </div>

    </div>

</body>

</html>