<?php
require_once('../../config/config.php');
session_start();
// Check if the user is logged in
if (!isset($_SESSION['SESS_USER_ID'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['SESS_USER_ID'];

// Connect to the database
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_DATABASE);
if (!$link) {
    die('Failed to connect to server: ' . mysqli_connect_error());
}


// Fetch user details from the database
$query = "SELECT first_name, last_name, email FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $first_name, $last_name, $email);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
</head>
<body>
    <h1>User Profile</h1>
    <p>First Name: <?php echo htmlspecialchars($first_name); ?></p>
    <p>Last Name: <?php echo htmlspecialchars($last_name); ?></p>
    <p>Email: <?php echo htmlspecialchars($email); ?></p>

    <h2>Change Your Password</h2>
    <p>If you want to change your password, click the button below:</p>
    <form action="update_profile.php" method="get">
        <button type="submit">Update Password</button>
    </form>

    <form action="logout.php" method="post">
        <button type="submit">Logout</button>
    </form>
</body>
</html>
