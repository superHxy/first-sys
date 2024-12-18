<?php
require_once '../admin_working_copy/config_copy.php';
require_once '../admin_working_copy/function_copy.php';
include('sidebar_user.php');

// Fetch all events using the function
$result = getEvents();

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $id = intval($_POST['id']);

    if (!$id) {
        die('Invalid ID provided.');
    }

    // Fetch the media file path
    $stmt = $conn->prepare("SELECT media_file FROM events WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $mediaResult = $stmt->get_result()->fetch_assoc();

    // Delete the media file if it exists
    if ($mediaResult && !empty($mediaResult['media_file']) && file_exists($mediaResult['media_file'])) {
        unlink($mediaResult['media_file']);
    }

    // Delete the event from the database
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        header('Location: events_list.php'); // Redirect to refresh the page
        exit();
    } else {
        echo "Error deleting the event.";
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event List</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body >



    <div class="main-content">
        <h1 class="text-2xl font-bold mb-4">Event Management</h1>

        

        <!-- Event List -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            
                <!-- Add Event Button -->
                <div class="flex items-center justify-between mb-6">
                    <!-- Title with Icon -->
                    <h2 class="text-2xl font-semibold">
                        <span class="mr-2">
                            <i class="fas fa-calendar-check text-blue-600"></i>
                        </span>
                        Event List
                    </h2>


                    <div class="flex space-x-4">
                        <a href="event_history.php" 
                        class="flex items-center text-gray-600 hover:text-red-600 transition-all duration-300 ease-in-out">
                            <i class="fas fa-history mr-2"></i> History
                        </a>
                        
                    </div>
                </div>

                <?php if ($result && $result->num_rows > 0): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="bg-white rounded-lg shadow-lg p-4 hover:shadow-2xl transition duration-300">
                <?php if (!empty($row['media_file']) && file_exists($row['media_file'])): ?>
                    <img src="<?= htmlspecialchars($row['media_file']) ?>" 
                         alt="<?= htmlspecialchars($row['title']) ?>" 
                         class="w-full h-48 object-cover rounded mb-4">
                <?php else: ?>
                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center rounded mb-4">
                        <span class="text-gray-500">No Media Available</span>
                    </div>
                <?php endif; ?>

                <h3 class="text-xl font-bold mb-2"><?= htmlspecialchars($row['title']) ?></h3>
                <p class="text-gray-600 mb-2">
                    <?= htmlspecialchars(strlen($row['location']) > 50 
                        ? substr($row['location'], 0, 50) . '...' 
                        : $row['location']) ?>
                </p>
                <p class="text-gray-500 text-sm mb-4">
                    <strong>Date:</strong> <?= date('F j, Y', strtotime($row['event_date'])) ?><br>
                    <strong>Start Time:</strong> <?= date('g:i A', strtotime($row['start_time'])) ?><br>
                    <strong>End Time:</strong> <?= date('g:i A', strtotime($row['end_time'])) ?>
                </p>
                
            </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <p class="text-gray-600">No events available at the moment.</p>
<?php endif; ?>

        </div>
    </div>
</body>
</html>
