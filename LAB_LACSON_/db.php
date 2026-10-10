<?php
$host     = "localhost";
$username = "root";
$password = "";
$database = "login_system";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    error_log("Connection error: " . $conn->connect_error);
    exit("Database connection failed. Please ensure MySQL is running.");
}

$conn->set_charset("utf8mb4");
?>