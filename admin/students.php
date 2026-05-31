<?php
include('../auth.php');


include('../db_connection.php');

// ===========================
// Insert Student
// ===========================

if (isset($_POST['save_student'])) {

    $id          = $_POST['id'];
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
// Delete Student
// ===========================

if (isset($_GET['delete'])) {

    $delete_id = $_GET['delete'];

    // Delete related thesis

    $conn->query("DELETE FROM thesis
    WHERE Student_ID='$delete_id'");

    // Delete related articles

    $conn->query("DELETE FROM articles
    WHERE Student_ID='$delete_id'");


    // Delete student

    $conn->query("DELETE FROM students
    WHERE ID='$delete_id'");

    header("Location: students.php");
    exit();
}
// ===========================
// Edit Student
// ===========================

$edit_id         = "";
$edit_name       = "";
$edit_lastname   = "";
$edit_email      = "";
$edit_contact    = "";
$edit_department = "";

if (isset($_GET['edit'])) {

    $edit_id = $_GET['edit'];

    $edit_query = "SELECT * FROM students
    WHERE ID='$edit_id'";

    $edit_result = $conn->query($edit_query);

    if ($edit_result->num_rows > 0) {

        $edit_row = $edit_result->fetch_assoc();

        $edit_name       = $edit_row['Name'];
        $edit_lastname   = $edit_row['Last_Name'];
        $edit_email      = $edit_row['Email'];
        $edit_contact    = $edit_row['Contact'];
        $edit_department = $edit_row['Department'];
    }
}

// ===========================
// Update Student
// ===========================

if (isset($_POST['update_student'])) {

    $id          = $_POST['id'];
    $name        = $_POST['name'];
    $lastname    = $_POST['lastname'];
    $email       = $_POST['email'];
    $contact     = $_POST['contact'];
    $department  = $_POST['department'];

    $update_query = "UPDATE students SET

    Name='$name',
    Last_Name='$lastname',
    Email='$email',
    Contact='$contact',
    Department='$department'

    WHERE ID='$id'";

    $conn->query($update_query);

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
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Students</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet"
        href="../css/bootstrap.min.css">

    <script src="../js/bootstrap.bundle.min.js"></script>

</head>

<body>
    <?php include('header.php'); ?>

    <div class="main-wrapper">

        <!-- TABLE SECTION -->

        <div class="table-section">

            <!-- SEARCH -->

            <div class="search-wrapper">

                <form method="GET"
                    class="search-form">

                    <input type="text"

                        name="search"

                        class="search-input"

                        placeholder="Search students..."

                        value="<?php echo $search; ?>">

                    <button type="submit"
                        class="search-btn">

                        Search

                    </button>

                </form>

            </div>

            <!-- TABLE -->

            <div class="table-card">

                <table class="table table-hover">

                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Name</th>

                            <th>Last Name</th>

                            <th>Email</th>

                            <th>Contact</th>

                            <th>Department</th>

                            <th width="160">Action</th>

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

                                <td>

                                    <div class="action-icons">

                                        <a href="students.php?edit=<?php echo $row['ID']; ?>"
                                            class="edit-btn">

                                            Edit

                                        </a>

                                        <a href="students.php?delete=<?php echo $row['ID']; ?>"

                                            class="delete-btn"

                                            onclick="return confirm('Are you sure you want to delete this Student? It will delete all data that related to this students in other tables.')">

                                            Delete

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
                        ? "Edit Student"
                        : "Add Student";
                    ?>

                </div>

                <form method="POST">

                    <input type="hidden"

                        name="id"

                        value="<?php echo $edit_id; ?>">
                    <!-- ID -->

                    <div class="mb-3">

                        <label class="form-label">

                            ID

                        </label>

                        <input type="text"

                            name="id"

                            class="form-control"

                            placeholder="Enter Student ID"

                            value="<?php echo $edit_id; ?>"

                            <?php echo isset($_GET['edit']) ? 'readonly' : ''; ?>

                            required>

                    </div>

                    <!-- Name -->

                    <div class="mb-3">

                        <label class="form-label">

                            Name

                        </label>

                        <input type="text"

                            name="name"

                            class="form-control"

                            placeholder="Enter student name"

                            value="<?php echo $edit_name; ?>"

                            required>

                    </div>

                    <!-- Last Name -->

                    <div class="mb-3">

                        <label class="form-label">

                            Last Name

                        </label>

                        <input type="text"

                            name="lastname"

                            class="form-control"

                            placeholder="Enter last name"

                            value="<?php echo $edit_lastname; ?>"

                            required>

                    </div>

                    <!-- Email -->

                    <div class="mb-3">

                        <label class="form-label">

                            Email

                        </label>

                        <input type="email"

                            name="email"

                            class="form-control"

                            placeholder="Enter email"

                            value="<?php echo $edit_email; ?>"

                            required>

                    </div>

                    <!-- Contact -->

                    <div class="mb-3">

                        <label class="form-label">

                            Contact

                        </label>

                        <input type="text"

                            name="contact"

                            class="form-control"

                            placeholder="Enter contact"

                            value="<?php echo $edit_contact; ?>"

                            required>

                    </div>

                    <!-- Department -->

                    <div class="mb-4">

                        <label class="form-label">

                            Department

                        </label>

                        <select name="department"
                            class="custom-select">

                            <?php

                            $department_query = "SELECT * FROM department";

                            $department_result = $conn->query($department_query);

                            while ($department = $department_result->fetch_assoc()) {

                            ?>

                                <option value="<?php echo $department['ID']; ?>"

                                    <?php
                                    if ($edit_department == $department['ID']) {
                                        echo "selected";
                                    }
                                    ?>>

                                    <?php echo $department['Name']; ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <!-- BUTTON -->

                    <button class="save-btn"

                        name="<?php
                                echo isset($_GET['edit'])
                                    ? 'update_student'
                                    : 'save_student';
                                ?>">

                        <?php
                        echo isset($_GET['edit'])
                            ? 'Update Student'
                            : 'Save Student';
                        ?>

                    </button>

                </form>

            </div>

        </div>

    </div>

</body>

</html>