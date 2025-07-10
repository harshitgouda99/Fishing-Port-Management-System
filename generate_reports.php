<?php
// filepath: c:\wamp64\www\New folder\generate_reports.php

// Start session
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.html");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "fishing_port"; // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data for reports
// Total number of users by role
$users_sql = "SELECT user_role, COUNT(*) AS count FROM users GROUP BY user_role";
$users_result = $conn->query($users_sql);

// Total fish listings
$fish_listings_sql = "SELECT COUNT(*) AS total_listings, SUM(quantity) AS total_quantity FROM fish_listings";
$fish_listings_result = $conn->query($fish_listings_sql);
$fish_listings_data = $fish_listings_result->fetch_assoc();

// Total orders and revenue
$orders_sql = "SELECT COUNT(*) AS total_orders, SUM(total_price) AS total_revenue FROM orders WHERE status = 'completed'";
$orders_result = $conn->query($orders_sql);
$orders_data = $orders_result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Reports - Fishing Port Management System</title>
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
            display: flex;
            align-items: center;
        }

        .navbar .logo .logo-image {
            max-height: 50px;
        }

        .navbar .website-name-container {
            margin-left: 10px;
        }

        .navbar .website-name {
            font-size: 1.8em;
            font-weight: bold;
            color: #fff;
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

        .dashboard-container table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .dashboard-container table th,
        .dashboard-container table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        .dashboard-container table th {
            background-color: #007BFF;
            color: white;
        }

        .dashboard-container table tr:nth-child(even) {
            background-color: #f2f2f2;
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

            .dashboard-container table th,
            .dashboard-container table td {
                font-size: 0.9em;
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
           
            <a href="logout.php">Logout</a>
            <a href="contact.html">Contact</a>
        </div>
    </div>
    <div class="dashboard-container">
        <h1>System Reports</h1>

        <!-- Users Report -->
        <h2>Users Report</h2>
        <?php if ($users_result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>User Role</th>
                        <th>Count</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $users_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo ucfirst($row['user_role']); ?></td>
                            <td><?php echo $row['count']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No user data available.</p>
        <?php endif; ?>

        <!-- Fish Listings Report -->
        <h2>Fish Listings Report</h2>
        <table>
            <thead>
                <tr>
                    <th>Total Listings</th>
                    <th>Total Quantity (kg)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo $fish_listings_data['total_listings'] ?: 0; ?></td>
                    <td><?php echo $fish_listings_data['total_quantity'] ?: 0; ?></td>
                </tr>
            </tbody>
        </table>

        <!-- Orders and Revenue Report -->
        <h2>Orders and Revenue Report</h2>
        <table>
            <thead>
                <tr>
                    <th>Total Orders</th>
                    <th>Total Revenue (₹)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo $orders_data['total_orders'] ?: 0; ?></td>
                    <td><?php echo $orders_data['total_revenue'] ?: 0; ?></td>
                </tr>
            </tbody>
        </table>

        <!-- Back to Dashboard -->
        <p><a href="admin_dashboard.php">Back to Dashboard</a></p>
    </div>
</body>
</html>
<?php
// Close the connection
$conn->close();
?>