<?php

// Check if the user is an admin
if (!isset($_SESSION['SESS_ROLE']) || $_SESSION['SESS_ROLE'] !== 'admin') {
    header("Location: ../../errors/403.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
</head>
<body>
    <h1>Add a New Product</h1>
    <form action="content/add_product_process.php" method="POST" enctype="multipart/form-data">
        <label for="name">Product Name:</label>
        <input type="text" id="name" name="name" required><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea><br>

        <label for="price">Price:</label>
        <input type="number" step="0.01" id="price" name="price" required><br>

        <label for="stock_quantity">Stock Quantity:</label>
        <input type="number" id="stock_quantity" name="stock_quantity"  min="0" required><br>

        <label for="image">Product Image:</label>
        <input type="file" id="image" name="image" accept="image/*" required><br>

        <button type="submit">Add Product</button>
    </form>
    <a href="index.php?page=admin_profile.php">Back to Admin Profile</a>
</body>
</html>
