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

$link = getDbConnection();

// Handle image upload
if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        echo "Upload error code: " . $_FILES['image']['error'];
        exit();
    }

    // ? Set full path to your real image folder
    $targetDir = $_SERVER["CONTEXT_DOCUMENT_ROOT"].'/Verkkokauppa/public/images/';

    // Generate unique filename
    $extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
    $uniqueName = uniqid('product_', true) . '.' . $extension;
    $targetFile = $targetDir . $uniqueName;

    // Debug (optional)
    echo "TMP file: " . $_FILES["image"]["tmp_name"] . "<br>";
    echo "Target: " . $targetFile . "<br>";

    if (!is_uploaded_file($_FILES["image"]["tmp_name"])) {
        echo "Invalid uploaded file.";
        exit();
    }

    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0755, true)) {
            echo "Failed to create directory: $targetDir";
            exit();
        }
    }

    if (!is_writable($targetDir)) {
        echo "Directory not writable: $targetDir";
        exit();
    }

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        $imageUrl = "images/" . $uniqueName; // This goes in DB and is used on the front-end
    } else {
        echo "Error uploading the image.";
        exit();
    }
} else {
    // Use current image if no new one is uploaded
    $imageUrl = $_POST['current_image'];
}

// Update product in DB
$updateProductQuery = "UPDATE products SET name = ?, description = ?, price = ?, stock_quantity = ?, image_url = ? WHERE product_id = ?";
$stmt = mysqli_prepare($link, $updateProductQuery);
mysqli_stmt_bind_param($stmt, "ssdisi", $name, $description, $price, $stock_quantity, $imageUrl, $productId);
mysqli_stmt_execute($stmt);

// Update category
$updateCategoryQuery = "UPDATE product_categories SET category_id = ? WHERE product_id = ?";
$stmt = mysqli_prepare($link, $updateCategoryQuery);
mysqli_stmt_bind_param($stmt, "ii", $categoryId, $productId);
mysqli_stmt_execute($stmt);

mysqli_close($link);

// Redirect
header("Location: ../index.php?page=product&id=$productId");
exit();
?>
