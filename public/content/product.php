<?php
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Product not found.</p>";
    exit();
}
$productId = intval($_GET['id']);
$link = getDbConnection();

// Hae tuotteen tiedot
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

    // Hae tuotteen kategoriat
    $categoryQuery = "
        SELECT c.name 
        FROM categories c
        INNER JOIN product_categories pc ON c.category_id = pc.category_id
        WHERE pc.product_id = ?";
    $categoryStmt = mysqli_prepare($link, $categoryQuery);
    mysqli_stmt_bind_param($categoryStmt, "i", $productId);
    mysqli_stmt_execute($categoryStmt);
    $categoryResult = mysqli_stmt_get_result($categoryStmt);

    $categories = [];
    while ($categoryRow = mysqli_fetch_assoc($categoryResult)) {
        $categories[] = htmlspecialchars($categoryRow['name']);
    }
    $categoryList = empty($categories) ? "No categories available" : implode(", ", $categories);

    echo "
    <div class='product-details-page'>
        <img src='$imageUrl' alt='$name' class='product-image img-fluid' style='height: 300px; object-fit: cover;'>
        <h1>$name</h1>
        <p>$description</p>
        <p><strong>Price:</strong> $$price</p>
        <p><strong>Stock:</strong> $stock_quantity</p>
        <p><strong>Categories:</strong> $categoryList</p>

        <!-- Add to Cart Form -->
        <form action='content/cart_add.php' method='post' onsubmit='return showAddToCartAlert(\"$name\", this.quantity.value)'>
            <input type='hidden' name='product_id' value='$productId'>
            <input type='hidden' name='name' value='$name'>
            <input type='hidden' name='price' value='{$row['price']}'>
            <label for='quantity_$productId'>Quantity:</label>
            <input type='number' id='quantity_$productId' name='quantity' value='1' min='1' max='$stock_quantity' class='form-control d-inline' style='width: 70px;'>
            <button type='submit' class='btn btn-hotpink mt-2'>Add to Cart</button>
        </form>
    </div>";
} else {
    echo "<p>Product not found.</p>";
}
mysqli_stmt_close($stmt);
mysqli_close($link);
?>
