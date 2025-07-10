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
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "fishing_port"; // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle adding a new notification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_notification'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $message = $conn->real_escape_string($_POST['message']);
    $sql = "INSERT INTO notifications (title, message, created_at) VALUES ('$title', '$message', NOW())";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Notification added successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

// Handle deleting a notification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_notification'])) {
    $notification_id = (int)$_POST['notification_id'];
    $sql = "DELETE FROM notifications WHERE id = $notification_id";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Notification deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

// Fetch all notifications
$sql = "SELECT id, title, message, created_at FROM notifications ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Notifications - Fishing Port Management System</title>
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

        .dashboard-container form {
            margin-bottom: 30px;
        }

        .dashboard-container form input,
        .dashboard-container form textarea,
        .dashboard-container form button {
            font-size: 1em;
            padding: 10px;
            margin: 5px 0;
            width: 100%;
            max-width: 500px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .dashboard-container form button {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }

        .dashboard-container form button:hover {
            background-color: #0056b3;
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
            <a href="index.html">DIGIPORTXPRESS</a>
        </div>
        <div class="nav-links">
            <a href="index.html">Home</a>
            <a href="about.html">About</a>
            <a href="contact.html">Contact</a>
        </div>
    </div>
    <div class="dashboard-container">
        <h1>Manage Notifications</h1>

        <!-- Add Notification Form -->
        <form action="manage_notifications.php" method="POST">
            <input type="text" name="title" placeholder="Notification Title" required>
            <textarea name="message" placeholder="Notification Message" rows="4" required></textarea>
            <button type="submit" name="add_notification">Add Notification</button>
        </form>

        <!-- Notifications Table -->
        <h2>Existing Notifications</h2>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Message</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['message']); ?></td>
                            <td><?php echo $row['created_at']; ?></td>
                            <td>
                                <form action="manage_notifications.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="notification_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="delete_notification">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No notifications found.</p>
        <?php endif; ?>

        <!-- Back to Dashboard -->
        <p><a href="admin_dashboard.php">Back to Dashboard</a></p>
    </div>
</body>
</html>
<?php
// Close the connection
$conn->close();
?>