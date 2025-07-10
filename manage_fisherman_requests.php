<!-- filepath: c:\wamp64\www\basic structure\manage_fisherman_requests.php -->
<?php
// Start session
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.html");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'fishing_port'); // Update database name if needed

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle approval or rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'];
    $action = $_POST['action']; // 'approve' or 'reject'

    // Get user_id and email for this request
    $user_result = $conn->query("SELECT u.email, u.full_name FROM fisherman_requests fr JOIN users u ON fr.user_id = u.id WHERE fr.id = $request_id");
    $user_email = '';
    $user_name = '';
    if ($user_result && $user_result->num_rows > 0) {
        $user_row = $user_result->fetch_assoc();
        $user_email = $user_row['email'];
        $user_name = $user_row['full_name'];
    }

    if ($action === 'approve') {
        $conn->query("UPDATE fisherman_requests SET status = 'approved' WHERE id = $request_id");
        $conn->query("UPDATE users SET user_role = 'fisherman' WHERE id = (SELECT user_id FROM fisherman_requests WHERE id = $request_id)");
        // Send approval email
        if ($user_email) {
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
                $mail->addAddress($user_email, $user_name);
                $mail->Subject = 'Fisherman Request Approved';
                $mail->Body = "Dear $user_name,\n\nCongratulations! Your request to register as a fisherman has been approved. You can now access all fisherman features.\n\nThank you for using DigiPortXpress.";
                $mail->send();
            } catch (Exception $e) {
                // Optionally log or display error
            }
        }
    } elseif ($action === 'reject') {
        $conn->query("UPDATE fisherman_requests SET status = 'rejected' WHERE id = $request_id");
        // Send rejection email
        if ($user_email) {
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
                $mail->addAddress($user_email, $user_name);
                $mail->Subject = 'Fisherman Request Rejected';
                $mail->Body = "Dear $user_name,\n\nWe regret to inform you that your request to register as a fisherman has been rejected. For more information, please contact support.\n\nThank you for using DigiPortXpress.";
                $mail->send();
            } catch (Exception $e) {
                // Optionally log or display error
            }
        }
    }

    // Redirect to the same page to refresh the list
    header("Location: manage_fisherman_requests.php");
    exit();
}

// Fetch pending fisherman requests from the database
$result = $conn->query("SELECT fr.id, fr.name, fr.age, fr.gender, fr.phone, fr.id_proof, u.id AS user_id 
                        FROM fisherman_requests fr 
                        JOIN users u ON fr.user_id = u.id 
                        WHERE fr.status = 'pending'");

$requests = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Fisherman Requests - Fishing Port Management System</title>
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
        <h1>Manage Fisherman Requests</h1>
        <?php if (count($requests) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Phone</th>
                        <th>ID Proof</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $request): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($request['name']); ?></td>
                            <td><?php echo htmlspecialchars($request['age']); ?></td>
                            <td><?php echo htmlspecialchars($request['gender']); ?></td>
                            <td><?php echo htmlspecialchars($request['phone']); ?></td>
                            <td><a href="<?php echo htmlspecialchars($request['id_proof']); ?>" target="_blank">View ID Proof</a></td>
                            <td>
                                <form action="manage_fisherman_requests.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                    <button type="submit" name="action" value="approve">Approve</button>
                                </form>
                                <form action="manage_fisherman_requests.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                    <button type="submit" name="action" value="reject">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No pending requests.</p>
        <?php endif; ?>
        <p><a href="admin_dashboard.php">Back to Admin Dashboard</a></p>
    </div>
</body>
</html>
<?php
$conn->close();
?>