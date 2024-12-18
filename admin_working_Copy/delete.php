<?php
include('config_copy.php');

$id = $_GET['id'];

$query = "DELETE FROM schedules WHERE id = $id";

if (mysqli_query($conn, $query)) {
    header('Location: ad_dashboard_copy.php');
} else {
    echo "Error deleting record: " . mysqli_error($conn);
}
