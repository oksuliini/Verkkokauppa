<div class="cart-container container mt-5">
    <h1 class="mb-4 text-center">🛒 Ostoskorisi</h1>

    <?php if (!empty($_SESSION['cart'])): ?>
        <div class="table-responsive">
            <table class="table table-bordered shadow-sm rounded" >
                <thead class="table-light">
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
                            <td class="align-middle"><?php echo htmlspecialchars($item['name']); ?></td>
                            <td class="align-middle"><?php echo number_format($item['price'], 2); ?> €</td>
                            <td class="align-middle">
                                <form action="content/cart_update.php" method="post" class="d-flex justify-content-center align-items-center gap-2">
                                    <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="form-control text-center" style="width: 80px;">
                                    <button type="submit" class="btn btn-hotpink btn-sm">Päivitä</button>
                                </form>
                            </td>
                            <td class="align-middle"><?php echo number_format($itemTotal, 2); ?> €</td>
                            <td class="align-middle text-center">
                                <form action="content/cart_remove.php" method="post">
                                    <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm">❌ Poista</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end fs-5"><strong>Kokonaissumma:</strong></td>
                        <td colspan="2" class="fs-5"><strong><?php echo number_format($total, 2); ?> €</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="text-end mt-4">
            <a href="index.php?page=checkout" class="btn btn-lg btn-success shadow-sm px-4 py-2">
                ✅ Jatka kassalle
            </a>
        </div>
    <?php else: ?>
        <div class="text-center p-5">
            <p class="fs-4">🛍️ Ostoskorisi on tyhjä.</p>
            <a href="index.php?page=etusivu" class="btn btn-hotpink btn-lg mt-3">Jatka ostoksia</a>
        </div>
    <?php endif; ?>
</div>
