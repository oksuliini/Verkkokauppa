<?php

// Tarkista, että käyttäjä on kirjautunut
if (!isset($_SESSION['SESS_USER_ID'])) {
    header("Location: index.php?page=login"); 
    exit();
}

$user_id = $_SESSION['SESS_USER_ID'];

// Yhdistä tietokantaan
$link = getDbConnection();

// Hae käyttäjän tiedot
$query = "SELECT first_name, last_name, email FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $first_name, $last_name, $email);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Hae käyttäjän tilaushistoria
$ordersQuery = "SELECT order_id, total_price, status, delivery_method, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($link, $ordersQuery);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}
mysqli_stmt_close($stmt);
mysqli_close($link);
?>


    <h1>User Profile</h1>
    <p><strong>First Name:</strong> <?php echo htmlspecialchars($first_name); ?></p>
    <p><strong>Last Name:</strong> <?php echo htmlspecialchars($last_name); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>

    <h2>Order History</h2>

    <?php if (empty($orders)): ?>
        <p>No orders found.</p>
    <?php else: ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Delivery Method</th>
                    <th>Order Date</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo $order['order_id']; ?></td>
                        <td><?php echo number_format($order['total_price'], 2); ?> €</td>
                        <td><?php echo ucfirst($order['status']); ?></td>
                        <td><?php echo ucfirst($order['delivery_method']); ?></td>
                        <td><?php echo $order['created_at']; ?></td>
                        <td><a href="index.php?page=order_details&order_id=<?php echo $order['order_id']; ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h2>Change Your Password</h2>
    <p>If you want to change your password, click the button below:</p>
    <form action="index.php?page=update_profile" method="post">
        <button type="submit" class="btn btn-hotpink mt-2">Update Profile</button>
    </form>

    <br>
    <form action="content/logout.php" method="post">
        <button type="submit" class="btn btn-secondary">Logout</button>
    </form>

