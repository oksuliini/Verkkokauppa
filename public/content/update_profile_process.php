<?php
session_start();
require_once('../../config/config.php');

if (!isset($_SESSION['SESS_USER_ID'])) {
    header("Location: ../index.php?page=login");
    exit();
}

$user_id = $_SESSION['SESS_USER_ID'];
$link = getDbConnection();

// Fetch the current hashed password from the database
$query = "SELECT password FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $hashed_password);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// If the form is submitted, process the password update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate the inputs
    if (!password_verify($current_password, $hashed_password)) {
        $_SESSION['ERROR_MESSAGE'] = "The current password is incorrect.";
    } elseif ($new_password !== $confirm_password) {
        $_SESSION['ERROR_MESSAGE'] = "The new passwords do not match.";
    } else {
        // Hash the new password
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the password in the database
        $update_query = "UPDATE users SET password = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($link, $update_query);
        mysqli_stmt_bind_param($stmt, "si", $new_hashed_password, $user_id);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            $_SESSION['SUCCESS_MESSAGE'] = "Password successfully updated.";
        } else {
            $_SESSION['ERROR_MESSAGE'] = "An error occurred while updating the password.";
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($link);

    // Redirect back to the update_profile page without including messages in the URL
    header("Location: ../index.php?page=update_profile");
    exit();
} else {
    // If the form is not submitted properly, redirect to the update form
    header("Location: ../index.php?page=update_profile");
    exit();
}
