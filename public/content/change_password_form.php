<?php
if (!isset($_SESSION['SESS_USER_ID'])) {
    header("Location: index.php?page=login");
    exit();
}
?>

<h1>Change Password</h1>

<form action="content/change_password.php" method="POST">
    <label for="current_password">Current Password:</label>
    <input type="password" id="current_password" name="current_password" required><br>

    <label for="new_password">New Password:</label>
    <input type="password" id="new_password" name="new_password" required><br>

    <label for="confirm_password">Confirm New Password:</label>
    <input type="password" id="confirm_password" name="confirm_password" required><br><br>

    <button type="submit" class="btn btn-hotpink mt-2">Change Password</button>
    <a href="index.php?page=profile" class="btn btn-secondary mt-2">Back to Profile</a>

</form>


