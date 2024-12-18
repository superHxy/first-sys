<?php
require_once 'config_copy.php';

// Move past events to event_history
$sql = "SELECT * FROM events WHERE CONCAT(event_date, ' ', end_time) < NOW()";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    // Insert into event_history
    $stmt = $conn->prepare("INSERT INTO event_history (title, description, event_date, start_time, end_time, location, media_file) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "sssssss",
        $row['title'],
        $row['description'],
        $row['event_date'],
        $row['start_time'],
        $row['end_time'],
        $row['location'],
        $row['media_file']
    );
    $stmt->execute();

    // Delete from active events
    $deleteStmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $deleteStmt->bind_param("i", $row['id']);
    $deleteStmt->execute();
}

// Redirect back to event list or cron-friendly output
echo "Past events moved successfully!";
?>
