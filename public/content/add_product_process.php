<?php
session_start();
require_once('../../config/config.php');

// Tarkista, onko käyttäjä admin
if (!isset($_SESSION['SESS_ROLE']) || $_SESSION['SESS_ROLE'] !== 'admin') {
    header("Location: ../../errors/403.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $link = getDbConnection();

    // Lomakkeen tiedot
    $name = $_POST['name'] ?? ''; 
    $description = $_POST['description'] ?? ''; 
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
    $stock_quantity = isset($_POST['stock_quantity']) && is_numeric($_POST['stock_quantity']) ? intval($_POST['stock_quantity']) : 0;
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $subcategory_id = isset($_POST['subcategory_id']) ? intval($_POST['subcategory_id']) : null;  // Optional subcategory

    // Tarkista, onko kategoria valittu
    if (!$category_id) {
        header("Location: ../index.php?page=add_product&error=Category is required.");
        exit();
    }

    // Tiedoston käsittely
    $targetDir = "../images/"; // Varmista, että tämä polku on oikea
    $imageName = basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . $imageName;

    // Hyväksytyt tiedostotyypit
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png'];

    // Optional: Verify file size (e.g., limit to 5MB)
    if ($_FILES["image"]["size"] > 5000000) { // 5MB
        header("Location: ../index.php?page=add_product&error=File is too large.");
        exit();
    }

    if (!in_array($fileType, $allowedTypes)) {
        header("Location: ../index.php?page=add_product&error=Invalid file type.");
        exit();
    }

    // Try moving the uploaded file
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        $image_url = "images/" . $imageName; // Suhteellinen polku kuvalle
    } else {
        header("Location: ../index.php?page=add_product&error=Image upload failed.");
        exit();
    }

    // Lisää tuote tietokantaan (Using prepared statements)
    $query = "INSERT INTO products (name, description, price, stock_quantity, image_url) 
              VALUES (?, ?, ?, ?, ?)";
    
    if ($stmt = mysqli_prepare($link, $query)) {
        mysqli_stmt_bind_param($stmt, "ssdis", $name, $description, $price, $stock_quantity, $image_url);

        if (mysqli_stmt_execute($stmt)) {
            $productId = mysqli_insert_id($link); // Hanki juuri lisätyn tuotteen ID

            // Add product category using prepared statement
            $categoryQuery = "INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)";
            if ($categoryStmt = mysqli_prepare($link, $categoryQuery)) {
                mysqli_stmt_bind_param($categoryStmt, "ii", $productId, $category_id);
                if (!mysqli_stmt_execute($categoryStmt)) {
                    header("Location: ../index.php?page=add_product&error=Failed to add product category.");
                    exit();
                }
            } else {
                header("Location: ../index.php?page=add_product&error=Database error while adding category.");
                exit();
            }

            // If subcategory is selected, insert that as well
            if ($subcategory_id) {
                $subcategoryQuery = "INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)";
                if ($subcategoryStmt = mysqli_prepare($link, $subcategoryQuery)) {
                    mysqli_stmt_bind_param($subcategoryStmt, "ii", $productId, $subcategory_id);
                    if (!mysqli_stmt_execute($subcategoryStmt)) {
                        header("Location: ../index.php?page=add_product&error=Failed to add product subcategory.");
                        exit();
                    }
                }
            }

            header("Location: ../index.php?page=add_product&success=Product added successfully!");
            exit();
        } else {
            header("Location: ../index.php?page=add_product&error=Failed to add product.");
            exit();
        }
    } else {
        header("Location: ../index.php?page=add_product&error=Database error.");
        exit();
    }

    // Close database connection
    mysqli_close($link);
} else {
    header("Location: ../index.php?page=add_product");
    exit();
}
?>
