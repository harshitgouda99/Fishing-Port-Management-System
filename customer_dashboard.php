<?php
// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Store session data for use in the HTML
$full_name = htmlspecialchars($_SESSION['full_name']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - Fishing Port Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #e3f2fd, #ffffff);
            color: #333;
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

        .navbar .back-button {
            color: #fff;
            text-decoration: none;
            font-size: 1.2em;
            font-weight: 500;
            padding: 8px 15px;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.2);
            margin-right: 15px;
        }

        .navbar .back-button:hover {
            background-color: rgba(255, 255, 255, 0.4);
            color: #ffeb3b;
        }

        .logo-image {
            height: 80px; /* Reduced height for better fit */
            width: auto; /* Maintain aspect ratio */
            border-radius: 5px; /* Optional: Add rounded corners */
            margin-right: 10px; /* Space between logo and website name */
        }

        .dashboard-container {
            margin: 0 auto;
            text-align: center;
            width: 90%;
            max-width: 1200px;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh; /* Ensures the container takes the full height of the viewport */
        }

        .dashboard-container h1 {
            color: #007BFF;
            font-size: 2.5em;
            margin-bottom: 20px;
        }

        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .card {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            width: 250px;
        }

        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .card i {
            font-size: 2.5em;
            color: #007BFF;
            margin-bottom: 10px;
        }

        .card h3 {
            font-size: 1.5em;
            margin: 10px 0;
            color: #333;
        }

        .card p {
            font-size: 1em;
            color: #666;
        }

        @media (max-width: 768px) {
            .dashboard-container h1 {
                font-size: 2em;
            }

            .card i {
                font-size: 2em;
            }

            .card h3 {
                font-size: 1.2em;
            }

            .card p {
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
        <h1>Welcome, <?php echo $full_name; ?>!</h1>
        <div class="card-container">
            <div class="card">
                <a href="place_order.php">
                    <i class="fa fa-shopping-cart"></i>
                    <h3>Place Order</h3>
                    <p>Order your desired items easily.</p>
                </a>
            </div>
            <div class="card">
                <a href="view_orders.php">
                    <i class="fa fa-list-alt"></i>
                    <h3>View Orders</h3>
                    <p>Check the status of your orders.</p>
                </a>
            </div>
            <div class="card">
                <a href="update_profile.php">
                    <i class="fa fa-user-edit"></i>
                    <h3>Update Profile</h3>
                    <p>Keep your profile information up-to-date.</p>
                </a>
            </div>
            <div class="card">
                <a href="customer_manual.php">
                    <i class="fa fa-book"></i>
                    <h3>Customer Manual</h3>
                    <p>Learn how to use the system effectively.</p>
                </a>
            </div>
            <div class="card">
                <a href="register_as_fisherman.php">
                    <i class="fa fa-fish"></i>
                    <h3>Register as Fisherman</h3>
                    <p>Apply to become a registered fisherman.</p>
                </a>
            </div>
            <div class="card">
                <a href="boat_arrival_details.php">
                    <i class="fa fa-ship"></i>
                    <h3>Boat Arrival Details</h3>
                    <p>View the latest boat arrival schedules.</p>
                </a>
            </div>
            <div class="card">
                <a href="view_notifications.php">
                    <i class="fa fa-bell"></i>
                    <h3>View Notifications</h3>
                    <p>See all notifications posted by the admin.</p>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
