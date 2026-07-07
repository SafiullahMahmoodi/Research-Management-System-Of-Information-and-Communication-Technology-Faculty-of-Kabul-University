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
// INSERT BOOK
// ===========================

if (isset($_POST['save_book'])) {

    $id            = mysqli_real_escape_string($conn, $_POST['id']);
    $title         = mysqli_real_escape_string($conn, $_POST['title']);
    $description   = mysqli_real_escape_string($conn, $_POST['description']);
    $category      = mysqli_real_escape_string($conn, $_POST['category']);
    $author        = mysqli_real_escape_string($conn, $_POST['author']);
    $department_id = mysqli_real_escape_string($conn, $_POST['department']);
    $pages         = isset($_POST['pages']) ? (int)$_POST['pages'] : 0;
    $publish_date  = mysqli_real_escape_string($conn, $_POST['publish_date']);

    // ===========================
    // CHECK AUTHOR
    // ===========================

    $check_teacher = $conn->query("
    SELECT ID
    FROM teacher
    WHERE ID='$author'
    ");

    if ($check_teacher->num_rows == 0) {

        die("Selected Author does not exist.");
    }

    // ===========================
    // PDF FILE
    // ===========================

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

    // ===========================
    // INSERT
    // ===========================
    // ===========================
    // CHECK DUPLICATE ID
    // ===========================

    $check = mysqli_query($conn, "SELECT ID FROM books WHERE ID='$id'");

    if (mysqli_num_rows($check) > 0) {

        $error = ($lang == 'fa')
            ? "این آی دی قبلاً ثبت شده است."
            : "This ID already exists.";
    } else {

        // ===========================
        // INSERT
        // ===========================

        $conn->query("
    INSERT INTO books
    (
        ID,
        Title,
        Description,
        Category,
        Author,
        Department,
        Pages,
        PDF_File,
        Publish_Date
    )
    VALUES
    (
        '$id',
        '$title',
        '$description',
        '$category',
        '$author',
        '$department_id',
        '$pages',
        '$pdf_file',
        '$publish_date'
    )
    ");

        header("Location: books.php");
        exit();
    }
}


// ===========================
// SEARCH BOOKS
// ===========================

$search = "";

if (isset($_GET['search'])) {

    $search = mysqli_real_escape_string(
        $conn,
        $_GET['search']
    );

    $book_result = $conn->query("

    SELECT books.*,
    teacher.Name AS author_name,
    department.Name AS department_name

    FROM books

    LEFT JOIN teacher
    ON books.Author = teacher.ID

    LEFT JOIN department
    ON books.Department = department.ID

    WHERE

    books.ID LIKE '%$search%'
    OR books.Title LIKE '%$search%'
    OR books.Category LIKE '%$search%'
    OR teacher.Name LIKE '%$search%'
    OR department.Name LIKE '%$search%'

    ORDER BY books.ID DESC
    ");
} else {

    $book_result = $conn->query("

    SELECT books.*,
    teacher.Name AS author_name,
    department.Name AS department_name

    FROM books

    LEFT JOIN teacher
    ON books.Author = teacher.ID

    LEFT JOIN department
    ON books.Department = department.ID

    ORDER BY books.ID DESC
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

    <title><?= ($lang == 'fa') ? 'کتاب‌ها' : 'Books'; ?></title>
    <link rel="stylesheet" href="style.css">

    <link rel="stylesheet"
        href="../css/bootstrap.min.css">

    <script src="../js/bootstrap.bundle.min.js"></script>
    <style>
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

        html[dir="rtl"] .table-card,
        html[dir="rtl"] .form-card,
        html[dir="rtl"] .search-wrapper {
            direction: rtl;
        }

        html[dir="rtl"] .table th,
        html[dir="rtl"] .table td {
            text-align: right;
        }

        html[dir="rtl"] .form-label,
        html[dir="rtl"] .form-title {
            display: block;
            text-align: right;
        }

        html[dir="rtl"] .form-control,
        html[dir="rtl"] .custom-select,
        html[dir="rtl"] textarea,
        html[dir="rtl"] .search-input {
            text-align: right;
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
    </Style>

</head>

<body>

    <?php include('header.php'); ?>

    <div class="main-wrapper">

        <div class="table-section">

            <div class="search-wrapper">

                <form method="GET" class="search-form">

                    <input
                        type="text"
                        name="search"
                        class="search-input"
                        placeholder="<?= ($lang == 'fa') ? 'جستجوی کتاب‌ها...' : 'Search Books...'; ?>"
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

                            <th><?= ($lang == 'fa') ? 'کتګوری' : 'Category'; ?></th>

                            <th><?= ($lang == 'fa') ? 'نویسنده' : 'Author'; ?></th>

                            <th><?= ($lang == 'fa') ? 'دیپارتمنت' : 'Department'; ?></th>

                            <th><?= ($lang == 'fa') ? 'تعداد صفحات' : 'Pages'; ?></th>

                            <th><?= ($lang == 'fa') ? 'تاریخ نشر' : 'Publish Date'; ?></th>

                            <th><?= ($lang == 'fa') ? 'فایل PDF' : 'PDF File'; ?></th>
                        </tr>

                    </thead>

                    <tbody>
                        <?php $no = 1; ?>

                        <?php while ($row = $book_result->fetch_assoc()) { ?>

                            <tr>
                                <td><?= $no++; ?></td>

                                <td><?php echo $row['ID']; ?></td>

                                <td><?php echo $row['Title']; ?></td>

                                <td><?php echo $row['Description']; ?></td>

                                <td><?php echo $row['Category']; ?></td>

                                <td><?php echo $row['author_name']; ?></td>

                                <td><?php echo $row['department_name']; ?></td>

                                <td><?php echo $row['Pages']; ?></td>

                                <td><?php echo $row['Publish_Date']; ?></td>

                                <td>

                                    <?php if ($row['PDF_File'] != "") { ?>

                                        <a href="../PDF_File/<?php echo $row['PDF_File']; ?>"
                                            target="_blank"
                                            class="pdf-btn">

                                            <?= ($lang == 'fa') ? 'نمایش PDF' : 'View PDF'; ?>

                                        </a>

                                    <?php } else { ?>

                                        <?= ($lang == 'fa') ? 'فایلی موجود نیست' : 'No File'; ?>

                                    <?php } ?>

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

                <div class="form-title" style="text-align: center; font-size: 1rem;">
                    <?= ($lang == 'fa') ? 'اضافه نمودن کتاب' : 'Add Book'; ?>
                </div>

                <form method="POST"
                    enctype="multipart/form-data">

                    <?php if (!empty($error)) { ?>
                        <div class="alert alert-danger py-2 mb-2">
                            <?= $error; ?>
                        </div>
                    <?php } ?>
                    <div class="mb-2">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'آی دی' : 'ID'; ?>
                        </label>
                        <input type="text"
                            name="id"
                            class="form-control"
                            required
                            value="<?= isset($_POST['id']) ? htmlspecialchars($_POST['id']) : ''; ?>">
                    </div>

                    <div class="mb-2">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'عنوان' : 'Title'; ?>
                        </label>

                        <input type="text"
                            name="title"
                            class="form-control"
                            required>
                    </div>

                    <div class="mb-2">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'توضیحات' : 'Description'; ?>
                        </label>

                        <textarea name="description"
                            class="form-control"
                            required></textarea>

                    </div>

                    <div class="mb-2">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'کتگوری' : 'Category'; ?>
                        </label>

                        <input type="text"
                            name="category"
                            class="form-control"
                            required>

                    </div>

                    <div class="mb-2">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'نویسنده' : 'Author'; ?>
                        </label>

                        <select name="author"
                            class="custom-select"
                            required>

                            <option value="">
                                <?= ($lang == 'fa') ? 'انتخاب نویسنده' : 'Select Author'; ?>
                            </option>

                            <?php

                            $teacher = $conn->query("SELECT * FROM teacher");

                            while ($t = $teacher->fetch_assoc()) {

                            ?>

                                <option value="<?php echo $t['ID']; ?>">

                                    <?php echo $t['Name']; ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <div class="mb-2">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'دیپارتمنت' : 'Department'; ?>
                        </label>

                        <select name="department"
                            class="custom-select"
                            required>

                            <option value="">
                                <?= ($lang == 'fa') ? 'انتخاب دیپارتمنت' : 'Select Department'; ?>
                            </option>

                            <?php

                            $dep = $conn->query("SELECT * FROM department");

                            while ($d = $dep->fetch_assoc()) {

                            ?>

                                <option value="<?php echo $d['ID']; ?>">

                                    <?php echo $d['Name']; ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <div class="mb-2">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'صفحات' : 'Pages'; ?>
                        </label>

                        <input type="number"
                            name="pages"
                            class="form-control"
                            required>

                    </div>

                    <div class="mb-2">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'فایل PDF' : 'PDF File'; ?>
                        </label>

                        <input type="file"
                            name="pdf_file"
                            class="form-control">

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            <?= ($lang == 'fa') ? 'تاریخ انتشار' : 'Publish Date'; ?>
                        </label>

                        <input type="date"
                            name="publish_date"
                            class="form-control"
                            required>

                    </div>


                    <div class="form-buttons">

                        <button type="submit"
                            class="save-btn"
                            name="save_book">
                            <?= ($lang == 'fa') ? 'ذخیره کتاب' : 'Save Book'; ?>
                        </button>

                        <button type="button"
                            class="cancel-btn"
                            onclick="window.location.href='books.php'">
                            <?= ($lang == 'fa') ? 'لغو' : 'Cancel'; ?>
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</body>

</html>