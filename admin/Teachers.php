<?php
include('../auth.php');
include('../db_connection.php');
$lang = $_SESSION['lang'] ?? 'en';
// ===========================
// Message
// ===========================

$message = "";
$message_type = "";

// ===========================
// Insert Teacher
// ===========================
if (isset($_POST['save_teacher'])) {

    $id          = $_POST['id'];
    $name        = $_POST['name'];
    $lastname    = $_POST['lastname'];
    $email       = $_POST['email'];
    $contact     = $_POST['contact'];
    $education   = $_POST['education'];
    $department  = $_POST['department'];

    $conn->query("INSERT INTO teacher
    (
        ID,
        Name,
        Last_Name,
        Email,
        Contact,
        Education,
        Department
    )

    VALUES

    (
        '$id',
        '$name',
        '$lastname',
        '$email',
        '$contact',
        '$education',
        '$department'
    )");

    header("Location: teachers.php");
    exit();
}
// ===========================
// Delete
// ===========================

if (isset($_GET['delete'])) {

    $id = $_GET['delete'];

    // Delete related articles

    $conn->query("DELETE FROM articles
    WHERE Teacher_ID='$id'");

    // Delete related books

    $conn->query("DELETE FROM books
    WHERE Author='$id'");

    // Delete related translated books

    $conn->query("DELETE FROM translated_books
    WHERE translated_by='$id'");

    // Delete related thesis

    $conn->query("DELETE FROM thesis
    WHERE Instructor='$id'");

    // Then delete teacher

    $conn->query("DELETE FROM teacher
    WHERE ID='$id'");

    header("Location: teachers.php");
    exit();
}
// ===========================
// Edit
// ===========================

$edit_id = "";
$edit_name = "";
$edit_lastname = "";
$edit_email = "";
$edit_contact = "";
$edit_education = "";
$edit_department = "";

if (isset($_GET['edit'])) {

    $id = $_GET['edit'];

    $res = $conn->query("SELECT * FROM teacher
    WHERE ID='$id'");

    if ($res->num_rows > 0) {

        $row = $res->fetch_assoc();

        $edit_id = $row['ID'];
        $edit_name = $row['Name'];
        $edit_lastname = $row['Last_Name'];
        $edit_email = $row['Email'];
        $edit_contact = $row['Contact'];
        $edit_education = $row['Education'];
        $edit_department = $row['Department'];
    }
}

// ===========================
// Update
// ===========================

if (isset($_POST['update_teacher'])) {

    $id = $_POST['id'];
    $name = $_POST['name'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $education = $_POST['education'];
    $department = $_POST['department'];

    $conn->query("UPDATE teacher SET

    Name='$name',
    Last_Name='$lastname',
    Email='$email',
    Contact='$contact',
    Education='$education',
    Department='$department'

    WHERE ID='$id'");

    header("Location: teachers.php");
    exit();
}

// ===========================
// Search
// ===========================

$search = "";

if (isset($_GET['search'])) {

    $search = $_GET['search'];

    $teacher_result = $conn->query("

    SELECT teacher.*, department.Name AS department_name

    FROM teacher

    LEFT JOIN department
    ON teacher.Department=department.ID

    WHERE

    teacher.Name LIKE '%$search%'
    OR teacher.Last_Name LIKE '%$search%'
    OR teacher.Email LIKE '%$search%'
    OR teacher.Contact LIKE '%$search%'
    OR teacher.Education LIKE '%$search%'
    ");
} else {

    $teacher_result = $conn->query("

    SELECT teacher.*, department.Name AS department_name

    FROM teacher

    LEFT JOIN department
    ON teacher.Department=department.ID
    ");
}
?>

<!DOCTYPE html>
<html lang="<?= ($lang == 'fa') ? 'fa' : 'en'; ?>"
    dir="<?= ($lang == 'fa') ? 'rtl' : 'ltr'; ?>">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title><?= ($lang == 'fa') ? 'استادان' : 'Teachers'; ?></title>

    <link rel="stylesheet" href="style.css">

    <link rel="stylesheet"
        href="../css/bootstrap.min.css">

    <script src="../js/bootstrap.bundle.min.js"></script>
    <style>
        html[dir="rtl"] .main-wrapper,
        html[dir="rtl"] .table-section,
        html[dir="rtl"] .form-section,
        html[dir="rtl"] .table-card,
        html[dir="rtl"] .form-card {
            direction: rtl;
            text-align: right;
        }

        html[dir="rtl"] .table th,
        html[dir="rtl"] .table td {
            text-align: right;
        }

        html[dir="rtl"] .form-label {
            text-align: right;
            display: block;
        }

        html[dir="rtl"] .form-control,
        html[dir="rtl"] .custom-select,
        html[dir="rtl"] .search-input {
            text-align: right;
        }

        html[dir="rtl"] .form-title {
            text-align: right;
        }
    </style>
</head>

<body>

    <?php include('header.php'); ?>

    <div class="main-wrapper">

        <!-- TABLE -->

        <div class="table-section">

            <!-- ALERT MESSAGE -->

            <?php if ($message != "") { ?>

                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">

                    <?php echo $message; ?>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"></button>

                </div>

            <?php } ?>

            <!-- SEARCH -->
            <div class="search-wrapper"
                dir="<?= ($lang == 'fa') ? 'rtl' : 'ltr'; ?>">

                <form method="GET" class="search-form">

                    <input type="text"
                        name="search"
                        class="search-input"
                        placeholder="<?= ($lang == 'fa') ? 'جستجوی استادان...' : 'Search teachers...'; ?>"
                        value="<?php echo $search; ?>">

                    <button type="submit" class="search-btn">
                        <?= ($lang == 'fa') ? 'جستجو' : 'Search'; ?>
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
                            <th><?= ($lang == 'fa') ? 'مدرک تحصیلی' : 'Degree'; ?></th>
                            <th><?= ($lang == 'fa') ? 'دیپارتمنت' : 'Department'; ?></th>
                            <th width="160"><?= ($lang == 'fa') ? 'عملیات' : 'Action'; ?></th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($row = $teacher_result->fetch_assoc()) { ?>

                            <tr>

                                <td><?php echo $row['ID']; ?></td>

                                <td><?php echo $row['Name']; ?></td>

                                <td><?php echo $row['Last_Name']; ?></td>

                                <td><?php echo $row['Email']; ?></td>

                                <td><?php echo $row['Contact']; ?></td>

                                <td><?php echo $row['Education']; ?></td>

                                <td><?php echo $row['department_name']; ?></td>

                                <td>

                                    <div class="action-icons">

                                        <a href="teachers.php?edit=<?php echo $row['ID']; ?>"
                                            class="edit-btn">

                                            <?= ($lang == 'fa') ? 'ویرایش' : 'Edit'; ?>

                                        </a>

                                        <a href="teachers.php?delete=<?php echo $row['ID']; ?>"
                                            class="delete-btn"

                                            onclick="return confirm('<?= ($lang == 'fa')
                                                                            ? 'آیا مطمئن هستید؟ با حذف این استاد تمام اطلاعات مرتبط نیز حذف خواهد شد.'
                                                                            : 'Are you sure you want to delete this teacher? It will delete all related data.'; ?>')">

                                            <?= ($lang == 'fa') ? 'حذف' : 'Delete'; ?>

                                        </a>
                                    </div>

                                </td>

                            </tr>

                        <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

        <!-- FORM -->

        <div class="form-section">

            <div class="form-card">
                <div class="form-title">

                    <?php
                    echo isset($_GET['edit'])
                        ? (($lang == 'fa') ? 'ویرایش استاد' : 'Edit Teacher')
                        : (($lang == 'fa') ? 'افزودن استاد' : 'Add Teacher');
                    ?>

                </div>

                <form method="POST">

                    <input type="hidden"

                        name="id"

                        value="<?php echo $edit_id; ?>">
                    <!-- ID -->

                    <div class="mb-3">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'شناسه' : 'ID'; ?>
                        </label>

                        <input type="text"

                            name="id"

                            class="form-control"


                            value="<?php echo $edit_id; ?>"

                            <?php echo isset($_GET['edit']) ? 'readonly' : ''; ?>

                            required>

                    </div>

                    <!-- Name -->

                    <div class="mb-3">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'نام' : 'Name'; ?>
                        </label>


                        <input type="text"

                            name="name"

                            class="form-control"

                            value="<?php echo $edit_name; ?>"

                            required>

                    </div>

                    <!-- Last Name -->

                    <div class="mb-3">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'تخلص' : 'Last Name'; ?>
                        </label>

                        <input type="text"

                            name="lastname"

                            class="form-control"

                            value="<?php echo $edit_lastname; ?>"

                            required>

                    </div>

                    <!-- Email -->

                    <div class="mb-3">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'ایمیل' : 'Email'; ?>
                        </label>

                        <input type="email"

                            name="email"

                            class="form-control"



                            value="<?php echo $edit_email; ?>"

                            required>

                    </div>

                    <!-- Contact -->

                    <div class="mb-3">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'شماره تماس' : 'Contact'; ?>
                        </label>

                        <input type="text"

                            name="contact"

                            class="form-control"



                            value="<?php echo $edit_contact; ?>"

                            required>

                    </div>

                    <!-- Education -->

                    <div class="mb-3">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'مدرک تحصیلی' : 'Degree'; ?>
                        </label>

                        <input type="text"

                            name="education"

                            class="form-control"


                            value="<?php echo $edit_education; ?>"

                            required>

                    </div>

                    <!-- Department -->

                    <div class="mb-4">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'دیپارتمنت' : 'Department'; ?>
                        </label>

                        <select name="department"
                            class="custom-select">

                            <?php

                            $d = $conn->query("SELECT * FROM department");

                            while ($dep = $d->fetch_assoc()) {

                            ?>

                                <option value="<?php echo $dep['ID']; ?>"

                                    <?php
                                    if ($edit_department == $dep['ID'])
                                        echo "selected";
                                    ?>>

                                    <?php echo $dep['Name']; ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <!-- Button -->

                    <button class="save-btn"
                        name="<?php
                                echo isset($_GET['edit'])
                                    ? 'update_teacher'
                                    : 'save_teacher';
                                ?>">

                        <?php
                        echo isset($_GET['edit'])
                            ? (($lang == 'fa') ? 'بروزرسانی استاد' : 'Update Teacher')
                            : (($lang == 'fa') ? 'ذخیره استاد' : 'Save Teacher');
                        ?>

                    </button>

                </form>

            </div>

        </div>

    </div>

</body>

</html>