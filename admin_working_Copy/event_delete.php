<?php
require_once 'config_copy.php';

// Check if the request is POST and the ID is set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    // Get the media file path before deletion
    $sql = "SELECT media_file FROM events WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $mediaResult = $stmt->get_result()->fetch_assoc();
    
    if ($mediaResult && file_exists($mediaResult['media_file'])) {
        unlink($mediaResult['media_file']); // Delete the file
    }
    
    // Delete event from database
    $stmt = $conn->prepare("DELETE FROM events WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    header('Location: events_list.php'); // Redirect to events_list.php after deletion
    exit();
}
?>
