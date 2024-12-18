<?php
require_once 'config_copy.php';
require_once 'function_copy.php';

$error_message = $success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course = trim($_POST['course']);
    $lecturer = trim($_POST['lecturer']);
    $room = trim($_POST['room']);
    $date_time = $_POST['date_time'];
    $end_time = $_POST['end_time'];
    $status = $_POST['status'];

    // Validate inputs
    if (empty($course) || empty($lecturer) || empty($room) || empty($date_time) || empty($end_time) || empty($status)) {
        $error_message = "All fields are required.";
    } elseif (!preg_match("/^[a-zA-Z0-9\s]+$/", $course) || !preg_match("/^[a-zA-Z\s]+$/", $lecturer) || !preg_match("/^[a-zA-Z0-9\s]+$/", $room)) {
        $error_message = "Invalid characters detected in course, lecturer, or room.";
    } elseif (strtotime($date_time) === false || strtotime($end_time) === false) {
        $error_message = "Invalid date or time format.";
    } else {
        if (add_schedule($course, $lecturer, $room, $date_time, $end_time, $status)) {
            $success_message = "Schedule added successfully!";
            header("Location: ad_dashboard_copy.php?success=1");
            exit;
        } else {
            $error_message = "Error adding schedule. Please try again.";
        }
    }
}
?>
<?php include('sidebar.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Schedule</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <div class="main-content">
        <h1 class="text-2xl font-bold mb-4">Add New Schedule</h1>

        <?php if (!empty($success_message)): ?>
            <div class="bg-green-200 text-green-800 px-4 py-2 rounded mb-4">
                <?= htmlspecialchars($success_message); ?>
            </div>
        <?php elseif (!empty($error_message)): ?>
            <div class="bg-red-200 text-red-800 px-4 py-2 rounded mb-4">
                <?= htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form action="add_schedule2.php" method="POST" class="bg-white p-6 rounded-lg shadow-md">
            <div class="mb-4">
                <label for="course" class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                <input type="text" id="course" name="course" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-4">
                <label for="lecturer" class="block text-sm font-medium text-gray-700 mb-1">Lecturer</label>
                <input type="text" id="lecturer" name="lecturer" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-4">
                <label for="room" class="block text-sm font-medium text-gray-700 mb-1">Room</label>
                <input type="text" id="room" name="room" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-4">
                <label for="date_time" class="block text-sm font-medium text-gray-700 mb-1">Date & Time</label>
                <input type="datetime-local" id="date_time" name="date_time" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-4">
                <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                <input type="datetime-local" id="end_time" name="end_time" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="usual">Usual</option>
                    <option value="no-class">No Class</option>
                    <option value="make-up">Make-up</option>
                </select>
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" class="px-5 py-2 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition-all duration-300">
                    Add Schedule
                </button>
            </div>
        </form>
    </div>
</body>
</html>
