<form id="loginForm" name="loginForm" method="post" action="register_process.php">
  <table>
    <tr>
      <th>Etunimi</th>
      <td><input name="fname" type="text" required /></td>
    </tr>
    <tr>
      <th>Sukunimi</th>
      <td><input name="lname" type="text" required /></td>
    </tr>
    <tr>
      <th>Sähköposti</th>
      <td><input name="email" type="email" required /></td>
    </tr>
    <tr>
      <th>Käyttäjätunnus</th>
      <td><input name="login" type="text" required /></td>
    </tr>
    <tr>
      <th>Salasana</th>
      <td><input name="password" type="password" required /></td>
    </tr>
    <tr>
      <th>Vahvista salasana</th>
      <td><input name="cpassword" type="password" required /></td>
    </tr>
    <tr>
      <td colspan="2"><input type="submit" value="Rekisteröidy" /></td>
    </tr>
    <tr>
      <td colspan="2">
        <b>Oletko jo käyttäjä?</b>
        <a href="login.php">Kirjaudu sisään tästä</a>
      </td>
    </tr>
  </table>
</form>
