<?php
// Database connection settings for XAMPP
$host = "localhost";
$username = "root";
$password = "";
$database = "student_registration";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed.");
}

$conn->set_charset("utf8mb4");
?>
