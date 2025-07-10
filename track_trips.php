<?php
session_start();

// Check if the user is logged in and is a fisherman
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'fisherman') {
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

// Fetch trips for the logged-in fisherman
$fisherman_id = $_SESSION['user_id'];
$sql = "SELECT t.id, t.boat_id, br.boat_name, t.departure_time, t.departed_direction, t.arrival_time, t.arrival_direction, t.driver_ids, t.worker_ids 
        FROM attendance t
        JOIN boat_registrations br ON t.boat_id = br.id
        WHERE t.fisherman_id = '$fisherman_id'
        ORDER BY t.departure_time DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Trips - Fishing Port Management System</title>
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

        .container {
            max-width: 1100px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.13);
            padding: 32px;
        }

        h1 {
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

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            text-decoration: none;
            color: #007BFF;
            font-size: 1.2em;
            font-weight: bold;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Track Your Trips</h1>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Boat Name</th>
                        <th>Trip Date</th>
                        <th>Departure Time</th>
                        <th>Departed Direction</th>
                        <th>Arrival Time</th>
                        <th>Arrival Direction</th>
                        <th>Drivers</th>
                        <th>Workers</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                        // Extract trip date from departure_time
                        $trip_date = date("F j, Y", strtotime($row['departure_time'])); // Format: "May 11, 2025"

                        // Fetch driver names
                        $driver_names = [];
                        if (!empty($row['driver_ids'])) {
                            $driver_ids = implode(',', array_map('intval', explode(',', $row['driver_ids'])));
                            $drivers_sql = "SELECT name FROM workers WHERE id IN ($driver_ids)";
                            $drivers_result = $conn->query($drivers_sql);
                            while ($driver = $drivers_result->fetch_assoc()) {
                                $driver_names[] = htmlspecialchars($driver['name']);
                            }
                        }

                        // Fetch worker names
                        $worker_names = [];
                        if (!empty($row['worker_ids'])) {
                            $worker_ids = implode(',', array_map('intval', explode(',', $row['worker_ids'])));
                            $workers_sql = "SELECT name FROM workers WHERE id IN ($worker_ids)";
                            $workers_result = $conn->query($workers_sql);
                            while ($worker = $workers_result->fetch_assoc()) {
                                $worker_names[] = htmlspecialchars($worker['name']);
                            }
                        }
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['boat_name']); ?></td>
                            <td><?php echo htmlspecialchars($trip_date); ?></td> <!-- Trip Date -->
                            <td><?php echo htmlspecialchars(date("g:i A", strtotime($row['departure_time']))); ?></td> <!-- Departure Time -->
                            <td><?php echo htmlspecialchars($row['departed_direction']); ?></td>
                            <td><?php echo htmlspecialchars($row['arrival_time'] ? date("g:i A", strtotime($row['arrival_time'])) : 'Pending'); ?></td>
                            <td><?php echo htmlspecialchars($row['arrival_direction'] ?: 'Pending'); ?></td>
                            <td><?php echo !empty($driver_names) ? implode(', ', $driver_names) : 'No Drivers'; ?></td>
                            <td><?php echo !empty($worker_names) ? implode(', ', $worker_names) : 'No Workers'; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align:center;">No trips found.</p>
        <?php endif; ?>
        <div class="back-link">
            <a href="fisherman_dashboard.php">&#8592; Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?>