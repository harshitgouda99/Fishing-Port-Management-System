<?php
session_start();

// Only admin can access
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

// Handle approve/reject
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['worker_id'], $_POST['action'])) {
    $worker_id = (int)$_POST['worker_id'];
    $action = $_POST['action'] === 'approve' ? 'approved' : 'rejected';

    $conn->query("UPDATE workers SET status='$action' WHERE id=$worker_id");

    // Send email notification to worker
    $result = $conn->query("SELECT name, email FROM workers WHERE id=$worker_id");
    if ($row = $result->fetch_assoc()) {
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
            $mail->addAddress($row['email'], $row['name']);
            if ($action === 'approved') {
                $mail->Subject = 'Worker Registration Approved';
                $mail->Body = "Dear {$row['name']},\n\nYour registration as a worker has been approved by the admin. You may now work under your fisherman.\n\nThank you!";
            } else {
                $mail->Subject = 'Worker Registration Rejected';
                $mail->Body = "Dear {$row['name']},\n\nWe regret to inform you that your registration as a worker has been rejected. Please contact your fisherman or admin for more details.\n\nThank you!";
            }
            $mail->send();
        } catch (Exception $e) {
            // Optionally log error
        }
    }
    header("Location: manage_workers_requests.php");
    exit();
}

// Fetch pending workers
$pending_workers = $conn->query(
    "SELECT w.*, u.full_name AS fisherman_name 
     FROM workers w 
     JOIN users u ON w.fisherman_id = u.id 
     WHERE w.status = 'pending' 
     ORDER BY w.created_at DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Workers Requests</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: #f4f8fb;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.13);
            padding: 32px;
        }
        h2 {
            color: #007BFF;
            margin-bottom: 22px;
            font-weight: 600;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        th, td {
            padding: 12px 10px;
            border-bottom: 1px solid #e0e0e0;
            text-align: left;
        }
        th {
            background: #f0f6ff;
        }
        .actions button {
            padding: 7px 16px;
            border: none;
            border-radius: 5px;
            font-weight: 500;
            cursor: pointer;
            margin-right: 7px;
        }
        .approve-btn {
            background: #28a745;
            color: #fff;
        }
        .reject-btn {
            background: #dc3545;
            color: #fff;
        }
        .idproof-link {
            color: #007BFF;
            text-decoration: underline;
        }
        .back-link {
            display: inline-block;
            margin-top: 18px;
            color: #007BFF;
            text-decoration: none;
            font-weight: 500;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Workers Requests</h2>
        <?php if ($pending_workers && $pending_workers->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Fisherman</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>ID Proof</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $pending_workers->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['fisherman_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td><?php echo htmlspecialchars($row['role']); ?></td>
                    <td>
                        <?php if (!empty($row['idproof'])): ?>
                            <a class="idproof-link" href="<?php echo htmlspecialchars($row['idproof']); ?>" target="_blank">View</a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td class="actions">
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="worker_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="action" value="approve" class="approve-btn">Approve</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="worker_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="action" value="reject" class="reject-btn">Reject</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p style="text-align:center;">No pending worker requests.</p>
        <?php endif; ?>
        <a href="admin_dashboard.php" class="back-link">&#8592; Back to Dashboard</a>
    </div>
</body>
</html>
<?php
$conn->close();
?>