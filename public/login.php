<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="loginmodule.css" rel="stylesheet" type="text/css" />
</head>
<body>
<center> <h1>Login</h1><center>

    <?php
    if (isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) > 0) {
        echo '<ul class="err">';
        foreach ($_SESSION['ERRMSG_ARR'] as $msg) {
            echo '<li>', $msg, '</li>';
        }
        echo '</ul>';
        unset($_SESSION['ERRMSG_ARR']);
    }

    if (isset($_SESSION['SUCCESS_MSG'])) {
        echo '<p class="success">' . $_SESSION['SUCCESS_MSG'] . '</p>';
        unset($_SESSION['SUCCESS_MSG']);
    }
    ?>

    <form id="login-form" name="loginform" method="post" action="login_process.php">
        <table width="300" border="0" align="center" cellpadding="2" cellspacing="0">
            <tr>
                <th>Username</th>
                <td><input name="username" type="text" class="textfield" id="username" required /></td>
            </tr>
            <tr>
                <th>Password</th>
                <td><input name="password" type="password" class="textfield" id="password" required /></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><input type="submit" name="Submit" value="Login" /></td>
            </tr>
            <tr>
                <td colspan="2">
                    <b>Don't have an account?</b> <a href="register.php">Register here!</a>
                </td>
            </tr>
        </table>
    </form>
</body>
</html>
