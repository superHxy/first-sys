<?php
include('db_connection.php');

// Fetch schedule entries
$schedule_entries = fetch_schedule_entries($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule List</title>
</head>
<body>
    <h1>Schedule List</h1>
    <table border="1">
        <tr>
            <th>Course</th>
            <th>Lecturer</th>
            <th>Room</th>
            <th>Date/Time</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($schedule_entries as $entry): ?>
            <tr>
                <td><?php echo htmlspecialchars($entry['course']); ?></td>
                <td><?php echo htmlspecialchars($entry['lecturer']); ?></td>
                <td><?php echo htmlspecialchars($entry['room']); ?></td>
                <td><?php echo htmlspecialchars($entry['date_time']); ?></td>
                <td><?php echo htmlspecialchars($entry['status']); ?></td>
                <td>
                    <a href="edit.php?id=<?php echo $entry['id']; ?>">Edit</a> | 
                    <a href="delete.php?id=<?php echo $entry['id']; ?>" onclick="return confirm('Are you sure you want to delete this entry?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
