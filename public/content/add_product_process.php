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
    $stock_quantity = isset($_POST['stock_quantity']) && is_numeric($_POST['stock_quantity']) ? intval($_POST['stock_quantity']) : 0;

    // Handle file upload
    $targetDir = "../images/"; // Ensure this points to the correct images folder in your public directory
    $imageName = basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . $imageName; // Path for storing image on the server

    // Validate file type (only JPG, JPEG, PNG allowed)
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png'];

    if (!in_array($fileType, $allowedTypes)) {
        echo "<p>Invalid file type. Only JPG, JPEG, and PNG are allowed.</p>";
        echo '<a href="../index.php?page=add_product">Try Again</a>';
        exit();
    }

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        // Store the image URL relative to the public folder (images/{image_name})
        $image_url = "images/" . $imageName; // Relative path from the public folder
    } else {
        echo "<p>Failed to upload image. Please try again.</p>";
        echo '<a href="../index.php?page=add_product">Try Again</a>';
        exit();
    }

    // Insert into the database
    $query = "INSERT INTO products (name, description, price, stock_quantity, image_url) 
              VALUES ('$name', '$description', $price, $stock_quantity, '$image_url')";

    if (mysqli_query($link, $query)) {
        header("Location: ../index.php?page=add_product&success=Product added successfully!");
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
