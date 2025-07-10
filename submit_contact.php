<?php
// filepath: c:\wamp64\www\basic structure\submit_contact.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// Database connection
$conn = new mysqli('localhost', 'root', '', 'fishing_port'); // Replace with your database credentials

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    // Insert data into the database
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $message);

    if ($stmt->execute()) {
        // Send email notification to the admin using PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'digiportxpress@gmail.com';
            $mail->Password = 'xaty dolc nnwa qkmk'; // App password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('digiportxpress@gmail.com', 'DigiPortXpress Contact');
            $mail->addAddress('digiportxpress@gmail.com');
            $mail->addReplyTo($email, $name);
            $mail->Subject = 'New Contact Message from ' . $name;
            $mail->Body = "You have received a new message:\n\nName: $name\nEmail: $email\nMessage:\n$message";
            $mail->send();
            echo '<div style="background:#e3f2fd;padding:40px 0;min-height:100vh;display:flex;align-items:center;justify-content:center;"><div style="background:#fff;padding:32px 40px;border-radius:14px;box-shadow:0 8px 24px rgba(0,0,0,0.13);max-width:420px;width:100%;text-align:center;"><h2 style="color:#388e3c;">Message sent successfully!</h2><p>Thank you for contacting us. We will get back to you soon.</p><a href="contact.html" style="color:#007BFF;">Back to Contact</a></div></div>';
        } catch (Exception $e) {
            echo '<div style="background:#e3f2fd;padding:40px 0;min-height:100vh;display:flex;align-items:center;justify-content:center;"><div style="background:#fff;padding:32px 40px;border-radius:14px;box-shadow:0 8px 24px rgba(0,0,0,0.13);max-width:420px;width:100%;text-align:center;"><h2 style="color:#d32f2f;">Message saved, but email notification failed.</h2><p>Please try again later or contact us directly at digiportxpress@gmail.com.<br>Error: ' . $mail->ErrorInfo . '</p><a href="contact.html" style="color:#007BFF;">Back to Contact</a></div></div>';
        }
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    // Redirect to the contact page if accessed directly
    header('Location: contact.html');
    exit();
}
?>