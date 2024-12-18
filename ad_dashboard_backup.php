<?php
include('sidebar.php');
require_once 'function_copy.php';
require_once 'config_copy.php';

// Transfer expired schedules (function logic goes here)


// Success and error messages
$success_message = $error_message = '';

// Fetch schedules
$schedules = get_schedules();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['edit_schedule_id']) ? intval($_POST['edit_schedule_id']) : null;
    $course = htmlspecialchars($_POST['course']);
    $lecturer = htmlspecialchars($_POST['lecturer']);
    $room = htmlspecialchars($_POST['room']);
    $schedule_date = htmlspecialchars($_POST['schedule_date']);
    $start_time = htmlspecialchars($_POST['start_time']);
    $end_time = htmlspecialchars($_POST['end_time']);
    $status = htmlspecialchars($_POST['status']);

    if (isset($id) && $id > 0) {
        // Update existing schedule
        if (update_schedule($id, $course, $lecturer, $room, $schedule_date, $start_time, $end_time, $status)) {
            $success_message = "Schedule updated successfully!";
            header("Location: ad_dashboard_copy.php?success=1");
            exit;
        } else {
            $error_message = "Error updating schedule. Please try again.";
        }
    } else {
        // Add new schedule
        if (add_schedule($course, $lecturer, $room, $schedule_date, $start_time, $end_time, $status)) {
            $success_message = "Schedule added successfully!";
            header("Location: ad_dashboard_copy.php?success=1");
            exit;
        } else {
            $error_message = "Error adding schedule. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script>
        function searchSchedules() {
            const searchQuery = document.getElementById('search').value.toLowerCase();
            document.querySelectorAll('.schedule-row').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(searchQuery) ? '' : 'none';
            });
        }
    </script>
</head>
<body>
    <div class="main-content">
        <h1 class="text-xl font-bold mb-4">Schedule Management</h1>

        <?php if ($success_message): ?>
            <div class="bg-green-200 text-green-800 px-4 py-2 rounded mb-4">
                <?= $success_message; ?>
            </div>
        <?php elseif ($error_message): ?>
            <div class="bg-red-200 text-red-800 px-4 py-2 rounded mb-4">
                <?= $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-700 flex items-center">
                    <span class="mr-2">
                        <i class="fas fa-calendar-alt text-red-600"></i>
                    </span>
                    Schedule List
                </h2>
                <div class="flex space-x-4">
                    <a href="history.php" class="flex items-center text-gray-600 hover:text-red-600 transition-all duration-300 ease-in-out">
                        <i class="fas fa-history mr-2"></i> History
                    </a>
                    <a href="add_schedule2.php" class="flex items-center px-5 py-2 border-2 border-red-600 text-red-600 bg-white rounded-full hover:bg-red-600 hover:text-white transition-all duration-300 ease-in-out shadow-md">
                        <i class="fas fa-plus-circle mr-2"></i> Add Schedule
                    </a>
                </div>
            </div>

            <input type="text" id="search" placeholder="Search schedule..." class="w-full px-3 py-2 mb-4 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" onkeyup="searchSchedules()">

            <table class="table-auto w-full bg-white border border-gray-200 rounded-lg shadow-md">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2">Course</th>
                        <th class="px-4 py-2">Lecturer</th>
                        <th class="px-4 py-2">Room</th>
                        <th class="px-4 py-2">Date</th>
                        <th class="px-4 py-2">Start Time</th>
                        <th class="px-4 py-2">End Time</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedules as $schedule): ?>
                        <?php
                        $now = date("Y-m-d H:i:s");
                        $statusColor = '';

                        switch ($schedule['status']) {
                            case 'usual':
                                $statusColor = 'text-green-500';
                                break;
                            case 'no-class':
                                $statusColor = 'text-red-700';
                                break;
                            case 'make-up':
                                $statusColor = 'text-yellow-400';
                                break;
                        }
                        ?>
                        <tr class="schedule-row <?= strtotime($schedule['end_time']) < strtotime($now) ? 'text-gray-400 line-through' : '' ?>">
                            <td class="border px-4 py-2"><?= htmlspecialchars($schedule['course']); ?></td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($schedule['lecturer']); ?></td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($schedule['room']); ?></td>
                            <td class="border px-4 py-2"><?= date("Y-m-d", strtotime($schedule['schedule_date'])); ?></td>
                            <td class="border px-4 py-2"><?= date("H:i", strtotime($schedule['start_time'])); ?></td>
                            <td class="border px-4 py-2"><?= date("H:i", strtotime($schedule['end_time'])); ?></td>
                            <td class="border px-4 py-2 <?= $statusColor; ?>">
                                <?= ucfirst($schedule['status']); ?>
                            </td>
                            <td class="border px-4 py-2">
                                <a href="edit.php?id=<?= $schedule['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                                <a href="delete.php?id=<?= $schedule['id']; ?>" class="text-red-600 hover:underline ml-4">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
