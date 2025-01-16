<?php
$link = getDbConnection();
$query = "SELECT * FROM products ORDER BY created_at DESC"; // Retrieve all products ordered by newest first
$result = mysqli_query($link, $query);

if (!$result) {
    die("Error fetching products: " . mysqli_error($link));
}
?>
<h1>Our Products</h1>
<div class="products-container">
    <?php
    if (mysqli_num_rows($result) > 0) {
        // Loop through and display each product
        while ($row = mysqli_fetch_assoc($result)) {
            $productId = $row['product_id'];
            $name = htmlspecialchars($row['name']);
            $description = htmlspecialchars($row['description']);
            $price = number_format($row['price'], 2); // Format price to 2 decimal places
            $stock_quantity = $row['stock_quantity'];
            $imageUrl = $row['image_url']; // Retrieve the image URL from the database

            // Check if the image exists, otherwise use a default placeholder
            if (!file_exists($imageUrl)) {
                $imageUrl = "images/placeholder.png"; // Path to a placeholder image
            }

            echo "
            <div class='product-card'>
                <img src='$imageUrl' alt='$name' class='product-image'>
                <h2 class='product-name'>$name</h2>
                <p class='product-description'>$description</p>
                <p class='product-price'>Price: $$price</p>
                <p class='product-stock'>Stock: $stock_quantity</p>
                <form action='content/cart_add.php' method='post'>
                    <input type='hidden' name='product_id' value='$productId'>
                    <input type='hidden' name='name' value='$name'>
                    <input type='hidden' name='price' value='{$row['price']}'>
                    <label for='quantity_$productId'>Quantity:</label>
                    <input type='number' id='quantity_$productId' name='quantity' value='1' min='1' max='$stock_quantity' class='form-control d-inline' style='width: 70px;'>
                    <button type='submit' class='btn btn-primary mt-2'>Add to Cart</button>
                </form>
            </div>";
        }
    } else {
        echo "<p>No products available at the moment. Please check back later.</p>";
    }
    ?>
</div>
