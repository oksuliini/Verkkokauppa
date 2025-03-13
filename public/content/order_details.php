<?php

if (!isset($_GET['order_id'])) {
    header("Location: index.php?page=profile"); 
    exit();
}

$order_id = intval($_GET['order_id']);
$link = getDbConnection();

// Hae tilauksen tiedot
$orderQuery = "SELECT o.order_id, o.total_price, o.status, o.delivery_method, o.created_at, u.first_name, u.last_name, u.email 
               FROM orders o
               JOIN users u ON o.user_id = u.user_id
               WHERE o.order_id = ?";
$stmt = mysqli_prepare($link, $orderQuery);
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    echo "<p>Order not found.</p>";
    exit();
}

// Hae tilauksen tuotteet
$itemsQuery = "SELECT oi.product_id, p.name, oi.quantity, oi.price 
               FROM order_items oi
               JOIN products p ON oi.product_id = p.product_id
               WHERE oi.order_id = ?";
$stmt = mysqli_prepare($link, $itemsQuery);
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$orderItems = [];
while ($row = mysqli_fetch_assoc($result)) {
    $orderItems[] = $row;
}

mysqli_stmt_close($stmt);
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
</head>
<body>
    <h1>Order Details</h1>
    <p><strong>Order ID:</strong> <?php echo $order['order_id']; ?></p>
    <p><strong>Total Price:</strong> <?php echo number_format($order['total_price'], 2); ?> €</p>
    <p><strong>Status:</strong> <?php echo ucfirst($order['status']); ?></p>
    <p><strong>Delivery Method:</strong> <?php echo ucfirst($order['delivery_method']); ?></p>
    <p><strong>Order Date:</strong> <?php echo $order['created_at']; ?></p>
    <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['first_name'] . " " . $order['last_name']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>

    <h2>Ordered Products</h2>
    <?php if (empty($orderItems)): ?>
        <p>No products found.</p>
    <?php else: ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderItems as $item): ?>
                    <tr>
                        <td><?php echo $item['product_id']; ?></td>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo number_format($item['price'], 2); ?> €</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <br>
    <a href="index.php?page=profile">Back to Profile</a>
</body>
</html>
