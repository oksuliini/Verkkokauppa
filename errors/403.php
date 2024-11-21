<?php
http_response_code(403);
?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pääsy kielletty</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 50px; }
        h1 { color: #ff0000; }
    </style>
</head>
<body>
    <h1>403 - Pääsy kielletty</h1>
    <p>Sinulla ei ole oikeuksia käyttää tätä sivua.</p>
    <a href="/public/index.php">Palaa etusivulle</a>
</body>
</html>