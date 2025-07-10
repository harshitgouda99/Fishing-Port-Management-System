<?php
// Start session
session_start();

// Check if the user is logged in and is a fisherman
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'fisherman') {
    header("Location: login.html");
    exit();
}

// Get the current date
$current_date = date("l, F j, Y"); // Example: "Monday, May 11, 2025"
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fisherman Dashboard - Fishing Port Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
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
            background: linear-gradient(90deg, #0fb7ffe9, #0fb7ffe9);
            padding: 20px 20px; /* Increased padding for height */
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .logo-image {
            height: 50px; /* Adjust the height of the logo */
            width: auto;
            border-radius: 5px; /* Optional: Add rounded corners */
            margin-right: 10px; /* Space between logo and website name */
        }

        .website-name-container {
            text-align: center;
        }

        .website-name {
            font-size: 1.5em;
            font-weight: bold;
            color: #fff;
            text-transform: uppercase;
            font-family: 'Roboto', Arial, sans-serif;
        }

        .nav-links {
            display: flex;
            gap: 15px;
        }

        .nav-links a {
            color: #fff; /* Changed to white for better contrast */
            text-decoration: none;
            font-size: 1.2em;
            font-weight: 500;
            transition: all 0.3s ease-in-out;
            padding: 8px 15px;
            border-radius: 5px;
        }

        .nav-links a:hover {
            background-color: rgba(255, 255, 255, 0.2);
            color: #ffeb3b; /* Change color on hover */
            transform: scale(1.1);
        }

        /* Dashboard Container */
        .dashboard-container {
            margin-top: 100px; /* Adjust for navbar height */
            text-align: center;
        }

        .dashboard-container h1 {
            color: #007BFF;
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .dashboard-container .date {
            font-size: 1.2em;
            color: #555;
            margin-bottom: 20px;
        }

        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            background: #ffffff;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
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

        .card a {
            text-decoration: none;
            color: inherit;
        }

        .back-link {
            margin-top: 20px;
            text-align: center;
        }

        .back-link a {
            text-decoration: none;
            color: #007BFF;
            font-size: 1.2em;
            font-weight: bold;
        }

        /* Responsive Design */
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
        <h1>Welcome, Fisherman!</h1>
        <div class="date"><?php echo $current_date; ?></div>
        <div class="card-container">
            <div class="card">
                <a href="mark_attendance.php">
                    <i class="fa fa-check-circle"></i>
                    <h3>Mark Attendance</h3>
                    <p>Mark your daily attendance.</p>
                </a>
            </div>
            <div class="card">
                <a href="view_customer_orders.php">
                    <i class="fa fa-list-alt"></i>
                    <h3>View Orders</h3>
                    <p>Check customer orders.</p>
                </a>
            </div>
            <div class="card">
                <a href="list_fish.php">
                    <i class="fa fa-fish"></i>
                    <h3>List Fish</h3>
                    <p>List fish for sale.</p>
                </a>
            </div>
            <div class="card">
                <a href="view_fish_listings.php">
                    <i class="fa fa-eye"></i>
                    <h3>View Listings</h3>
                    <p>View your fish listings.</p>
                </a>
            </div>
            <div class="card">
                <a href="boat_registration.php">
                    <i class="fa fa-ship"></i>
                    <h3>Register Boat</h3>
                    <p>Register your fishing boat.</p>
                </a>
            </div>
            <div class="card">
                <a href="show_your_boats.php">
                    <i class="fa fa-anchor"></i>
                    <h3>Show Boats</h3>
                    <p>View your registered boats.</p>
                </a>
            </div>
            <div class="card">
                <a href="todayactivity2.php">
                    <i class="fa fa-calendar-day"></i>
                    <h3>Today's Port Activity</h3>
                    <p>View your daily activities.</p>
                </a>
            </div>
            <div class="card">
                <a href="fisherman_analytics.php">
                    <i class="fa fa-chart-line"></i>
                    <h3>Analytics</h3>
                    <p>View your performance analytics.</p>
                </a>
            </div>
            <div class="card">
                <a href="view_notifications2.php">
                    <i class="fa fa-bell"></i>
                    <h3>View Notifications</h3>
                    <p>See all notifications posted by the admin.</p>
                </a>
            </div>
            <div class="card">
                <a href="add_workers.php">
                    <i class="fa fa-users"></i>
                    <h3>Add Your Workers</h3>
                    <p>Add and manage your fishing crew or workers.</p>
                </a>
            </div>
            <div class="card">
                <a href="track_trips.php">
                    <i class="fa fa-map-marker-alt"></i>
                    <h3>Track Your Trips</h3>
                    <p>View and manage your fishing trips.</p>
                </a>
            </div>
        </div>
        <div class="back-link">
            <a href="index.html">Back to home</a>
        </div>
    </div>
</body>
</html>