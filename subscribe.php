<?php
// Start session
session_start();

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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email address!'); window.location.href='index.html';</script>";
        exit();
    }

    // Insert email into the database
    $sql = "INSERT INTO newsletter_subscribers (email) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);

    if ($stmt->execute()) {
        echo "<script>alert('Thank you for subscribing to our newsletter!'); window.location.href='index.html';</script>";
    } else {
        echo "<script>alert('Error: Unable to subscribe. Please try again later.'); window.location.href='index.html';</script>";
    }

    $stmt->close();
}

$conn->close();
?>