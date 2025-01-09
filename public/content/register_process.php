<?php
session_start();
require_once('../../config.php');
$errmsg_arr = array();

// Validation error flag
$errflag = false;

// Connect to MySQL server
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_DATABASE);
if (!$link) {
    die('Failed to connect to server: ' . mysqli_connect_error());
}

// Function to sanitize values received from the form
function clean($link, $str) {
    $str = stripslashes($str);
    return mysqli_real_escape_string($link, $str);
}

// Sanitize the POST values
$first_name = isset($_POST['first_name']) ? clean($link, $_POST['first_name']) : '';
$last_name = isset($_POST['last_name']) ? clean($link, $_POST['last_name']) : '';
$email = isset($_POST['email']) ? clean($link, $_POST['email']) : '';
$phone = isset($_POST['phone']) ? clean($link, $_POST['phone']) : '';
$address = isset($_POST['address']) ? clean($link, $_POST['address']) : '';
$username = isset($_POST['username']) ? clean($link, $_POST['username']) : '';
$password = isset($_POST['password']) ? clean($link, $_POST['password']) : '';
$cpassword = isset($_POST['cpassword']) ? clean($link, $_POST['cpassword']) : '';

if ($first_name == '') $errmsg_arr[] = 'First name missing';
if ($last_name == '') $errmsg_arr[] = 'Last name missing';
if ($email == '') $errmsg_arr[] = 'Email missing';
if ($phone == '') $errmsg_arr[] = 'Phonenumber is missing';
if ($address == '') $errmsg_arr[] = 'Address is missing';
if ($username == '') $errmsg_arr[] = 'Username missing';
if ($password == '') $errmsg_arr[] = 'Password missing';
if ($cpassword == '') $errmsg_arr[] = 'Confirm password missing';
if (strcmp($password, $cpassword) != 0) $errmsg_arr[] = 'Passwords do not match';

// Check for duplicate username
if ($username != '') {
    $qry = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($link, $qry);
    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $errmsg_arr[] = 'Username already in use';
            $errflag = true;
        }
        mysqli_free_result($result);
    } else {
        die("Query failed" . mysqli_error($link));
    }
}

// If there are input validations, redirect back to the registration form
if ($errflag) {
    $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
    session_write_close();
    header("location: ../index.php?page=register");
    exit();
}

// Hash the password securely
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Create INSERT query
$qry = "INSERT INTO users (first_name, last_name, email, phone, address, username, password) VALUES ('$first_name', '$last_name', '$email', '$phone', '$address', '$username', '$hashed_password')";
$result = mysqli_query($link, $qry);

// Check whether the query was successful or not
if ($result) {
    header("location: ../index.php?page=login.php"); // Redirect to success page
    exit();
} else {
    die("Query failed" . mysqli_error($link));
}

mysqli_close($link);