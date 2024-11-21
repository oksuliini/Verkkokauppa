<?php
require 'config.php';

// Create a new MySQLi connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check for connection errors
if ($conn->connect_error) {
    die("Tietokantavirhe: " . $conn->connect_error);
}

// Run a simple query to test the connection
$sql = "SELECT 1";
$result = $conn->query($sql);

if ($result) {
    echo "Tietokantayhteys toimii!";
} else {
    echo "Tietokantavirhe: " . $conn->error;
}

// Close the connection
$conn->close();
?>
