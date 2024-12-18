<?php
require_once '../admin_working_copy/config_copy.php'; // Database connection
require_once '../admin_working_copy/function_copy.php'; // Custom functions

/**
 * Fetches all news items from the database, ordered by latest first.
 * 
 * @param mysqli $conn The database connection object.
 * @return mysqli_result|false The result set of the query or false on failure.
 */


// Fetch all news
$result = fetchAllNews($conn);



// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $id = intval($_POST['id']);
    $delete_stmt = $conn->prepare("DELETE FROM content WHERE id = ?");
    $delete_stmt->bind_param('i', $id);
    $delete_stmt->execute();
    header('Location: content_list.php'); // Redirect to reload page
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content List</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">

    <!-- Sidebar Include -->
    <?php include('sidebar_user.php'); ?>

    <div class="main-content">
        <h1 class="text-2xl font-bold mb-4">News Management</h1>

        <!-- News List Section -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-semibold">Latest News</h2>
                
            </div>

            <!-- News Cards Grid -->
            <?php if ($result  > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach($result as $row){ ?>
                        <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition duration-300">
                            <img src="<?= !empty($row['file_path']) ? htmlspecialchars($row['file_path']) : 'default_image.jpg' ?>" 
                                 alt="<?= htmlspecialchars($row['title']) ?>" 
                                 class="w-full h-48 object-cover rounded mb-4">
                            <h3 class="text-xl font-semibold mb-2"><?= htmlspecialchars($row['title']) ?></h3>
                            <p class="text-sm text-gray-500 mb-2">
                                Posted on: <?= date('F j, Y, g:i a', strtotime($row['created_at'])) ?>
                            </p>
                            <p class="text-gray-600 mb-4">
                                <?= htmlspecialchars(strlen($row['description']) > 100 
                                    ? substr($row['description'], 0, 100) . '...' 
                                    : $row['description']) ?>
                            </p>
                            <a href="news_detail_view.php?id=<?= $row['id'] ?>" class="text-blue-600 hover:underline">View More</a>
                            
                        </div>
                    <?php } ?>
                </div>
            <?php else: ?>
                <p class="text-gray-600">No news available at the moment.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
