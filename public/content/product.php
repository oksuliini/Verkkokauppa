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
            </div>";
        }
    } else {
        echo "<p>No products available at the moment. Please check back later.</p>";
    }
    ?>
</div>
