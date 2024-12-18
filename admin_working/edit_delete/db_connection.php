<?php
// Database connection
$servername = "localhost"; // Replace with your server name
$username = "root";     // Replace with your database username
$password = "@Hxy080904997788nhim";     // Replace with your database password
$database = "sample2_db"; // Replace with your database name

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to fetch schedule entries
function fetch_schedule_entries($conn) {
    $query = "SELECT * FROM schedules"; // Select all entries from schedule_list table
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // Fetch all entries
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        return [];
    }
}
?>
