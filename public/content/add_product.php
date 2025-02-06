<?php
// Tarkista, onko käyttäjä admin
if (!isset($_SESSION['SESS_ROLE']) || $_SESSION['SESS_ROLE'] !== 'admin') {
    header("Location: ../../errors/403.php");
    exit();
}

// Hae kaikki kategoriat tietokannasta
$link = getDbConnection();
$query = "SELECT * FROM categories WHERE parent_id IS NULL ORDER BY name ASC"; // Get main categories
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
    <select id="category" name="category_id" required onchange="fetchSubcategories(this.value, 'subcategory')">
        <option value="">Select a category</option>
        <?php
        $query = "SELECT * FROM categories WHERE parent_id IS NULL ORDER BY name ASC";
        $result = mysqli_query($link, $query);

        while ($row = mysqli_fetch_assoc($result)) {
            $categoryId = $row['category_id'];
            $categoryName = htmlspecialchars($row['name']);
            echo "<option value='$categoryId'>$categoryName</option>";
        }
        ?>
    </select><br>

    <label for="subcategory">Subcategory (Optional):</label>
    <select id="subcategory" name="subcategory_id">
        <option value="">Select a subcategory</option>
    </select><br>

    <button type="submit">Add Product</button>

</form>

<script>
// Fetch subcategories based on the selected main category
function fetchSubcategories(categoryId, targetElement) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'content/fetch_subcategories.php?category_id=' + categoryId, true);
    xhr.onload = function() {
        if (xhr.status == 200) {
            var subcategories = JSON.parse(xhr.responseText);
            if (targetElement) {
                // If targetElement exists, we are populating the subcategory dropdown for the form
                var subcategorySelect = document.getElementById(targetElement);
                subcategorySelect.innerHTML = "<option value=''>Select a subcategory</option>"; // Reset subcategories
                subcategories.forEach(function(subcategory) {
                    var option = document.createElement("option");
                    option.value = subcategory.category_id;
                    option.textContent = subcategory.name;
                    subcategorySelect.appendChild(option);
                });
            } else {
                // If targetElement is not passed, we're updating the navbar dropdown
                var dropdownMenu = document.getElementById('navbarDropdown').nextElementSibling;
                dropdownMenu.innerHTML = ''; // Clear existing dropdown items

                // Add main category back to the dropdown
                var mainCategory = document.createElement('li');
                mainCategory.innerHTML = '<a class="dropdown-item" href="#">Back to Categories</a>';
                dropdownMenu.appendChild(mainCategory);

                // Add the subcategories to the dropdown
                subcategories.forEach(function(subcategory) {
                    var subcategoryItem = document.createElement('li');
                    subcategoryItem.innerHTML = `<a class="dropdown-item" href="index.php?page=etusivu&category=${subcategory.category_id}">${subcategory.name}</a>`;
                    dropdownMenu.appendChild(subcategoryItem);
                });
            }
        }
    };
    xhr.send();
}

</script>

<?php mysqli_close($link); ?>
