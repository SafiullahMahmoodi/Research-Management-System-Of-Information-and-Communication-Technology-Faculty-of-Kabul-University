<?php
include('../auth.php');



include('../db_connection.php');

// ===========================
// Insert Teacher
// ===========================
if (isset($_POST['save_teacher'])) {

    $name        = $_POST['name'];
    $lastname    = $_POST['lastname'];
    $email       = $_POST['email'];
    $contact     = $_POST['contact'];
    $education   = $_POST['education'];
    $department  = $_POST['department'];

    $conn->query("INSERT INTO teacher
    (Name, Last_Name, Email, Contact, Education, Department)

    VALUES

    ('$name','$lastname','$email','$contact','$education','$department')");

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
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Teachers</title>
    <link rel="stylesheet" href="style.css">

    <link rel="stylesheet"
        href="../css/bootstrap.min.css">

    <script src="../js/bootstrap.bundle.min.js"></script>

</head>

<body>

    <?php include('header.php'); ?>

    <div class="main-wrapper">

        <!-- TABLE -->

        <div class="table-section">

            <div class="search-wrapper">

                <form method="GET"
                    class="search-form">

                    <input type="text"

                        name="search"

                        class="search-input"

                        placeholder="Search teachers..."

                        value="<?php echo $search; ?>">

                    <button type="submit"
                        class="search-btn">

                        Search

                    </button>

                </form>

            </div>

            <div class="table-card">

                <table class="table table-hover">

                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Name</th>

                            <th>Last Name</th>

                            <th>Email</th>

                            <th>Contact</th>

                            <th>Education</th>

                            <th>Department</th>



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

                    Add Teacher

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

                            placeholder="Enter teacher name"

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

                    <!-- Education -->

                    <div class="mb-3">

                        <label class="form-label">

                            Education

                        </label>

                        <input type="text"

                            name="education"

                            class="form-control"

                            placeholder="Enter education"

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

                            $d = $conn->query("SELECT * FROM department");

                            while ($dep = $d->fetch_assoc()) {

                            ?>

                                <option value="<?php echo $dep['ID']; ?>">

                                    <?php echo $dep['Name']; ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <!-- Button -->

                    <button class="save-btn"

                        name="save_teacher">

                        Save Teacher

                    </button>

                </form>

            </div>

        </div>

    </div>

</body>

</html>