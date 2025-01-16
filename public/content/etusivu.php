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

<div class="products-container">
    <?php
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $productId = $row['product_id'];
            $name = htmlspecialchars($row['name']);
            $price = number_format($row['price'], 2);
            $imageUrl = $row['image_url'];

            if (!file_exists($imageUrl)) {
                $imageUrl = "images/placeholder.png";
            }

            // Each product is a link that will show the details
            echo "
<div class='product-card' id='product_$productId'>
    <a href='#' onclick='showDetails($productId)' style='text-decoration: none;'>
        <img src='$imageUrl' alt='$name' class='product-image'>
        <h2 class='product-name'>$name</h2>
        <p class='product-price'>Price: $$price</p>
    </a>
</div>";
        }
    } else {
        echo "<p>No products available at the moment. Please check back later.</p>";
    }
    ?>
</div>

<!-- Styling for product cards -->
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
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 16px;
    text-align: center;
    transition: transform 0.3s ease;
}

.product-image {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
    margin: 0 auto;
    display: block;
}

.product-card:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}
</style>
