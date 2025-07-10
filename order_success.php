<?php
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
    <title>Order Success - Fishing Port Management System</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            width: 100%;
            background: url('assets/images/background_image5.jpg') no-repeat center center fixed;
            background-size: cover;
            position: relative;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        /* Add a semi-transparent overlay */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6); /* Darker overlay for better contrast */
            z-index: -1; /* Place it behind the content */
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

        .navbar .back-button a {
            text-decoration: none;
            color: white;
            background-color: #28a745; /* Green button */
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 1em;
            font-weight: bold;
            display: flex;
            align-items: center;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .navbar .back-button a:hover {
            background-color: #218838; /* Darker green on hover */
            transform: scale(1.05); /* Slight zoom effect */
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

        /* Success Container */
        .dashboard-container {
            background: linear-gradient(135deg, #ffffff, #e3f2fd); /* Light gradient background */
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 600px;
            text-align: center;
            margin-top: 100px; /* Adjust for navbar height */
        }

        .dashboard-container h1 {
            color: #28a745; /* Green for success message */
            margin-bottom: 20px;
        }

        .dashboard-container p {
            color: #333; /* Dark text */
            font-size: 1.2em;
            margin-bottom: 20px;
        }

        .dashboard-container a {
            text-decoration: none;
            color: white;
            background-color: #007BFF; /* Bright blue button */
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1.2em;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .dashboard-container a:hover {
            background-color: #0056b3; /* Darker blue on hover */
            transform: scale(1.05); /* Slight zoom effect */
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="back-button">
            <a href="customer_dashboard.php">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
        <div class="logo">
            <a href="index.html">DIGIPORTXPRESS</a>
        </div>
        <div class="nav-links">
            <a href="index.html">Home</a>
            <a href="about.html">About</a>
            <a href="contact.html">Contact</a>
        </div>
    </div>
    <div class="dashboard-container">
        <h1>Order Placed Successfully!</h1>
        <p>Your order has been placed successfully. You can view your order details on the "View Orders" page.</p>
        <a href="view_orders.php">Go to View Orders</a>
    </div>
</body>
</html>