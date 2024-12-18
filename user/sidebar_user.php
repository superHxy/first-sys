<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard with Navbar and Sidebar</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }

        .navbar {
            background-color: white;
            color: red;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e5e5e5;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar .logo img {
            width: 100px;
        }

        .navbar .utilities {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .navbar .utilities .clock {
            font-size: 16px;
            color: grey;
        }

        .navbar .profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .navbar .profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .sidebar {
            background-color: white;
            margin: 10px;
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 20px;
        }

        .sidebar a {
            display: block;

            color: darkred;
            text-decoration: none;
            padding: 10px 15px;
            margin: 10px 0;
            border: 1px solid transparent;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .sidebar a:hover {
            background-color: darkred;
            color: #f4f4f9;
            border-color: darkred;
        }

        .logo img {
            width: 120px;
            height: auto;
            margin-left: auto;
            margin-right: auto;
            display: block;
            margin-bottom: 15px;
        }

        .main-content {
            margin-left: 270px;
            margin-top: 20px;
            margin-right: 15px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <!-- Logo -->
        <div class="logo">
            <img src="img_user/EAMU-1024x438.png" alt="EAMU Logo">
        </div>

        <!-- Utilities: Real-time clock and profile -->
        <div class="utilities">


            <div class="clock" id="clock"></div>
            <div class="profile">
                <img src="img/Screenshot 2024-12-05 141151.png" alt="User Profile">
                <span class="username">user</span>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <img src="img_user/EAMU-1024x438.png" alt="EAMU Logo">
        </div>
        <a href="main_dashboard_user.php">Dashboard</a>
        <a href="content_list_user.php">News</a>
        <a href="events_list_user.php">Events</a>
        <a href="ad_dashboard_user.php">Schedule Announcement</a>
        <a href="../login_system/logout.php">Log Out</a>

        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200">
            <p class="text-center text-xs text-red-700">&copy; 2024 EAMU University. All Rights Reserved.</p>
        </div>
    </div>

    


        <script>
        function updateClock() {
            const now = new Date();
            const dateOptions = { year: 'numeric', month: 'long', day: 'numeric' };
            const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
            const formattedDate = now.toLocaleDateString(undefined, dateOptions);
            const formattedTime = now.toLocaleTimeString(undefined, timeOptions);

            document.getElementById('clock').innerText = `${formattedDate} | ${formattedTime}`;
        }

        // Update the clock every second
        setInterval(updateClock, 1000);
        updateClock(); // Initial call
    </script>

    <!-- Script for Real-Time Clock -->

</body>
</html>
