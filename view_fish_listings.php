<?php
// filepath: c:\wamp64\www\New folder\view_fish_listings.php

// Start session
session_start();

// Check if the user is logged in and is a fisherman
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'fisherman') {
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

// Delete listings older than 18 hours
$conn->query("DELETE FROM fish_listings WHERE TIMESTAMPDIFF(HOUR, created_at, NOW()) > 18");

// Handle updates to quantity and price
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_listing'])) {
    $listing_id = intval($_POST['listing_id']);
    $quantity = intval($_POST['quantity']);
    $price = floatval($_POST['price']);

    $stmt = $conn->prepare("UPDATE fish_listings SET quantity = ?, price_per_kg = ? WHERE id = ?");
    $stmt->bind_param("ddi", $quantity, $price, $listing_id);

    if ($stmt->execute()) {
        echo "<script>alert('Listing updated successfully!');</script>";
    } else {
        echo "<script>alert('Failed to update listing.');</script>";
    }

    $stmt->close();
}

// Fetch fish listings for the logged-in fisherman
$fisherman_id = $_SESSION['user_id'];
$sql = "SELECT id, fish_type, quantity, price_per_kg, created_at FROM fish_listings WHERE fisherman_id = '$fisherman_id'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View My Fish Listings - Fishing Port Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #e3f2fd, #ffffff);
            color: #333;
        }

        .dashboard-container {
            margin: 50px auto;
            text-align: center;
            width: 90%;
            max-width: 1200px;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        table th {
            background-color: #007BFF;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .form-container {
            margin-top: 20px;
        }

        .form-container input[type="number"] {
            width: 80px;
            padding: 5px;
            margin-right: 10px;
        }

        .form-container button {
            padding: 5px 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>My Fish Listings</h1>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Fish Type</th>
                        <th>Quantity (kg)</th>
                        <th>Price per kg (₹)</th>
                        <th>Listed On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['fish_type']); ?></td>
                            <td><?php echo $row['quantity']; ?></td>
                            <td><?php echo $row['price_per_kg']; ?></td>
                            <td><?php echo $row['created_at']; ?></td>
                            <td>
                                <!-- Update Listing -->
                                <form action="" method="POST" class="form-container">
                                    <input type="hidden" name="listing_id" value="<?php echo $row['id']; ?>">
                                    <input type="number" name="quantity" value="<?php echo $row['quantity']; ?>" min="1" required>
                                    <input type="number" name="price" value="<?php echo $row['price_per_kg']; ?>" step="0.01" required>
                                    <button type="submit" name="update_listing">Update</button>
                                </form>
                                <!-- Delete Listing -->
                                <form action="delete_fish_listing.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="listing_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this listing?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have not listed any fish for sale yet.</p>
        <?php endif; ?>

        <!-- Back to Dashboard -->
        <p><a href="fisherman_dashboard.php">Back to Dashboard</a></p>
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