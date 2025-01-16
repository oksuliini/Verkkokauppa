<?php
session_start();
require_once('../../config/config.php'); // Polku konfiguraatiotiedostoon

// Tarkista, onko lomake lähetetty
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Varmista, että kaikki tarvittavat tiedot on lähetetty
    if (isset($_POST['product_id'], $_POST['name'], $_POST['price'], $_POST['quantity'])) {
        $productId = intval($_POST['product_id']);
        $name = htmlspecialchars($_POST['name']);
        $price = floatval($_POST['price']);
        $quantity = intval($_POST['quantity']);

        // Tarkista, että määrä on vähintään 1
        if ($quantity < 1) {
            $quantity = 1;
        }

        // Jos sessiossa ei ole vielä ostoskoria, luodaan se
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Tarkista, onko tuote jo korissa
        if (isset($_SESSION['cart'][$productId])) {
            // Päivitä olemassa olevan tuotteen määrä
            $_SESSION['cart'][$productId]['quantity'] += $quantity;
        } else {
            // Lisää uusi tuote ostoskoriin
            $_SESSION['cart'][$productId] = [
                'name' => $name,
                'price' => $price,
                'quantity' => $quantity,
            ];
        }

        // Ohjaa takaisin tuotesivulle onnistuneen lisäyksen jälkeen
        header("Location: ../index.php?page=etusivu&success=Product added to cart!");
        exit();
    } else {
        // Virheilmoitus puuttuvista tiedoista
        header("Location: ../index.php?page=etusivu&error=Missing product data.");
        exit();
    }
} else {
    // Ohjaa takaisin, jos yritetään suoraa pääsyä ilman lomakkeen lähetystä
    header("Location: ../index.php?page=etusivu");
    exit();
}
?>
