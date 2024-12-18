<?php
require_once '../admin_working_copy/config_copy.php';
require_once '../admin_working_copy/function_copy.php';
include('sidebar_user.php');

// Fetch history records
$stmt = $conn->prepare("SELECT * FROM schedule_history ORDER BY transfer_timestamp DESC");
$stmt->execute();
$history_records = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Schedule History</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="main-content">
        <div class="bg-white shadow-md rounded-lg">
            <div class="p-6 bg-red-600 text-white">
                <h1 class="text-2xl font-bold">
                    <i class="fas fa-history mr-3"></i>
                    Schedule History
                </h1>
            </div>

            <table class="w-full">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-3 text-left">Course</th>
                        <th class="p-3 text-left">Lecturer</th>
                        <th class="p-3 text-left">Room</th>
                        <th class="p-3 text-left">Date</th>
                        <th class="p-3 text-left">Time</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-left">Transferred At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($history_records->num_rows > 0): ?>
                        <?php while ($record = $history_records->fetch_assoc()): ?>
                            <tr class="border-b hover:bg-gray-100">
                                <td class="p-3"><?= htmlspecialchars($record['course']) ?></td>
                                <td class="p-3"><?= htmlspecialchars($record['lecturer']) ?></td>
                                <td class="p-3"><?= htmlspecialchars($record['room']) ?></td>
                                <td class="p-3"><?= htmlspecialchars($record['schedule_date']) ?></td>
                                <td class="p-3">
                                    <?= htmlspecialchars($record['start_time']) ?> - 
                                    <?= htmlspecialchars($record['end_time']) ?>
                                </td>
                                <td class="p-3">
                                    <span class="px-2 py-1 rounded 
                                        <?= $record['status'] == 'no-class' ? 'bg-red-200 text-red-800' : 
                                            ($record['status'] == 'make-up' ? 'bg-yellow-200 text-yellow-800' : 'bg-green-200 text-green-800') ?>">
                                        <?= htmlspecialchars(ucfirst($record['status'])) ?>
                                    </span>
                                </td>
                                <td class="p-3"><?= htmlspecialchars($record['transfer_timestamp']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="p-4 text-center text-gray-500">
                                No historical schedules found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>