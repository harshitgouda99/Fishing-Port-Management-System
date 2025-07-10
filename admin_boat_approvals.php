<?php
// filepath: c:\wamp64\www\New folder\admin_boat_approvals.php

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

// Handle approval or rejection
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $boat_id = (int)$_POST['boat_id'];
    $action = $_POST['action'];

    if ($action == "approve") {
        $sql = "UPDATE boat_registrations SET status = 'approved' WHERE id = ?";
    } elseif ($action == "reject") {
        $sql = "UPDATE boat_registrations SET status = 'rejected' WHERE id = ?";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $boat_id);

    if ($stmt->execute()) {
        echo "<script>alert('Boat registration has been " . ($action == "approve" ? "approved" : "rejected") . ".');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
    $stmt->close();
}

// Fetch pending boat registrations
$sql = "SELECT br.id, br.boat_name, br.registration_number, br.capacity, br.document_path, u.full_name AS fisherman_name 
        FROM boat_registrations br
        JOIN users u ON br.fisherman_id = u.id
        WHERE br.status = 'pending'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Boat Registrations - Fishing Port Management System</title>
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

        .navbar .nav-links {
            display: flex;
            align-items: center;
        }

        .navbar .nav-links .back-button {
            margin-right: auto;
            color: #fff;
            text-decoration: none;
            font-size: 1.2em;
            font-weight: 500;
            transition: all 0.3s ease-in-out;
            padding: 8px 15px;
            border-radius: 5px;
        }

        .navbar .nav-links .back-button:hover {
            background-color: rgba(255, 255, 255, 0.2);
            color: #ffeb3b;
            transform: scale(1.1);
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

        .logo-image {
            height: 80px; /* Adjust the height for better fit */
            width: auto; /* Maintain aspect ratio */
            border-radius: 5px; /* Optional: Add rounded corners */
            margin-right: 10px; /* Space between logo and other elements */
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
        <h1>Manage Boat Registrations</h1>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Boat ID</th>
                        <th>Boat Name</th>
                        <th>Registration Number</th>
                        <th>Capacity (tons)</th>
                        <th>Fisherman</th>
                        <th>Document</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['boat_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['registration_number']); ?></td>
                            <td><?php echo $row['capacity']; ?></td>
                            <td><?php echo htmlspecialchars($row['fisherman_name']); ?></td>
                            <td>
                                <?php if (!empty($row['document_path'])): ?>
                                    <a href="<?php echo htmlspecialchars($row['document_path']); ?>" target="_blank">View Document</a>
                                <?php else: ?>
                                    No Document
                                <?php endif; ?>
                            </td>
                            <td>
                                <form action="admin_boat_approvals.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="boat_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="action" value="approve">Approve</button>
                                </form>
                                <form action="admin_boat_approvals.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="boat_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="action" value="reject">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No pending boat registrations.</p>
        <?php endif; ?>

        <!-- Back to Admin Dashboard -->
        <p><a href="admin_dashboard.php">Back to Dashboard</a></p>
    </div>
</body>
</html>
<?php
// Close the connection
$conn->close();
?>