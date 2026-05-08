<?php
// config/database.php

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "research_management_system_of_ict";

// Create Connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check Connection
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}
