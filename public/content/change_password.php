<?php
session_start();
require_once '../../config/config.php'; // Lataa tietokantayhteyden asetukset

// Luo tietokantayhteys
$conn = getDbConnection(); // Kutsu funktiota saadaksesi yhteys

// Tarkista, onko käyttäjä kirjautunut sisään
if (!isset($_SESSION['SESS_USER_ID'])) {
    header("Location: ../index.php?page=login");
    exit();
}

// Hae käyttäjän ID istunnosta ja varmista, että se on kokonaisluku
$user_id = (int) $_SESSION['SESS_USER_ID'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Tarkista, että uusi salasana ja varmistus täsmäävät
    if ($new_password !== $confirm_password) {
        die("Error: New passwords do not match!");
    }

    // Varmista, että uusi salasana on vähintään 6 merkkiä pitkä
    if (strlen($new_password) < 6) {
        die("Error: New password must be at least 6 characters long!");
    }

    // Hae käyttäjän nykyinen salasana tietokannasta
    $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
    if (!$stmt) {
        die("Database error: " . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    
    if (!$stmt->fetch()) {
        die("Error: User not found!");
    }
    $stmt->close();

    // Tarkista, vastaako annettu salasana tallennettua hashia
    if (!password_verify($current_password, $hashed_password)) {
        die("Error: Incorrect current password!");
    }

    // Luo hash uudelle salasanalle
    $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Päivitä salasana tietokantaan
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
    if (!$stmt) {
        die("Database error: " . $conn->error);
    }
    $stmt->bind_param("si", $new_hashed_password, $user_id);

    if ($stmt->execute()) {
        echo "Success: Password changed successfully!";
    } else {
        echo "Error: Failed to update password.";
    }

    $stmt->close();
    $conn->close();
}
?>
