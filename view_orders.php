<?php
// filepath: c:\wamp64\www\New folder\view_orders.php

// Start session
session_start();

// Check if the user is logged in and is a customer
if (!isset($_SESSION['user_id'])) {
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

// Fetch orders for the logged-in customer
$customer_id = $_SESSION['user_id'];
$sql = "SELECT o.id, o.fish_type, o.quantity, o.total_price, o.status, o.order_date, u.full_name AS fisherman_name, u.phone AS fisherman_phone, o.otp 
        FROM orders o
        JOIN users u ON o.fisherman_id = u.id
        WHERE o.customer_id = '$customer_id'
        ORDER BY o.id DESC"; // Sort by order ID in descending order
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders - Fishing Port Management System</title>
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

        /* Orders Container */
        .dashboard-container {
            background: linear-gradient(135deg, #ffffff, #e3f2fd); /* Light gradient background */
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 900px;
            text-align: center;
            margin-top: 100px; /* Adjust for navbar height */
        }

        .dashboard-container h1 {
            color: #007BFF; /* Bright blue for the heading */
            margin-bottom: 20px;
        }

        .dashboard-container table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .dashboard-container table th,
        .dashboard-container table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
            color: #333; /* Dark text */
        }

        .dashboard-container table th {
            background-color: #007BFF; /* Bright blue for table header */
            color: white;
        }

        .dashboard-container table tr:nth-child(even) {
            background-color: #f2f2f2; /* Light gray for alternate rows */
        }

        .dashboard-container p a {
            text-decoration: none;
            color: white;
            background-color: #007BFF; /* Bright blue button */
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1.2em;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .dashboard-container p a:hover {
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
        <h1>My Orders</h1>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Fish Type</th>
                        <th>Quantity (kg)</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Order Date</th>
                        <th>Fisherman</th>
                        <th>Fisherman Phone</th>
                        <th>OTP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['fish_type']); ?></td>
                            <td><?php echo $row['quantity']; ?></td>
                            <td><?php echo $row['total_price']; ?></td>
                            <td><?php echo ucfirst($row['status']); ?></td>
                            <td><?php echo $row['order_date']; ?></td>
                            <td><?php echo htmlspecialchars($row['fisherman_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['fisherman_phone']); ?></td>
                            <td><?php echo $row['otp'] ? $row['otp'] : 'N/A'; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have no orders yet.</p>
        <?php endif; ?>

        <!-- Back to Dashboard -->
        <p><a href="customer_dashboard.php">Back to Dashboard</a></p>
    </div>
</body>
</html>
<?php
// Close the connection
$conn->close();
?>