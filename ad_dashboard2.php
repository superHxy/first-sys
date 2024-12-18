<?php
ob_start(); // Start output buffering

require_once 'config_copy.php';
require_once 'function_copy.php';
include('sidebar.php');

// Error and success messages
$error_message = $success_message = '';



// Fetch existing schedules
$schedules = get_schedules();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <div class="main-content">

        <!-- Schedule List -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-4">Schedule List</h2>
            <a href="add_schedule2.php" class="text-blue-600 hover:underline mb-4 block">Add New Schedule</a>
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="border px-4 py-2">Course</th>
                        <th class="border px-4 py-2">Lecturer</th>
                        <th class="border px-4 py-2">Room</th>
                        <th class="border px-4 py-2">Date</th>
                        <th class="border px-4 py-2">Start Time</th>
                        <th class="border px-4 py-2">End Time</th>
                        <th class="border px-4 py-2">Status</th>
                        <th class="border px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedules as $schedule): ?>
                        <tr>
                            <td class="border px-4 py-2"><?= htmlspecialchars($schedule['course']); ?></td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($schedule['lecturer']); ?></td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($schedule['room']); ?></td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($schedule['schedule_date']); ?></td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($schedule['start_time']); ?></td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($schedule['end_time']); ?></td>
                            <td class="border px-4 py-2">
                                <span class="px-2 py-1 rounded bg-<?= $schedule['status'] == 'no-class' ? 'red' : ($schedule['status'] == 'make-up' ? 'yellow' : 'green'); ?>-200 text-<?= $schedule['status'] == 'no-class' ? 'red' : ($schedule['status'] == 'make-up' ? 'yellow' : 'green'); ?>-800">
                                    <?= htmlspecialchars(ucfirst($schedule['status'])); ?>
                                </span>
                            </td>
                            <td class="border px-4 py-2">
                                <a href="edit.php?id=<?= $schedule['id']; ?>" class="text-blue-600 hover:underline">Edit</a> | 
                                <a href="delete.php?id=<?= $schedule['id']; ?>" class="text-red-600 hover:underline" onclick="return confirm('Are you sure?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php ob_end_flush(); ?>
</body>
</html>
