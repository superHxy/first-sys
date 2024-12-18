<?php
require_once 'config_copy.php';

// Fetch all events from history
$sql = "SELECT * FROM history_events ORDER BY moved_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event History</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
</head>
<body class="bg-gray-100">

    <?php include('sidebar.php'); ?>

    <div class="main-content ">
        <h1 class="text-3xl font-bold mb-6">Event History</h1>

        <div class="bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-xl font-bold mb-6">History list</h1>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="bg-white rounded shadow p-4">
                        <?php if (!empty($row['media_file'])): ?>
                            <img src="<?= htmlspecialchars($row['media_file']) ?>" 
                                 alt="<?= htmlspecialchars($row['title']) ?>" 
                                 class="w-full h-48 object-cover rounded mb-4">
                        <?php else: ?>
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center rounded mb-4">
                                <span class="text-gray-500">No Media</span>
                            </div>
                        <?php endif; ?>

                        <h3 class="text-xl font-semibold mb-2"><?= htmlspecialchars($row['title']) ?></h3>
                        <p class="text-gray-600 mb-4"><?= htmlspecialchars($row['description']) ?></p>
                        <p class="text-gray-600 text-sm">Date: <?= htmlspecialchars($row['event_date']) ?></p>
                        <p class="text-gray-600 text-sm">Archived: <?= htmlspecialchars($row['moved_at']) ?></p>
                    </div>
                <?php endwhile; ?>

            </div>
            
        </div>
        <a href="events_list.php" class="mt-6 inline-block bg-red-800 text-white px-4 py-2 rounded hover:bg-red-900">Back to News List</a>
    </div>

</body>
</html>
