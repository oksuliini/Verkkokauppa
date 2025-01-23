<?php
session_start();
require_once('../../config/config.php');
// Tarkista, että ostoskori ei ole tyhjä
if (empty($_SESSION['cart'])) {
    header("Location: ../index.php?page=cart");
    exit();
}

// Tarkista, että lomake lähetettiin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $link = getDbConnection();

    // Hae lomakkeelta tiedot
    $userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null; // Käyttäjän ID, jos kirjautunut
    $deliveryMethod = mysqli_real_escape_string($link, $_POST['delivery_method']);
    $name = mysqli_real_escape_string($link, $_POST['name']);
    $address = mysqli_real_escape_string($link, $_POST['address']);
    $email = mysqli_real_escape_string($link, $_POST['email']);

    // Aloita tietokantatransaktio
    mysqli_begin_transaction($link);

    try {
        // Laske tilauksen kokonaissumma
        $total = 0;
        foreach ($_SESSION['cart'] as $productId => $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Lisää tilaus tietokantaan
        $orderQuery = "INSERT INTO orders (user_id, total_price, delivery_method, created_at) 
        VALUES (" . ($userId === null ? 'NULL' : $userId) . ", $total, '$deliveryMethod', NOW())";


        if (!mysqli_query($link, $orderQuery)) {
            throw new Exception("Tilausta ei voitu tallentaa: " . mysqli_error($link));
        }

        // Hae luodun tilauksen ID
        $orderId = mysqli_insert_id($link);

        // Lisää jokainen tuote tilauksen tuotteisiin ja päivitä varastomäärä
        foreach ($_SESSION['cart'] as $productId => $item) {
            $quantity = $item['quantity'];
            $price = $item['price'];

            // Lisää tilauksen tuotteet
            $orderItemQuery = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                               VALUES ($orderId, $productId, $quantity, $price)";
            if (!mysqli_query($link, $orderItemQuery)) {
                throw new Exception("Tuotteen lisääminen tilaukseen epäonnistui: " . mysqli_error($link));
            }

            // Päivitä tuotteen varastomäärä
            $updateStockQuery = "UPDATE products SET stock_quantity = stock_quantity - $quantity 
                                 WHERE product_id = $productId AND stock_quantity >= $quantity";
            if (!mysqli_query($link, $updateStockQuery)) {
                throw new Exception("Varaston päivitys epäonnistui: " . mysqli_error($link));
            }

            // Tarkista, että varastoa ei ole alle nollan
            if (mysqli_affected_rows($link) === 0) {
                throw new Exception("Tuotetta ei ole tarpeeksi varastossa: " . htmlspecialchars($item['name']));
            }
        }

        // Jos kaikki onnistui, suorita transaktio
        mysqli_commit($link);

        // Tyhjennä ostoskori
        unset($_SESSION['cart']);

        // Ohjaa kiitossivulle
        header("Location: ../index.php?page=order_success&order_id=$orderId");
        exit();
    } catch (Exception $e) {
        // Jos virhe tapahtuu, peru transaktio
        mysqli_rollback($link);
        echo "<p>Virhe: " . $e->getMessage() . "</p>";
        echo '<a href="../index.php?page=cart">Palaa ostoskoriin</a>';
    } finally {
        mysqli_close($link);
    }
} else {
    // Jos sivulle pääsy ilman POST-pyyntöä, palauta käyttäjä ostoskoriin
    header("Location: ../index.php?page=cart");
    exit();
}
