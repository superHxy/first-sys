<?php
require_once'config_copy.php';
include('sidebar.php');
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
    $course = trim($_POST['course']);
    $lecturer = trim($_POST['lecturer']);
    $room = trim($_POST['room']);
    $schedule_date = trim($_POST['schedule_date']);
    $start_time = trim($_POST['start_time']);
    $end_time = trim($_POST['end_time']);
    $status = $_POST['status'];

    if ($id > 0) {
        $stmt = $conn->prepare(
            "UPDATE schedules SET course = ?, lecturer = ?, room = ?, schedule_date = ?,start_time = ?, end_time=?, status = ? WHERE id = ?"
        );
        $stmt->bind_param("sssssi", $course, $lecturer, $room, $schedule_date,$start_time,$end_time, $status, $id);
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
    

        <!-- Main Content -->
        <div class="main-content flex-1 p-6">
            <h1 class="text-2xl font-bold mb-6">Edit Schedule</h1>

            <?php if (isset($error_message)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="bg-white p-6 rounded shadow mb-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="course" class="block text-sm font-medium text-gray-700">Course</label>
                        <input type="text" id="course" name="course" value="<?= htmlspecialchars($schedule['course'] ?? ''); ?>" required class="w-full px-3 py-2 border rounded">
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
                        <label for="schedule_date" class="block text-sm font-medium text-gray-700">Date</label>
                        <input type="datetime-local" id="schedule_date" name="schedule_date" value="<?= isset($schedule['schedule_date']) ? date('Y-m-d\TH:i', strtotime($schedule['schedule_date'])) : ''; ?>" required class="w-full px-3 py-2 border rounded">
                    </div>
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time</label>
                        <input type="datetime-local" id="start_time" name="start_time" value="<?= isset($schedule['start_time']) ? date('TH:i', strtotime($schedule['start_time'])) : ''; ?>" required class="w-full px-3 py-2 border rounded">
                    </div>
                    <div>
                        <label for="schedule_date" class="block text-sm font-medium text-gray-700">Date</label>
                        <input type="datetime-local" id="end_time" name="end_time" value="<?= isset($schedule['end_time']) ? date('TH:i', strtotime($schedule['end_time'])) : ''; ?>" required class="w-full px-3 py-2 border rounded">
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
                <button type="submit" class="mt-6 px-4 py-2 bg-red-700 text-white rounded hover:bg-red-800">Save Changes</button>
            </form>

            <h2 class="text-xl font-bold mb-4">Schedule List</h2>
            <table class="w-full bg-white rounded shadow overflow-hidden">
                <thead class="bg-gray-200">
                    <tr>
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
                        <tr class="border-t">
                            <td class="px-4 py-2"><?= htmlspecialchars($schedule['course']); ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($schedule['lecturer']); ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($schedule['room']); ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($schedule['schedule_date']); ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($schedule['start_time']); ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($schedule['end_time']); ?></td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded text-white <?= $schedule['status'] === 'make-up' ? 'bg-yellow-500' : ($schedule['status'] === 'no-class' ? 'bg-red-500' : 'bg-green-500'); ?>">
                                    <?= htmlspecialchars(ucfirst($schedule['status'])); ?>
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <a href="edit.php?id=<?= $schedule['id']; ?>" class="text-blue-600 hover:underline">Edit</a> |
                                <a href="delete.php?id=<?= $schedule['id']; ?>" class="text-red-600 hover:underline" onclick="return confirm('Are you sure you want to delete this entry?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>