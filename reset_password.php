<?php
// reset_password.php: Handles OTP verification and password reset
session_start();
if (!isset($_SESSION['reset_email'])) {
    header('Location: forget_password.html');
    exit();
}
$email = $_SESSION['reset_email'];
$otp = isset($_POST['otp']) ? trim($_POST['otp']) : '';
$new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
$confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

if (!$otp || !$new_password || !$confirm_password) {
    $_SESSION['otp_error'] = 'All fields are required.';
    header('Location: verify_otp.php');
    exit();
}
if ($new_password !== $confirm_password) {
    $_SESSION['otp_error'] = 'Passwords do not match.';
    header('Location: verify_otp.php');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'fishing_port');
if ($conn->connect_error) {
    $_SESSION['otp_error'] = 'Database connection failed.';
    header('Location: verify_otp.php');
    exit();
}
// Check OTP validity (valid for 10 minutes)
$stmt = $conn->prepare("SELECT otp, created_at FROM password_resets WHERE email = ? ORDER BY created_at DESC LIMIT 1");
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->bind_result($db_otp, $created_at);
if ($stmt->fetch()) {
    $stmt->close();
    $created_time = strtotime($created_at);
    if ($db_otp !== $otp) {
        $_SESSION['otp_error'] = 'Invalid OTP.';
        header('Location: verify_otp.php');
        exit();
    }
    if (time() - $created_time > 600) { // 10 minutes
        $_SESSION['otp_error'] = 'OTP expired. Please request a new one.';
        header('Location: forget_password.html');
        exit();
    }
    // Update password
    $hashed = password_hash($new_password, PASSWORD_DEFAULT);
    $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $update->bind_param('ss', $hashed, $email);
    $update->execute();
    // Optionally, delete/reset OTP
    $conn->query("DELETE FROM password_resets WHERE email = '" . $conn->real_escape_string($email) . "'");
    unset($_SESSION['reset_email']);
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Password Reset Successful</title><style>body{font-family:Poppins,Arial,sans-serif;background:#e3f2fd;display:flex;align-items:center;justify-content:center;min-height:100vh;} .container{background:#fff;padding:30px 40px;border-radius:10px;box-shadow:0 4px 16px rgba(0,0,0,0.15);max-width:400px;width:100%;text-align:center;} h2{color:#388e3c;} a{color:#007BFF;text-decoration:none;}</style></head><body><div class="container"><h2>Password Reset Successful!</h2><p>Your password has been updated. <a href="login.html">Login</a></p></div></body></html>';
    exit();
} else {
    $_SESSION['otp_error'] = 'No OTP found for this email.';
    header('Location: forget_password.html');
    exit();
}
?>
