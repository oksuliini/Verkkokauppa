<?php
session_start();
require_once('../../config/config.php');

// Check if the user is an admin
if (!isset($_SESSION['SESS_ROLE']) || $_SESSION['SESS_ROLE'] !== 'admin') {
    header("Location: ../../errors/403.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $link = getDbConnection();

    // Sanitize input
    $name = mysqli_real_escape_string($link, $_POST['name']);
    $description = mysqli_real_escape_string($link, $_POST['description']);
    $price = floatval($_POST['price']);
    if (isset($_POST['stock_quantity']) && is_numeric($_POST['stock_quantity'])) {
        $stock_quantity = intval($_POST['stock_quantity']);
    } else {
        $stock_quantity = 0;  // Default to 0 if not set or invalid
    }

    // Handle file upload
    $targetDir = "../images/"; // Directory to store images
    $imageName = basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . $imageName;

    // Validate file type
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png',];
    if (!in_array($fileType, $allowedTypes)) {
        echo "<p>Invalid file type. Only JPG, JPEG and PNG are allowed.</p>";
        echo '<a href="../index.php?page=add_product">Try Again</a>';
        exit();
    }

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        $image_url = $targetFile; // Save the file path for the database
    } else {
        echo "<p>Failed to upload image. Please try again.</p>";
        echo '<a href="../index.php?page=add_product">Try Again</a>';
        exit();
    }

    // Insert into database
    $query = "INSERT INTO products (name, description, price, stock_quantity, image_url) 
              VALUES ('$name', '$description', $price, $stock_quantity, '$image_url')";
    if (mysqli_query($link, $query)) {
        header("Location: ../index.php?page=add_product");
    exit();
    } else {
        echo "<p>Error: " . mysqli_error($link) . "</p>";
        echo '<a href="../index.php?page=add_product">Try Again</a>';
    }

    mysqli_close($link);
} else {
    header("Location: ../index.php?page=add_product");
    exit();
}
?>
