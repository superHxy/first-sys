<?php
ob_start(); // Start output buffering

require_once 'config_copy.php';
require_once 'function_copy.php';
include('sidebar.php');

// Error and success messages
$error_message = $success_message = '';

// Fetch existing schedules with error handling
$schedules = [];
try {
    // Fetch schedules and ensure it's an array
    $fetchedSchedules = get_schedules();
    
    // Check if fetchedSchedules is actually an array
    if (is_array($fetchedSchedules)) {
        $schedules = $fetchedSchedules;
    } else {
        // Log the error or set an error message
        error_log('get_schedules() did not return an array');
        $error_message = "Unable to retrieve schedules. Please contact support.";
    }
} catch (Exception $e) {
    // Catch any exceptions from get_schedules()
    error_log('Error fetching schedules: ' . $e->getMessage());
    $error_message = "Error retrieving schedules: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course = trim($_POST['course']);
    $lecturer = trim($_POST['lecturer']);
    $room = trim($_POST['room']);
    $schedule_date = trim($_POST['schedule_date']);
    $start_time = trim($_POST['start_time']);
    $end_time = trim($_POST['end_time']);
    $status = $_POST['status'];

    // Validate input
    if (empty($course) || empty($lecturer) || empty($room) || empty($schedule_date) || empty($start_time) || empty($end_time) || empty($status)) {
        $error_message = "All fields are required!";
    } elseif (strtotime($start_time) >= strtotime($end_time)) {
        $error_message = "Start time must be before end time.";
    } else {
        if (add_schedule($course, $lecturer, $room, $schedule_date, $start_time, $end_time, $status)) {
            $success_message = "Schedule added successfully!";
            header("Location: ad_dashboard_copy.php?success=1");
            exit; // Exit to avoid form resubmission
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <div class="main-content">
        <!-- Error Handling -->
        <?php if (!empty($error_message)): ?>
            <div class="bg-red-200 text-red-800 px-4 py-2 rounded mb-4">
                <?= htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="bg-green-200 text-green-800 px-4 py-2 rounded mb-4">
                <?= htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-semibold">
                    <span class="mr-2">
                        <i class="fas fa-calendar-check text-blue-600"></i>
                    </span>
                    Schedule List
                </h2>

                <div class="flex space-x-4">
                    <div class="relative">
                        <input 
                            type="text" 
                            id="scheduleFilter" 
                            placeholder="Search schedules..." 
                            class="w-64 px-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-red-600 pl-10"
                        >
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>

                    <a href="history.php" 
                    class="flex items-center text-gray-600 hover:text-red-600 transition-all duration-300 ease-in-out">
                        <i class="fas fa-history mr-2"></i> History
                    </a>
                    <a href="add_schedule2.php" 
                    class="flex items-center px-5 py-2 border-2 border-red-600 text-red-600 bg-white rounded-full hover:bg-red-600 hover:text-white transition-all duration-300 ease-in-out shadow-md">
                        <i class="fas fa-plus-circle mr-2"></i> Add Events
                    </a>
                </div>
            </div>

            <table id="schedulesTable" class="w-full border-collapse">
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
                    <?php if (!empty($schedules)): ?>
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
                                    <a href="edit.php?id=<?= htmlspecialchars($schedule['id']); ?>" class="text-blue-600 hover:underline">Edit</a> | 
                                    <a href="delete.php?id=<?= htmlspecialchars($schedule['id']); ?>" class="text-red-600 hover:underline" onclick="return confirm('Are you sure?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-gray-500">
                                No schedules found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- JavaScript for filtering -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterInput = document.getElementById('scheduleFilter');
            const table = document.getElementById('schedulesTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            filterInput.addEventListener('keyup', function() {
                const filterValue = this.value.toLowerCase().trim();

                for (let row of rows) {
                    // Skip the "No schedules found" row
                    if (row.querySelector('td[colspan]')) continue;

                    let match = false;
                    const cells = row.getElementsByTagName('td');

                    // Check each cell for a match
                    for (let cell of cells) {
                        if (cell.textContent.toLowerCase().includes(filterValue)) {
                            match = true;
                            break;
                        }
                    }

                    // Show or hide row based on match
                    row.style.display = match ? '' : 'none';
                }
            });
        });
    </script>

<?php ob_end_flush(); ?>
</body>
</html>