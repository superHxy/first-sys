<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = getDatabaseConnection();
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $conn->real_escape_string($_POST['role']);

    // Validate inputs
    if (empty($username) || empty($password) || empty($role)) {
        $error = "All fields are required";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long";
    } elseif (!in_array($role, ['admin', 'user'])) {
        $error = "Invalid role selected";
    } else {
        // Check if the username already exists
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = "Username already exists";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Insert new user into the database
            $insert_stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $insert_stmt->bind_param("sss", $username, $hashed_password, $role);

            if ($insert_stmt->execute()) {
                $success = "Registration successful! You can now <a href='login.php'>Login</a>.";
                // Optionally, redirect after success
                // header("Location: login.php");
                // exit();
            } else {
                $error = "Registration failed. Please try again.";
            }

            $insert_stmt->close();
        }

        $check_stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EAMUPortal - Register</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="login_css/form.css">
    <link rel="stylesheet" href="login_css/style.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .success-message {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            padding: 10px;
            margin: 15px 0;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }
        .error-message {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            padding: 10px;
            margin: 15px 0;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-form">
                <div class="logo">
                    <img src="../img/EAMU-1024x438.png" alt="EAMU Logo">
                </div>
                <h1>Welcome to EAMUPortal 👋</h1>
                <p class="subtitle">Register now to keep track of your schedule<br>Sign Up to start managing your projects.</p>

                <!-- Display error messages -->
                <?php if (!empty($error)): ?>
                <div class="error-message">
                    <?= htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>

                <!-- Display success message with link to login -->
                <?php if (!empty($success)): ?>
                <div class="success-message">
                    <?= $success; ?>
                </div>
                <?php endif; ?>

                <form action="register.php" method="POST" id="register-form">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Enter your username" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="At least 8 characters" required>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Verify the password" required>
                    </div>

                    <div class="form-group">
                        <label for="role">Role:</label>
                        <select name="role" id="role" required>
                            <option value="" disabled selected>Select Role</option>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <button type="submit" class="sign-in-btn">Sign Up</button>

                    <div class="forgot-password">
                        <a href="index.php"><span>Already have an account?</span> Login</a>
                    </div>
                </form>
            </div>
            <div class="building-image">
                <img src="../admin_working_Copy/img/cambodia 2016-2019.jpg" alt="EAMU Building">
            </div>
        </div>
        <footer>
            <p>&copy; 2023 ALL RIGHTS RESERVED</p>
        </footer>
    </div>

    <script>
        // Client-side validation for passwords
        document.getElementById('register-form').addEventListener('submit', function (e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {
                alert("Passwords do not match!");
                e.preventDefault();
            }
        });

        // Smooth fade-in for success message
        document.addEventListener('DOMContentLoaded', function () {
            const successMessage = document.querySelector('.success-message');
            if (successMessage) {
                successMessage.style.opacity = 0;
                successMessage.style.transition = 'opacity 1s ease-in-out';
                setTimeout(() => {
                    successMessage.style.opacity = 1;
                }, 100); // Delay to ensure visibility
            }
        });
    </script>
</body>
</html>
