<?php
// filepath: c:\wamp64\www\New folder\fisherman_analytics.php

// Start session
session_start();

// Check if the user is logged in and is a fisherman
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'fisherman') {
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

// Fetch fisherman ID
$fisherman_id = $_SESSION['user_id'];

// Fetch analytics data for the last 2 years
$two_years_ago = date("Y-m-d", strtotime("-2 years"));

// Total trips
$total_trips_sql = "SELECT COUNT(*) AS total_trips FROM attendance WHERE fisherman_id = '$fisherman_id' AND created_at >= '$two_years_ago'";
$total_trips_result = $conn->query($total_trips_sql);
$total_trips = $total_trips_result->fetch_assoc()['total_trips'];

// Total fish listed
$total_fish_sql = "SELECT COUNT(*) AS total_listings, SUM(quantity) AS total_quantity FROM fish_listings WHERE fisherman_id = '$fisherman_id' AND created_at >= '$two_years_ago'";
$total_fish_result = $conn->query($total_fish_sql);
$total_fish_data = $total_fish_result->fetch_assoc();
$total_listings = $total_fish_data['total_listings'];
$total_quantity = $total_fish_data['total_quantity'];

// Total orders fulfilled
$total_orders_sql = "SELECT COUNT(*) AS total_orders, SUM(total_price) AS total_revenue FROM orders WHERE fisherman_id = '$fisherman_id' AND status = 'completed' AND order_date >= '$two_years_ago'";
$total_orders_result = $conn->query($total_orders_sql);
$total_orders_data = $total_orders_result->fetch_assoc();
$total_orders = $total_orders_data['total_orders'];
$total_revenue = $total_orders_data['total_revenue'];

// Monthly breakdown
$monthly_breakdown_sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS trips FROM attendance WHERE fisherman_id = '$fisherman_id' AND created_at >= '$two_years_ago' GROUP BY month ORDER BY month DESC";
$monthly_breakdown_result = $conn->query($monthly_breakdown_sql);

// Top fish types
$top_fish_sql = "SELECT fish_type, COUNT(*) AS count FROM fish_listings WHERE fisherman_id = '$fisherman_id' AND created_at >= '$two_years_ago' GROUP BY fish_type ORDER BY count DESC LIMIT 5";
$top_fish_result = $conn->query($top_fish_sql);

// Order status breakdown
$order_status_sql = "SELECT status, COUNT(*) AS count FROM orders WHERE fisherman_id = '$fisherman_id' AND order_date >= '$two_years_ago' GROUP BY status";
$order_status_result = $conn->query($order_status_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fisherman Analytics - Fishing Port Management System</title>
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
            background: #007BFF; /* Solid blue background */
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

        /* Dashboard Container */
        .dashboard-container {
            margin-top: 100px; /* Adjust for navbar height */
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

        /* Responsive Design */
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
            <a href="index.html">DIGIPORTXPRESS</a>
        </div>
        <div class="nav-links">
            <a href="index.html">Home</a>
            <a href="about.html">About</a>
            <a href="contact.html">Contact</a>
        </div>
    </div>
    <div class="dashboard-container">
        <h1>Fisherman Analytics</h1>
        <p>Here is a summary of your activities over the last 2 years:</p>

        <h2>Analytics Summary</h2>
        <table>
            <thead>
                <tr>
                    <th>Metric</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Total Trips</td>
                    <td><?php echo $total_trips ?: 0; ?></td>
                </tr>
                <tr>
                    <td>Total Fish Listed</td>
                    <td><?php echo $total_listings ?: 0; ?> listings (<?php echo $total_quantity ?: 0; ?> kg)</td>
                </tr>
                <tr>
                    <td>Total Orders Fulfilled</td>
                    <td><?php echo $total_orders ?: 0; ?></td>
                </tr>
                <tr>
                    <td>Total Revenue Generated</td>
                    <td>₹<?php echo $total_revenue ?: 0; ?></td>
                </tr>
            </tbody>
        </table>

        <h2>Monthly Breakdown</h2>
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Total Trips</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $monthly_breakdown_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['month']; ?></td>
                        <td><?php echo $row['trips']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h2>Top Fish Types</h2>
        <table>
            <thead>
                <tr>
                    <th>Fish Type</th>
                    <th>Listings</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $top_fish_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['fish_type']); ?></td>
                        <td><?php echo $row['count']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h2>Order Status Breakdown</h2>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $order_status_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo ucfirst($row['status']); ?></td>
                        <td><?php echo $row['count']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Back to Dashboard -->
        <p><a href="fisherman_dashboard.php">Back to Dashboard</a></p>
    </div>
</body>
</html>
<?php
// Close the connection
$conn->close();
?>