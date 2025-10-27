<?php
$host = "localhost";      // Your server (default for XAMPP)
$user = "root";           // Default XAMPP username
$pass = "";               // Default XAMPP has no password
$dbname = "flight_app";   // Your database name

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
