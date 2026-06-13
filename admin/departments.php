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
// Delete Department
// ===========================

if (isset($_GET['delete'])) {

    $delete_id = $_GET['delete'];

    // Delete articles

    $conn->query("DELETE FROM articles
    WHERE Department='$delete_id'");

    // Delete books

    $conn->query("DELETE FROM books
    WHERE Department='$delete_id'");

    // Delete translated books

    $conn->query("DELETE FROM translated_books
    WHERE Department='$delete_id'");

    // Delete thesis

    $conn->query("DELETE FROM thesis
    WHERE Department='$delete_id'");

    // Delete students

    $conn->query("DELETE FROM students
    WHERE Department='$delete_id'");

    // Delete teachers

    $conn->query("DELETE FROM teacher
    WHERE Department='$delete_id'");

    // Delete department

    $conn->query("DELETE FROM department
    WHERE ID='$delete_id'");

    header("Location: departments.php");
    exit();
}
// ===========================
// Edit Department
// ===========================

$edit_id         = "";
$edit_department = "";

if (isset($_GET['edit'])) {

    $edit_id = $_GET['edit'];

    $edit_query = "SELECT * FROM department
    WHERE ID='$edit_id'";

    $edit_result = $conn->query($edit_query);

    if ($edit_result->num_rows > 0) {

        $edit_row = $edit_result->fetch_assoc();

        $edit_department = $edit_row['Name'];
    }
}

// ===========================
// Update Department
// ===========================

if (isset($_POST['update_department'])) {

    $id         = $_POST['id'];
    $department = $_POST['department'];

    $update_query = "UPDATE department SET

    Name='$department'

    WHERE ID='$id'";

    $conn->query($update_query);

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
        /* Persian Language */

        html[dir="rtl"] .table,
        html[dir="rtl"] .table th,
        html[dir="rtl"] .table td {
            text-align: right;
        }

        html[dir="rtl"] .form-label {
            display: block;
            text-align: right;
        }

        html[dir="rtl"] .form-control,
        html[dir="rtl"] .search-input {
            text-align: right;
        }

        html[dir="rtl"] .search-form {
            direction: rtl;
        }

        html[dir="rtl"] .form-card {
            text-align: right;
        }

        html[dir="rtl"] .action-icons {
            justify-content: flex-start;
        }

        /* English Language */

        html[dir="ltr"] .table,
        html[dir="ltr"] .table th,
        html[dir="ltr"] .table td {
            text-align: left;
        }

        html[dir="ltr"] .form-card {
            text-align: left;
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

                <form method="GET"
                    class="search-form">
                    <input type="text"
                        name="search"
                        class="search-input"
                        placeholder="<?= ($lang == 'fa') ? 'جستجوی دیپارتمنت‌ها...' : 'Search departments...'; ?>"
                        value="<?php echo $search; ?>">

                    <button type="submit" class="search-btn">
                        <?= ($lang == 'fa') ? 'جستجو' : 'Search'; ?>
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
                                <?= ($lang == 'fa') ? 'نام دیپارتمنت' : 'Department Name'; ?>
                            </th>

                            <th width="180">
                                <?= ($lang == 'fa') ? 'عملیات' : 'Action'; ?>
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($row = $department_result->fetch_assoc()) { ?>

                            <tr>

                                <td><?php echo $row['ID']; ?></td>

                                <td><?php echo $row['Name']; ?></td>

                                <td>

                                    <div class="action-icons">

                                        <a href="departments.php?edit=<?php echo $row['ID']; ?>"
                                            class="edit-btn">

                                            <?= ($lang == 'fa') ? 'ویرایش' : 'Edit'; ?>

                                        </a>

                                        <a href="departments.php?delete=<?php echo $row['ID']; ?>"
                                            class="delete-btn"

                                            onclick="return confirm('<?= ($lang == 'fa')
                                                                            ? 'آیا مطمئن هستید؟ با حذف این دیپارتمنت تمام اطلاعات مرتبط نیز حذف خواهد شد.'
                                                                            : 'Are you sure to delete this department? It will delete all related data.'; ?>')">

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

        <!-- FORM SECTION -->

        <div class="form-section">

            <div class="form-card">
                <div class="form-title">

                    <?php
                    echo isset($_GET['edit'])
                        ? (($lang == 'fa') ? 'ویرایش دیپارتمنت' : 'Edit Department')
                        : (($lang == 'fa') ? 'افزودن دیپارتمنت' : 'Add Department');
                    ?>

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



                            required

                            value="<?php echo $edit_id; ?>"

                            <?php if (isset($_GET['edit'])) echo "readonly"; ?>>

                    </div>

                    <!-- Department Name -->

                    <div class="mb-4">
                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'نام دیپارتمنت' : 'Department Name'; ?>
                        </label>

                        <input type="text"

                            name="department"

                            class="form-control"


                            required

                            value="<?php echo $edit_department; ?>">

                    </div>

                    <!-- Save Button -->

                    <button type="submit"
                        class="save-btn"
                        name="<?php
                                echo isset($_GET['edit'])
                                    ? 'update_department'
                                    : 'save_department';
                                ?>">

                        <?php
                        echo isset($_GET['edit'])
                            ? (($lang == 'fa') ? 'بروزرسانی دیپارتمنت' : 'Update Department')
                            : (($lang == 'fa') ? 'ذخیره دیپارتمنت' : 'Save Department');
                        ?>

                    </button>

                </form>

            </div>

        </div>

    </div>

</body>

</html>