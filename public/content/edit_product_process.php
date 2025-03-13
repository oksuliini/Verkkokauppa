<?php
session_start();
require_once('../../config/config.php');

// Ensure only admins can perform this action
if (!isset($_SESSION['SESS_USER_ID']) || $_SESSION['SESS_ROLE'] !== 'admin') {
    header("Location: index.php?page=login");
    exit();
}

$productId = intval($_POST['product_id']);
$name = $_POST['name'];
$description = $_POST['description'];
$price = $_POST['price'];
$stock_quantity = $_POST['stock_quantity'];
$categoryId = $_POST['category_id'];

// Connect to the database
$link = getDbConnection();

// Handle image upload
if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
    // Specify target directory
    $targetDir = $_SERVER['DOCUMENT_ROOT'] . '/Verkkokauppa/public/images/';
    $targetFile = $targetDir . basename($_FILES["image"]["name"]);
    
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        // Successfully uploaded, get the new image URL
        $imageUrl = "images/" . basename($_FILES["image"]["name"]);
    } else {
        echo "Error uploading the image.";
        exit();
    }
} else {
    // If no new image was uploaded, use the old image
    $imageUrl = $_POST['current_image'];
}

// Update the product details in the products table
$updateProductQuery = "UPDATE products SET name = ?, description = ?, price = ?, stock_quantity = ?, image_url = ? WHERE product_id = ?";
$stmt = mysqli_prepare($link, $updateProductQuery);
mysqli_stmt_bind_param($stmt, "ssdisi", $name, $description, $price, $stock_quantity, $imageUrl, $productId);
mysqli_stmt_execute($stmt);

// Update the product's category in the product_categories table
$updateCategoryQuery = "UPDATE product_categories SET category_id = ? WHERE product_id = ?";
$stmt = mysqli_prepare($link, $updateCategoryQuery);
mysqli_stmt_bind_param($stmt, "ii", $categoryId, $productId);
mysqli_stmt_execute($stmt);

mysqli_close($link);

// Redirect back to the product page or another page
header("Location: ../index.php?page=product&id=$productId");
exit();
?>
