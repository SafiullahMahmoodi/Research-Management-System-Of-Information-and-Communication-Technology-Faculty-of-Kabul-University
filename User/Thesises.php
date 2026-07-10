<?php
include('../auth.php');
$lang = $_SESSION['lang'] ?? 'en';
include('../db_connection.php');
$message = "";
// ===========================
// CREATE PDF FOLDER
// ===========================

if (!file_exists("../PDF_File")) {

    mkdir("../PDF_File", 0777, true);
}

// ===========================
// INSERT THESIS
// ===========================

if (isset($_POST['save_thesis'])) {

    $id            = mysqli_real_escape_string($conn, $_POST['id']);
    $title         = mysqli_real_escape_string($conn, $_POST['title']);
    $description   = mysqli_real_escape_string($conn, $_POST['description']);
    $category      = mysqli_real_escape_string($conn, $_POST['category']);
    $student_id    = mysqli_real_escape_string($conn, $_POST['student_id']);
    $instructor    = mysqli_real_escape_string($conn, $_POST['instructor']);
    $department    = mysqli_real_escape_string($conn, $_POST['department']);
    $publish_date  = mysqli_real_escape_string($conn, $_POST['publish_date']);

    // Check duplicate ID
    $check_id = $conn->query("SELECT ID FROM thesis WHERE ID='$id'");

    if ($check_id->num_rows > 0) {

        $message = ($lang == 'fa')
            ? "این آی‌دی قبلاً ثبت شده است."
            : "This ID already exists.";
    } else {
        // CHECK STUDENT

        $check_student = $conn->query("
    SELECT ID
    FROM students
    WHERE ID='$student_id'
    ");

        if ($check_student->num_rows == 0) {

            die("Selected Student does not exist.");
        }

        // CHECK INSTRUCTOR

        $check_teacher = $conn->query("
    SELECT ID
    FROM teacher
    WHERE ID='$instructor'
    ");

        if ($check_teacher->num_rows == 0) {

            die("Selected Instructor does not exist.");
        }

        // PDF FILE

        $pdf_file = "";

        if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['name'] != "") {

            $file_name = $_FILES['pdf_file']['name'];
            $file_tmp  = $_FILES['pdf_file']['tmp_name'];
            $file_size = $_FILES['pdf_file']['size'];

            $extension = strtolower(
                pathinfo($file_name, PATHINFO_EXTENSION)
            );

            if ($extension != "pdf") {

                die("Only PDF files are allowed.");
            }

            if ($file_size > 209715200) {

                die("File size must be less than 200MB.");
            }

            $pdf_file = time() . "_" . $file_name;

            move_uploaded_file(
                $file_tmp,
                "../PDF_File/" . $pdf_file
            );
        }

        // INSERT

        $conn->query("
    INSERT INTO thesis
    (
        ID,
        Title,
        Description,
        Category,
        Student_ID,
        Instructor,
        Department,
        PDF_File,
        Publish_Date
    )

    VALUES
    (
        '$id',
        '$title',
        '$description',
        '$category',
        '$student_id',
        '$instructor',
        '$department',
        '$pdf_file',
        '$publish_date'
    )
    ");

        header("Location: thesis.php");
        exit();
    }
}
// ===========================
// SEARCH
// ===========================

$search = "";

if (isset($_GET['search'])) {

    $search = mysqli_real_escape_string(
        $conn,
        $_GET['search']
    );

    $thesis_result = $conn->query("

    SELECT thesis.*,
    students.Name AS student_name,
    teacher.Name AS instructor_name,
    department.Name AS department_name

    FROM thesis

    LEFT JOIN students
    ON thesis.Student_ID = students.ID

    LEFT JOIN teacher
    ON thesis.Instructor = teacher.ID

    LEFT JOIN department
    ON thesis.Department = department.ID

    WHERE

    thesis.ID LIKE '%$search%'
    OR thesis.Title LIKE '%$search%'
    OR thesis.Description LIKE '%$search%'
    OR thesis.Category LIKE '%$search%'
    OR students.Name LIKE '%$search%'
    OR teacher.Name LIKE '%$search%'
    OR department.Name LIKE '%$search%'

    ORDER BY thesis.ID DESC
    ");
} else {

    $thesis_result = $conn->query("

    SELECT thesis.*,
    students.Name AS student_name,
    teacher.Name AS instructor_name,
    department.Name AS department_name

    FROM thesis

    LEFT JOIN students
    ON thesis.Student_ID = students.ID

    LEFT JOIN teacher
    ON thesis.Instructor = teacher.ID

    LEFT JOIN department
    ON thesis.Department = department.ID

    ORDER BY thesis.ID DESC
    ");
}
?>

<!DOCTYPE html>
<html lang="<?= ($lang == 'fa') ? 'fa' : 'en'; ?>"
    dir="<?= ($lang == 'fa') ? 'rtl' : 'ltr'; ?>">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= ($lang == 'fa') ? 'مونوگراف ها‌ها' : 'Thesis'; ?>
    </title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
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
<script>
    function checkPDF(input) {

        if (input.files.length > 0) {

            let file = input.files[0];

            let extension = file.name.split('.').pop().toLowerCase();

            if (extension !== "pdf") {

                alert("<?= ($lang == 'fa')
                            ? 'فقط فایل PDF مجاز است!'
                            : 'Only PDF files are allowed!'; ?>");

                input.value = "";
            }
        }
    }
</script>

<body dir="<?= ($lang == 'fa') ? 'rtl' : 'ltr'; ?>">

    <?php include('header.php'); ?>

    <div class="main-wrapper">

        <!-- ================= SEARCH ================= -->
        <div class="table-section">
            <div class="search-wrapper">

                <form method="GET" class="search-form">

                    <input
                        type="text"
                        name="search"
                        class="search-input"
                        placeholder="<?= ($lang == 'fa') ? 'جستجوی مونوگراف ها...' : 'Search Theses...'; ?>"
                        value="<?= htmlspecialchars($search); ?>">

                    <button type="submit" class="search-btn">
                        🔍
                    </button>

                </form>

            </div>

            <!-- ================= TABLE ================= -->
            <div class="table-card">

                <table class="table table-hover">

                    <thead>

                        <tr>
                            <th><?= ($lang == 'fa') ? 'شماره' : 'No.'; ?></th>
                            <th><?= ($lang == 'fa') ? 'آی‌دی' : 'ID'; ?></th>
                            <th><?= ($lang == 'fa') ? 'عنوان' : 'Title'; ?></th>
                            <th><?= ($lang == 'fa') ? 'توضیحات' : 'Description'; ?></th>
                            <th><?= ($lang == 'fa') ? 'کتگوری' : 'Category'; ?></th>
                            <th><?= ($lang == 'fa') ? 'محصل' : 'Student'; ?></th>
                            <th><?= ($lang == 'fa') ? 'استاد' : 'Instructor'; ?></th>
                            <th><?= ($lang == 'fa') ? 'دیپارتمنت' : 'Department'; ?></th>
                            <th><?= ($lang == 'fa') ? 'تاریخ نشر' : 'Publish Date'; ?></th>
                            <th><?= ($lang == 'fa') ? 'فایل PDF' : 'PDF'; ?></th>
                        </tr>

                    </thead>

                    <tbody>
                        <?php $no = 1; ?>

                        <?php while ($row = $thesis_result->fetch_assoc()) { ?>

                            <tr>

                                <td><?= $no++; ?></td>
                                <td><?= $row['ID']; ?></td>
                                <td><?= $row['Title']; ?></td>
                                <td><?= $row['Description']; ?></td>
                                <td><?= $row['Category']; ?></td>
                                <td><?= $row['student_name']; ?></td>
                                <td><?= $row['instructor_name']; ?></td>
                                <td><?= $row['department_name']; ?></td>
                                <td><?= $row['Publish_Date']; ?></td>

                                <td>
                                    <?php if ($row['PDF_File'] != "") { ?>
                                        <a href="../PDF_File/<?= $row['PDF_File']; ?>"
                                            target="_blank"
                                            class="pdf-btn">
                                            PDF
                                        </a>
                                    <?php } else { ?>
                                        <?= ($lang == 'fa') ? 'ندارد' : 'No File'; ?>
                                    <?php } ?>
                                </td>

                            </tr>

                        <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

        <!-- ================= FORM ================= -->
        <div class="form-section">

            <div class="form-card">

                <div class="form-title" style="text-align: center; font-size: 1rem;">

                    <?= ($lang == 'fa')
                        ? 'اضافه نمودن مونوگراف'
                        : 'Add Thesises'; ?>

                </div>

                <form method="POST" enctype="multipart/form-data">
                    <?php if (!empty($message)) { ?>
                        <div class="alert alert-danger">
                            <?= $message; ?>
                        </div>
                    <?php } ?>
                    <div class="mb-2">
                        <label class="form-label"><?= ($lang == 'fa') ? 'آی‌دی' : 'ID'; ?></label>
                        <input type="text" name="id" class="form-control"
                            required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label"><?= ($lang == 'fa') ? 'عنوان' : 'Title'; ?></label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label"><?= ($lang == 'fa') ? 'توضیحات' : 'Description'; ?></label>
                        <textarea name="description" class="form-control" required></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label"><?= ($lang == 'fa') ? 'کتگوری' : 'Category'; ?></label>
                        <input type="text" name="category" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label"><?= ($lang == 'fa') ? 'محصل' : 'Student'; ?></label>

                        <select name="student_id" class="custom-select" required>
                            <option value="">
                                <?= ($lang == 'fa') ? 'انتخاب محصل' : 'Select Student'; ?>
                            </option>

                            <?php
                            $student = $conn->query("SELECT * FROM students");
                            while ($s = $student->fetch_assoc()) {
                            ?>
                                <option value="<?= $s['ID']; ?>">
                                    <?= $s['Name']; ?>
                                </option>
                            <?php } ?>
                        </select>

                    </div>

                    <div class="mb-2">
                        <label class="form-label"><?= ($lang == 'fa') ? 'استاد' : 'Instructor'; ?></label>

                        <select name="instructor" class="custom-select" required>
                            <option value="">
                                <?= ($lang == 'fa') ? 'انتخاب استاد' : 'Select Instructor'; ?>
                            </option>

                            <?php
                            $teacher = $conn->query("SELECT * FROM teacher");
                            while ($t = $teacher->fetch_assoc()) {
                            ?>
                                <option value="<?= $t['ID']; ?>">
                                    <?= $t['Name']; ?>
                                </option>
                            <?php } ?>
                        </select>

                    </div>

                    <div class="mb-2">
                        <label class="form-label"><?= ($lang == 'fa') ? 'دیپارتمنت' : 'Department'; ?></label>

                        <select name="department" class="custom-select" required>
                            <option value="">
                                <?= ($lang == 'fa') ? 'انتخاب دیپارتمنت' : 'Select Department'; ?>
                            </option>

                            <?php
                            $dep = $conn->query("SELECT * FROM department");
                            while ($d = $dep->fetch_assoc()) {
                            ?>
                                <option value="<?= $d['ID']; ?>">
                                    <?= $d['Name']; ?>
                                </option>
                            <?php } ?>
                        </select>

                    </div>
                    <div class="mb-2">
                        <label class="form-label">
                            <?= ($lang == 'fa')
                                ? 'فایل PDF'
                                : 'PDF File'; ?>
                        </label>
                        <input type="file"
                            name="pdf_file"
                            class="form-control"
                            accept=".pdf"
                            onchange="checkPDF(this)">
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?= ($lang == 'fa') ? 'تاریخ نشر' : 'Publish Date'; ?></label>
                        <input type="date" name="publish_date" class="form-control" required>
                    </div>

                    <div class="form-buttons">

                        <button
                            type="submit"
                            class="save-btn"
                            name="save_thesis">

                            <?php echo ($lang == 'fa') ? 'ذخیره مونوگراف' : 'Save Thesis'; ?>

                        </button>

                        <button
                            type="button"
                            class="cancel-btn"
                            onclick="window.location.href='Thesises.php'">

                            <?php echo ($lang == 'fa') ? 'لغو' : 'Cancel'; ?>

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</body>

</html>