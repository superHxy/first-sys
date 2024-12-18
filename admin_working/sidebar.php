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
            padding: 15px;
            text-align: center;
            font-size: 20px;
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
            margin: 5px 0;
            
        }

        .sidebar a:hover {
            background-color: darkred;
            color: #f4f4f9;
            border-radius: 5px;
            margin: 10px;
            transition: 0.3s ease;
        }

        .logo img {
            width: 120px;
            height: auto;
            margin-left: 10px;
            margin-bottom: 15px;
        }

        .main-content {
            margin-left: 270px;
            margin-top: 20px;
            margin-right: 15px;
        }


    </style>

<div class="navbar">University Admin Dashboard</div>

<div class="sidebar">
    <div class="logo">
        <img src="img/EAMU-1024x438.png" alt="EAMU Logo">
    </div>
    <a href="#">Dashboard</a>
    <a href="../news.php">News</a>
    <a href="#">Events</a>
    <a href="#">Schedule Announcement</a>
    <a href="#">Log Out</a>

    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200">
        <p class="text-center text-xs text-red-700">&copy; 2024 EAMU University. All Rights Reserved.</p>
    </div>
</div>
