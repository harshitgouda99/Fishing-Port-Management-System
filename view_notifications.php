<?php
// filepath: c:\wamp64\www\basicstructure\view_notifications.php

// Start session
session_start();

// Check if the user is logged in
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

// Fetch notifications
$sql = "SELECT title, message, created_at FROM notifications ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Notifications - Fishing Port Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
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

        .notifications-container {
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 90%;
            max-width: 800px;
        }

        .notifications-container h1 {
            color: #007BFF;
            font-size: 2.5em;
            margin-bottom: 20px;
        }

        .notification {
            border-bottom: 1px solid #ccc;
            padding: 15px 0;
            text-align: left;
        }

        .notification:last-child {
            border-bottom: none;
        }

        .notification h3 {
            font-size: 1.5em;
            color: #007BFF;
            margin: 0 0 10px;
        }

        .notification p {
            font-size: 1em;
            color: #555;
            margin: 0;
        }

        .notification small {
            display: block;
            margin-top: 10px;
            font-size: 0.9em;
            color: #999;
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #007BFF;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1em;
            transition: background 0.3s ease;
        }

        .back-button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="notifications-container">
        <h1>Notifications</h1>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="notification">
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p><?php echo htmlspecialchars($row['message']); ?></p>
                    <small>Posted on: <?php echo htmlspecialchars(date("F j, Y, g:i a", strtotime($row['created_at']))); ?></small>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No notifications available.</p>
        <?php endif; ?>
        <a href="customer_dashboard.php" class="back-button">Back to Dashboard</a>
    </div>
</body>
</html>
<?php
$conn->close();
?>