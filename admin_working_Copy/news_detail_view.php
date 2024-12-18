<?php
require_once 'config_copy.php';
require_once 'function_copy.php';

$id = intval($_GET['id']);
$sql = "SELECT * FROM content WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "Content not found.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail View</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <?php include('sidebar.php'); ?>
    
    <div class="main-content p-6">
        <h1 class="text-3xl font-bold mb-6">Detail View</h1>

        <div class="bg-white p-6 rounded-lg shadow-md flex space-x-6">
            <img src="<?= htmlspecialchars($row['file_path']) ?>" alt="<?= htmlspecialchars($row['title']) ?>" class="w-1/4 h-auto object-cover rounded mb-4"> <!-- 25% width -->
            <div>
                <h2 class="text-2xl font-semibold mb-2"><?= htmlspecialchars($row['title']) ?></h2>
                <p class="text-gray-600"><?= nl2br(htmlspecialchars($row['description'])) ?></p>
            </div>
        </div>

        <a href="content_list.php" class="mt-6 inline-block bg-red-800 text-white px-4 py-2 rounded hover:bg-red-900">Back to News List</a>
    </div>
</body>
</html>
