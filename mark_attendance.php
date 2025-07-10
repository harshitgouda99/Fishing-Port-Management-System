<?php
// filepath: c:\wamp64\www\basicstructure\mark_attendance.php

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

// Fetch the fisherman's boats that do not have pending arrival details
$fisherman_id = $_SESSION['user_id'];
$boats_sql = "
    SELECT br.id, br.boat_name 
    FROM boat_registrations br
    LEFT JOIN attendance a ON br.id = a.boat_id AND a.arrival_time IS NULL
    WHERE br.fisherman_id = '$fisherman_id' AND br.status = 'approved' AND a.id IS NULL
";
$boats_result = $conn->query($boats_sql);

// Fetch approved drivers and workers for this fisherman
$drivers_sql = "SELECT id, name FROM workers WHERE fisherman_id = '$fisherman_id' AND role = 'Driver' AND status = 'approved'";
$drivers_result = $conn->query($drivers_sql);

$workers_sql = "SELECT id, name FROM workers WHERE fisherman_id = '$fisherman_id' AND role = 'Worker' AND status = 'approved'";
$workers_result = $conn->query($workers_sql);

// Handle form submission for marking attendance
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mark_attendance'])) {
    $boat_id = (int)$_POST['boat_id'];
    $driver_ids = isset($_POST['driver_ids']) ? implode(',', array_map('intval', $_POST['driver_ids'])) : '';
    $worker_ids = isset($_POST['worker_ids']) ? implode(',', array_map('intval', $_POST['worker_ids'])) : '';
    $departure_time = $conn->real_escape_string($_POST['departure_time']);
    $departed_direction = $conn->real_escape_string($_POST['departed_direction']);
    $workers_count = (int)$_POST['workers_count'];
    $drivers_count = count(explode(',', $driver_ids));
    $current_date = date("Y-m-d"); // Get the current date in "YYYY-MM-DD" format

    // Backend validation for at least one driver and one worker
    if (empty($driver_ids)) {
        echo "<script>alert('Please select at least one driver.');</script>";
    } elseif (empty($worker_ids)) {
        echo "<script>alert('Please select at least one worker.');</script>";
    } else {
        // Insert attendance record with the current date
        $sql = "INSERT INTO attendance (fisherman_id, boat_id, driver_ids, worker_ids, departure_time, departed_direction, workers_count, drivers_count, date) 
                VALUES ('$fisherman_id', '$boat_id', '$driver_ids', '$worker_ids', '$departure_time', '$departed_direction', '$workers_count', '$drivers_count', '$current_date')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Attendance marked successfully!');</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    }
}

// Handle form submission for updating arrival details
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_arrival'])) {
    $attendance_id = (int)$_POST['attendance_id'];
    $arrival_time = $conn->real_escape_string($_POST['arrival_time']);
    $arrival_direction = $conn->real_escape_string($_POST['arrival_direction']);

    // Update arrival details
    $sql = "UPDATE attendance SET arrival_time = '$arrival_time', arrival_direction = '$arrival_direction' 
            WHERE id = '$attendance_id' AND fisherman_id = '$fisherman_id'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Arrival details updated successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

// Fetch pending arrival details
$pending_sql = "SELECT a.id, a.departure_time, a.departed_direction, b.boat_name 
                FROM attendance a
                JOIN boat_registrations b ON a.boat_id = b.id
                WHERE a.fisherman_id = '$fisherman_id' AND a.arrival_time IS NULL";
$pending_result = $conn->query($pending_sql);

// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Attendance - Fishing Port Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #e3f2fd, #ffffff); /* Light gradient background */
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        /* Navigation Bar */
        .navbar {
            width: 100%;
            background: #007BFF; /* Solid blue background */
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

        /* Dashboard Container */
        .dashboard-container {
            margin-top: 100px; /* Adjust for navbar height */
            text-align: center;
            width: 90%;
            max-width: 800px;
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

        .dashboard-container label {
            display: block;
            margin: 10px 0 5px;
            color: #333;
            font-weight: bold;
        }

        .dashboard-container input,
        .dashboard-container select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }

        .dashboard-container button {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1.2em;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .dashboard-container button:hover {
            background-color: #0056b3;
            transform: scale(1.05);
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .dashboard-container h1 {
                font-size: 2em;
            }

            .dashboard-container label {
                font-size: 1em;
            }

            .dashboard-container input,
            .dashboard-container select {
                font-size: 0.9em;
            }

            .dashboard-container button {
                font-size: 1em;
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
        <h1>Mark Attendance</h1>
        <form action="mark_attendance.php" method="POST" onsubmit="return validateAttendanceForm();">
            <!-- Boat Selection -->
            <label for="boat_id">Select Boat:</label>
            <select id="boat_id" name="boat_id" required>
                <option value="">-- Select Your Boat --</option>
                <?php while ($boat = $boats_result->fetch_assoc()): ?>
                    <option value="<?php echo $boat['id']; ?>"><?php echo htmlspecialchars($boat['boat_name']); ?></option>
                <?php endwhile; ?>
            </select>

            <!-- Driver Selection (checkboxes) -->
            <label for="driver_ids">Select Drivers:</label>
            <div id="driver_ids">
                <?php while ($driver = $drivers_result->fetch_assoc()): ?>
                    <label>
                        <input type="checkbox" name="driver_ids[]" value="<?php echo $driver['id']; ?>">
                        <?php echo htmlspecialchars($driver['name']); ?>
                    </label><br>
                <?php endwhile; ?>
            </div>
            <small style="display:block;margin-bottom:10px;color:#888;">Select one or more drivers.</small>

            <!-- Worker Selection (checkboxes) -->
            <label for="worker_ids">Select Workers:</label>
            <div id="worker_ids">
                <?php while ($worker = $workers_result->fetch_assoc()): ?>
                    <label>
                        <input type="checkbox" name="worker_ids[]" value="<?php echo $worker['id']; ?>">
                        <?php echo htmlspecialchars($worker['name']); ?>
                    </label><br>
                <?php endwhile; ?>
            </div>
            <small style="display:block;margin-bottom:10px;color:#888;">Select one or more workers.</small>

            <!-- Departure Time -->
            <label for="departure_time">Departure Time:</label>
            <input type="time" id="departure_time" name="departure_time" required>

            <!-- Departed Direction -->
            <label for="departed_direction">Departed Direction:</label>
            <input type="text" id="departed_direction" name="departed_direction" required>

            <!-- Drivers Count -->
            <label for="drivers_count">Drivers Count:</label>
            <input type="number" id="drivers_count" name="drivers_count" min="1" required readonly>

            <!-- Workers Count -->
            <label for="workers_count">Workers Count:</label>
            <input type="number" id="workers_count" name="workers_count" min="1" required readonly>

            <!-- Submit Button -->
            <button type="submit" name="mark_attendance">Mark Attendance</button>
        </form>
        <script>
        // Auto-update workers_count based on selected workers
        document.getElementById('worker_ids').addEventListener('change', function() {
            document.getElementById('workers_count').value = this.selectedOptions.length;
        });
        </script>
        <script>
    function validateAttendanceForm() {
        const driverSelect = document.getElementById('driver_ids');
        const workerSelect = document.getElementById('worker_ids');

        if (driverSelect.selectedOptions.length === 0) {
            alert("Please select at least one driver.");
            return false;
        }

        if (workerSelect.selectedOptions.length === 0) {
            alert("Please select at least one worker.");
            return false;
        }

        return true;
    }
</script>
<script>
    // Auto-update workers_count and drivers_count based on selected options
    document.getElementById('worker_ids').addEventListener('change', function() {
        document.getElementById('workers_count').value = this.selectedOptions.length;
    });

    document.getElementById('driver_ids').addEventListener('change', function() {
        document.getElementById('drivers_count').value = this.selectedOptions.length;
    });
</script>
<script>
    // Auto-update workers_count and drivers_count based on selected checkboxes
    document.querySelectorAll('input[name="worker_ids[]"]').forEach(function(workerCheckbox) {
        workerCheckbox.addEventListener('change', function() {
            const selectedWorkers = document.querySelectorAll('input[name="worker_ids[]"]:checked').length;
            document.getElementById('workers_count').value = selectedWorkers;
        });
    });

    document.querySelectorAll('input[name="driver_ids[]"]').forEach(function(driverCheckbox) {
        driverCheckbox.addEventListener('change', function() {
            const selectedDrivers = document.querySelectorAll('input[name="driver_ids[]"]:checked').length;
            document.getElementById('drivers_count').value = selectedDrivers;
        });
    });
</script>

        <h2>Update Arrival Details</h2>
        <?php if ($pending_result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Boat Name</th>
                        <th>Departure Time</th>
                        <th>Departed Direction</th>
                        <th>Update Arrival</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $pending_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['boat_name']); ?></td>
                            <td><?php echo $row['departure_time']; ?></td>
                            <td><?php echo htmlspecialchars($row['departed_direction']); ?></td>
                            <td>
                                <form action="mark_attendance.php" method="POST">
                                    <input type="hidden" name="attendance_id" value="<?php echo $row['id']; ?>">
                                    <label for="arrival_time">Arrival Time:</label>
                                    <input type="time" id="arrival_time" name="arrival_time" required>
                                    <label for="arrival_direction">Arrival Direction:</label>
                                    <input type="text" id="arrival_direction" name="arrival_direction" required>
                                    <button type="submit" name="update_arrival">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No pending arrival details to update.</p>
        <?php endif; ?>

        <p><a href="fisherman_dashboard.php">Back to Dashboard</a></p>
    </div>
</body>
</html>