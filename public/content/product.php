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
        <p>Stock: $stock_quantity</p>

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

<style>
.product-details-container {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    margin: 20px;
}

.product-image-large {
    width: 400px;
    height: 400px;
    object-fit: cover;
    border-radius: 8px;
    margin: 20px;
}

.product-info {
    max-width: 400px;
    text-align: left;
}

.product-info h2 {
    font-size: 2em;
    margin-bottom: 10px;
}

.product-description {
    font-size: 1.2em;
    margin-bottom: 10px;
}

.product-price, .product-stock {
    font-size: 1.1em;
    margin-bottom: 10px;
}
.btn-hotpink {
    background-color: hotpink;
    color: white;
    border: none;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.btn-hotpink:hover {
    background-color: #ff69b4;
    transform: scale(1.1);
    color: white;
}

.btn-hotpink:focus {
    box-shadow: 0 0 0 0.25rem rgba(255, 105, 180, 0.5);
    outline: none;
}
</style>
