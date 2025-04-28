<?php
session_start();
require_once '../../config/config.php';

// Luo tietokantayhteys
$conn = getDbConnection();

// Tarkista, onko käyttäjä kirjautunut sisään
if (!isset($_SESSION['SESS_USER_ID'])) {
    echo "<script>alert('You must be logged in.'); window.location.href = '../index.php?page=login';</script>";
    exit();
}

$user_id = (int) $_SESSION['SESS_USER_ID'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($new_password !== $confirm_password) {
        echo "<script>alert('Error: New passwords do not match!'); window.history.back();</script>";
        exit();
    }

    // Hae nykyinen salasana
    $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
    if (!$stmt) {
        echo "<script>alert('Database error.'); window.history.back();</script>";
        exit();
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($hashed_password);

    if (!$stmt->fetch()) {
        echo "<script>alert('Error: User not found!'); window.history.back();</script>";
        exit();
    }
    $stmt->close();

    // Tarkista nykyinen salasana
    if (!password_verify($current_password, $hashed_password)) {
        echo "<script>alert('Error: Incorrect current password!'); window.history.back();</script>";
        exit();
    }

    // Päivitä salasana
    $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
    if (!$stmt) {
        echo "<script>alert('Database error while updating password.'); window.history.back();</script>";
        exit();
    }
    $stmt->bind_param("si", $new_hashed_password, $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('Password changed successfully!'); window.location.href = '../index.php?page=profile';</script>";
    } else {
        echo "<script>alert('Error: Failed to update password.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
