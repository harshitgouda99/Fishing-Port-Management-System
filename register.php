<?php
// filepath: c:\wamp64\www\basic structure\register.php

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

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    session_start();
    // CAPTCHA validation
    if (!isset($_POST['captcha']) || !isset($_SESSION['captcha_code']) || strtolower($_POST['captcha']) !== strtolower($_SESSION['captcha_code'])) {
        echo '<script>alert("Incorrect CAPTCHA. Please try again."); window.history.back();</script>';
        exit();
    }
    unset($_SESSION['captcha_code']); // Prevent reuse

    // Retrieve form data
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);
    $confirm_password = $conn->real_escape_string($_POST['confirm_password']);
    $phone = $conn->real_escape_string($_POST['phone']); // Retrieve phone number

    // Validate passwords
    if ($password !== $confirm_password) {
        die("Passwords do not match.");
    }

    // Check if the email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        // Redirect to an error page if the email already exists
        header("Location: email_exists.html");
        exit();
    }
    $stmt->close();

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Set default user role as 'customer'
    $user_role = 'customer';

    // Insert user into the database
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, phone, user_role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $full_name, $email, $hashed_password, $phone, $user_role);

    if ($stmt->execute()) {
        // Redirect to the login page after successful registration
        header("Location: login.html");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Close the connection
$conn->close();
?>