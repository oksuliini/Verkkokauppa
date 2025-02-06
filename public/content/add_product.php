<?php
// Tarkista, onko käyttäjä admin
if (!isset($_SESSION['SESS_ROLE']) || $_SESSION['SESS_ROLE'] !== 'admin') {
    header("Location: ../../errors/403.php");
    exit();
}

// Hae kaikki kategoriat tietokannasta
$link = getDbConnection();
$query = "SELECT * FROM categories ORDER BY name ASC";
$result = mysqli_query($link, $query);

if (!$result) {
    die("Error fetching categories: " . mysqli_error($link));
}
?>
<h1>Add a New Product</h1>

<!-- Näytä onnistumis- tai virheilmoitus -->
<?php if (isset($_GET['success'])): ?>
    <p style="color: green;"><?php echo htmlspecialchars($_GET['success']); ?></p>
<?php elseif (isset($_GET['error'])): ?>
    <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
<?php endif; ?>

<form action="content/add_product_process.php" method="POST" enctype="multipart/form-data">
    <label for="name">Product Name:</label>
    <input type="text" id="name" name="name" required><br>

    <label for="description">Description:</label>
    <textarea id="description" name="description" required></textarea><br>

    <label for="price">Price:</label>
    <input type="number" step="0.01" id="price" name="price" required><br>

    <label for="stock_quantity">Stock Quantity:</label>
    <input type="number" id="stock_quantity" name="stock_quantity" min="0" required><br>

    <label for="image">Product Image:</label>
    <input type="file" id="image" name="image" accept="image/*" required><br>

    <label for="category">Category:</label>
    <select id="category" name="category_id" required>
        <option value="">Select a category</option>
        <?php
        // Lisää kategoriat valintalistaan
        while ($row = mysqli_fetch_assoc($result)) {
            $categoryId = $row['category_id'];
            $categoryName = htmlspecialchars($row['name']);
            echo "<option value='$categoryId'>$categoryName</option>";
        }
        ?>
    </select><br>

    <button type="submit" class="btn btn-hotpink mt-2">Add Product</button>
</form>



  