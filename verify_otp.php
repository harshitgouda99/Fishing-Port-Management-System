<?php
// verify_otp.php: Form for OTP and new password
session_start();
$email = isset($_SESSION['reset_email']) ? $_SESSION['reset_email'] : '';
if (!$email) {
    header('Location: forget_password.html');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - Reset Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', Arial, sans-serif; background: #e3f2fd; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .container { background: #fff; padding: 30px 40px; border-radius: 10px; box-shadow: 0 4px 16px rgba(0,0,0,0.15); max-width: 400px; width: 100%; }
        h2 { color: #007BFF; margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 500; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; margin-bottom: 18px; border: 1px solid #ccc; border-radius: 5px; }
        button { width: 100%; background: #007BFF; color: #fff; border: none; padding: 12px; border-radius: 5px; font-size: 1.1em; font-weight: 600; cursor: pointer; }
        button:hover { background: #0056b3; }
        .error { color: #d32f2f; margin-bottom: 10px; }
        .success { color: #388e3c; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <?php if (isset($_SESSION['otp_error'])): ?>
            <div class="error"><?php echo $_SESSION['otp_error']; unset($_SESSION['otp_error']); ?></div>
        <?php endif; ?>
        <form action="reset_password.php" method="POST">
            <label for="otp">Enter OTP (sent to <?php echo htmlspecialchars($email); ?>):</label>
            <input type="text" id="otp" name="otp" maxlength="6" required>
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required>
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>
