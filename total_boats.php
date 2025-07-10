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
if (isset($_POST['delete_boat']) && isset($_POST['boat_id'])) {
    $boat_id = (int)$_POST['boat_id'];
    $stmt = $conn->prepare("DELETE FROM boat_registrations WHERE id = ?");
    $stmt->bind_param("i", $boat_id);
    if ($stmt->execute()) {
        header("Location: total_boats.php");
        exit();
    } else {
        echo "<script>alert('Error deleting boat.');</script>";
    }
    $stmt->close();
}

// Handle update
$update_message = "";
if (isset($_POST['update_boat'])) {
    $boat_id = (int)$_POST['boat_id'];
    $boat_name = $conn->real_escape_string($_POST['boat_name']);
    $registration_number = $conn->real_escape_string($_POST['registration_number']);
    $status = $conn->real_escape_string($_POST['status']);

    $stmt = $conn->prepare("UPDATE boat_registrations SET boat_name = ?, registration_number = ?, status = ? WHERE id = ?");
    $stmt->bind_param("sssi", $boat_name, $registration_number, $status, $boat_id);

    if ($stmt->execute()) {
        $update_message = "<span style='color:green;'>Boat updated successfully!</span>";
    } else {
        $update_message = "<span style='color:red;'>Error updating boat: " . $conn->error . "</span>";
    }
    $stmt->close();
}

// Fetch all boats
$boats = $conn->query(
    "SELECT br.*, u.full_name AS fisherman_name 
     FROM boat_registrations br 
     LEFT JOIN users u ON br.fisherman_id = u.id 
     ORDER BY br.created_at DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Total Boats</title>
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
        <h2>Total Boats</h2>
        <?php if ($update_message) echo '<div class="msg">'.$update_message.'</div>'; ?>
        <?php if ($boats && $boats->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Fisherman</th>
                    <th>Boat Name</th>
                    <th>Registration No</th>
                    <th>Status</th>
                    <th>Document</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $boats->fetch_assoc()): ?>
                <!-- Display Row -->
                <tr id="display-row-<?php echo $row['id']; ?>">
                    <td><?php echo htmlspecialchars($row['fisherman_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['boat_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['registration_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>
                        <?php if (!empty($row['document_path'])): ?>
                            <a href="<?php echo htmlspecialchars($row['document_path']); ?>" target="_blank">View Document</a>
                        <?php else: ?>
                            No Document
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td class="actions">
                        <button class="update-btn" type="button" onclick="showEditForm(<?php echo $row['id']; ?>)">Edit</button>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this boat?');">
                            <input type="hidden" name="boat_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete_boat" class="delete-btn">Delete</button>
                        </form>
                    </td>
                </tr>
                <!-- Edit Row (hidden by default) -->
                <tr id="edit-row-<?php echo $row['id']; ?>" style="display:none;">
                    <form method="POST" class="edit-form">
                        <td><?php echo htmlspecialchars($row['fisherman_name']); ?></td>
                        <td><input type="text" name="boat_name" value="<?php echo htmlspecialchars($row['boat_name']); ?>" required></td>
                        <td><input type="text" name="registration_number" value="<?php echo htmlspecialchars($row['registration_number']); ?>" required></td>
                        <td>
                            <select name="status" required>
                                <option value="pending" <?php if($row['status']=='pending') echo 'selected'; ?>>Pending</option>
                                <option value="approved" <?php if($row['status']=='approved') echo 'selected'; ?>>Approved</option>
                                <option value="rejected" <?php if($row['status']=='rejected') echo 'selected'; ?>>Rejected</option>
                            </select>
                        </td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td class="actions">
                            <input type="hidden" name="boat_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="update_boat" class="update-btn">Save</button>
                            <button type="button" onclick="hideEditForm(<?php echo $row['id']; ?>)">Cancel</button>
                        </td>
                    </form>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p style="text-align:center;">No boats found.</p>
        <?php endif; ?>
        <a href="admin_dashboard.php" class="back-link">&#8592; Back to Dashboard</a>
    </div>
</body>
</html>
<?php
$conn->close();
?>