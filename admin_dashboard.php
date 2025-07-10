<?php
// Start session
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.html");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fishing_port";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get total boats
$total_boats = 0;
$boats_result = $conn->query("SELECT COUNT(*) AS total FROM boat_registrations");
if ($boats_result && $row = $boats_result->fetch_assoc()) {
    $total_boats = $row['total'];
}

// Get total workers
$total_workers = 0;
$workers_result = $conn->query("SELECT COUNT(*) AS total FROM workers");
if ($workers_result && $row = $workers_result->fetch_assoc()) {
    $total_workers = $row['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Fishing Port Management System</title>
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
            display: flex;
            align-items: center;
        }

        .navbar .logo .logo-image {
            max-height: 50px;
            margin-right: 10px;
        }

        .navbar .website-name-container {
            flex-grow: 1;
            text-align: center;
        }

        .navbar .website-name {
            font-size: 1.8em;
            font-weight: bold;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 0;
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
        }

        .dashboard-container h1 {
            color: #007BFF;
            font-size: 2.5em;
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
        <h1>Welcome, Admin!</h1>
        <div class="card-container">
           
            <!-- Existing Features -->
            <div class="card">
                <a href="manage_users.php">
                    <i class="fa fa-users"></i>
                    <h3>Manage Users</h3>
                    <p>View and manage all users.</p>
                </a>
            </div>
           
            <div class="card">
                <a href="generate_reports.php">
                    <i class="fa fa-chart-bar"></i>
                    <h3>Generate Reports</h3>
                    <p>Create detailed system reports.</p>
                </a>
            </div>
            <div class="card">
                <a href="admin_boat_approvals.php">
                    <i class="fa fa-ship"></i>
                    <h3>Manage Boat Registrations</h3>
                    <p>Approve or reject boat registrations.</p>
                </a>
            </div>
            <div class="card">
                <a href="manage_fisherman_requests.php">
                    <i class="fa fa-user-check"></i>
                    <h3>Manage Fisherman Requests</h3>
                    <p>Handle requests from fishermen.</p>
                </a>
            </div>
            <div class="card">
                <a href="today_activity.php">
                    <i class="fa fa-calendar-day"></i>
                    <h3>Today's Activity</h3>
                    <p>View today's activities and logs.</p>
                </a>
            </div>
            <div class="card">
                <a href="admin_manual.php">
                    <i class="fa fa-book"></i>
                    <h3>Admin Manual</h3>
                    <p>Access the admin manual.</p>
                </a>
            </div>
            <!-- New Features -->
            
            <div class="card">
                <a href="manage_notifications.php">
                    <i class="fa fa-bell"></i>
                    <h3>Manage Notifications</h3>
                    <p>Send announcements and updates to users.</p>
                </a>
            </div>
            <div class="card">
                <a href="view_feedback.php">
                    <i class="fa fa-comments"></i>
                    <h3>Feedback & Support</h3>
                    <p>View and respond to user feedback.</p>
                </a>
            </div>
           
           
            <div class="card">
                <a href="view_messages.php">
                    <i class="fa fa-envelope"></i>
                    <h3>View Messages</h3>
                    <p>View messages sent by users through the contact form.</p>
                </a>
            </div>
            
            <div class="card">
                <a href="admin_profile.php">
                    <i class="fa fa-user-shield"></i>
                    <h3>Admin Profile</h3>
                    <p>View and update your admin profile.</p>
                </a>
            </div>
            <div class="card">
                <a href="help_support.php">
                    <i class="fa fa-life-ring"></i>
                    <h3>Help & Support</h3>
                    <p>Access help documentation and support.</p>
                </a>
            </div>
            <div class="card">
                <a href="manage_workers_requests.php">
                    <i class="fa fa-id-badge"></i>
                    <h3>Manage Workers Requests</h3>
                    <p>Approve or reject workers added by fishermen.</p>
                </a>
            </div>
             <!-- Total Boats Feature -->
            <div class="card">
                <a href="total_boats.php">
                <i class="fa fa-ship"></i>
                <h3>Total Boats</h3>
                <p><?php echo $total_boats; ?> boats registered</p>
            </div>
            <!-- Total Workers Feature -->
            <div class="card">
                <a href="total_workers.php">
                <i class="fa fa-users"></i>
                <h3>Total Workers</h3>
                <p><?php echo $total_workers; ?> workers registered</p>
            </div>
            
        </div>
        <!-- Logout -->
        <p><a href="logout.php" class="logout">Logout</a></p>
    </div>
</body>
</html>