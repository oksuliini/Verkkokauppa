<?php
// Assuming the database connection is already set
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($productId <= 0) {
    echo "Invalid product ID.";
    exit;
}

// Fetch product details from the database
$query = "SELECT * FROM products WHERE product_id = $productId";
$result = mysqli_query($link, $query);

if (!$result) {
    die("Error fetching product details: " . mysqli_error($link));
}

$product = mysqli_fetch_assoc($result);

if (!$product) {
    die("Product not found.");
}

// Extract product details
$productName = htmlspecialchars($product['name']);
$productDescription = htmlspecialchars($product['description']);
$productPrice = number_format($product['price'], 2);
$productStock = $product['stock_quantity'];
$productImage = $product['image_url'];
if (!file_exists($productImage)) {
    $productImage = "images/placeholder.png"; // Use placeholder image if the product image is missing
}
?>

<h1><?php echo $productName; ?></h1>
<div class="product-details-container">
    <img src="<?php echo $productImage; ?>" alt="<?php echo $productName; ?>" class="product-image-large">
    <div class="product-info">
        <p class="product-description"><?php echo $productDescription; ?></p>
        <p class="product-price">Price: €<?php echo $productPrice; ?></p>
        <p class="product-stock">Stock: <?php echo $productStock; ?></p>

        <form action="content/cart_add.php" method="post">
            <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
            <input type="hidden" name="name" value="<?php echo $productName; ?>">
            <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $productStock; ?>" class="form-control d-inline" style="width: 70px;">
            <button type="submit" class="btn btn-primary mt-2">Add to Cart</button>
        </form>
    </div>
</div>

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
</style>
