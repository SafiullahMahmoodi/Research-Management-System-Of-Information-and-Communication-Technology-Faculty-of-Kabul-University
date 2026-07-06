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

    <Style>
        /* ==========================
   MODERN SEARCH BOX
========================== */

        .search-wrapper {
            display: flex;
            justify-content: center;
            margin: 13px 0;
        }

        .search-form {
            width: 100%;
            max-width: 450px;
            display: flex;
            align-items: center;
            background: #fff;
            border: 2px solid #3d3d3d;
            border-radius: 50px;
            overflow: hidden;
            transition: .3s;
        }

        .search-form:focus-within {
            border-color: #0f9d58;
            box-shadow: 0 0 15px rgba(15, 157, 88, .18);
        }

        /* Input */

        .search-input {
            flex: 1;

            border: none;
            outline: none;

            background: transparent;

            padding: 10px 15px;

            font-size: 13px;

            color: #222;
        }

        .search-input::placeholder {
            color: #777;
        }

        /* Button */

        .search-btn {

            width: 60px;
            height: 60px;

            border: none;
            background: transparent;

            cursor: pointer;

            font-size: 26px;

            display: flex;
            justify-content: center;
            align-items: center;

            transition: .25s;
        }

        .search-btn:hover {

            color: #0f9d58;
        }

        /* English */

        html[dir="ltr"] .search-form {
            flex-direction: row;
        }

        html[dir="ltr"] .search-input {
            text-align: left;
        }

        html[dir="ltr"] .search-btn {
            border-left: 1px solid #ddd;
        }

        /* Persian */

        html[dir="rtl"] .search-form {
            flex-direction: row-reverse;
        }

        html[dir="rtl"] .search-input {
            direction: rtl;
            text-align: right;
        }

        html[dir="rtl"] .search-btn {
            border-right: 1px solid #ddd;
        }

        html[dir="rtl"] {
            direction: rtl;
        }

        .form-buttons {
            display: flex;
            gap: 8px;
            margin-top: 10px;
        }

        .form-buttons button {
            flex: 1 1 0;
            padding: 10px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            font-weight: 400;
        }

        .save-btn {
            background: #0f9d58;
            color: white;
        }

        .save-btn:hover {
            background: #0c7c45;
        }

        .cancel-btn {
            background: #6c757d;
            color: white;
        }


        html[dir="rtl"] .table-card,
        html[dir="rtl"] .form-card,
        html[dir="rtl"] .search-form,
        html[dir="rtl"] .table,
        html[dir="rtl"] .form-control,
        html[dir="rtl"] .custom-select,
        html[dir="rtl"] .form-label,
        html[dir="rtl"] .form-title {
            text-align: right;
        }

        html[dir="rtl"] .action-icons {
            justify-content: flex-start;
        }

        html[dir="rtl"] .search-wrapper {
            direction: rtl;
        }

        html[dir="rtl"] .table th,
        html[dir="rtl"] .table td {
            text-align: right;
        }
    </Style>
</head>

<body>
    <?php include('header.php'); ?>
    <div class="main-wrapper">

        <!-- TABLE SECTION -->

        <div class="table-section">

            <div class="search-wrapper">

                <form method="GET" class="search-form">

                    <input
                        type="text"
                        name="search"
                        class="search-input"
                        placeholder="<?= ($lang == 'fa') ? 'جستجوی دیپارتمنت ها...' : 'Search...'; ?>"
                        value="<?= htmlspecialchars($search); ?>">

                    <button type="submit" class="search-btn">
                        🔍
                    </button>

                </form>

            </div>

            <div class="table-card">

                <table class="table table-hover">

                    <thead>

                        <tr>

                            <th width="100">
                                <?= ($lang == 'fa') ? 'آی دی' : 'ID'; ?>
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

                                            <?= ($lang == 'fa') ? 'تغییر آوری' : 'Edit'; ?>

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
                <div class="form-title" style="text-align: center;">

                    <?php
                    echo isset($_GET['edit'])
                        ? (($lang == 'fa') ? 'تغییر آوردن دیپارتمنت' : 'Edit Department')
                        : (($lang == 'fa') ? 'اضافه نمودن دیپارتمنت' : 'Add Department');
                    ?>

                </div>

                <form method="POST">

                    <!-- ID -->

                    <div class="mb-3">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'آی دی' : 'ID'; ?>
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
                    <div class="form-buttons">
                        <button type="submit"
                            class="save-btn"
                            name="<?php
                                    echo isset($_GET['edit'])
                                        ? 'update_department'
                                        : 'save_department';
                                    ?>">

                            <?php
                            echo isset($_GET['edit'])
                                ? (($lang == 'fa') ? 'تغییر آوری دیپارتمنت' : 'Update Department')
                                : (($lang == 'fa') ? 'ذخیره دیپارتمنت' : 'Save Department');
                            ?>

                        </button>

                        <button
                            type="button"
                            class="cancel-btn"
                            onclick="window.location.href='departments.php'">

                            <?php echo ($lang == 'fa') ? 'لغو' : 'Cancel'; ?>

                        </button>
                    </div>

                </form>

            </div>

        </div>

    </div>

</body>

</html>