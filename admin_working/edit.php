<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "@Hxy080904997788nhim";
$database = "sample2_db";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch schedule entry for editing
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

// Update schedule entry if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course = $_POST['course'];
    $lecturer = $_POST['lecturer'];
    $room = $_POST['room'];
    $date_time = $_POST['date_time'];
    $status = $_POST['status'];

    if ($id > 0) {
        $stmt = $conn->prepare(
            "UPDATE schedules SET course = ?, lecturer = ?, room = ?, date_time = ?, status = ? WHERE id = ?"
        );
        $stmt->bind_param("sssssi", $course, $lecturer, $room, $date_time, $status, $id);
        if ($stmt->execute()) {
            header("Location: ad_dashboard_copy.php?success=1");
            exit;
        } else {
            $error_message = "Error updating record: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch all schedules for the list
$schedules = [];
$result = $conn->query("SELECT * FROM schedules");
if ($result) {
    $schedules = $result->fetch_all(MYSQLI_ASSOC);
    $result->close();
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

    <body>
        <?php
            include('sidebar.php');
        ?>

        <!-- Main Content -->
        <div class="main-content ">
            <h1 class="text-2xl font-bold mb-4">Schedule Management</h1>

            <?php if (isset($error_message)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="bg-white p-6 rounded-lg shadow-md mb-6">
                <h2 class="text-xl font-semibold mb-4">Edit Schedule</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="course" class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                        <input type="text" id="course" name="course" value="<?= htmlspecialchars($schedule['course'] ?? ''); ?>" required class="w-full px-3 py-2 border rounded-md">
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
                        <label for="date_time" class="block text-sm font-medium text-gray-700">Date/Time</label>
                        <input type="datetime-local" id="date_time" name="date_time" value="<?= isset($schedule['date_time']) ? date('Y-m-d\TH:i', strtotime($schedule['date_time'])) : ''; ?>" required class="w-full px-3 py-2 border rounded">
                    </div>
                </div>
                <div class="mt-4">
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="status" name="status" class="w-full px-3 py-2 border rounded">
                        <option value="usual" <?= ($schedule['status'] ?? '') === 'usual' ? 'selected' : ''; ?>>Usual Class</option>
                        <option value="make-up" <?= ($schedule['status'] ?? '') === 'make-up' ? 'selected' : ''; ?>>Make-Up Class</option>
                        <option value="no-class" <?= ($schedule['status'] ?? '') === 'no-class' ? 'selected' : ''; ?>>No Class</option>
                    </select>
                </div>

                <button type="submit" class="mt-6 px-4 py-2 border-2 border-red-800 text-red-800 bg-white rounded-lg hover:bg-red-800 hover:text-white transition-all duration-300 ease-in-out" >Save Change</button>
            </form>

            
        </div>
    </div>
</body>
</html>
