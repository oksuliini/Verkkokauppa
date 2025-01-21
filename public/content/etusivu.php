<?php
// Fetch all products from the database
$link = getDbConnection();
$query = "SELECT * FROM products ORDER BY created_at DESC";
$result = mysqli_query($link, $query);

if (!$result) {
    die("Error fetching products: " . mysqli_error($link));
}
?>

<h1>Tervetuloa Hello Kitty kauppaamme</h1>
<h1>Our Products</h1>

<div class="products-container d-flex flex-wrap">
    <?php
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $productId = $row['product_id'];
            $name = htmlspecialchars($row['name']);
            $price = number_format($row['price'], 2);
            $stock_quantity = $row['stock_quantity'];
            $imageUrl = file_exists($row['image_url']) ? $row['image_url'] : "images/placeholder.png";

            echo "
            <div class='product-card border m-2 p-3' style='width: 300px;'>
                <img src='$imageUrl' alt='$name' class='product-image img-fluid' style='height: 200px; object-fit: cover;'>
                <h2 class='product-name mt-2'>$name</h2>
                <p class='product-price'>Price: $$price</p>
                <p class='product-stock'>Stock: $stock_quantity</p>

                <!-- Add to Cart Form -->
                <form action='content/cart_add.php' method='post' onsubmit='return showAddToCartAlert(\"$name\", this.quantity.value)'>
                    <input type='hidden' name='product_id' value='$productId'>
                    <input type='hidden' name='name' value='$name'>
                    <input type='hidden' name='price' value='{$row['price']}'>
                    <label for='quantity_$productId'>Quantity:</label>
                    <input type='number' id='quantity_$productId' name='quantity' value='1' min='1' max='$stock_quantity' class='form-control d-inline' style='width: 70px;'>
                    <button type='submit' class='btn btn-hotpink mt-2'>Add to Cart</button>
                </form>

                <!-- View Details Button -->
                <a href='index.php?page=product&id=$productId' class='btn btn-secondary mt-2'>View Details</a>
            </div>";
        }
    } else {
        echo "<p>No products available at the moment. Please check back later.</p>";
    }
    ?>
</div>

<!-- Styling for product cards and buttons -->
<style>
.products-container {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    justify-content: flex-start;
    margin: 0 auto;
    padding: 20px;
}

.product-card {
    flex: 1 1 calc(25% - 16px);
    max-width: calc(25% - 16px);
    background-color: #ffccd5;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 16px;
    text-align: center;
    transition: transform 0.3s ease;
}

.product-image {
    width: 200px;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
    margin: 0 auto;
    display: block;
}

.product-card:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* Custom hot pink button for Add to Cart */
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
