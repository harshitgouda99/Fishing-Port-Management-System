<?php
// Start session
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Manual - Fishing Port Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #e3f2fd, #ffffff);
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .navbar {
            width: 100%;
            background: #007BFF;
            padding: 15px 30px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        .navbar .logo a {
            font-size: 1.8em;
            font-weight: bold;
            color: #fff;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .navbar .logo a img.logo-image {
            width: 50px;
            height: auto;
            margin-right: 10px;
        }

        .navbar .website-name-container {
            flex-grow: 1;
            text-align: center;
        }

        .navbar .website-name {
            color: #fff;
            font-size: 1.5em;
            margin: 0;
        }

        .navbar .nav-links a {
            color: #fff;
            text-decoration: none;
            margin: 0 15px;
            font-size: 1.2em;
            font-weight: 500;
            transition: all 0.3s ease-in-out;
            padding: 8px 15px;
            border-radius: 5px;
        }

        .navbar .nav-links a:hover {
            background-color: rgba(255, 255, 255, 0.2);
            color: #ffeb3b;
            transform: scale(1.1);
        }

        .dashboard-container {
            margin-top: 100px;
            text-align: center;
            width: 90%;
            max-width: 1200px;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .dashboard-container h1 {
            color: #007BFF;
            font-size: 2.5em;
            margin-bottom: 20px;
        }

        .dashboard-container p {
            font-size: 1.2em;
            line-height: 1.8;
            color: #555;
            text-align: left;
        }

        .dashboard-container ul {
            text-align: left;
            margin: 20px 0;
            padding-left: 20px;
        }

        .dashboard-container ul li {
            font-size: 1.1em;
            margin-bottom: 10px;
            color: #555;
        }

        .dashboard-container a {
            text-decoration: none;
            color: #007BFF;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .dashboard-container a:hover {
            color: #0056b3;
        }

        @media (max-width: 768px) {
            .dashboard-container h1 {
                font-size: 2em;
            }

            .dashboard-container p {
                font-size: 1em;
            }

            .dashboard-container ul li {
                font-size: 1em;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">
            <a href="index.html" style="display: flex; align-items: center;">
                <img src="assets/images/background_image5.jpg" alt="DIGIPORTXPRESS Logo" class="logo-image">
            </a>
        </div>
        <div class="website-name-container">
            <h1 class="website-name">DIGIPORTXPRESS</h1>
        </div>
        <div class="nav-links">
            <a href="index.html">Home</a>
            <a href="about.html">About</a>
            <a href="registration.html">Register</a>
            <a href="login.html">Login</a>
            <a href="contact.html">Contact</a>
        </div>
    </div>
    <div class="dashboard-container">
        <h1>Admin Manual</h1>
        <p>Welcome to the Admin Manual. This guide will help you understand how to use the admin features of the Fishing Port Management System.</p>
        <ul>
            <li><strong>Manage Users:</strong> Navigate to the "Manage Users" section to view, edit, or delete user accounts.</li>
            <li><strong>View Logs:</strong> Use the "View Logs" section to monitor system activities and user actions.</li>
            <li><strong>Generate Reports:</strong> Generate detailed reports on users, fish listings, and orders in the "Generate Reports" section.</li>
            <li><strong>Manage Boat Registrations:</strong> Approve or reject boat registration requests in the "Manage Boat Registrations" section.</li>
            <li><strong>Manage Fisherman Requests:</strong> Handle requests from users to become fishermen in the "Manage Fisherman Requests" section.</li>
            <li><strong>Today's Activity:</strong> View all activities and logs for the current day in the "Today's Activity" section.</li>
            <li><strong>System Analytics:</strong> Access system-wide analytics to monitor performance and usage trends.</li>
            <li><strong>Manage Notifications:</strong> Send announcements and updates to users in the "Manage Notifications" section.</li>
            <li><strong>Feedback & Support:</strong> View and respond to user feedback in the "Feedback & Support" section.</li>
            <li><strong>Manage Transactions:</strong> Monitor and manage all financial transactions in the "Manage Transactions" section.</li>
            <li><strong>User Activity Logs:</strong> Track user activities and system events in the "User Activity Logs" section.</li>
            <li><strong>View Messages:</strong> View messages sent by users through the contact form in the "View Messages" section.</li>
        </ul>
        <p>If you have any questions or need further assistance, please contact the system administrator.</p>
        <p><a href="admin_dashboard.php">Back to Dashboard</a></p>
    </div>
</body>
</html>