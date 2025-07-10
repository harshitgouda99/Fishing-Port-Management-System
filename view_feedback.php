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

// Handle deleting feedback
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_feedback'])) {
    $feedback_id = (int)$_POST['feedback_id'];
    $sql = "DELETE FROM feedback WHERE id = $feedback_id";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Feedback deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

// Handle reply to feedback
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_feedback'])) {
    $reply_email = $_POST['reply_email'];
    $reply_message = $_POST['reply_message'];
    $user_name = $_POST['user_name'];

    // Send reply email using PHPMailer
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
        $mail->addAddress($reply_email, $user_name);
        $mail->Subject = 'Reply to your feedback - DigiPortXpress';
        $mail->Body = $reply_message;
        $mail->send();
        echo "<script>alert('Reply sent successfully!');</script>";
    } catch (Exception $e) {
        echo "<script>alert('Failed to send reply.');</script>";
    }
}

// Fetch all feedback
$sql = "SELECT id, user_name, email, message, created_at FROM feedback ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback & Support - Fishing Port Management System</title>
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
    <script>
        function showReplyForm(id) {
            document.getElementById('reply-form-' + id).style.display = 'block';
        }
        function hideReplyForm(id) {
            document.getElementById('reply-form-' + id).style.display = 'none';
        }
    </script>
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
        <h1>Feedback & Support</h1>

        <!-- Feedback Table -->
        <h2>User Feedback</h2>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>User Name</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['message']); ?></td>
                            <td><?php echo $row['created_at']; ?></td>
                            <td>
                                <form action="view_feedback.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="feedback_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="delete_feedback">Delete</button>
                                </form>
                                <form action="view_feedback.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="reply_email" value="<?php echo htmlspecialchars($row['email']); ?>">
                                    <input type="hidden" name="user_name" value="<?php echo htmlspecialchars($row['user_name']); ?>">
                                    <input type="hidden" name="feedback_id" value="<?php echo $row['id']; ?>">
                                    <button type="button" onclick="showReplyForm(<?php echo $row['id']; ?>)">Reply</button>
                                </form>
                                <div id="reply-form-<?php echo $row['id']; ?>" style="display:none; margin-top:10px;">
                                    <form action="view_feedback.php" method="POST">
                                        <input type="hidden" name="reply_email" value="<?php echo htmlspecialchars($row['email']); ?>">
                                        <input type="hidden" name="user_name" value="<?php echo htmlspecialchars($row['user_name']); ?>">
                                        <textarea name="reply_message" rows="3" style="width:100%;" placeholder="Type your reply here..." required></textarea>
                                        <button type="submit" name="reply_feedback">Send Reply</button>
                                        <button type="button" onclick="hideReplyForm(<?php echo $row['id']; ?>)">Cancel</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No feedback found.</p>
        <?php endif; ?>

        <!-- Back to Dashboard -->
        <p><a href="admin_dashboard.php">Back to Dashboard</a></p>
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