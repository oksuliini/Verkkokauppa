<?php

// Check if the user is logged in
if (!isset($_SESSION['SESS_USER_ID'])) {
    header("Location: index.php?page=login");
    exit();
}

$user_id = $_SESSION['SESS_USER_ID'];

// Connect to the database
$link = getDbConnection();

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
    <h1>Update Your Password</h1>

    <?php
// Display success or error messages
if (isset($_SESSION['SUCCESS_MESSAGE'])) {
    echo "<p style='color: green;'>" . htmlspecialchars($_SESSION['SUCCESS_MESSAGE']) . "</p>";
    unset($_SESSION['SUCCESS_MESSAGE']); // Clear the message after displaying it
}

if (isset($_SESSION['ERROR_MESSAGE'])) {
    echo "<p style='color: red;'>" . htmlspecialchars($_SESSION['ERROR_MESSAGE']) . "</p>";
    unset($_SESSION['ERROR_MESSAGE']); // Clear the message after displaying it
}
?>


    <form action="content/update_profile_process.php" method="POST">
        <label for="current_password">Current Password:</label>
        <input type="password" id="current_password" name="current_password" required><br>

        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required><br>

        <label for="confirm_password">Confirm New Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required><br><br>

        <form action="index.php?page=update_profile" method="post">
        <button type="submit" class="btn btn-hotpink mt-2">Update Profile</button>
    </form>
    </form>
    <form action="content/logout.php" method="post">
        <button type="submit" class="btn btn-hotpink mt-2">Logout</button>
    </form>
   