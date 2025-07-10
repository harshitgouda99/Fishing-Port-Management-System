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
    <title>Fishing Port Management System - Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
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
            <a href="registration.html">Register</a>
            <a href="login.html">Login</a>
            <a href="contact.html">Contact</a>
        </div>
    </div>
    <div class="dashboard-container">
        <h1>Welcome to the Fishing Port Management System</h1>
        <p>Hello, <strong><?php echo htmlspecialchars($_SESSION['full_name']); ?></strong>! You are logged in as <strong><?php echo ucfirst($_SESSION['user_role']); ?></strong>.</p>

        <!-- Navigation to Role-Specific Dashboards -->
        <h2>Navigate to Your Dashboard</h2>
        <ul>
            <?php if ($_SESSION['user_role'] == 'admin'): ?>
                <li><a href="admin_dashboard.php">Admin Dashboard</a></li>
            <?php elseif ($_SESSION['user_role'] == 'fisherman'): ?>
                <li><a href="fisherman_dashboard.php">Fisherman Dashboard</a></li>
            <?php elseif ($_SESSION['user_role'] == 'customer'): ?>
                <li><a href="customer_dashboard.php">Customer Dashboard</a></li>
            <?php endif; ?>
        </ul>

        <!-- Logout -->
        <p><a href="logout.php">Logout</a></p>
    </div>
</body>
</html>