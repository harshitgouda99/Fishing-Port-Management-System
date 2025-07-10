<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli('localhost', 'root', '', 'fishing_port'); // Update database name if needed

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the token is provided
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verify the token
    $stmt = $conn->prepare("SELECT id FROM users WHERE token = ? AND status = 'pending'");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Update the user's status to "active"
        $stmt = $conn->prepare("UPDATE users SET status = 'active', token = NULL WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();

        echo "Email verified successfully! You can now log in.";
    } else {
        echo "Invalid or expired token.";
    }
} else {
    echo "No token provided.";
}

$verification_link = "http://yourdomain.com/verify_email.php?token=$token";
echo "Verification link: $verification_link";

$conn->close();
echo "Connection closed.";
?>