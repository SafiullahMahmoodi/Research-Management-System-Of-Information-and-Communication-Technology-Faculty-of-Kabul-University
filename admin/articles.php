<?php

include('../auth.php');

$lang = $_SESSION['lang'] ?? 'en';


include('../db_connection.php');
$error = "";
// ===========================
// CREATE PDF FOLDER
// ===========================

if (!file_exists("../PDF_File")) {

    mkdir("../PDF_File", 0777, true);
}

// ===========================
// INSERT ARTICLE
// ===========================

if (isset($_POST['save_article'])) {

    $id          = mysqli_real_escape_string($conn, $_POST['id']);
    $title       = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category    = mysqli_real_escape_string($conn, $_POST['category']);
    $teacher_id  = mysqli_real_escape_string($conn, $_POST['teacher_id']);
    $student_id  = mysqli_real_escape_string($conn, $_POST['student_id']);
    $department  = mysqli_real_escape_string($conn, $_POST['department']);
    $date        = mysqli_real_escape_string($conn, $_POST['date']);

    // ===========================
    // CHECK TEACHER OR STUDENT
    // ===========================

    if (empty($teacher_id) && empty($student_id)) {

        $error = "Please select at least one Teacher or Student.";
    }

    if (!empty($teacher_id)) {

        $check_teacher = $conn->query("
        SELECT ID
        FROM teacher
        WHERE ID='$teacher_id'
        ");

        if ($check_teacher->num_rows == 0) {

            die("Selected Teacher does not exist.");
        }
    }

    // ===========================
    // CHECK STUDENT
    // ===========================

    if (!empty($student_id)) {

        $check_student = $conn->query("
        SELECT ID
        FROM students
        WHERE ID='$student_id'
        ");

        if ($check_student->num_rows == 0) {

            die("Selected Student does not exist.");
        }
    }

    // ===========================
    // NULL VALUES
    // ===========================

    $teacher_value = !empty($teacher_id)
        ? "'$teacher_id'"
        : "NULL";

    $student_value = !empty($student_id)
        ? "'$student_id'"
        : "NULL";

    $pdf_file = "";

    // ===========================
    // UPLOAD PDF
    // ===========================

    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['name'] != "") {

        $file_name = $_FILES['pdf_file']['name'];
        $file_tmp  = $_FILES['pdf_file']['tmp_name'];
        $file_size = $_FILES['pdf_file']['size'];

        $extension = strtolower(
            pathinfo($file_name, PATHINFO_EXTENSION)
        );

        if ($extension != "pdf") {

            echo "<script>
            alert('Only PDF files are allowed!');
            window.history.back();
          </script>";
            exit();
        }
        // MAX 200MB

        if ($file_size > 209715200) {

            die("File size must be less than 200MB.");
        }

        $pdf_file = time() . "_" . $file_name;

        move_uploaded_file(
            $file_tmp,
            "../PDF_File/" . $pdf_file
        );
    }

    // ===========================
    // INSERT
    // ===========================

    $conn->query("
    INSERT INTO articles
    (
        ID,
        Title,
        Description,
        Category,
        Teacher_ID,
        Student_ID,
        Department,
        PDF_File,
        Date
    )

    VALUES
    (
        '$id',
        '$title',
        '$description',
        '$category',
        $teacher_value,
        $student_value,
        '$department',
        '$pdf_file',
        '$date'
    )
    ");

    header("Location: articles.php");
    exit();
}

// ===========================
// DELETE ARTICLE
// ===========================

if (isset($_GET['delete'])) {

    $id = $_GET['delete'];

    $pdf = $conn->query("
    SELECT PDF_File
    FROM articles
    WHERE ID='$id'
    ");

    if ($pdf->num_rows > 0) {

        $p = $pdf->fetch_assoc();

        if (
            $p['PDF_File'] != ""
            &&
            file_exists("../PDF_File/" . $p['PDF_File'])
        ) {

            unlink("../PDF_File/" . $p['PDF_File']);
        }
    }

    $conn->query("
    DELETE FROM articles
    WHERE ID='$id'
    ");

    header("Location: articles.php");
    exit();
}

// ===========================
// EDIT ARTICLE
// ===========================

$edit_id          = "";
$edit_title       = "";
$edit_description = "";
$edit_category    = "";
$edit_teacher     = "";
$edit_student     = "";
$edit_department  = "";
$edit_date        = "";

if (isset($_GET['edit'])) {

    $id = $_GET['edit'];

    $res = $conn->query("
    SELECT *
    FROM articles
    WHERE ID='$id'
    ");

    if ($res->num_rows > 0) {

        $row = $res->fetch_assoc();

        $edit_id          = $row['ID'];
        $edit_title       = $row['Title'];
        $edit_description = $row['Description'];
        $edit_category    = $row['Category'];
        $edit_teacher     = $row['Teacher_ID'];
        $edit_student     = $row['Student_ID'];
        $edit_department  = $row['Department'];
        $edit_date        = $row['Date'];
    }
}

// ===========================
// UPDATE ARTICLE
// ===========================

if (isset($_POST['update_article'])) {

    $id          = mysqli_real_escape_string($conn, $_POST['id']);
    $title       = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category    = mysqli_real_escape_string($conn, $_POST['category']);
    $teacher_id  = mysqli_real_escape_string($conn, $_POST['teacher_id']);
    $student_id  = mysqli_real_escape_string($conn, $_POST['student_id']);
    $department  = mysqli_real_escape_string($conn, $_POST['department']);
    $date        = mysqli_real_escape_string($conn, $_POST['date']);

    if (empty($teacher_id) && empty($student_id)) {
        $error = ($lang == 'fa')
            ? 'حداقل  استاد یا محصل را انتخاب کنید.'
            : 'Please select at least one Teacher or Student.';
    }


    $teacher_value = !empty($teacher_id)
        ? "'$teacher_id'"
        : "NULL";

    $student_value = !empty($student_id)
        ? "'$student_id'"
        : "NULL";

    $query = "
    UPDATE articles SET

    Title='$title',
    Description='$description',
    Category='$category',
    Teacher_ID=$teacher_value,
    Student_ID=$student_value,
    Department='$department',
    Date='$date'
    ";

    // ===========================
    // UPDATE PDF
    // ===========================

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

        // DELETE OLD PDF

        $old = $conn->query("
        SELECT PDF_File
        FROM articles
        WHERE ID='$id'
        ");

        if ($old->num_rows > 0) {

            $o = $old->fetch_assoc();

            if (
                $o['PDF_File'] != ""
                &&
                file_exists("../PDF_File/" . $o['PDF_File'])
            ) {

                unlink("../PDF_File/" . $o['PDF_File']);
            }
        }

        $pdf_file = time() . "_" . $file_name;

        move_uploaded_file(
            $file_tmp,
            "../PDF_File/" . $pdf_file
        );

        $query .= ", PDF_File='$pdf_file'";
    }

    $query .= " WHERE ID='$id'";

    $conn->query($query);

    header("Location: articles.php");
    exit();
}

// ===========================
// SEARCH ARTICLES
// ===========================

$search = "";

if (isset($_GET['search'])) {

    $search = mysqli_real_escape_string(
        $conn,
        $_GET['search']
    );

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

WHERE

articles.ID LIKE '%$search%'
OR articles.Title LIKE '%$search%'
OR articles.Category LIKE '%$search%'
OR teacher.Name LIKE '%$search%'
OR students.Name LIKE '%$search%'
OR department.Name LIKE '%$search%'
OR articles.`Date` LIKE '%$search%'

ORDER BY articles.ID DESC
    ");
} else {

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

    ORDER BY articles.ID DESC
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
    <title><?= ($lang == 'fa') ? 'مقالات' : 'Articles'; ?></title>
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
<script>
    function validateForm() {

        let teacher =
            document.querySelector('[name="teacher_id"]').value;

        let student =
            document.querySelector('[name="student_id"]').value;

        if (teacher === "" && student === "") {

            alert("<?= ($lang == 'fa')
                        ? 'حداقل یک استاد یا محصل را انتخاب کنید.'
                        : 'Please select at least one Teacher or Student.'; ?>");

            return false;
        }

        return true;
    }
</script>
<script>
    function checkPDF(input) {

        if (input.files.length > 0) {

            let file = input.files[0];

            let extension = file.name
                .split('.')
                .pop()
                .toLowerCase();

            if (extension !== "pdf") {

                alert("<?= ($lang == 'fa')
                            ? 'فقط فایل PDF مجاز است.'
                            : 'Only PDF files are allowed!'; ?>");

                input.value = "";
            }
        }
    }
</script>

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
                        placeholder="<?= ($lang == 'fa') ? 'جستجوی مقالات...' : 'Search...'; ?>"
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
                            <th><?= ($lang == 'fa') ? 'آی دی' : 'ID'; ?></th>
                            <th><?= ($lang == 'fa') ? 'عنوان' : 'Title'; ?></th>
                            <th><?= ($lang == 'fa') ? 'توضیحات' : 'Description'; ?></th>
                            <th><?= ($lang == 'fa') ? 'کتگوری' : 'Category'; ?></th>
                            <th><?= ($lang == 'fa') ? 'استاد' : 'Teacher'; ?></th>
                            <th><?= ($lang == 'fa') ? 'محصل' : 'Student'; ?></th>
                            <th><?= ($lang == 'fa') ? 'دیپارتمنت' : 'Department'; ?></th>
                            <th><?= ($lang == 'fa') ? 'تاریخ' : 'Date'; ?></th>
                            <th><?= ($lang == 'fa') ? 'فایل PDF' : 'PDF File'; ?></th>
                            <th><?= ($lang == 'fa') ? 'عملیات' : 'Action'; ?></th>

                        </tr>

                    </thead>

                    <tbody>


                        <?php $no = 1; ?>

                        <?php while ($row = $article_result->fetch_assoc()) { ?>

                            <tr>

                            <tr>

                                <td><?= $no++; ?></td>

                                <td><?= $row['ID']; ?></td>

                                <td><?php echo $row['Title']; ?></td>
                                <td><?php echo $row['Description']; ?></td>

                                <td><?php echo $row['Category']; ?></td>

                                <td><?php echo $row['teacher_name']; ?></td>

                                <td><?php echo $row['student_name']; ?></td>

                                <td><?php echo $row['department_name']; ?></td>

                                <td><?php echo $row['Date']; ?></td>

                                <td>

                                    <?php if ($row['PDF_File'] != "") { ?>

                                        <a href="../PDF_File/<?php echo $row['PDF_File']; ?>"
                                            target="_blank"
                                            class="pdf-btn">

                                            <?= ($lang == 'fa') ? 'مشاهده PDF' : 'View PDF'; ?>

                                        </a>

                                    <?php } else { ?>

                                        <?= ($lang == 'fa') ? 'بدون فایل' : 'No File'; ?>

                                    <?php } ?>

                                </td>

                                <td>

                                    <div class="action-icons">

                                        <a href="articles.php?edit=<?php echo $row['ID']; ?>"
                                            class="edit-btn">

                                            <?= ($lang == 'fa') ? 'تغییر دادن' : 'Edit'; ?>

                                        </a>

                                        <a href="articles.php?delete=<?php echo $row['ID']; ?>"
                                            class="delete-btn"

                                            onclick="return confirm('<?= ($lang == 'fa')
                                                                            ? 'آیا از حذف این مقاله مطمئن هستید؟'
                                                                            : 'Delete this article?'; ?>')">

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

                <div class="form-title" style="text-align: center;">

                    <?php
                    echo isset($_GET['edit'])
                        ? (($lang == 'fa')
                            ? 'تغییر دادن مقاله'
                            : 'Edit Article')
                        : (($lang == 'fa')
                            ? 'اضافه نمودن مقاله'
                            : 'Add Article');
                    ?>

                </div>
                <?php if (!empty($error)) { ?>

                    <div class="alert alert-danger">

                        <?php echo $error; ?>

                    </div>

                <?php } ?>

                <form method="POST"
                    enctype="multipart/form-data"
                    onsubmit="return validateForm()">

                    <div class="mb-2">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'آی دی' : 'ID'; ?>
                        </label>

                        <input type="text"

                            name="id"

                            class="form-control"

                            required

                            value="<?php echo $edit_id; ?>">

                    </div>

                    <div class="mb-2">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'عنوان' : 'Title'; ?>
                        </label>


                        <input type="text"

                            name="title"

                            class="form-control"


                            required

                            value="<?php echo $edit_title; ?>">

                    </div>

                    <div class="mb-2">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'توضیحات' : 'Description'; ?>
                        </label>

                        <textarea name="description"

                            class="form-control"

                            required><?php echo $edit_description; ?></textarea>

                    </div>

                    <div class="mb-2">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'کتگوری' : 'Category'; ?>
                        </label>


                        <input type="text"

                            name="category"

                            class="form-control"


                            required

                            value="<?php echo $edit_category; ?>">

                    </div>

                    <div class="mb-2">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'استاد' : 'Teacher'; ?>
                        </label>

                        <select name="teacher_id"
                            class="custom-select">
                            <option value="">

                            </option>

                            <?php

                            $teacher = $conn->query("SELECT * FROM teacher");

                            while ($t = $teacher->fetch_assoc()) {

                            ?>

                                <option value="<?php echo $t['ID']; ?>"

                                    <?php
                                    if ($edit_teacher == $t['ID'])
                                        echo "selected";
                                    ?>>

                                    <?php echo $t['Name']; ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <div class="mb-2">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'محصل' : 'Student'; ?>
                        </label>


                        <select name="student_id"
                            class="custom-select">
                            <option value="">

                            </option>

                            <?php

                            $student = $conn->query("SELECT * FROM students");

                            while ($s = $student->fetch_assoc()) {

                            ?>

                                <option value="<?php echo $s['ID']; ?>"

                                    <?php
                                    if ($edit_student == $s['ID'])
                                        echo "selected";
                                    ?>>

                                    <?php echo $s['Name']; ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <div class="mb-2">
                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'دیپارتمنت' : 'Department'; ?>
                        </label>

                        <select name="department"
                            class="custom-select">

                            <?php

                            $dep = $conn->query("SELECT * FROM department");

                            while ($d = $dep->fetch_assoc()) {

                            ?>
                                <option value="">
                                    <?= ($lang == 'fa')
                                        ? 'انتخاب دیپارتمنت'
                                        : 'Select Department'; ?>
                                </option>

                                <option value="<?php echo $d['ID']; ?>"

                                    <?php
                                    if ($edit_department == $d['ID'])
                                        echo "selected";
                                    ?>>

                                    <?php echo $d['Name']; ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'فایل PDF' : 'PDF File'; ?>
                        </label>

                        <input
                            type="file"
                            name="pdf_file"
                            id="pdf_file"
                            class="form-control form-control-sm"
                            accept=".pdf"
                            onchange="checkPDF(this)">

                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'تاریخ' : 'Date'; ?>
                        </label>

                        <input type="date"

                            name="date"

                            class="form-control"

                            required

                            value="<?php echo $edit_date; ?>">

                    </div>

                    <div class="form-buttons">
                        <button type="submit"
                            class="save-btn"
                            name="<?= isset($_GET['edit'])
                                        ? 'update_article'
                                        : 'save_article'; ?>">

                            <?= isset($_GET['edit'])
                                ? (($lang == 'fa')
                                    ? 'تغییر دادن مقاله'
                                    : 'Update Article')
                                : (($lang == 'fa')
                                    ? 'ذخیره مقاله'
                                    : 'Save Article'); ?>

                        </button>
                        <button
                            type="button"
                            class="cancel-btn"
                            onclick="window.location.href='articles.php'">

                            <?php echo ($lang == 'fa') ? 'لغو' : 'Cancel'; ?>

                        </button>
                    </div>
                </form>

            </div>

        </div>

    </div>

</body>

</html>