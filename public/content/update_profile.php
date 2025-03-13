<?php
session_start();
require_once('../../config/config.php');

if (!isset($_SESSION['SESS_USER_ID'])) {
    header("Location: ../index.php?page=login");
    exit();
}

$user_id = $_SESSION['SESS_USER_ID'];
$link = getDbConnection();

$first_name = trim($_POST['first_name']);
$last_name = trim($_POST['last_name']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$address = trim($_POST['address']);

// Tarkista, onko sähköposti jo käytössä toisella käyttäjällä
$email_check_query = "SELECT user_id FROM users WHERE email = ? AND user_id != ?";
$stmt = mysqli_prepare($link, $email_check_query);
mysqli_stmt_bind_param($stmt, "si", $email, $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
if (mysqli_stmt_num_rows($stmt) > 0) {
    $_SESSION['ERROR_MESSAGE'] = "This email is already in use.";
    header("Location: ../index.php?page=profile");
    exit();
}
mysqli_stmt_close($stmt);

// Päivitä käyttäjätiedot
$update_query = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, address = ? WHERE user_id = ?";
$stmt = mysqli_prepare($link, $update_query);
mysqli_stmt_bind_param($stmt, "sssssi", $first_name, $last_name, $email, $phone, $address, $user_id);
$result = mysqli_stmt_execute($stmt);

if ($result) {
    $_SESSION['SUCCESS_MESSAGE'] = "Profile updated successfully.";
} else {
    $_SESSION['ERROR_MESSAGE'] = "An error occurred while updating your profile.";
}

mysqli_stmt_close($stmt);
mysqli_close($link);

// Palataan profiilisivulle
header("Location: ../index.php?page=profile");
exit();
