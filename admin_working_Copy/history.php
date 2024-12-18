<?php
include('sidebar.php');
require_once 'function_copy.php';

// Get history records
$history_records = get_history();

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_selected'])) {
    $ids_to_delete = $_POST['ids'] ?? [];
    
    if (!empty($ids_to_delete)) {
        foreach ($ids_to_delete as $id) {
            $id = intval($id);
            // Call a function to delete records from database
            delete_history_record($id);
        }
        header('Location: history.php'); // Redirect to avoid form resubmission
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History - University Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <div class="main-content">
        <h1 class="text-2xl font-bold mb-4">History</h1>
        <a href="ad_dashboard_copy.php" class="mb-4 inline-block">Back to Dashboard</a>

        <form method="POST" action="history.php">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4">History List</h2>
                <table class="w-full border-collapse">
                    <thead>
                        <tr>
                            <th class="border px-4 py-2 text-left">
                                <input type="checkbox" id="select-all" class="form-checkbox">
                            </th>
                            <th class="border px-4 py-2 text-left">Course</th>
                            <th class="border px-4 py-2 text-left">Lecturer</th>
                            <th class="border px-4 py-2 text-left">Room</th>
                            <th class="border px-4 py-2 text-left">Start-Time</th>
                            <th class="border px-4 py-2 text-left">End-Time</th>
                            <th class="border px-4 py-2 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history_records as $record): ?>
                            <tr>
                                <td class="border px-4 py-2">
                                    <input type="checkbox" name="selected_ids[]" value="<?= $record['id']; ?>" class="form-checkbox">
                                </td>
                                <td class="border px-4 py-2"><?= htmlspecialchars($record['course']); ?></td>
                                <td class="border px-4 py-2"><?= htmlspecialchars($record['lecturer']); ?></td>
                                <td class="border px-4 py-2"><?= htmlspecialchars($record['room']); ?></td>
                                <td class="border px-4 py-2"><?= htmlspecialchars($record['date_time']); ?></td>
                                <td class="border px-4 py-2"><?= htmlspecialchars($record['end_time']); ?></td>
                                <td class="border px-4 py-2"><?= htmlspecialchars(ucfirst($record['status'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="mt-4 flex justify-end">
                    <button type="button" id="delete-selected-btn" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Delete Selected</button>
                </div>
            </div>
        </form>

        <!-- Confirmation Modal -->
        <div id="confirmation-modal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-50 flex items-center justify-center">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-semibold mb-4">Confirm Deletion</h3>
                <p class="mb-4">Are you sure you want to delete selected records?</p>
                <form method="POST" action="history.php">
                    <input type="hidden" name="ids" id="modal-ids">
                    <div class="flex justify-end">
                        <button type="button" id="cancel-btn" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 mr-2">Cancel</button>
                        <button type="submit" name="delete_selected" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Select all checkboxes script
        document.getElementById('select-all').addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('input[name="selected_ids[]"]');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        });

        // Open confirmation modal
        document.getElementById('delete-selected-btn').addEventListener('click', function() {
            const selectedIds = Array.from(document.querySelectorAll('input[name="selected_ids[]"]:checked'))
                .map(checkbox => checkbox.value);

            if (selectedIds.length > 0) {
                document.getElementById('modal-ids').value = selectedIds.join(',');
                document.getElementById('confirmation-modal').classList.remove('hidden');
            }
        });

        // Close the modal
        document.getElementById('cancel-btn').addEventListener('click', function() {
            document.getElementById('confirmation-modal').classList.add('hidden');
        });
    </script>
</body>
</html>
