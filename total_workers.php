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

// Handle delete
if (isset($_POST['delete_worker']) && isset($_POST['worker_id'])) {
    $worker_id = (int)$_POST['worker_id'];
    $conn->query("DELETE FROM workers WHERE id = $worker_id");
    header("Location: total_workers.php");
    exit();
}

// Handle update
$update_message = "";
if (isset($_POST['update_worker'])) {
    $worker_id = (int)$_POST['worker_id'];
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $role = $conn->real_escape_string($_POST['role']);
    $status = $conn->real_escape_string($_POST['status']);
    $sql = "UPDATE workers SET name='$name', email='$email', phone='$phone', role='$role', status='$status' WHERE id=$worker_id";
    if ($conn->query($sql) === TRUE) {
        $update_message = "<span style='color:green;'>Worker updated successfully!</span>";
    } else {
        $update_message = "<span style='color:red;'>Error updating worker: " . $conn->error . "</span>";
    }
}

// Fetch all workers
$workers = $conn->query(
    "SELECT w.*, u.full_name AS fisherman_name 
     FROM workers w 
     LEFT JOIN users u ON w.fisherman_id = u.id 
     ORDER BY w.created_at DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Total Workers</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: #f4f8fb;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1100px;
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
            padding: 10px 8px;
            border-bottom: 1px solid #e0e0e0;
            text-align: left;
        }
        th {
            background: #f0f6ff;
        }
        .actions button {
            padding: 6px 14px;
            border: none;
            border-radius: 5px;
            font-weight: 500;
            cursor: pointer;
            margin-right: 5px;
        }
        .update-btn {
            background: #007BFF;
            color: #fff;
        }
        .delete-btn {
            background: #dc3545;
            color: #fff;
        }
        .edit-form input, .edit-form select {
            width: 100%;
            padding: 5px;
            margin-bottom: 2px;
            border: 1px solid #ccc;
            border-radius: 3px;
            font-size: 1em;
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
        .msg {
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
    <script>
        function showEditForm(id) {
            document.getElementById('display-row-' + id).style.display = 'none';
            document.getElementById('edit-row-' + id).style.display = '';
        }
        function hideEditForm(id) {
            document.getElementById('display-row-' + id).style.display = '';
            document.getElementById('edit-row-' + id).style.display = 'none';
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Total Workers</h2>
        <?php if ($update_message) echo '<div class="msg">'.$update_message.'</div>'; ?>
        <?php if ($workers && $workers->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Fisherman</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>ID Proof</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $workers->fetch_assoc()): ?>
                <!-- Display Row -->
                <tr id="display-row-<?php echo $row['id']; ?>">
                    <td><?php echo htmlspecialchars($row['fisherman_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td><?php echo htmlspecialchars($row['role']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>
                        <?php if (!empty($row['idproof'])): ?>
                            <a href="<?php echo htmlspecialchars($row['idproof']); ?>" target="_blank">View</a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td class="actions">
                        <button class="update-btn" onclick="showEditForm(<?php echo $row['id']; ?>)">Edit</button>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this worker?');">
                            <input type="hidden" name="worker_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete_worker" class="delete-btn">Delete</button>
                        </form>
                    </td>
                </tr>
                <!-- Edit Row (hidden by default) -->
                <tr id="edit-row-<?php echo $row['id']; ?>" style="display:none;">
                    <form method="POST" class="edit-form">
                        <td><?php echo htmlspecialchars($row['fisherman_name']); ?></td>
                        <td><input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required></td>
                        <td><input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required></td>
                        <td><input type="text" name="phone" value="<?php echo htmlspecialchars($row['phone']); ?>" required></td>
                        <td>
                            <select name="role" required>
                                <option value="Driver" <?php if($row['role']=='Driver') echo 'selected'; ?>>Driver</option>
                                <option value="Worker" <?php if($row['role']=='Worker') echo 'selected'; ?>>Other Worker</option>
                            </select>
                        </td>
                        <td>
                            <select name="status" required>
                                <option value="pending" <?php if($row['status']=='pending') echo 'selected'; ?>>Pending</option>
                                <option value="approved" <?php if($row['status']=='approved') echo 'selected'; ?>>Approved</option>
                                <option value="rejected" <?php if($row['status']=='rejected') echo 'selected'; ?>>Rejected</option>
                            </select>
                        </td>
                        <td>
                            <?php if (!empty($row['idproof'])): ?>
                                <a href="<?php echo htmlspecialchars($row['idproof']); ?>" target="_blank">View</a>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <input type="hidden" name="worker_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="update_worker" class="update-btn">Save</button>
                            <button type="button" onclick="hideEditForm(<?php echo $row['id']; ?>)">Cancel</button>
                        </td>
                    </form>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p style="text-align:center;">No workers found.</p>
        <?php endif; ?>
        <a href="admin_dashboard.php" class="back-link">&#8592; Back to Dashboard</a>
    </div>
</body>
</html>
<?php
$conn->close();
?>