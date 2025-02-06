<?php
// Check if the user is logged in
if (!isset($_SESSION['SESS_USER_ID'])) {
    header("Location: index.php?page=login"); 
    exit();
}

$user_id = $_SESSION['SESS_USER_ID'];

// Connect to the database
$link = getDbConnection();

// Fetch user details from the database
$query = "SELECT first_name, address, email FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $first_name, $address, $email);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
mysqli_close($link);

// Tarkista, että ostoskori ei ole tyhjä
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
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($first_name);?>" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Osoite</label>
                <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($address);?></textarea>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Sähköposti</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email);?>" required>
            </div>
            <div class="mb-3">
                <label for="delivery_method" class="form-label">Toimitustapa</label>
                <select class="form-select" id="delivery_method" name="delivery_method" required>
                    <option value="pickup">Nouto</option>
                    <option value="shipping">Toimitus</option>
                </select>
            </div>
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


