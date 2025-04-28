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
    $name = mysqli_real_escape_string($link, $_POST['name']);
    $description = mysqli_real_escape_string($link, $_POST['description']);
    $price = floatval($_POST['price']);
    $stock_quantity = isset($_POST['stock_quantity']) && is_numeric($_POST['stock_quantity']) ? intval($_POST['stock_quantity']) : 0;
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : null;

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

    if (!in_array($fileType, $allowedTypes)) {
        header("Location: ../index.php?page=add_product&error=Invalid file type.");
        exit();
    }

if ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
    die("Upload error: " . $_FILES["image"]["error"]);
}
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        $image_url = "images/" . $imageName; // Suhteellinen polku kuvalle
    } else {
        header("Location: ../index.php?page=add_product&error=Image upload failed.");
        exit();
    }

    // Lisää tuote tietokantaan
    $query = "INSERT INTO products (name, description, price, stock_quantity, image_url) 
              VALUES ('$name', '$description', $price, $stock_quantity, '$image_url')";

    if (mysqli_query($link, $query)) {
        $productId = mysqli_insert_id($link); // Hanki juuri lisätyn tuotteen ID

        // Lisää tuotteen kategoria
        $categoryQuery = "INSERT INTO product_categories (product_id, category_id) 
                          VALUES ($productId, $category_id)";
        if (mysqli_query($link, $categoryQuery)) {
            header("Location: ../index.php?page=add_product&success=Product added successfully!");
            exit();
        } else {
            header("Location: ../index.php?page=add_product&error=Failed to add product category.");
            exit();
        }
    } else {
        header("Location: ../index.php?page=add_product&error=Failed to add product.");
        exit();
    }

    mysqli_close($link);
} else {
    header("Location: ../index.php?page=add_product");
    exit();
}

