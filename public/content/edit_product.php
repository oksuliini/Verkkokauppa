<?php

// Ensure only admins can access this page
if (!isset($_SESSION['SESS_USER_ID']) || $_SESSION['SESS_ROLE'] !== 'admin') {
    header("Location: index.php?page=login");
    exit();
}

// Get the product ID from the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Invalid product ID.</p>";
    exit();
}

$productId = intval($_GET['id']);
$link = getDbConnection();

// Fetch the product details
$query = "SELECT * FROM products WHERE product_id = ?";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "i", $productId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$row = mysqli_fetch_assoc($result)) {
    echo "<p>Product not found.</p>";
    exit();
}

// Fetch the current category ID for the product
$categoryQuery = "SELECT category_id FROM product_categories WHERE product_id = ?";
$categoryStmt = mysqli_prepare($link, $categoryQuery);
mysqli_stmt_bind_param($categoryStmt, "i", $productId);
mysqli_stmt_execute($categoryStmt);
$categoryResult = mysqli_stmt_get_result($categoryStmt);
$categoryRow = mysqli_fetch_assoc($categoryResult);
$currentCategoryId = $categoryRow['category_id'] ?? null;

// Fetch all categories for the dropdown
$allCategoriesQuery = "SELECT * FROM categories";
$categoryResult = mysqli_query($link, $allCategoriesQuery);

mysqli_close($link);
?>

<div class="container">
    <h2>Edit Product</h2>
    <form action="content/edit_product_process.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
        
        <div class="mb-3">
            <label class="form-label">Product Name</label>
            <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" required><?php echo htmlspecialchars($row['description']); ?></textarea>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Price (€)</label>
            <input type="number" class="form-control" name="price" step="0.01" value="<?php echo $row['price']; ?>" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Stock Quantity</label>
            <input type="number" class="form-control" name="stock_quantity" value="<?php echo $row['stock_quantity']; ?>" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Category</label>
            <select class="form-control" name="category_id" required>
                <?php while ($category = mysqli_fetch_assoc($categoryResult)) { ?>
                    <option value="<?php echo $category['category_id']; ?>" 
                        <?php echo ($category['category_id'] == $currentCategoryId) ? 'selected' : ''; ?> >
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Product Image</label>
            <input type="file" class="form-control" name="image">
            <p>Current Image:</p>
            <img src="<?php echo $row['image_url']; ?>" alt="Product Image" style="height: 100px;">
        </div>
        
        <button type="submit" class="btn btn-hotpink mt-2">Update Product</button>
        <a href="index.php?page=etusivu" class="btn btn-secondary">Cancel</a>
                </form>
        <!-- Delete Button -->
        <form action="content/delete_product.php" method="POST"
      onsubmit="return confirm('Are you sure you want to delete this product?');">
    <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
    <button type="submit" class="btn btn-danger mt-3">Delete Product</button>
</form>
</div>

