<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user role from the database
$conn = getDatabaseConnection();
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();

$role = $user['role'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    
    <p>Your role: <strong><?php echo htmlspecialchars($role); ?></strong></p>
    
    <p>This is your dashboard.</p>
    
    <?php if ($role === 'admin'): ?>
        <h3>Admin Features</h3>
        <ul>
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="settings.php">Site Settings</a></li>
        </ul>
    <?php else: ?>
        <h3>User Features</h3>
        <ul>
            <li><a href="profile.php">View Profile</a></li>
            <li><a href="support.php">Contact Support</a></li>
        </ul>
    <?php endif; ?>
    
    <a href="logout.php">Logout</a>
</body>
</html>
