<?php
require_once 'config_copy.php';
require_once 'function_copy.php';

$editRecord = null;

// Handle Add/Edit actions
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
                mkdir('uploads', 0777, true);
            }
            if (move_uploaded_file($fileTmpName, $fileDestination)) {
                $sql = "INSERT INTO content (title, description, file_path) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('sss', $title, $description, $fileDestination);
                $stmt->execute();
                header('Location: content_list.php'); // Redirect to content list
                exit();
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
        header('Location: content_list.php'); // Redirect to content list
        exit();
    }
}

// If editing, fetch the record
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $sql = "SELECT * FROM content WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $editRecord = $stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $editRecord ? 'Edit Content' : 'Add Content' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <?php
        include('sidebar.php')
    ?>
    <div class="main-content ">
        <h1 class="text-3xl font-bold mb-6"><?= $editRecord ? 'Edit Content' : 'Add Content' ?></h1>
        <form method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow-md">
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

            <button 
                type="submit" 
                name="<?= $editRecord ? 'edit-save' : 'add' ?>" 
                class="mt-6 px-4 py-2 border-2 border-red-800 text-red-800 bg-white rounded-lg hover:bg-red-800 hover:text-white transition-all duration-300 ease-in-out">
                <?= $editRecord ? 'Update Content' : 'Add Content' ?>
            </button>

            <a href="content_list.php" 
                class="inline-block mt-6 px-4 py-2 border-2 border-red-800 text-red-800 bg-white rounded-lg hover:bg-red-800 hover:text-white transition-all duration-300 ease-in-out">
                Cancel
            </a>





                    





        </form>
    </div>
</body>
</html>
