<?php
// filepath: c:\wamp64\www\New folder\list_fish.php

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

// Handle form submission for listing fish
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fisherman_id = $_SESSION['user_id'];
    $fish_type = $conn->real_escape_string($_POST['fish_type']);
    $quantity = (int)$_POST['quantity'];
    $price_per_kg = (float)$_POST['price_per_kg'];

    // Insert fish listing into the database
    $sql = "INSERT INTO fish_listings (fisherman_id, fish_type, quantity, price_per_kg) 
            VALUES ('$fisherman_id', '$fish_type', '$quantity', '$price_per_kg')";

    if ($conn->query($sql) === TRUE) {
        // Redirect to the success page
        header("Location: fish_listed_success.php");
        exit();
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
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
    <title>List Fish for Sale - Fishing Port Management System</title>
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

        .navbar .logo a img {
            height: 40px;
            margin-right: 10px;
        }

        .navbar .website-name-container {
            flex-grow: 1;
            text-align: center;
        }

        .navbar .website-name {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.2);
            color: #ffeb3b;
            transform: scale(1.1);
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
            <a href="index.html">DIGIPORTXPRESS</a>
        </div>
        <div class="nav-links">
            <a href="index.html">Home</a>
            <a href="about.html">About</a>
            <a href="contact.html">Contact</a>
        </div>
    </div>
    <div class="dashboard-container">
        <h1>List Fish for Sale</h1>
        <form action="list_fish.php" method="POST">
            <!-- Fish Type -->
            <label for="fish_type">Fish Type:</label>
            <input type="text" id="fish_type" name="fish_type" required>

            <!-- Quantity -->
            <label for="quantity">Quantity (in kg):</label>
            <input type="number" id="quantity" name="quantity" min="1" required>

            <!-- Price per kg -->
            <label for="price_per_kg">Price per kg (in ₹):</label>
            <input type="number" id="price_per_kg" name="price_per_kg" step="0.01" min="0" required>

            <!-- Submit Button -->
            <button type="submit">List Fish</button>
        </form>

        <!-- Back to Dashboard -->
        <p><a href="fisherman_dashboard.php">Back to Dashboard</a></p>
    </div>
</body>
</html>