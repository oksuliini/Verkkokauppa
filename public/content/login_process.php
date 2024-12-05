<?php
// Start session
session_start();

// Include database connection details
require_once('config.php');

// Array to store validation errors
$errmsg_arr = array();

// Validation error flag
$errflag = false;

// Connect to MySQL server
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_DATABASE);
if (!$link) {
    die('Failed to connect to server: ' . mysqli_connect_error());
}

// Function to sanitize values received from the form. Prevents SQL injection
function clean($link, $str) {
    $str = trim($str);
    return mysqli_real_escape_string($link, htmlspecialchars($str, ENT_QUOTES, 'UTF-8'));
}

// Sanitize the POST values
$username = clean($link, $_POST['username']);
$password = clean($link, $_POST['password']);

// Input Validations
if ($username == '') {
    $errmsg_arr[] = 'Username missing';
    $errflag = true;
}
if ($password == '') {
    $errmsg_arr[] = 'Password missing';
    $errflag = true;
}

// If there are input validations, redirect back to the login form
if ($errflag) {
    $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
    session_write_close();
    header("location: login.php");
    exit();
}

// Create query to get user details by username
$qry = "SELECT * FROM users WHERE username='$username'";
$result = mysqli_query($link, $qry);

if ($result && mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_assoc($result);
    // Use password_verify() to check the password
    if (password_verify($password, $user['password'])) {
        // Login success
        session_regenerate_id();
        $_SESSION['SESS_USER_ID'] = $user['user_id'];
        $_SESSION['SESS_FIRST_NAME'] = $user['first_name'];
        $_SESSION['SESS_LAST_NAME'] = $user['last_name'];
        $_SESSION['SESS_EMAIL'] = $user['email'];
        session_write_close();
        header("location: ../index.php");
        exit();
    } else {
        // Incorrect password
        $_SESSION['ERRMSG_ARR'] = ['Invalid password'];
        header("location: login.php");
        exit();
    }
} else {
    // No such username found
    $_SESSION['ERRMSG_ARR'] = ['Invalid username '];
    header("location: login.php");
    exit();
}

mysqli_close($link);
?>
