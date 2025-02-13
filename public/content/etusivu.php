<?php
$link = getDbConnection();

$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$categoryId = isset($_GET['category']) ? intval($_GET['category']) : 0;

$categoryList = []; // Stores selected category + its subcategories

if ($categoryId > 0) {
    // Get parent ID of selected category
    $parentCheckQuery = "SELECT parent_id FROM categories WHERE category_id = ?";
    $parentCheckStmt = mysqli_prepare($link, $parentCheckQuery);
    
    if ($parentCheckStmt) {
        mysqli_stmt_bind_param($parentCheckStmt, "i", $categoryId);
        mysqli_stmt_execute($parentCheckStmt);
        $parentResult = mysqli_stmt_get_result($parentCheckStmt);
        $parentRow = mysqli_fetch_assoc($parentResult);
        $parentId = $parentRow ? $parentRow['parent_id'] : null;
        mysqli_stmt_close($parentCheckStmt);
    }

    // If this is a main category, fetch its subcategories
    if ($parentId === null) {
        $categoryList[] = $categoryId; // Include main category

        // Get all its subcategories
        $subQuery = "SELECT category_id FROM categories WHERE parent_id = ?";
        $subStmt = mysqli_prepare($link, $subQuery);
        
        if ($subStmt) {
            mysqli_stmt_bind_param($subStmt, "i", $categoryId);
            mysqli_stmt_execute($subStmt);
            $subResult = mysqli_stmt_get_result($subStmt);
            
            while ($subRow = mysqli_fetch_assoc($subResult)) {
                $categoryList[] = $subRow['category_id']; // Add subcategories
            }

            mysqli_stmt_close($subStmt);
        }
    } else {
        // If it's a subcategory, show only its products
        $categoryList[] = $categoryId;
    }
}

// Prepare SQL query
if (!empty($categoryList)) {
    $placeholders = implode(',', array_fill(0, count($categoryList), '?'));
    $query = "SELECT DISTINCT p.* FROM products p
              JOIN product_categories pc ON p.product_id = pc.product_id
              WHERE pc.category_id IN ($placeholders) 
              AND p.name LIKE ?
              ORDER BY p.created_at DESC";
    
    $stmt = mysqli_prepare($link, $query);

    if ($stmt) {
        $types = str_repeat('i', count($categoryList)) . 's';
        $params = array_merge($categoryList, ["%$searchQuery%"]);

        // Bind parameters correctly
        $refParams = [];
        $refParams[] = &$types;
        foreach ($params as $key => $value) {
            $refParams[] = &$params[$key];
        }

        call_user_func_array([$stmt, 'bind_param'], $refParams);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        die("Database error: " . mysqli_error($link));
    }
} else {
    // If no category is selected, show all products
    $query = "SELECT * FROM products WHERE name LIKE ? ORDER BY created_at DESC";
    $stmt = mysqli_prepare($link, $query);

    if ($stmt) {
        $searchParam = "%$searchQuery%";
        mysqli_stmt_bind_param($stmt, "s", $searchParam);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        die("Database error: " . mysqli_error($link));
    }
}
?>

<h1>Tervetuloa Hello Kitty kauppaamme</h1>

<?php if (!empty($searchQuery)): ?>
    <h2>Search Results for "<?php echo htmlspecialchars($searchQuery); ?>"</h2>
<?php elseif ($categoryId > 0): ?>
    <h2>Category: <?php echo htmlspecialchars(getCategoryName($categoryId, $link)); ?></h2>
<?php else: ?>
    <h2>Our Products</h2>
<?php endif; ?>

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
        echo "<p>No products found.</p>";
    }
    ?>
</div>

<?php
// Function to get category name
function getCategoryName($categoryId, $link)
{
    $stmt = mysqli_prepare($link, "SELECT name FROM categories WHERE category_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $categoryId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row ? $row['name'] : "Unknown Category";
}

mysqli_stmt_close($stmt);
mysqli_close($link);
?>