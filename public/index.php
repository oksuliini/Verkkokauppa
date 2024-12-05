<?php
$pages = array("etusivu", "tuotteet", "yhteystiedot");
$page = "etusivu";

if (isset($_GET['page']) && in_array($_GET['page'], $pages)) {
    $page = $_GET['page'];
}
?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hello Kitty Verkkokauppa</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include("partials/navbar.php"); ?>

    <div class="content">
        <?php
        $contentFile = "content/" . $page . ".php";
        if (file_exists($contentFile)) {
            include($contentFile);
        } else {
            echo "<p>Sivua ei löytynyt.</p>";
        }
        ?>
    </div>

    <?php include("partials/footer.php"); ?>
</body>
</html>