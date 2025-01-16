<?php
session_start();

// Tarkista, onko lomake lähetetty
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Tarkista, onko tarvittavat tiedot lähetetty
    if (isset($_POST['product_id'], $_POST['quantity'])) {
        $productId = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity']);

        // Varmista, että ostoskori on olemassa ja tuote on korissa
        if (isset($_SESSION['cart']) && isset($_SESSION['cart'][$productId])) {
            if ($quantity > 0) {
                // Päivitä tuotteen määrä
                $_SESSION['cart'][$productId]['quantity'] = $quantity;

                // Ohjaa takaisin ostoskorisivulle onnistuneen päivityksen jälkeen
                header("Location: ../index.php?page=cart&success=Cart updated successfully!");
                exit();
            } else {
                // Jos määrä on 0 tai vähemmän, poista tuote korista
                unset($_SESSION['cart'][$productId]);

                // Tarkista, onko ostoskori tyhjä ja poista se, jos on
                if (empty($_SESSION['cart'])) {
                    unset($_SESSION['cart']);
                }

                // Ohjaa takaisin ostoskorisivulle
                header("Location: ../index.php?page=cart&success=Product removed from cart!");
                exit();
            }
        } else {
            // Tuotetta ei löytynyt korista
            header("Location: ../index.php?page=cart&error=Product not found in cart.");
            exit();
        }
    } else {
        // Virhe: tarvittavat tiedot puuttuvat
        header("Location: ../index.php?page=cart&error=Missing product data.");
        exit();
    }
} else {
    // Estä suora pääsy
    header("Location: ../index.php?page=cart");
    exit();
}
