<?php
session_start();

// Tarkista, onko lomake lähetetty
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Tarkista, onko tuotteen ID lähetetty
    if (isset($_POST['product_id'])) {
        $productId = intval($_POST['product_id']);

        // Varmista, että ostoskori on olemassa
        if (isset($_SESSION['cart']) && isset($_SESSION['cart'][$productId])) {
            // Poista tuote ostoskorista
            unset($_SESSION['cart'][$productId]);

            // Tarkista, onko ostoskori tyhjä ja poista se, jos on
            if (empty($_SESSION['cart'])) {
                unset($_SESSION['cart']);
            }

            // Ohjaa takaisin ostoskorisivulle onnistuneen poistamisen jälkeen
            header("Location: ../index.php?page=cart&success=Product removed from cart!");
            exit();
        } else {
            // Tuotetta ei löytynyt korista
            header("Location: ../index.php?page=cart&error=Product not found in cart.");
            exit();
        }
    } else {
        // Virheilmoitus, jos tuotteen ID puuttuu
        header("Location: ../index.php?page=cart&error=Missing product ID.");
        exit();
    }
} else {
    // Ohjaa takaisin, jos yritetään suoraa pääsyä ilman lomakkeen lähetystä
    header("Location: ../index.php?page=cart");
    exit();
}
