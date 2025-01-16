<?php
$link = getDbConnection();

// Fetch all products from the database
$query = "SELECT * FROM products ORDER BY created_at DESC";
$result = mysqli_query($link, $query);

if (!$result) {
    die("Error fetching products: " . mysqli_error($link));
}
?>
<h1>Tervetuloa Hello Kitty kauppaamme<h1>
<h1>Our Products</h1>

<div id="product-container">
    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="products-container">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <?php
                // Get product details
                $productId = $row['product_id'];
                $name = htmlspecialchars($row['name']);
                $description = htmlspecialchars($row['description']);
                $price = number_format($row['price'], 2); // Format price
                $stock_quantity = $row['stock_quantity'];
                $imageUrl = $row['image_url']; // Image URL
                
                // If the image does not exist, use a placeholder
                if (!file_exists($imageUrl)) {
                    $imageUrl = "images/placeholder.png";
                }
                ?>

                <!-- Product card -->
                <div class="product-card" id="product-card-<?php echo $productId; ?>">
                    <img src="<?php echo $imageUrl; ?>" alt="<?php echo $name; ?>" class="product-image">
                    <h2 class="product-name"><?php echo $name; ?></h2>
                    <p class="product-price">Price: $<?php echo $price; ?></p>
                    <p class="product-stock">Stock: <?php echo $stock_quantity; ?></p>
                    <button class="view-details-btn" onclick="viewProductDetails(<?php echo $productId; ?>)">View Details</button>
                    
                    <!-- Add to Cart form -->
                    <form action="content/cart_add.php" method="post" class="add-to-cart-form" id="cart-form-<?php echo $productId; ?>" onsubmit="return showAddToCartAlert('<?php echo $name; ?>', document.getElementById('quantity_<?php echo $productId; ?>').value)">
                        <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                        <input type="hidden" name="name" value="<?php echo $name; ?>">
                        <input type="hidden" name="price" value="<?php echo $row['price']; ?>">
                        <label for="quantity_<?php echo $productId; ?>">Quantity:</label>
                        <input type="number" id="quantity_<?php echo $productId; ?>" name="quantity" value="1" min="1" max="<?php echo $stock_quantity; ?>" class="form-control d-inline" style="width: 70px;">
                        <button type="submit" class="btn btn-primary mt-2">Add to Cart</button>
                    </form>
                </div>

                <!-- Product details (hidden by default) -->
                <div class="product-details" id="product-details-<?php echo $productId; ?>" style="display:none;">
                    <img src="<?php echo $imageUrl; ?>" alt="<?php echo $name; ?>" class="product-image-large">
                    <h2><?php echo $name; ?></h2>
                    <p><?php echo $description; ?></p>
                    <p>Price: $<?php echo $price; ?></p>
                    <p>Stock: <?php echo $stock_quantity; ?></p>
                    <form action="content/cart_add.php" method="post" class="add-to-cart-form" onsubmit="return showAddToCartAlert('<?php echo $name; ?>', document.getElementById('quantity_<?php echo $productId; ?>').value)">
                        <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                        <input type="hidden" name="name" value="<?php echo $name; ?>">
                        <input type="hidden" name="price" value="<?php echo $row['price']; ?>">
                        <label for="quantity_<?php echo $productId; ?>">Quantity:</label>
                        <input type="number" id="quantity_<?php echo $productId; ?>" name="quantity" value="1" min="1" max="<?php echo $stock_quantity; ?>" class="form-control d-inline" style="width: 70px;">
                        <button type="submit" class="btn btn-primary mt-2">Add to Cart</button>
                    </form>
                    <button class="back-to-products-btn" onclick="backToProducts(<?php echo $productId; ?>)">Back to Products</button>
                </div>

            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No products available at the moment. Please check back later.</p>
    <?php endif; ?>
</div>

<!-- JavaScript to toggle product details and show add to cart alert -->
<script>
function viewProductDetails(productId) {
    // Hide all other product details and show the selected product details
    var allProductDetails = document.querySelectorAll('.product-details');
    var allProductCards = document.querySelectorAll('.product-card');
    
    allProductDetails.forEach(function(detail) {
        detail.style.display = 'none';
    });
    
    allProductCards.forEach(function(card) {
        card.style.display = 'none';
    });
    
    // Show the details of the clicked product
    document.getElementById('product-details-' + productId).style.display = 'block';
}

function backToProducts(productId) {
    // Hide the product details and show the product cards again
    var allProductDetails = document.querySelectorAll('.product-details');
    var allProductCards = document.querySelectorAll('.product-card');
    
    allProductDetails.forEach(function(detail) {
        detail.style.display = 'none';
    });
    
    allProductCards.forEach(function(card) {
        card.style.display = 'block';
    });
}

function showAddToCartAlert(productName, quantity) {
    alert(quantity + " x " + productName + " added to your cart!");
    return true; // Allow the form to submit and add the item to the cart
}
</script>

<style>
/* Styling for product cards */
.product-card, .product-details {
    border: 1px solid #ccc;
    padding: 20px;
    text-align: center;
    background-color: #f9f9f9;
    width: 300px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.product-card img, .product-details img {
    max-width: 100%;
    height: auto;
}

.product-details {
    display: none;
    flex-basis: 100%;
    max-width: 600px;
    margin: 0 auto;
}

.view-details-btn, .back-to-products-btn {
    margin-top: 10px;
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
}

.view-details-btn:hover, .back-to-products-btn:hover {
    background-color: #0056b3;
}
body {
    font-family: "Comic Sans MS", "Comic Sans", cursive;
    background-color: #ffe4e1;
    color: #333;
    margin: 0;
    padding: 0;
}
</style>
