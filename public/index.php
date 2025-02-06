<?php
session_start();
require_once('../config/config.php');  // Include database connection configuration
// Define available pages
$pages = array(
    "etusivu", "cart", "checkout", "profile", "login", "product", "register", "update_profile",
    "admin_profile", "add_product", "edit_product", "order_details"
);

// Default page to display is 'etusivu'
$page = "etusivu";

// Check if 'page' is set in the URL and is a valid page
if (isset($_GET['page']) && in_array($_GET['page'], $pages)) {
    $page = $_GET['page'];  // Get the requested page
}

// Define pages that require user authentication
$restrictedPages = ["cart", "checkout", "profile", "admin_profile", "orders", "add_product", "update_profile"];

// Redirect to login if trying to access restricted pages without being logged in
if (in_array($page, $restrictedPages) && !isset($_SESSION['SESS_USER_ID'])) {
    header("Location: index.php?page=login");
    exit();
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

    <!-- Include Navigation Bar -->
    <?php include("partials/navbar.php"); ?>

    <div class="content">
        <?php
        // Dynamically load the page content based on the 'page' query parameter
        $contentFile = "content/" . $page . ".php";  // Path to the page content file

        // Check if the content file exists, and include it
        if (file_exists($contentFile)) {
            include($contentFile);
        } else {
            echo "<p>Sivua ei löytynyt.</p>";  // If the page doesn't exist, show an error message
        }
        ?>
    </div>

    <!-- Include Footer -->
    <?php include("partials/footer.php"); ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
    <script>
function showAddToCartAlert(productName, quantity) {
    if (quantity > 0) {
        alert(`${quantity} x ${productName} added to your cart!`);
    } else {
        alert("Please select a valid quantity.");
    }
    return true; // Allow form submission to proceed
}
</script>
</body>
</html>
