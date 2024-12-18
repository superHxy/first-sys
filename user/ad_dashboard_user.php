<?php
ob_start(); // Start output buffering

require_once '../admin_working_copy/config_copy.php';
require_once '../admin_working_copy/function_copy.php';
include('sidebar_user.php');

// Error and success messages
$error_message = $success_message = '';

// Fetch existing schedules
$schedules = get_schedules();

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
</head>
<body>
    <div class="main-content">
        <!-- Success or Error Messages -->
        <?php if (!empty($success_message)): ?>
            <div class="bg-green-200 text-green-800 px-4 py-2 rounded mb-4">
                <?= htmlspecialchars($success_message); ?>
            </div>
        <?php elseif (!empty($error_message)): ?>
            <div class="bg-red-200 text-red-800 px-4 py-2 rounded mb-4">
                <?= htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        <h1 class="text-2xl font-bold mb-4">Schedule Management</h1>
        <!-- Add Schedule Form -->
       
            
            <!-- <form action="ad_dashboard_copy.php" method="POST" class="grid grid-cols-2 gap-4">
                <div>
                    <label for="course" class="block text-sm font-medium">Course</label>
                    <input type="text" id="course" name="course" value="<?= isset($course) ? htmlspecialchars($course) : ''; ?>" class="w-full px-2 py-1 border rounded focus:ring-1 focus:ring-blue-500" required>
                </div>
                <div>
                    <label for="lecturer" class="block text-sm font-medium">Lecturer</label>
                    <input type="text" id="lecturer" name="lecturer" value="<?= isset($lecturer) ? htmlspecialchars($lecturer) : ''; ?>" class="w-full px-2 py-1 border rounded focus:ring-1 focus:ring-blue-500" required>
                </div>
                <div>
                    <label for="room" class="block text-sm font-medium">Room</label>
                    <input type="text" id="room" name="room" value="<?= isset($room) ? htmlspecialchars($room) : ''; ?>" class="w-full px-2 py-1 border rounded focus:ring-1 focus:ring-blue-500" required>
                </div>
                <div>
                    <label for="schedule_date" class="block text-sm font-medium">Date</label>
                    <input type="date" id="schedule_date" name="schedule_date" value="<?= isset($schedule_date) ? htmlspecialchars($schedule_date) : ''; ?>" class="w-full px-2 py-1 border rounded focus:ring-1 focus:ring-blue-500" required>
                </div>
                <div>
                    <label for="start_time" class="block text-sm font-medium">Start Time</label>
                    <input type="time" id="start_time" name="start_time" value="<?= isset($start_time) ? htmlspecialchars($start_time) : ''; ?>" class="w-full px-2 py-1 border rounded focus:ring-1 focus:ring-blue-500" required>
                </div>
                <div>
                    <label for="end_time" class="block text-sm font-medium">End Time</label>
                    <input type="time" id="end_time" name="end_time" value="<?= isset($end_time) ? htmlspecialchars($end_time) : ''; ?>" class="w-full px-2 py-1 border rounded focus:ring-1 focus:ring-blue-500" required>
                </div>
                <div class="col-span-2">
                    <label for="status" class="block text-sm font-medium">Status</label>
                    <select id="status" name="status" class="w-full px-2 py-1 border rounded focus:ring-1 focus:ring-blue-500" required>
                        <option value="usual" <?= isset($status) && $status == 'usual' ? 'selected' : ''; ?>>Usual</option>
                        <option value="no-class" <?= isset($status) && $status == 'no-class' ? 'selected' : ''; ?>>No Class</option>
                        <option value="make-up" <?= isset($status) && $status == 'make-up' ? 'selected' : ''; ?>>Make-up</option>
                    </select>
                </div>
                <div class="col-span-2 flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white font-bold rounded hover:bg-red-700">Add Schedule</button>
                </div>
            </form> -->
        

        <!-- Schedule List -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            

        <div class="flex items-center justify-between mb-6">
                    <!-- Title with Icon -->
                    <h2 class="text-2xl font-semibold">
                        <span class="mr-2">
                            <i class="fas fa-calendar-check text-blue-600"></i>
                        </span>
                        Schedule List
                    </h2>


                    <div class="flex space-x-4">
                        <a href="history.php" 
                        class="flex items-center text-gray-600 hover:text-red-600 transition-all duration-300 ease-in-out">
                            <i class="fas fa-history mr-2"></i> History
                        </a>
                        
                    </div>
                </div>


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
                        
                    </tr>
                </thead>
                <tbody>
                   
                    <?php // foreach ($schedules as $schedule): ?>
                    <?php 
                        for($i=0; $i<count($schedules); $i++)  {  
                    ?>
                        <tr>
                            <td class="border px-4 py-2"><?= htmlspecialchars($schedules[$i]['course']); ?></td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($schedules[$i]['lecturer']); ?></td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($schedules[$i]['room']); ?></td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($schedules[$i]['schedule_date']); ?></td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($schedules[$i]['start_time']); ?></td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($schedules[$i]['end_time']); ?></td>
                            <td class="border px-4 py-2">
                                <span class="px-2 py-1 rounded bg-<?= $schedules[$i]['status'] == 'no-class' ? 'red' : ($schedules[$i]['status'] == 'make-up' ? 'yellow' : 'green'); ?>-200 text-<?= $schedules[$i]['status'] == 'no-class' ? 'red' : ($schedules[$i]['status'] == 'make-up' ? 'yellow' : 'green'); ?>-800">
                                    <?= htmlspecialchars(ucfirst($schedules[$i]['status'])); ?>
                                </span>
                            </td>
                            
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
<?php ob_end_flush(); ?>
</body>
</html>