<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fish Listed Successfully</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #e3f2fd, #ffffff);
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #007BFF;
            padding: 10px 20px;
            width: 100%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .logo-image {
            height: 50px;
            width: auto;
        }

        .website-name-container {
            flex-grow: 1;
            text-align: center;
        }

        .website-name {
            color: white;
            font-size: 1.5em;
            margin: 0;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
            font-size: 1em;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #e3f2fd;
        }

        .container {
            text-align: center;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 600px;
        }

        .container h1 {
            color: #007BFF;
            font-size: 2.5em;
            margin-bottom: 20px;
        }

        .container p {
            font-size: 1.2em;
            margin-bottom: 20px;
        }

        .container a {
            text-decoration: none;
            color: white;
            background-color: #007BFF;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1.2em;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .container a:hover {
            background-color: #0056b3;
            transform: scale(1.05);
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
    <div class="container">
        <h1>Fish Listed Successfully!</h1>
        <p>Your fish has been listed successfully. What would you like to do next?</p>
        <a href="view_fish_listings.php">View Fish Listings</a>
        <a href="fisherman_dashboard.php" style="margin-left: 20px;">Back to Dashboard</a>
    </div>
</body>
</html>