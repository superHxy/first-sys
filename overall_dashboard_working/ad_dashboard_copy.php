<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }

        .navbar {
            background-color: white;
            color: red;
            padding: 15px;
            text-align: center;
            font-size: 20px;
        }

        .sidebar {
            background-color: white;
            margin: 10px;
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 20px;
        }

        .sidebar a {
            display: block;
            color: darkred;
            text-decoration: none;
            padding: 10px 15px;
            margin: 5px 0;
        }

        .sidebar a:hover {
            background-color: white;
            border-radius: 5px;
            margin: 10px;
            transition: 0.3s ease;
        }

        .logo img {
            width: 120px;
            height: auto;
            margin-left: 10px;
            margin-bottom: 15px;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .card {
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }

        .card h3 {
            margin: 10px 0;
            font-size: 24px;
        }

        .card p {
            margin: 5px 0;
            color: #7f8c8d;
        }
    </style>
</head>
<body>

    <div class="navbar">University Admin Dashboard</div>

    <div class="sidebar">
        <div class="logo">
            <img src="img/EAMU-1024x438.png" alt="EAMU Logo">
        </div>
        <a href="#">Dashboard</a>
        <a href="#">News</a>
        <a href="#">Events</a>
        <a href="#">Schedule Announcement</a>
        <a href="#">Log Out</a>

        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-black-700">
            <p class="text-center text-xs text-red-700">
                &copy; 2024 EAMU University. All Rights Reserved.
            </p>
        </div>
    </div>

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
                                <label for="date_time" class="block text-sm font-medium text-gray-700 mb-1">Date/Time</label>
                                <input type="datetime-local" id="date_time" name="date_time" required class="w-full px-3 py-2 border rounded-md">
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
                                    <th class="text-left">Date/Time</th>
                                    <th class="text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($schedules as $schedule): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($schedule['course']); ?></td>
                                        <td><?php echo htmlspecialchars($schedule['lecturer']); ?></td>
                                        <td><?php echo htmlspecialchars($schedule['room']); ?></td>
                                        <td><?php echo htmlspecialchars($schedule['date_time']); ?></td>
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

    </div>
</body>
</html>