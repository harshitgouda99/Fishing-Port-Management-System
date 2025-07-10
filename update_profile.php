<?php
// Start session
session_start();

// Check if the user is logged in and is a customer
if (!isset($_SESSION['user_id'])) {
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

// Fetch current user data
$user_id = $_SESSION['user_id'];
$sql = "SELECT full_name, email FROM users WHERE id = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
} else {
    die("User not found.");
}

// Update profile if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    // Update query
    $update_sql = "UPDATE users SET full_name = '$full_name', email = '$email'";
    if ($password) {
        $update_sql .= ", password = '$password'";
    }
    $update_sql .= " WHERE id = '$user_id'";

    if ($conn->query($update_sql) === TRUE) {
        echo "<script>alert('Profile updated successfully!');</script>";
        // Update session variables
        $_SESSION['full_name'] = $full_name;
    } else {
        echo "<script>alert('Error updating profile: " . $conn->error . "');</script>";
    }
}

// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile - Fishing Port Management System</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            width: 100%;
            background: url('assets/images/background_image5.jpg') no-repeat center center fixed;
            background-size: cover;
            position: relative;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        /* Add a semi-transparent overlay */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6); /* Darker overlay for better contrast */
            z-index: -1; /* Place it behind the content */
        }

        /* Navigation Bar */
        .navbar {
            width: 100%;
            background: linear-gradient(90deg, #0fb7ff, #007BFF); /* Gradient background */
            padding: 20px 30px; /* Increased padding for better height */
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2); /* Add shadow */
        }

        .navbar .back-button a {
            text-decoration: none;
            color: white;
            background-color: #28a745; /* Green button */
            padding: 10px 20px; /* Increased padding for consistency */
            border-radius: 5px;
            font-size: 1em;
            font-weight: bold;
            display: flex;
            align-items: center;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .navbar .back-button a:hover {
            background-color: #218838; /* Darker green on hover */
            transform: scale(1.05); /* Slight zoom effect */
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
            padding: 10px 20px; /* Increased padding for consistency */
            border-radius: 5px;
        }

        .navbar .nav-links a:hover {
            background-color: rgba(255, 255, 255, 0.2); /* Add hover effect */
            color: #ffeb3b; /* Yellow color on hover */
            transform: scale(1.1); /* Slight zoom effect */
        }

        /* Form Container */
        .dashboard-container {
            background: linear-gradient(135deg, #ffffff, #e3f2fd); /* Light gradient background */
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 500px;
            text-align: center;
            margin-top: 120px; /* Adjust for navbar height */
        }

        .dashboard-container h1 {
            color: #007BFF; /* Bright blue for the heading */
            margin-bottom: 20px;
        }

        .dashboard-container label {
            display: block;
            margin: 10px 0 5px;
            color: #333; /* Dark text for labels */
            font-weight: bold;
        }

        .dashboard-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }

        .dashboard-container button {
            background-color: #007BFF; /* Bright blue button */
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
            background-color: #0056b3; /* Darker blue on hover */
            transform: scale(1.05); /* Slight zoom effect */
        }

        .dashboard-container .back-link {
            margin-top: 20px; /* Added spacing between buttons */
            display: inline-block;
            text-decoration: none;
            color: #007BFF; /* Bright blue text */
            background-color: transparent; /* Transparent background */
            border: 2px solid #007BFF; /* Blue border */
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1.2em;
            font-weight: bold;
            transition: background-color 0.3s ease, color 0.3s ease, transform 0.2s ease;
        }

        .dashboard-container .back-link:hover {
            background-color:rgb(255, 0, 0); /* Bright blue background on hover */
            color: white; /* White text on hover */
            transform: scale(1.05); /* Slight zoom effect */
        }

        input, textarea {
            color: #000; /* Black text color */
            background-color: #fff; /* White background for contrast */
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="back-button">
            <a href="customer_dashboard.php">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
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
        <h1>Update Profile</h1>
        <form action="update_profile.php" method="POST">
            <!-- Full Name -->
            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>

            <!-- Email -->
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <!-- Password -->
            <label for="password">New Password (leave blank to keep current password):</label>
            <input type="password" id="password" name="password">

            <!-- Submit Button -->
            <button type="submit">Update Profile</button>
        </form>

        <!-- Back to Dashboard -->
        <a href="customer_dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>