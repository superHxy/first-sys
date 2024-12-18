<?php
$host = 'localhost';       // Database Host
$user = 'root';            // Database Username
$password = '@Hxy080904997788nhim';            // Database Password
$dbname = 'university_dashboard'; // Replace with your database name

$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
