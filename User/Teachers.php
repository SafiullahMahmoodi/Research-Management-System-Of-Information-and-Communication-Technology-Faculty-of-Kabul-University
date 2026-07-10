<?php
include('../auth.php');

$lang = $_SESSION['lang'] ?? 'en';

include('../db_connection.php');
$message = "";
// ===========================
// Insert Teacher
// ===========================
if (isset($_POST['save_teacher'])) {

    $id         = trim($_POST['id']);
    $name       = trim($_POST['name']);
    $lastname   = trim($_POST['lastname']);
    $email      = trim($_POST['email']);
    $contact    = trim($_POST['contact']);
    $education  = trim($_POST['education']);
    $department = trim($_POST['department']);

    // Check duplicate ID
    $check = $conn->query("SELECT ID FROM teacher WHERE ID='$id'");

    if ($check->num_rows > 0) {

        $message = ($lang == 'fa')
            ? "این آی دی قبلاً ثبت شده است."
            : "This ID already exists.";
    } else {

        $conn->query("INSERT INTO teacher
        (ID, Name, Last_Name, Email, Contact, Education, Department)
        VALUES
        ('$id','$name','$lastname','$email','$contact','$education','$department')");

        header("Location: teachers.php");
        exit();
    }
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
    <!-- <style>
        
        /* ==========================
   MODERN SEARCH BOX
========================== */

        .search-wrapper {
            display: flex;
            justify-content: center;
            margin: 18px 0;
        }

        .search-form {
            width: 100%;
            max-width: 520px;
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

            font-size: 12px;

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

            font-size: 16px;

            display: flex;
            justify-content: center;
            align-items: center;

            transition: .25s;
        }

        .search-btn:hover {

            color: #0f9d58;
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

        html[dir="rtl"] .form-label {
            display: block;
            width: 100%;
            text-align: right !important;
        }

        html[dir="rtl"] .form-control,
        html[dir="rtl"] .custom-select,
        html[dir="rtl"] .search-input {
            direction: rtl;
            text-align: right;
        }

        html[dir="rtl"] .form-card {
            direction: rtl;
        }

        html[dir="rtl"] .mb-3,
        html[dir="rtl"] .mb-4 {
            text-align: right;
        }
    </Style> -->
</head>

<body>

    <?php include('header.php'); ?>

    <div class="main-wrapper">

        <!-- TABLE -->

        <div class="table-section">

            <div class="search-wrapper">

                <form method="GET" class="search-form">

                    <input
                        type="text"
                        name="search"
                        class="search-input"
                        placeholder="<?= ($lang == 'fa') ? 'جستجوی استادان...' : 'Search Teachers...'; ?>"
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
                            <th><?= ($lang == 'fa') ? 'شماره' : 'No.'; ?></th>

                            <th><?= ($lang == 'fa') ? 'آی دی ' : 'ID'; ?></th>

                            <th><?= ($lang == 'fa') ? 'نام' : 'Name'; ?></th>

                            <th><?= ($lang == 'fa') ? 'تخلص' : 'Last Name'; ?></th>

                            <th><?= ($lang == 'fa') ? 'ایمیل' : 'Email'; ?></th>

                            <th><?= ($lang == 'fa') ? 'تماس' : 'Contact'; ?></th>

                            <th><?= ($lang == 'fa') ? 'در جه تحصیلی ' : 'Education'; ?></th>

                            <th><?= ($lang == 'fa') ? 'دیپارتمنت' : 'Department'; ?></th>

                        </tr>

                    </thead>

                    <tbody>
                        <?php $no = 1; ?>

                        <?php while ($row = $teacher_result->fetch_assoc()) { ?>

                            <tr>
                                <td><?= $no++; ?></td>

                                <td><?php echo $row['ID']; ?></td>

                                <td><?php echo $row['Name']; ?></td>

                                <td><?php echo $row['Last_Name']; ?></td>

                                <td><?php echo $row['Email']; ?></td>

                                <td><?php echo $row['Contact']; ?></td>

                                <td><?php echo $row['Education']; ?></td>

                                <td><?php echo $row['department_name']; ?></td>


                            </tr>

                        <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>
        <!-- FORM -->

        <div class="form-section">

            <div class="form-card">
                <div class="form-title" style="text-align: center; font-size: 1rem;">

                    <?= ($lang == 'fa')
                        ? 'افزودن استاد'
                        : 'Add Teacher'; ?>

                </div>

                <form method="POST">
                    <?php if (!empty($message)) { ?>
                        <div class="alert alert-danger">
                            <?= $message; ?>
                        </div>
                    <?php } ?>
                    <div class="mb-3">

                        <label class="form-label">

                            <?= ($lang == 'fa')
                                ? 'آی دی '
                                : 'ID'; ?>

                        </label>

                        <input type="text"

                            name="id"

                            class="form-control"

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
                            \
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

                            required>

                    </div>

                    <!-- Contact -->

                    <div class="mb-3">

                        <label class="form-label">

                            <?= ($lang == 'fa')
                                ? 'شماره تماس'
                                : 'Contact'; ?>

                        </label>

                        <input type="text"

                            name="contact"

                            class="form-control"


                            required>

                    </div>

                    <!-- Education -->

                    <div class="mb-3">

                        <label class="form-label">

                            <?= ($lang == 'fa')
                                ? 'در جه تحصیلی'
                                : 'Degree'; ?>

                        </label>

                        <input type="text"

                            name="education"

                            class="form-control"


                            required>

                    </div>

                    <!-- Department -->

                    <div class="mb-4">

                        <label class="form-label">

                            <?= ($lang == 'fa')
                                ? 'دیپارتمنت'
                                : 'Department'; ?>

                        </label>
                        <select name="department" class="custom-select">

                            <option value="">

                                <?= ($lang == 'fa')
                                    ? 'انتخاب دیپارتمنت'
                                    : 'Select Department'; ?>

                            </option>

                            <?php
                            $d = $conn->query("SELECT * FROM department");

                            while ($dep = $d->fetch_assoc()) {
                            ?>

                                <option value="<?= $dep['ID']; ?>">

                                    <?= $dep['Name']; ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>
                    <!-- save and cancel button -->
                    <div class="form-buttons">

                        <button type="submit"
                            class="save-btn"
                            name="save_teacher">

                            <?php echo ($lang == 'fa') ? 'ذخیره استاد' : 'Save Teacher'; ?>

                        </button>

                        <button
                            type="button"
                            class="cancel-btn"
                            onclick="window.location.href='Teachers.php'">

                            <?php echo ($lang == 'fa') ? 'لغو' : 'Cancel'; ?>

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</body>

</html>