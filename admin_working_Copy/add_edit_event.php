<?php
require_once 'config_copy.php';
require_once 'function_copy.php';

$editRecord = null;
$uploadError = null; // Initialize error message variable

// Start session for CSRF protection
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch all events from the database
$sql = "SELECT * FROM events ORDER BY event_date DESC";
$result = $conn->query($sql);

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $id = intval($_POST['id']);

    // Get media file path
    $stmt = $conn->prepare("SELECT media_file FROM events WHERE id=?");
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

    header('Location: events_list.php'); // Reload page
    exit();
}

// Handle Add/Edit actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF token validation
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Invalid CSRF token');
    }

    // Sanitize user inputs
    $title = htmlspecialchars(trim($_POST['title']));
    $description = htmlspecialchars(trim($_POST['description']));
    $eventDate = htmlspecialchars(trim($_POST['event_date']));
    $startTime = htmlspecialchars(trim($_POST['start_time']));
    $endTime = htmlspecialchars(trim($_POST['end_time']));
    $location = htmlspecialchars(trim($_POST['location']));
    $mediaFilePath = null;

    // Validate required fields
    if (empty($title) || empty($description) || empty($eventDate) || empty($startTime) || empty($endTime) || empty($location)) {
        die('Please fill in all required fields.');
    }

    // File upload handling
    if (isset($_FILES['media_file']) && $_FILES['media_file']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4'];
        $maxFileSize = 10 * 1024 * 1024; // 10MB

        $fileType = mime_content_type($_FILES['media_file']['tmp_name']);
        $fileSize = $_FILES['media_file']['size'];

        if (!in_array($fileType, $allowedTypes)) {
            $uploadError = 'Invalid file type. Only images and videos are allowed.';
        } elseif ($fileSize > $maxFileSize) {
            $uploadError = 'File size exceeds the maximum limit of 10MB.';
        } else {
            $uploadDir = 'uploads/';
            $uploadFile = $uploadDir . uniqid() . '_' . basename($_FILES['media_file']['name']);
            if (move_uploaded_file($_FILES['media_file']['tmp_name'], $uploadFile)) {
                $mediaFilePath = $uploadFile;
            } else {
                $uploadError = 'Failed to upload file.';
            }
        }
    }

    if (empty($uploadError)) {
        if (isset($_POST['add'])) {
            $sql = "INSERT INTO events (title, description, event_date, start_time, end_time, location, media_file, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sssssss', $title, $description, $eventDate, $startTime, $endTime, $location, $mediaFilePath);
            $stmt->execute();

            header('Location: events_list.php');
            exit();
        } elseif (isset($_POST['edit-save'])) {
            $id = intval($_POST['id']);

            // Optional: Delete the old file if a new one is uploaded
            if (!empty($mediaFilePath)) {
                $stmt = $conn->prepare("SELECT media_file FROM events WHERE id=?");
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $oldFile = $stmt->get_result()->fetch_assoc()['media_file'];
                if ($oldFile && file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            $sql = "UPDATE events SET title=?, description=?, event_date=?, start_time=?, end_time=?, location=?, media_file=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sssssssi', $title, $description, $eventDate, $startTime, $endTime, $location, $mediaFilePath, $id);
            $stmt->execute();

            header('Location: events_list.php');
            exit();
        }
    }
}

// If editing, fetch the record
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $sql = "SELECT * FROM events WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $editRecord = $stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $editRecord ? 'Edit Event' : 'Add Event' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <?php include('sidebar.php'); ?>
    <div class="main-content p-6">
        <h1 class="text-3xl font-bold mb-6"><?= $editRecord ? 'Edit Event' : 'Add Event' ?></h1>

        <form method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow-md">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <?php if ($editRecord): ?>
                <input type="hidden" name="id" value="<?= $editRecord['id'] ?>">
            <?php endif; ?>

            <label class="block mb-2 font-medium">Title:</label>
            <input type="text" name="title" required class="w-full p-2 border rounded mb-4" value="<?= $editRecord['title'] ?? '' ?>">

            <label class="block mb-2 font-medium">Description:</label>
            <textarea name="description" required class="w-full p-2 border rounded mb-4"><?= $editRecord['description'] ?? '' ?></textarea>

            <label class="block mb-2 font-medium">Event Date:</label>
            <input type="date" name="event_date" required class="w-full p-2 border rounded mb-4" value="<?= $editRecord['event_date'] ?? '' ?>">

            <label class="block mb-2 font-medium">Start Time:</label>
            <input type="time" name="start_time" required class="w-full p-2 border rounded mb-4" value="<?= $editRecord['start_time'] ?? '' ?>">

            <label class="block mb-2 font-medium">End Time:</label>
            <input type="time" name="end_time" required class="w-full p-2 border rounded mb-4" value="<?= $editRecord['end_time'] ?? '' ?>">

            <label class="block mb-2 font-medium">Location:</label>
            <input type="text" name="location" required class="w-full p-2 border rounded mb-4" value="<?= $editRecord['location'] ?? '' ?>">

            <label class="block mb-2 font-medium">Upload Picture or Video:</label>
            <input type="file" name="media_file" class="w-full p-2 border rounded mb-4">
            <?php if (!empty($uploadError)): ?>
                <p class="text-red-500 mb-4"><?= $uploadError ?></p>
            <?php endif; ?>

            <button 
                type="submit" 
                name="<?= $editRecord ? 'edit-save' : 'add' ?>" 
                class="mt-6 px-4 py-2 border-2 border-red-800 text-red-800 bg-white rounded-lg hover:bg-red-800 hover:text-white transition-all duration-300 ease-in-out">
                <?= $editRecord ? 'Update Event' : 'Add Event' ?>
            </button>

            <a href="events_list.php" 
                class="inline-block mt-6 px-4 py-2 border-2 border-red-800 text-red-800 bg-white rounded-lg hover:bg-red-800 hover:text-white transition-all duration-300 ease-in-out">
                Cancel
            </a>
        </form>
    </div>
</body>
</html>
