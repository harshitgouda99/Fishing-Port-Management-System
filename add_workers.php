<?php
session_start();

// Check if fisherman is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'fisherman') {
    header("Location: login.html");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fishing_port";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle worker registration
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CAPTCHA validation
    if (
        !isset($_POST['captcha']) ||
        !isset($_SESSION['captcha_code']) ||
        strtolower($_POST['captcha']) !== strtolower($_SESSION['captcha_code'])
    ) {
        $message = "<span style='color:red;'>Incorrect CAPTCHA. Please try again.</span>";
    } else {
        unset($_SESSION['captcha_code']); // Prevent reuse

        $fisherman_id = $_SESSION['user_id'];
        $name = $conn->real_escape_string($_POST['name']);
        $email = $conn->real_escape_string($_POST['email']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $role = $conn->real_escape_string($_POST['role']);

        // Handle ID proof upload
        $idproof_path = "";
        if (isset($_FILES['idproof']) && $_FILES['idproof']['error'] == UPLOAD_ERR_OK) {
            $target_dir = "uploads/idproofs/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $file_ext = strtolower(pathinfo($_FILES['idproof']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'pdf', 'webp'];
            if (in_array($file_ext, $allowed)) {
                $idproof_path = $target_dir . uniqid("idproof_") . "." . $file_ext;
                move_uploaded_file($_FILES['idproof']['tmp_name'], $idproof_path);
            } else {
                $message = "<span style='color:red;'>Invalid ID proof file type.</span>";
            }
        }

        if (empty($message)) {
            // Check if worker already exists for this fisherman
            $check = $conn->query("SELECT id FROM workers WHERE email='$email' AND fisherman_id='$fisherman_id'");
            if ($check && $check->num_rows > 0) {
                $message = "<span style='color:red;'>Worker with this email already exists under your account.</span>";
            } else {
                // Insert with status 'pending' for admin approval
                $sql = "INSERT INTO workers (fisherman_id, name, email, phone, role, idproof, status) VALUES ('$fisherman_id', '$name', '$email', '$phone', '$role', '$idproof_path', 'pending')";
                if ($conn->query($sql) === TRUE) {
                    $message = "<span style='color:green;'>Worker added successfully! Awaiting admin approval.</span>";
                } else {
                    $message = "<span style='color:red;'>Error: " . $conn->error . "</span>";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Your Workers</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: linear-gradient(135deg, #e3f2fd, #ffffff);
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: #fff;
            padding: 32px 40px;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.13);
            max-width: 400px;
            width: 100%;
            margin-top: 40px;
        }
        h2 {
            color: #007BFF;
            margin-bottom: 22px;
            font-weight: 600;
            text-align: center;
        }
        label {
            display: block;
            margin-bottom: 7px;
            font-weight: 500;
            color: #333;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
            background: #f8fafd;
        }
        .captcha-box {
            margin-bottom: 18px;
            text-align: left;
        }
        .captcha-box img {
            display: inline-block;
            vertical-align: middle;
            border-radius: 5px;
            height: 38px;
        }
        .refresh-btn {
            display: inline-block;
            vertical-align: middle;
            background: none;
            border: none;
            color: #007BFF;
            font-size: 1.2em;
            cursor: pointer;
            margin-left: 5px;
        }
        .refresh-btn:hover {
            color: #0056b3;
        }
        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        button[type="submit"]:hover {
            background: #0056b3;
        }
        .message {
            text-align: center;
            margin-bottom: 15px;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 18px;
            color: #007BFF;
            text-decoration: none;
            font-weight: 500;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add Worker</h2>
        <div class="message"><?php echo $message; ?></div>
        <form method="POST" action="" enctype="multipart/form-data">
            <label for="name">Full Name</label>
            <input type="text" name="name" id="name" required>

            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>

            <label for="phone">Phone Number</label>
            <input type="tel" name="phone" id="phone" required pattern="[0-9]{10,15}">

            <label for="role">Role</label>
            <select name="role" id="role" required>
                <option value="">Select Role</option>
                <option value="Driver">Driver</option>
                <option value="Worker">Other Worker</option>
            </select>

            <label for="idproof">ID Proof (jpg, jpeg, png, pdf, webp)</label>
            <input type="file" name="idproof" id="idproof" accept=".jpg,.jpeg,.png,.pdf,.webp" required>

            <div class="captcha-box">
                <label for="captcha">Enter the code shown:</label>
                <img src="captcha.php" id="captcha-img" alt="CAPTCHA">
                <button class="refresh-btn" type="button" title="Refresh CAPTCHA" onclick="document.getElementById('captcha-img').src='captcha.php?'+Math.random();return false;">
                    &#x21bb;
                </button>
                <input type="text" id="captcha" name="captcha" placeholder="Enter CAPTCHA" required style="margin-top:8px;">
            </div>

            <button type="submit">Add Worker</button>
        </form>
        <a href="fisherman_dashboard.php" class="back-link">&#8592; Back to Dashboard</a>
    </div>
</body>
</html>