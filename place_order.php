<?php
// filepath: c:\wamp64\www\New folder\place_order.php

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

// Fetch available fish listings
$sql = "SELECT f.id AS listing_id, f.fish_type, f.quantity, f.price_per_kg, u.full_name AS fisherman_name 
        FROM fish_listings f
        JOIN users u ON f.fisherman_id = u.id
        WHERE f.quantity > 0";
$result = $conn->query($sql);

// Handle form submission for placing an order
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = $_SESSION['user_id'];
    $listing_id = (int)$_POST['listing_id'];
    $quantity = (int)$_POST['quantity'];

    // Fetch the selected fish listing
    $listing_sql = "SELECT fisherman_id, fish_type, quantity, price_per_kg FROM fish_listings WHERE id = '$listing_id'";
    $listing_result = $conn->query($listing_sql);

    if ($listing_result->num_rows == 1) {
        $listing = $listing_result->fetch_assoc();

        // Check if the requested quantity is available
        if ($quantity > 0 && $quantity <= $listing['quantity']) {
            $fisherman_id = $listing['fisherman_id'];
            $fish_type = $listing['fish_type'];
            $price_per_kg = $listing['price_per_kg'];
            $total_price = $quantity * $price_per_kg;

            // Generate a random 6-digit OTP
            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $otp_expiry = date('Y-m-d H:i:s', strtotime('+45 minutes'));

            // Insert the order into the database
            $order_sql = "INSERT INTO orders (customer_id, fisherman_id, fish_type, quantity, total_price, status, otp, otp_expiry) 
                          VALUES ('$customer_id', '$fisherman_id', '$fish_type', '$quantity', '$total_price', 'pending', '$otp', '$otp_expiry')";

            if ($conn->query($order_sql) === TRUE) {
                // Update the quantity in the fish listing
                $new_quantity = $listing['quantity'] - $quantity;
                $update_sql = "UPDATE fish_listings SET quantity = '$new_quantity' WHERE id = '$listing_id'";
                $conn->query($update_sql);

                // Display success message
                $message = "<p class='success'>Order placed successfully! Your OTP is $otp.</p>";
            } else {
                // Display error message
                $message = "<p class='error'>Error placing order: " . $conn->error . "</p>";
            }
        } else {
            $message = "<p class='error'>Requested quantity is not available.</p>";
        }
    } else {
        $message = "<p class='error'>Invalid fish listing.</p>";
    }
}

// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place an Order - Fishing Port Management System</title>
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

        /* Form Container */
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
            color: #007BFF; /* Bright blue for the heading */
            margin-bottom: 20px;
        }

        .dashboard-container label {
            display: block;
            margin: 10px 0 5px;
            color: #333; /* Dark text for labels */
            font-weight: bold;
        }

        .dashboard-container select,
        .dashboard-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
            background: #f1f8e9; /* Light green background for dropdown and input */
            color: #333; /* Dark text */
        }

        .dashboard-container select:hover,
        .dashboard-container input:hover {
            background: #e8f5e9; /* Slightly darker green on hover */
        }

        .dashboard-container button {
            background-color: #007BFF; /* Bright blue button */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1.2em;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .dashboard-container button:hover {
            background-color: #0056b3; /* Darker blue on hover */
            transform: scale(1.05); /* Slight zoom effect */
        }

        .dashboard-container p a {
            color: #007BFF;
            text-decoration: none;
            font-weight: bold;
        }

        .dashboard-container p a:hover {
            text-decoration: underline;
        }

        .success {
            color: green;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .error {
            color: red;
            font-weight: bold;
            margin-bottom: 15px;
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
        <h1>Place an Order</h1>
        <?php if (!empty($message)): ?>
            <?php echo $message; ?>
        <?php endif; ?>
        <?php if ($result->num_rows > 0): ?>
            <form action="place_order.php" method="POST">
                <label for="listing_id">Select Fish:</label>
                <select id="listing_id" name="listing_id" required>
                    <option value="">-- Select Fish --</option>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <option value="<?php echo $row['listing_id']; ?>">
                            <?php echo htmlspecialchars($row['fish_type']) . " - ₹" . $row['price_per_kg'] . "/kg - " . $row['quantity'] . "kg available (Fisherman: " . htmlspecialchars($row['fisherman_name']) . ")"; ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label for="quantity">Quantity (kg):</label>
                <input type="number" id="quantity" name="quantity" min="1" required>

                <button type="submit">Place Order</button>
            </form>
        <?php else: ?>
            <p>No fish listings available at the moment.</p>
        <?php endif; ?>

        <!-- Back to Dashboard -->
        <p><a href="customer_dashboard.php">Back to Dashboard</a></p>
    </div>
</body>
</html>