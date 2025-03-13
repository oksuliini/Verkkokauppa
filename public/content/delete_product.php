<?php
session_start();
require_once('../../config/config.php');

// Ensure only admins can delete products
if (!isset($_SESSION['SESS_USER_ID']) || $_SESSION['SESS_ROLE'] !== 'admin') {
    die("Access denied.");
}

// Validate product ID
if (!isset($_POST['product_id']) || !is_numeric($_POST['product_id'])) {
    die("Invalid product ID.");
}

$productId = intval($_POST['product_id']);
$link = getDbConnection();

// Debug: Check if product_id is received
echo "Deleting product with ID: " . $productId . "<br>";

// Fetch product image path
$query = "SELECT image_url FROM products WHERE product_id = ?";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "i", $productId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/Verkkokauppa/public/' . $row['image_url'];

    // Delete image if it exists
    if (!empty($row['image_url']) && file_exists($imagePath)) {
        unlink($imagePath);
    }
} else {
    die("Product not found.");
}

// 🔹 FIXED: Check if product exists in `order_items` instead of `orders`
$checkOrdersQuery = "SELECT * FROM order_items WHERE product_id = ?";
$stmt = mysqli_prepare($link, $checkOrdersQuery);
mysqli_stmt_bind_param($stmt, "i", $productId);
mysqli_stmt_execute($stmt);
$orderResult = mysqli_stmt_get_result($stmt);

if (mysqli_fetch_assoc($orderResult)) {
    die("Cannot delete product! It exists in orders.");
}

// Delete from related tables
$deleteCategoryQuery = "DELETE FROM product_categories WHERE product_id = ?";
$stmt = mysqli_prepare($link, $deleteCategoryQuery);
mysqli_stmt_bind_param($stmt, "i", $productId);
$stmt->execute();

// Delete from products table
$deleteProductQuery = "DELETE FROM products WHERE product_id = ?";
$stmt = mysqli_prepare($link, $deleteProductQuery);
mysqli_stmt_bind_param($stmt, "i", $productId);
$stmt->execute();

mysqli_close($link);

// Redirect with success message
header("Location: ../index.php?page=etusivu&message=ProductDeleted");
exit();
?>
