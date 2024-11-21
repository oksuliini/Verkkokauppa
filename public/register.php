<?php
	session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register user</title>
<link href="loginmodule.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
	if( isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) >0 ) {
		echo '<ul class="err">';
		foreach($_SESSION['ERRMSG_ARR'] as $msg) {
			echo '<li>',$msg,'</li>'; 
		}
		echo '</ul>';
		unset($_SESSION['ERRMSG_ARR']);
	}
?>
<h1>Register new user</h1>
<form id="register-form" name="registerform" method="post" action="register_process.php">
  <table width="300" border="0" align="center" cellpadding="2" cellspacing="0">
    <tr>
      <th>First Name </th>
      <td><input name="first_name" type="text" class="textfield" id="first_name" /></td>
    </tr>
    <tr>
      <th>Last Name </th>
      <td><input name="last_name" type="text" class="textfield" id="last_name" /></td>
    </tr>
    <tr>
      <th>Email </th>
      <td><input name="email" type="email" class="textfield" id="email" /></td>
    </tr>
    <tr>
      <th>Phonenumber </th>
      <td><input name="phone" type="tel" class="textfield" id="phone" /></td>
    </tr>
    <tr>
      <th>Address</th>
      <td><input name="address" type="text" class="textfield" id="address" /></td>
    </tr>
    <tr>
      <th width="124">Username</th>
      <td width="168"><input name="username" type="text" class="textfield" id="username" /></td>
    </tr>
    <tr>
      <th>Password</th>
      <td><input name="password" type="password" class="textfield" id="password" /></td>
    </tr>
    <tr>
      <th>Confirm Password </th>
      <td><input name="cpassword" type="password" class="textfield" id="cpassword" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="submit" name="Submit" value="Register" /></td>
    </tr>
    <tr>
      <td>
      <b>Already a user?</b>
        <td>
        <a href='login.php' >Click here!</a>
    </tr>
  </table>
</form>
</body>
</html>
