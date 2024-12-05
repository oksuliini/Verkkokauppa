<?php
session_start();
require_once('config.php');

// Connect to MySQL server
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_DATABASE);
if (!$link) {
    die('Failed to connect to server: ' . mysqli_connect_error());
}

// Get the submitted username and password
$username = isset($_POST['username']) ? mysqli_real_escape_string($link, $_POST['username']) : '';
$password = isset($_POST['password']) ? $link, $_POST['password'] : '';

if ($username == '' || $password == '') {
    $_SESSION['ERRMSG_ARR'] = ['Username or Password is missing'];
    header("location: login.php");
    exit();
}

// Check if the username exists
$qry = "SELECT * FROM users WHERE username='$username'";
$result = mysqli_query($link, $qry);

if ($result) {
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        // Verify the password
        if (password_verify($password, $row['password'])) {
            $_SESSION['SESS_USERNAME'] = $row['username'];
            header("location: index.php"); // Redirect to a dashboard or home page
            exit();
        } else {
            $_SESSION['ERRMSG_ARR'] = ['Incorrect username or password'];
            header("location: login.php");
            exit();
        }
    } else {
        $_SESSION['ERRMSG_ARR'] = ['Incorrect username or password'];
        header("location: login.php");
        exit();
    }
} else {
    die("Query failed" . mysqli_error($link));
}

mysqli_close($link);
