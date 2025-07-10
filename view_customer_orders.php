<?php
// filepath: c:\wamp64\www\New folder\view_customer_orders.php

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

// Fetch customer orders for the logged-in fisherman
$fisherman_id = $_SESSION['user_id'];
$sql = "SELECT o.id AS order_id, o.fish_type, o.quantity, o.total_price, o.status, o.order_date, c.full_name AS customer_name 
        FROM orders o
        JOIN users c ON o.customer_id = c.id
        WHERE o.fisherman_id = '$fisherman_id'
        ORDER BY o.order_date DESC";
$result = $conn->query($sql);

// Handle form submission for accepting or rejecting orders
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['accept_order'])) {
        $order_id = (int)$_POST['order_id'];
        $update_sql = "UPDATE orders SET status = 'accepted' WHERE id = '$order_id' AND fisherman_id = '$fisherman_id'";
        $conn->query($update_sql);
        // Fetch customer email and order details
        $email_sql = "SELECT u.email, u.full_name, o.fish_type, o.quantity, o.total_price FROM orders o JOIN users u ON o.customer_id = u.id WHERE o.id = '$order_id'";
        $email_result = $conn->query($email_sql);
        if ($email_result && $email_result->num_rows > 0) {
            $row = $email_result->fetch_assoc();
            $customer_email = $row['email'];
            $customer_name = $row['full_name'];
            $fish_type = $row['fish_type'];
            $quantity = $row['quantity'];
            $total_price = $row['total_price'];
            // Send email using PHPMailer
            require_once __DIR__ . '/vendor/autoload.php';
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'digiportxpress@gmail.com';
                $mail->Password = 'xaty dolc nnwa qkmk';
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->setFrom('digiportxpress@gmail.com', 'DigiPortXpress');
                $mail->addAddress($customer_email, $customer_name);
                $mail->Subject = 'Your Order Has Been Accepted!';
                $mail->Body = "Dear $customer_name,\n\nYour order for $quantity kg of $fish_type (Order ID: $order_id) has been accepted!\nTotal Price: ₹$total_price\n\nThank you for using DigiPortXpress.";
                $mail->send();
            } catch (Exception $e) {
                // Optionally log or display error
            }
        }
    } elseif (isset($_POST['reject_order'])) {
        $order_id = (int)$_POST['order_id'];
        $update_sql = "UPDATE orders SET status = 'cancelled' WHERE id = '$order_id' AND fisherman_id = '$fisherman_id'";
        $conn->query($update_sql);
    } elseif (isset($_POST['verify_otp'])) {
        $order_id = (int)$_POST['order_id'];
        $entered_otp = $_POST['otp'];

        // Fetch the order details
        $order_sql = "SELECT otp, otp_expiry FROM orders WHERE id = '$order_id' AND fisherman_id = '$fisherman_id'";
        $order_result = $conn->query($order_sql);
        if ($order_result->num_rows == 1) {
            $order = $order_result->fetch_assoc();
            if ($order['otp'] === $entered_otp && strtotime($order['otp_expiry']) > time()) {
                // Mark the order as completed
                $update_sql = "UPDATE orders SET status = 'completed' WHERE id = '$order_id'";
                $conn->query($update_sql);
                // Fetch customer email and order details
                $email_sql = "SELECT u.email, u.full_name, o.fish_type, o.quantity, o.total_price FROM orders o JOIN users u ON o.customer_id = u.id WHERE o.id = '$order_id'";
                $email_result = $conn->query($email_sql);
                if ($email_result && $email_result->num_rows > 0) {
                    $row = $email_result->fetch_assoc();
                    $customer_email = $row['email'];
                    $customer_name = $row['full_name'];
                    $fish_type = $row['fish_type'];
                    $quantity = $row['quantity'];
                    $total_price = $row['total_price'];
                    // Send email using PHPMailer
                    require_once __DIR__ . '/vendor/autoload.php';
                    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'digiportxpress@gmail.com';
                        $mail->Password = 'xaty dolc nnwa qkmk';
                        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;
                        $mail->setFrom('digiportxpress@gmail.com', 'DigiPortXpress');
                        $mail->addAddress($customer_email, $customer_name);
                        $mail->Subject = 'Your Order is Completed!';
                        $mail->Body = "Dear $customer_name,\n\nYour order for $quantity kg of $fish_type (Order ID: $order_id) has been completed!\nTotal Price: ₹$total_price\n\nThank you for using DigiPortXpress.";
                        $mail->send();
                    } catch (Exception $e) {
                        // Optionally log or display error
                    }
                }
            } else {
                echo "<script>alert('Invalid or expired OTP.');</script>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Customer Orders - Fishing Port Management System</title>
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
        <h1>Customer Orders</h1>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Fish Type</th>
                        <th>Quantity (kg)</th>
                        <th>Total Price (₹)</th>
                        <th>Status</th>
                        <th>Order Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['order_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['fish_type']); ?></td>
                            <td><?php echo $row['quantity']; ?></td>
                            <td><?php echo $row['total_price']; ?></td>
                            <td><?php echo ucfirst($row['status']); ?></td>
                            <td><?php echo $row['order_date']; ?></td>
                            <td>
                                <?php if ($row['status'] === 'pending'): ?>
                                    <form action="view_customer_orders.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                                        <button type="submit" name="accept_order">Accept</button>
                                        <button type="submit" name="reject_order">Reject</button>
                                    </form>
                                <?php elseif ($row['status'] === 'accepted'): ?>
                                    <form action="view_customer_orders.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                                        <input type="text" name="otp" placeholder="Enter OTP" required>
                                        <button type="submit" name="verify_otp">Verify OTP</button>
                                    </form>
                                <?php else: ?>
                                    <?php echo ucfirst($row['status']); ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No customer orders found.</p>
        <?php endif; ?>

        <!-- Back to Dashboard -->
        <p><a href="fisherman_dashboard.php">Back to Dashboard</a></p>
    </div>
    <footer>
        <div style="text-align: center; padding: 10px; background-color: #f1f1f1;">
            <p>&copy; 2025 Fishing Port Management System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
<?php
// Close the connection
$conn->close();
?>