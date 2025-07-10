<?php
// filepath: c:\wamp64\www\basicstructure\help_support.php

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
    <title>Help & Support - Fishing Port Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
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

        .help-container {
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 90%;
            max-width: 800px;
        }

        .help-container h1 {
            color: #007BFF;
            font-size: 2.5em;
            margin-bottom: 20px;
        }

        .help-container p {
            font-size: 1.2em;
            margin: 10px 0;
            color: #555;
        }

        .help-container a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #007BFF;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1em;
            transition: background 0.3s ease;
        }

        .help-container a:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="help-container">
        <h1>Help & Support</h1>
        <p>If you need assistance, please refer to the admin manual or contact the support team.</p>
        <p><strong>Email:</strong> <a href="mailto:digiportxpress@gmail.com">digiportxpress@gmail.com</a></p>
        <p><strong>Phone:</strong> <a href="tel:+917975462119">+91 7975462119</a></p>
        <a href="admin_dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>