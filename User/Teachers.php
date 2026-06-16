<?php
include('../auth.php');

$lang = $_SESSION['lang'] ?? 'en';

include('../db_connection.php');

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
    (Name, Last_Name, Email, Contact, Education, Department)

    VALUES

    ( '  $id '  ,'$name','$lastname','$email','$contact','$education','$department')");

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
    </Style>
</head>

<body>

    <?php include('header.php'); ?>

    <div class="main-wrapper">

        <!-- TABLE -->

        <div class="table-section">

            <div class="search-wrapper"
                dir="<?= ($lang == 'fa') ? 'rtl' : 'ltr'; ?>">

                <form method="GET" class="search-form">

                    <input type="text"
                        name="search"
                        class="search-input"
                        placeholder="<?= ($lang == 'fa')
                                            ? 'جستجوی استادان...'
                                            : 'Search teachers...'; ?>"
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
                            <th><?= ($lang == 'fa') ? 'شناسه' : 'ID'; ?></th>

                            <th><?= ($lang == 'fa') ? 'نام' : 'Name'; ?></th>

                            <th><?= ($lang == 'fa') ? 'تخلص' : 'Last Name'; ?></th>

                            <th><?= ($lang == 'fa') ? 'ایمیل' : 'Email'; ?></th>

                            <th><?= ($lang == 'fa') ? 'تماس' : 'Contact'; ?></th>

                            <th><?= ($lang == 'fa') ? 'تحصیلات' : 'Education'; ?></th>

                            <th><?= ($lang == 'fa') ? 'دیپارتمنت' : 'Department'; ?></th>

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

                    <?= ($lang == 'fa')
                        ? 'افزودن استاد'
                        : 'Add Teacher'; ?>

                </div>

                <form method="POST">
                    <div class="mb-3">

                        <label class="form-label">

                            <?= ($lang == 'fa')
                                ? 'شناسه'
                                : 'ID'; ?>

                        </label>

                        <input type="text"

                            name="id"

                            class="form-control"

                            placeholder="<?= ($lang == 'fa')
                                                ? 'شناسه استاد را وارد کنید'
                                                : 'Enter teacher ID'; ?>"

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
                                                ? 'نام استاد را وارد کنید'
                                                : 'Enter teacher name'; ?>"
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
                                                ? 'تخلص استاد را وارد کنید'
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
                                                ? 'ایمیل استاد را وارد کنید'
                                                : 'Enter email'; ?>"

                            required>

                    </div>

                    <!-- Contact -->

                    <div class="mb-3">

                        <label class="form-label">

                            <?= ($lang == 'fa')
                                ? 'تماس'
                                : 'Contact'; ?>

                        </label>

                        <input type="text"

                            name="contact"

                            class="form-control"

                            placeholder="<?= ($lang == 'fa')
                                                ? 'شماره تماس استاد را وارد کنید'
                                                : 'Enter contact'; ?>"

                            required>

                    </div>

                    <!-- Education -->

                    <div class="mb-3">

                        <label class="form-label">

                            <?= ($lang == 'fa')
                                ? 'تحصیلات'
                                : 'Degree'; ?>

                        </label>

                        <input type="text"

                            name="education"

                            class="form-control"

                            placeholder="<?= ($lang == 'fa')
                                                ? 'تحصیلات استاد را وارد کنید'
                                                : 'Enter Degree'; ?>"

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

                    <!-- Button -->

                    <button class="save-btn"
                        name="save_teacher">

                        <?= ($lang == 'fa')
                            ? 'ذخیره استاد'
                            : 'Save Teacher'; ?>

                    </button>

                </form>

            </div>

        </div>

    </div>

</body>

</html>