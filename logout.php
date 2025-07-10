<?php
// filepath: c:\wamp64\www\New folder\logout.php

// Ensure no output is sent before this point
ob_start();

// Start session
session_start();

// Destroy all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to the login page
header("Location: login.html");
exit();
?>