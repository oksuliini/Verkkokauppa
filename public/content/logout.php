<?php
// Start the session
session_start();

// Destroy all session data
session_unset();  // Unset all session variables
session_destroy();  // Destroy the session

// Redirect the user to the homepage or login page
header("Location: ../index.php?page=login");
exit;