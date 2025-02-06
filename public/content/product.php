<?php
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Product not found.</p>";
    exit();
}

$productId = intval($_GET['id']);
$link = getDbConnection();
$query = "SELECT * FROM products WHERE product_id = ?";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "i", $productId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $name = htmlspecialchars($row['name']);
    $description = htmlspecialchars($row['description']);
    $price = number_format($row['price'], 2);
    $stock_quantity = $row['stock_quantity'];
    $imageUrl = file_exists($row['image_url']) ? $row['image_url'] : "images/placeholder.png";

    echo "
    <div class='product-details-page'>
        <img src='$imageUrl' alt='$name' class='product-image img-fluid' style='height: 300px; object-fit: cover;'>
        <h1>$name</h1>
        <p>$description</p>
        <p>Price: $$price</p>
        <p>Stock: $stock_quantity</p>";

    // Show Edit Button Only for Admins
    if (isset($_SESSION['SESS_ROLE']) && $_SESSION['SESS_ROLE'] === 'admin') {
        // Correct the Edit button link by concatenating $productId directly into the href attribute
        echo "<a href='index.php?page=edit_product&id=" . $productId . "' class='btn btn-warning mt-2'>Edit Product</a>";
    }

    echo "</div>";
} else {
    echo "<p>Product not found.</p>";
}

mysqli_stmt_close($stmt);
mysqli_close($link);
?>
