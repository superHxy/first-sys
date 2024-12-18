<?php
// config.php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '@Hxy080904997788nhim');
define('DB_NAME', 'sample2_db');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>