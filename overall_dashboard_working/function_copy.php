<?php
// functions.php

require_once 'config_copy.php';

function get_schedules() {
    global $conn;
    $query = "SELECT * FROM schedules ORDER BY date_time ASC";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function get_events($limit = 3) {
    global $conn;
    $query = "SELECT * FROM events ORDER BY date DESC LIMIT $limit";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function get_news($limit = 5) {
    global $conn;
    $query = "SELECT * FROM news ORDER BY date DESC LIMIT $limit";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function add_schedule($course, $lecturer, $room, $date_time, $status) {
    global $conn;
    $query = "INSERT INTO schedules (course, lecturer, room, date_time,  status) VALUES ( ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $course, $lecturer, $room, $date_time, $status);
    return $stmt->execute();
}
?>