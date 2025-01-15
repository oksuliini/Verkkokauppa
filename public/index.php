<?php
require_once('../config/config.php');
session_start();
$pages = array(
    "etusivu", "cart", "checkout", "profile", "login", "logout", "product", "register",
    "login_process", "register_process", "update_profile",
    "admin_profile", "add_product"
);
$page = "etusivu";

if (isset($_GET['page']) && in_array($_GET['page'], $pages)) {
    $page = $_GET['page'];
    $restrictedPages = ["cart", "checkout", "profile", "update_profile"];
    if (in_array($page, $restrictedPages) && !isset($_SESSION['SESS_USER_ID'])) {
        header("Location: index.php?page=login");
        exit(); // Stop further execution
    }
}


?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hello Kitty Verkkokauppa</title>

   
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include("partials/navbar.php"); ?>

    <div class="content">
        <?php
        $contentFile = "content/"  . $page . ".php";
        if (file_exists($contentFile)) {
            include($contentFile);
        } else {
            echo "<p>Sivua ei löytynyt.</p>";
        }
        ?>
    </div>

    <?php include("partials/footer.php"); ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
</body>
</html>
