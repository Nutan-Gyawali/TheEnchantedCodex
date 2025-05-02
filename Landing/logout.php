<?php
// Start the session if it hasn't been started already
session_start();

// Unset all session variables
$_SESSION = array();

// Delete the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}

// Destroy the session
session_destroy();

// Set a logout success message
session_start(); // Start a new session to store the message
$_SESSION['success_message'] = "You have been successfully logged out.";

// Redirect to the login page
header("Location: index.php");
exit();
