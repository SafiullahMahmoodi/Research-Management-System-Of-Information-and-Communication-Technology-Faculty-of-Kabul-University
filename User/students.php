<?php
include('../auth.php');


include('../db_connection.php');

// ===========================
// Insert Student
// ===========================

if(isset($_POST['save_student'])){

    $name        = $_POST['name'];
    $lastname    = $_POST['lastname'];
    $email       = $_POST['email'];
    $contact     = $_POST['contact'];
    $department  = $_POST['department'];

    $insert_query = "INSERT INTO students
    (Name, Last_Name, Email, Contact, Department)

    VALUES

    ('$name','$lastname','$email','$contact','$department')";

    $conn->query($insert_query);

    header("Location: students.php");
    exit();
}


// ===========================
// Search
// ===========================

$search = "";

if(isset($_GET['search'])){

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

}else{

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

                      

                    </tr>

                </thead>

                <tbody>

                <?php while($row = $student_result->fetch_assoc()){ ?>

                <tr>

                    <td><?php echo $row['ID']; ?></td>

                    <td><?php echo $row['Name']; ?></td>

                    <td><?php echo $row['Last_Name']; ?></td>

                    <td><?php echo $row['Email']; ?></td>

                    <td><?php echo $row['Contact']; ?></td>

                    <td><?php echo $row['department_name']; ?></td>

                </tr>

                <!-- DELETE MODAL -->

                <div class="modal fade"

                id="deleteModal<?php echo $row['ID']; ?>">

                    <div class="modal-dialog modal-dialog-centered">

                        <div class="modal-content"
                        style="border-radius:16px;">

                            <div class="modal-header bg-danger text-white">

                                <h5 class="modal-title">

                                    Delete Student

                                </h5>

                                <button class="btn-close btn-close-white"
                                data-bs-dismiss="modal">

                                </button>

                            </div>

                            <div class="modal-body text-center">

                                Delete

                                <strong>

                                    <?php echo $row['Name']; ?>

                                </strong>

                                ?

                            </div>

                            <div class="modal-footer">

                                <button class="btn btn-secondary"
                                data-bs-dismiss="modal">

                                    Cancel

                                </button>

                                <a href="students.php?delete=<?php echo $row['ID']; ?>"

                                class="btn btn-danger">

                                    Delete

                                </a>

                            </div>

                        </div>

                    </div>

                </div>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>
<!-- FORM SECTION -->

<div class="form-section">

    <div class="form-card">

        <div class="form-title">

            Add Student

        </div>

        <form method="POST">

            <!-- Name -->

            <div class="mb-3">

                <label class="form-label">

                    Name

                </label>

                <input type="text"

                name="name"

                class="form-control"

                placeholder="Enter student name"

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

                    while($department = $department_result->fetch_assoc()){

                    ?>

                    <option value="<?php echo $department['ID']; ?>">

                        <?php echo $department['Name']; ?>

                    </option>

                    <?php } ?>

                </select>

            </div>

            <!-- BUTTON -->

            <button class="save-btn"

            name="save_student">

                Save Student

            </button>

        </form>

    </div>

</div>

</div>

</body>
</html>