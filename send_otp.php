<?php
// Start session
session_start();

// Include PHPMailer
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database connection
$conn = new mysqli('localhost', 'root', '', 'fishing_port');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $result = $conn->query("SELECT id FROM users WHERE email = '$email'");
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $user_id = $user['id'];
        $otp = rand(100000, 999999);
        $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        // Create password_resets table if not exists
        $conn->query("CREATE TABLE IF NOT EXISTS password_resets (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, email VARCHAR(255), otp VARCHAR(10), expires_at DATETIME, used TINYINT DEFAULT 0)");
        // Remove old OTPs for this user
        $conn->query("DELETE FROM password_resets WHERE user_id = $user_id");
        // Insert new OTP
        $conn->query("INSERT INTO password_resets (user_id, email, otp, expires_at) VALUES ($user_id, '$email', '$otp', '$expires')");
        // Send OTP via email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'digiportxpress@gmail.com';
            $mail->Password = 'xaty dolc nnwa qkmk';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->setFrom('digiportxpress@gmail.com', 'DigiPortXpress');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Your DigiPortXpress Password Reset OTP';
            $mail->Body = '<h3>Your OTP for password reset is: <b>' . $otp . '</b></h3><p>This OTP is valid for 10 minutes.</p>';
            $mail->send();
            $_SESSION['reset_email'] = $email;
            header('Location: verify_otp.php');
            exit();
        } catch (Exception $e) {
            echo '<p style="color:red;">Failed to send OTP. Please try again later.</p>';
        }
    } else {
        echo '<p style="color:red;">Email not found. <a href="forget_password.html">Try again</a></p>';
    }
} else {
    header('Location: forget_password.html');
    exit();
}
?>
