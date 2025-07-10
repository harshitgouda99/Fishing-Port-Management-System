<?php
// filepath: c:\wamp64\www\New folder\customer_manual.php

// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Manual - Fishing Port Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #e3f2fd, #ffffff); /* Light gradient background */
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        /* Navigation Bar */
        .navbar {
            width: 100%;
            background: linear-gradient(90deg, #0fb7ff, #007BFF); /* Gradient background */
            padding: 15px 20px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2); /* Add shadow */
        }

        .navbar .logo a {
            font-size: 1.8em;
            font-weight: bold;
            color: #fff;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 2px;
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
            background-color: rgba(255, 255, 255, 0.2); /* Add hover effect */
            color: #ffeb3b; /* Yellow color on hover */
            transform: scale(1.1); /* Slight zoom effect */
        }

        .logo-image {
            width: 50px;
            height: auto;
            margin-right: 10px;
        }

        .website-name-container {
            display: flex;
            align-items: center;
        }

        .website-name {
            font-size: 1.5em;
            color: #fff;
            margin: 0;
        }

        /* Dashboard Container */
        .dashboard-container {
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 800px;
            margin-top: 100px; /* Adjust for navbar height */
            text-align: left;
        }

        .dashboard-container h1 {
            color: #007BFF;
            font-size: 2.5em;
            margin-bottom: 20px;
            text-align: center;
        }

        .dashboard-container p {
            font-size: 1.1em;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .dashboard-container h2 {
            color: #333;
            font-size: 1.8em;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 2px solid #007BFF;
            display: inline-block;
        }

        .dashboard-container ul, .dashboard-container ol {
            margin-left: 20px;
            font-size: 1.1em;
        }

        .dashboard-container ul li, .dashboard-container ol li {
            margin-bottom: 10px;
        }

        .dashboard-container ul li strong {
            color: #007BFF;
        }

        .dashboard-container a {
            color: #007BFF;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .dashboard-container a:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        /* Back Button */
        .back-button {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: white;
            background-color: #007BFF;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1.2em;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .back-button:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 20px;
            }

            .dashboard-container h1 {
                font-size: 2em;
            }

            .dashboard-container h2 {
                font-size: 1.5em;
            }

            .dashboard-container p, .dashboard-container ul, .dashboard-container ol {
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
        <h1>Customer Manual</h1>
        <p>Welcome to the Fishing Port Management System! This manual will guide you through the features and functionalities available to customers.</p>

        <h2>Features</h2>
        <ul>
            <li><strong>Place an Order:</strong> Customers can browse available fish listings and place orders.</li>
            <li><strong>View Orders:</strong> Customers can view their order history and track the status of their orders.</li>
            <li><strong>Update Profile:</strong> Customers can update their personal information.</li>
            <li><strong>Today's Activity:</strong> Customers can view their activities for the current day.</li>
        </ul>

        <h2>How to Use</h2>
        <ol>
            <li>Log in to your account using your credentials.</li>
            <li>Navigate through the dashboard to access various features.</li>
            <li>To place an order, go to the "Place an Order" section and select the fish you want to purchase.</li>
            <li>To view your orders, go to the "View My Orders" section.</li>
            <li>To update your profile, go to the "Update Profile" section.</li>
        </ol>

        <h2>Contact Us</h2>
        <p>If you have any questions or need assistance, feel free to reach out to our support team at <a href="mailto:digiportxpress@gmail.com">digiportxpress@gmail.com</a>.</p>

        <!-- Back to Dashboard -->
        <a href="customer_dashboard.php" class="back-button" style="color: #ffffff;">Back to Dashboard</a>
    </div>
</body>
</html>