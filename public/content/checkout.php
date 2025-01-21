<?php


// Varmista, että ostoskori ei ole tyhjä
if (empty($_SESSION['cart'])) {
    header("Location: index.php?page=cart");
    exit();
}

// Lasketaan ostoskorin kokonaissumma
$total = 0;
foreach ($_SESSION['cart'] as $productId => $item) {
    $total += $item['price'] * $item['quantity'];
}

?>
   
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Kassa</h1>

        <!-- Yhteenveto ostoskorista -->
        <h2 class="mb-3">Ostoskorin sisältö</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tuote</th>
                        <th>Hinta</th>
                        <th>Määrä</th>
                        <th>Yhteensä</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $productId => $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo number_format($item['price'], 2); ?> €</td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo number_format($item['price'] * $item['quantity'], 2); ?> €</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Kokonaissumma:</strong></td>
                        <td><strong><?php echo number_format($total, 2); ?> €</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Maksulomake -->
        <h2 class="mt-4">Maksutiedot</h2>
        <form action="content/checkout_process.php" method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Nimi</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Osoite</label>
                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Sähköposti</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <label for="delivery_method">Toimitustapa:</label>
    <select name="delivery_method" id="delivery_method" required>
        <option value="pickup">Nouto</option>
        <option value="shipping">Toimitus</option>
    </select>
            <div class="mb-3">
                <label for="payment_method" class="form-label">Maksutapa</label>
                <select class="form-select" id="payment_method" name="payment_method" required>
                    <option value="credit_card">Luottokortti</option>
                    <option value="paypal">PayPal</option>
                    <option value="bank_transfer">Tilisiirto</option>
                </select>
            </div>

            <button type="submit" class="btn btn-hotpink mt-2">Viimeistele tilaus</button>
        </form>

        <div class="mt-3">
            <a href="index.php?page=cart" class="btn btn-secondary">Palaa ostoskoriin</a>
        </div>
    </div>
    <style>
.btn-hotpink {
    background-color: hotpink;
    color: white;
    border: none;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.btn-hotpink:hover {
    background-color: #ff69b4;
    transform: scale(1.1);
    color: white;
}

.btn-hotpink:focus {
    box-shadow: 0 0 0 0.25rem rgba(255, 105, 180, 0.5);
    outline: none;
}
</style>
