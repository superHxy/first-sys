<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = getDatabaseConnection();
    
    // Ensure to get the 'username' with the correct form field name
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // Prepare the query to find the user
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Check if the password matches
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            session_regenerate_id(true); // Regenerate session ID to prevent session fixation

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid username or password";
        }
    } else {
        $error = "Invalid username or password";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EAMUPortal - University Attendance System</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="../login_css/style.css">
    <link rel="stylesheet" href="../login_css/form.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-form">
                <div class="logo">
                    <img src="../img/EAMU-1024x438.png" alt="EAMU Logo">
                </div>
                <h1>Welcome to EAMUPortal 👋</h1>
                <p class="subtitle">Today is a new day. It's your day. You shape it.<br>Sign in to start managing your projects.</p>

                <!-- Display error message if login failed -->
                <?php if (!empty($error)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>
                
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Username" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="At least 8 characters" required>
                    </div>
                    
                    <div class="forgot-password">
                        <a href="auth/forgot-password.php">Forgot Password?</a>
                        <a href="register.php"><span>No account?</span>Register</a>
                    </div>
                    
                    <button type="submit" class="sign-in-btn">Sign in</button>
                </form>
            </div>
            <div class="building-image">
                <img src="../img/cambodia 2016-2019.jpg " alt="EAMU Building">
            </div>
        </div>
        <footer>
            <p>&copy; 2023 ALL RIGHTS RESERVED</p>
        </footer>
    </div>
</body>
</html>
