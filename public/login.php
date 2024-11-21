<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirjaudu sisään</title>
</head>
<body>
    <h1>Kirjaudu sisään</h1>
    <form action="login_process.php" method="POST">
        <label for="email">Sähköposti:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Salasana:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit">Kirjaudu</button>
    </form>
    <p>Ei tiliä? <a href="register.php">Rekisteröidy</a></p>
</body>
</html>
