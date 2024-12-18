<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
</head>
<body>
    <?php
        include('sidebar.php');
    ?>
        
    <div class="main-content">
        
        <?php
        // schedule.php
        require_once 'function_copy.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $course = $_POST['course'] ?? '';
            $lecturer = $_POST['lecturer'] ?? '';
            $room = $_POST['room'] ?? '';
            $date_time = $_POST['date_time'] ?? '';
            $status = $_POST['status'] ?? 'usual';

            if (add_schedule($course, $lecturer, $room, $date_time, $status)) {
                $success_message = "Schedule added successfully!";
            } else {
                $error_message = "Error adding schedule. Please try again.";
            }
        }

        $schedules = get_schedules();
        ?>

            <h1 class="text-2xl font-bold mb-4">Schedule Management</h1>
            <div class="bg-white p-6 rounded-lg shadow-md">
                
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold mb-3">Schedule List</h2>
                <a href="add_schedule.php" class="px-4 py-2 border-2 border-red-800 text-red-800 bg-white rounded-lg hover:bg-red-800 hover:text-white transition-all duration-300 ease-in-out">
                    Add Schedule
                </a>
            </div>
                
                <table class="w-full border-collapse">
                    <thead>
                        <tr>
                            <th class="border px-4 py-2 text-left">Course</th>
                            <th class="border px-4 py-2 text-left">Lecturer</th>
                            <th class="border px-4 py-2 text-left">Room</th>
                            <th class="border px-4 py-2 text-left">Date/Time</th>
                            <th class="border px-4 py-2 text-left">Status</th>
                            <th class="border px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schedules as $schedule): ?>
                            <tr>
                                <td class="border px-4 py-2"><?= htmlspecialchars($schedule['course']); ?></td>
                                <td class="border px-4 py-2"><?= htmlspecialchars($schedule['lecturer']); ?></td>
                                <td class="border px-4 py-2"><?= htmlspecialchars($schedule['room']); ?></td>
                                <td class="border px-4 py-2"><?= htmlspecialchars($schedule['date_time']); ?></td>
                                <td class="border px-4 py-2">
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
                                    <span class="px-2 py-1 rounded <?= $status_class; ?>">
                                        <?= htmlspecialchars(ucfirst($schedule['status'])); ?>
                                    </span>
                                </td>
                                <td class="border px-4 py-2">
                                    <a href="edit.php?id=<?= $schedule['id']; ?>" class="text-blue-600 hover:underline">Edit</a> |
                                    <a href="delete.php?id=<?= $schedule['id']; ?>" class="text-red-600 hover:underline" onclick="return confirm('Are you sure you want to delete this entry?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
