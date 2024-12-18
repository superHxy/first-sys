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

            <?php if (isset($success_message)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
                    <?= htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                    <?= htmlspecialchars($error_message); ?>
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
                        <label for="date_time" class="block text-sm font-medium text-gray-700 mb-1">Date/Time</label>
                        <input type="datetime-local" id="date_time" name="date_time" required class="w-full px-3 py-2 border rounded-md">
                    </div>
                </div>
                <div class="mt-4">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status" name="status" class="w-full px-3 py-2 border rounded-md">
                        <option value="usual">Usual Class</option>
                        <option value="make-up">Make-Up Class</option>
                        <option value="no-class">No Class</option>
                    </select>
                </div>
                <div class="flex space-x-4">
                    <button type="submit" class="mt-6 px-4 py-2 border-2 border-red-800 text-red-800 bg-white rounded-lg hover:bg-red-800 hover:text-white transition-all duration-300 ease-in-out" >Add Schedule</button>

                    <a href="ad_dashboard_copy.php" class="mt-6 px-4 py-2 border-2 border-red-800 text-red-800 bg-white rounded-lg hover:bg-red-800 hover:text-white transition-all duration-300 ease-in-out">
                      Cancel
                    </a>
                </div>



            </form>

            
        
    </div>
</body>
</html>
