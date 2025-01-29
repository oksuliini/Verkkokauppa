<div class="container mt-5">
    <h1 class="mb-4">Ostoskorisi</h1>

    <?php if (!empty($_SESSION['cart'])): ?>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tuote</th>
                        <th>Hinta</th>
                        <th>Määrä</th>
                        <th>Yhteensä</th>
                        <th>Toiminnot</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    foreach ($_SESSION['cart'] as $productId => $item):
                        $itemTotal = $item['price'] * $item['quantity'];
                        $total += $itemTotal;
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo number_format($item['price'], 2); ?> €</td>
                            <td>
                                <form action="content/cart_update.php" method="post" class="d-inline">
                                    <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="form-control d-inline" style="width: 70px;">
                                    <button type="submit" class="btn btn-hotpink mt-2">Päivitä</button>
                                </form>
                            </td>
                            <td><?php echo number_format($itemTotal, 2); ?> €</td>
                            <td>
                                <form action="content/cart_remove.php" method="post">
                                    <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Poista</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Kokonaissumma:</strong></td>
                        <td colspan="2"><strong><?php echo number_format($total, 2); ?> €</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="text-end">
            <a href="index.php?page=checkout" class="btn btn-hotpink mt-2">Jatka kassalle</a>
        </div>
    <?php else: ?>
        <p>Ostoskorisi on tyhjä.</p>
        <a href="index.php?page=etusivu" class="btn btn-hotpink mt-2">Jatka ostoksia</a>
    <?php endif; ?>
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
