<?php
// schedule.php

require_once 'function_copy.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course = trim($_POST['course']);
    $lecturer = trim($_POST['lecturer']);
    $room = trim($_POST['room']);
    $schedule_date = trim($_POST['schedule_date']);
    $start_time = trim($_POST['start_time']);
    $end_time = trim($_POST['end_time']);
    $status = $_POST['status'];

    if (add_schedule($course, $lecturer, $room, $schedule_date,$start_time, $end_time, $status)) {
        $success_message = "Schedule added successfully!";
    } else {
        $error_message = "Error adding schedule. Please try again.";
    }
}

$schedules = get_schedules();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EAMU - Schedule Management</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Schedule Management</h1>

        <?php if (isset($success_message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-xl font-semibold mb-4">Add Schedule</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="course" class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                    <input type="text" id="course" name="course" required class="w-full px-3 py-2 border rounded-md">
                </div>
                <div>
                    <label for="lecturer" class="block text-sm font-medium text-gray-700 mb-1">Lecturer</label>
                    <input type="text" id="lecturer" name="lecturer" required class="w-full px-3 py-2 border rounded-md">
                </div>
                <div>
                    <label for="room" class="block text-sm font-medium text-gray-700 mb-1">Room</label>
                    <input type="text" id="room" name="room" required class="w-full px-3 py-2 border rounded-md">
                </div>
                <div>
                    <label for="schedule_date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="datetime-local" id="schedule_date" name="schedule_date" required class="w-full px-3 py-2 border rounded-md">
                </div>
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                    <input type="datetime-local" id="start_time" name="start_time" required class="w-full px-3 py-2 border rounded-md">
                </div>
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                    <input type="datetime-local" id="end" name="end_time" required class="w-full px-3 py-2 border rounded-md">
                </div>
            </div>

            <div class="mt-4">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status" class="w-full px-3 py-2 border rounded-md">
                    <option value="usual">Usual Class</option>
                    <option value="make-up">Make Up Class</option>
                    <option value="no-class">No Class</option>
                </select>
            </div>
            <button type="submit" class="mt-6 px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">Add Schedule</button>
        </form>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Schedule List</h2>
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="text-left">Course</th>
                        <th class="text-left">Lecturer</th>
                        <th class="text-left">Room</th>
                        <th class="text-left">Date</th>
                        <th class="text-left">Start Time</th>
                        <th class="text-left">End Time</th>
                        <th class="text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedules as $schedule): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($schedule['course']); ?></td>
                            <td><?php echo htmlspecialchars($schedule['lecturer']); ?></td>
                            <td><?php echo htmlspecialchars($schedule['room']); ?></td>
                            <td><?php echo htmlspecialchars($schedule['schedule_date']); ?></td>
                            <td><?php echo htmlspecialchars($schedule['start_time']); ?></td>
                            <td><?php echo htmlspecialchars($schedule['end_time']); ?></td>
                            <td>
                                <?php
                                $status_class = '';
                                switch ($schedule['status']) {
                                    case 'make-up':
                                        $status_class = 'bg-yellow-200 text-yellow-800';
                                        break;
                                    case 'no-class':
                                        $status_class = 'bg-red-200 text-red-800';
                                        break;
                                    default:
                                        $status_class = 'bg-green-200 text-green-800';
                                }
                                ?>
                                <span class="px-2 py-1 rounded <?php echo $status_class; ?>">
                                    <?php echo htmlspecialchars(ucfirst($schedule['status'])); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>