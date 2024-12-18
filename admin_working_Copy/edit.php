<?php
ob_start(); // Start output buffering
require_once 'config_copy.php';
include 'sidebar.php';

// Check for database connection errors early
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch schedule for editing
$id = intval($_GET['id'] ?? 0);
$schedule = null;

if ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM schedules WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $schedule = $result->fetch_assoc();
    $stmt->close();
}

// Update logic if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval(trim($_POST['id']));
    $course = trim($_POST['course']);
    $lecturer = trim($_POST['lecturer']);
    $room = trim($_POST['room']);
    $schedule_date = trim($_POST['schedule_date']);
    $start_time = trim($_POST['start_time']);
    $end_time = trim($_POST['end_time']);
    $status = trim($_POST['status']);

    // Input validation
    if ($id > 0 && $course && $lecturer && $room && $schedule_date && $start_time && $end_time && $status) {
        $stmt = $conn->prepare("UPDATE schedules 
                                SET course = ?, lecturer = ?, room = ?, schedule_date = ?, start_time = ?, end_time = ?, status = ?
                                WHERE id = ?");
        $stmt->bind_param("sssssssi", $course, $lecturer, $room, $schedule_date, $start_time, $end_time, $status, $id);

        if ($stmt->execute()) {
            header("Location: ad_dashboard_copy.php?success=1");
            exit;
        } else {
            $error_message = "Database Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error_message = "Please fill in all required fields.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Schedule</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="main-content flex-1 p-6">
        <h1 class="text-2xl font-bold mb-6">Edit Schedule</h1>

        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="bg-white p-6 rounded shadow mb-8">
            <input type="hidden" name="id" value="<?= htmlspecialchars($schedule['id'] ?? 0); ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="course" class="block text-sm font-medium text-gray-700">Course</label>
                    <input type="text" id="course" name="course" value="<?= htmlspecialchars($schedule['course'] ?? ''); ?>" required class="w-full px-3 py-2 border rounded">
                </div>
                <div>
                    <label for="lecturer" class="block text-sm font-medium text-gray-700">Lecturer</label>
                    <input type="text" id="lecturer" name="lecturer" value="<?= htmlspecialchars($schedule['lecturer'] ?? ''); ?>" required class="w-full px-3 py-2 border rounded">
                </div>
                <div>
                    <label for="room" class="block text-sm font-medium text-gray-700">Room</label>
                    <input type="text" id="room" name="room" value="<?= htmlspecialchars($schedule['room'] ?? ''); ?>" required class="w-full px-3 py-2 border rounded">
                </div>
                <div>
                    <label for="schedule_date" class="block text-sm font-medium text-gray-700">Date</label>
                    <input type="date" id="schedule_date" name="schedule_date" value="<?= htmlspecialchars($schedule['schedule_date'] ?? ''); ?>" required>
                </div>
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time</label>
                    <input type="time" id="start_time" name="start_time" value="<?= htmlspecialchars($schedule['start_time'] ?? ''); ?>" required>
                </div>
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700">End Time</label>
                    <input type="time" id="end_time" name="end_time" value="<?= htmlspecialchars($schedule['end_time'] ?? ''); ?>" required>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="status" name="status" class="w-full px-3 py-2 border rounded">
                        <option value="usual" <?= ($schedule['status'] ?? '') === 'usual' ? 'selected' : ''; ?>>Usual Class</option>
                        <option value="make-up" <?= ($schedule['status'] ?? '') === 'make-up' ? 'selected' : ''; ?>>Make-Up Class</option>
                        <option value="no-class" <?= ($schedule['status'] ?? '') === 'no-class' ? 'selected' : ''; ?>>No Class</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="mt-6 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save Changes</button>
        </form>
    </div>
<?php ob_end_flush(); ?>
</body>
</html>
