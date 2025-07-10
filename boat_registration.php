<?php
// filepath: c:\wamp64\www\New folder\boat_registration.php

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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fisherman_id = $_SESSION['user_id'];
    $boat_name = $conn->real_escape_string($_POST['boat_name']);
    $registration_number = $conn->real_escape_string($_POST['registration_number']);
    $capacity = (int)$_POST['capacity'];

    // Handle file upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["document"]["name"]);
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validate file type
    if ($file_type != "pdf" && $file_type != "doc" && $file_type != "docx") {
        echo "<script>alert('Only PDF, DOC, and DOCX files are allowed.');</script>";
    } elseif (move_uploaded_file($_FILES["document"]["tmp_name"], $target_file)) {
        // Insert boat registration into the database with "pending" status
        $sql = "INSERT INTO boat_registrations (fisherman_id, boat_name, registration_number, capacity, status, document_path) 
                VALUES ('$fisherman_id', '$boat_name', '$registration_number', '$capacity', 'pending', '$target_file')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>
                    alert('Boat registration submitted successfully! Waiting for admin approval.');
                    window.location.href = 'fisherman_dashboard.php';
                  </script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Error uploading the document.');</script>";
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
    <title>Boat Registration - Fishing Port Management System</title>
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

        .logo-image {
            width: 50px;
            height: auto;
            margin-right: 10px;
        }

        .website-name-container {
            display: flex;
            align-items: center;
        }

        .website-name {
            color: #fff;
            font-size: 1.5em;
            margin-left: 10px;
        }

        /* Dashboard Container */
        .dashboard-container {
            margin-top: 100px; /* Adjust for navbar height */
            text-align: center;
            width: 90%;
            max-width: 600px;
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

        .dashboard-container label {
            display: block;
            margin: 10px 0 5px;
            color: #333;
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

            .dashboard-container input {
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
            <a href="registration.html">Register</a>
            <a href="login.html">Login</a>
            <a href="contact.html">Contact</a>
        </div>
    </div>
    <div class="dashboard-container">
        <h1>Register Your Boat</h1>
        <form action="boat_registration.php" method="POST" enctype="multipart/form-data">
            <!-- Boat Name -->
            <label for="boat_name">Boat Name:</label>
            <input type="text" id="boat_name" name="boat_name" required>

            <!-- Registration Number -->
            <label for="registration_number">Registration Number:</label>
            <input type="text" id="registration_number" name="registration_number" required>

            <!-- Capacity -->
            <label for="capacity">Capacity (in tons):</label>
            <input type="number" id="capacity" name="capacity" min="1" required>

            <!-- Document Upload -->
            <label for="document">Upload Document (PDF, DOC, DOCX):</label>
            <input type="file" id="document" name="document" accept=".pdf,.doc,.docx" required>

            <!-- Submit Button -->
            <button type="submit">Submit Registration</button>
        </form>

        <!-- Back to Dashboard -->
        <p><a href="fisherman_dashboard.php">Back to Dashboard</a></p>
    </div>
</body>
</html>