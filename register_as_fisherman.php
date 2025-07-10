<?php
// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Database connection (replace with your actual database connection details)
$conn = new mysqli('localhost', 'root', '', 'fishing_port'); // Update database name if needed

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CAPTCHA validation
    if (!isset($_POST['captcha']) || !isset($_SESSION['captcha_code']) || strtolower($_POST['captcha']) !== strtolower($_SESSION['captcha_code'])) {
        echo '<script>alert("Incorrect CAPTCHA. Please try again."); window.history.back();</script>';
        exit();
    }
    unset($_SESSION['captcha_code']); // Prevent reuse

    // Retrieve form data
    $name = $_POST['name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone']; // New phone number field
    $id_proof = $_FILES['id_proof'];

    // Validate and process the data
    if ($id_proof['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        // Ensure the uploads directory exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $id_proof_path = $upload_dir . basename($id_proof['name']);
        if (move_uploaded_file($id_proof['tmp_name'], $id_proof_path)) {
            // Save the request to the database
            $user_id = $_SESSION['user_id'];
            $stmt = $conn->prepare("INSERT INTO fisherman_requests (user_id, name, age, gender, phone, id_proof, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->bind_param("isisss", $user_id, $name, $age, $gender, $phone, $id_proof_path);

            if ($stmt->execute()) {
                // Set session status to pending
                $_SESSION['fisherman_status'] = 'pending';

                // Redirect to customer dashboard with a pending status
                header("Location: customer_dashboard.php");
                exit();
            } else {
                echo "Failed to save your request. Please try again.";
            }
        } else {
            echo "Failed to upload ID proof. Please try again.";
        }
    } else {
        echo "Failed to upload ID proof. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register as Fisherman - DigiPortXpress</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: linear-gradient(135deg, #e3f2fd, #ffffff);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            background: #fff;
            padding: 32px 40px;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.13);
            text-align: center;
            max-width: 420px;
            width: 100%;
        }
        h2, h1 {
            color: #007BFF;
            margin-bottom: 22px;
            font-weight: 600;
        }
        label {
            display: block;
            text-align: left;
            margin-bottom: 7px;
            font-weight: 500;
            color: #333;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="tel"],
        input[type="number"],
        input[type="file"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
            background: #f8fafd;
            transition: border 0.2s;
        }
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="tel"]:focus,
        input[type="number"]:focus,
        select:focus {
            border: 1.5px solid #007BFF;
            outline: none;
            background: #fff;
        }
        button[type="submit"] {
            background: #007BFF;
            color: #fff;
            border: none;
            padding: 12px 0;
            border-radius: 5px;
            font-size: 1.1em;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 8px;
        }
        button[type="submit"]:hover {
            background: #0056b3;
        }
        .captcha-box {
            margin-bottom: 18px;
        }
        .captcha-box img {
            vertical-align: middle;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .captcha-box button {
            margin-left: 8px;
            padding: 4px 10px;
            font-size: 0.9em;
            background: #f1f1f1;
            color: #007BFF;
            border: 1px solid #007BFF;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }
        .captcha-box button:hover {
            background: #007BFF;
            color: #fff;
        }
        .back-link, .back-button a {
            margin-top: 20px;
            display: block;
            color: #007BFF;
            text-decoration: none;
        }
        .back-link:hover, .back-button a:hover {
            text-decoration: underline;
        }
        .error {
            color: #d32f2f;
            margin-bottom: 10px;
        }
        .success {
            color: #388e3c;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Register as Fisherman</h1>
        <form action="register_as_fisherman.php" method="POST" enctype="multipart/form-data">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="age">Age:</label>
            <input type="number" id="age" name="age" required>
            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>
            <label for="phone">Phone Number:</label>
            <input type="tel" id="phone" name="phone" pattern="[0-9]{10}" required>
            <label for="id_proof">ID Proof:</label>
            <input type="file" id="id_proof" name="id_proof" accept="image/*" required>
            <div class="captcha-box">
                <label for="captcha">Enter the code shown:</label><br>
                <img src="captcha.php" id="captcha-img" alt="CAPTCHA">
                <button type="button" onclick="document.getElementById('captcha-img').src='captcha.php?'+Math.random();">Refresh</button>
                <input type="text" id="captcha" name="captcha" placeholder="Enter CAPTCHA" required style="margin-top:10px; width:100%; padding:8px;">
            </div>
            <button type="submit">Submit</button>
        </form>
        <div class="back-button">
            <a href="customer_dashboard.php">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>