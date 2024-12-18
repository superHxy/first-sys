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
        require_once 'function_copy.php';

    ?>

            <?php
            

            // Variables
            $editRecord = null;

            // Handle actions: Add, Edit, Delete
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['add'])) {
                    $title = htmlspecialchars($_POST['title']);
                    $description = htmlspecialchars($_POST['description']);
                    $file = $_FILES['file'];

                    $fileName = basename($file['name']);
                    $fileTmpName = $file['tmp_name'];
                    $fileDestination = 'uploads/' . $fileName;

                    $allowedTypes = ['image/jpeg', 'image/png', 'video/mp4'];
                    if (in_array($file['type'], $allowedTypes)) {
                        if (!is_dir('uploads')) {
                            mkdir('uploads', 0777, true); // Create uploads directory if it doesn't exist
                        }

                        if (move_uploaded_file($fileTmpName, $fileDestination)) {
                            $sql = "INSERT INTO content (title, description,file_path) VALUES (?, ?, ?)";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param('sss', $title, $description, $fileDestination);
                            $stmt->execute();
                        }
                    }
                } elseif (isset($_POST['edit-save'])) {
                    $id = intval($_POST['id']);
                    $title = htmlspecialchars($_POST['title']);
                    $description = htmlspecialchars($_POST['description']);
                    $file = $_FILES['file'];
                    $fileName = $file['name'] ? basename($file['name']) : null;
                    $fileTmpName = $file['tmp_name'];
                    $fileDestination = $fileName ? 'uploads/' . $fileName : null;

                    if ($fileName && move_uploaded_file($fileTmpName, $fileDestination)) {
                        $sql = "UPDATE content SET title=?, description=?, file_path=? WHERE id=?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param('sssi', $title, $description, $fileDestination, $id);
                    } else {
                        $sql = "UPDATE content SET title=?, description=? WHERE id=?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param('ssi', $title, $description, $id);
                    }
                    $stmt->execute();
                } elseif (isset($_POST['edit'])) {
                    $id = intval($_POST['id']);
                    $sql = "SELECT * FROM content WHERE id=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('i', $id);
                    $stmt->execute();
                    $editRecord = $stmt->get_result()->fetch_assoc();
                } elseif (isset($_POST['delete'])) {
                    $id = intval($_POST['id']);
                    $sql = "DELETE FROM content WHERE id=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('i', $id);
                    $stmt->execute();
                }
            }

            // Fetch all content
            $sql = "SELECT * FROM content";
            $result = $conn->query($sql);
            ?>


        <div class="main-content">
        
        
            <h1 class="text-3xl font-bold mb-6">Admin Panel: Events and News</h1>

                <!-- Add/Edit Content Form -->

                
                <form method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow-md">
                    <h2 class="text-2xl font-semibold mb-4"><?= $editRecord ? 'Edit Content' : 'Add New Content' ?></h2>
                
                    <?php if ($editRecord): ?>
                        <input type="hidden" name="id" value="<?= $editRecord['id'] ?>">
                    <?php endif; ?>

                    <label class="block mb-2 font-medium">Title:</label>
                    <input type="text" name="title" required class="w-full p-2 border rounded mb-4" value="<?= $editRecord['title'] ?? '' ?>">

                    <label class="block mb-2 font-medium">Description:</label>
                    <textarea name="description" required class="w-full p-2 border rounded mb-4"><?= $editRecord['description'] ?? '' ?></textarea>

                    <label class="block mb-2 font-medium">Upload File (Image/Video):</label>
                    <input type="file" name="file" accept="image/*,video/*" class="w-full p-2 border rounded mb-4">
                    <?php if ($editRecord): ?>
                    <p class="text-sm text-gray-600">Leave empty to keep the current file.</p>
                <?php endif; ?>

                <button type="submit" name="<?= $editRecord ? 'edit-save' : 'add' ?>" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    <?= $editRecord ? 'Update Content' : 'Add Content' ?>
                </button>
            </form>

            <!-- Display Content List -->
            <h2 class="text-2xl font-semibold mt-8 mb-4">Events and News</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="bg-white rounded shadow p-4">
                        <img src="<?= htmlspecialchars($row['file_path']) ?>" alt="<?= htmlspecialchars($row['title']) ?>" class="w-full h-auto object-cover rounded mb-4">
                        <h3 class="text-xl font-semibold"><?= htmlspecialchars($row['title']) ?></h3>
                        <p class="text-gray-600"><?= htmlspecialchars($row['description']) ?></p>
                        <a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank" class="text-blue-500 hover:underline mt-2 block">View</a>
                        <div class="mt-4 flex space-x-4">
                            <form method="POST">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <button type="submit" name="edit" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Edit</button>
                            </form>
                            <form method="POST">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <button type="submit" name="delete" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
    </div>
</body>
</body>
</html>
