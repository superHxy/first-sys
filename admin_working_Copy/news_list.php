<?php
require_once 'config_copy.php';
require_once 'function_copy.php';

// Fetch all events
$sql = "SELECT * FROM events ORDER BY event_date DESC, start_time ASC";
$result = $conn->query($sql);

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $id = intval($_POST['id']);
    $stmt = $conn->prepare("DELETE FROM events WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    header('Location: events_list.php'); // Reload page
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Announcements</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <?php include('sidebar.php'); ?>

    <div class="main-content p-6">
        <h1 class="text-3xl font-bold mb-6">Event Announcements</h1>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-semibold">Upcoming Events</h2>
                <a href="add_edit_event.php" class="px-4 py-2 border-2 border-blue-800 text-blue-800 bg-white rounded-lg hover:bg-blue-800 hover:text-white transition-all duration-300">Add Event</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="bg-white rounded shadow p-4">
                        <h3 class="text-xl font-semibold mb-2"><?= htmlspecialchars($row['title']) ?></h3>
                        <p class="text-gray-600 mb-2">
                            <strong>Date:</strong> <?= htmlspecialchars($row['event_date']) ?>
                        </p>
                        <p class="text-gray-600 mb-2">
                            <strong>Time:</strong> <?= htmlspecialchars($row['start_time']) ?> - <?= htmlspecialchars($row['end_time']) ?>
                        </p>
                        <p class="text-gray-600 mb-4">
                            <strong>Location:</strong> <?= htmlspecialchars($row['location']) ?>
                        </p>
                        <p class="text-gray-600 mb-4">
                            <?= htmlspecialchars(strlen($row['description']) > 100 ? substr($row['description'], 0, 100) . '...' : $row['description']) ?>
                        </p>
                        <a href="event_detail_view.php?id=<?= $row['id'] ?>" class="text-blue-500 hover:underline">View More</a>
                        <div class="mt-4 flex space-x-4">
                            <a href="add_edit_event.php?edit=<?= $row['id'] ?>" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Edit</a>
                            <form method="POST" class="inline-block">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <button type="submit" name="delete" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

</body>
</html>
