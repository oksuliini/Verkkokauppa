<?php
if ($_SESSION['SESS_ROLE'] !== 'admin') {
    header("Location: ../errors/403.php");
    exit();
}
$link = getDbConnection();
$user_id = $_SESSION['SESS_USER_ID'];
$query = "SELECT first_name, last_name, email FROM users WHERE user_id = ?";
$stmt = $link->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $email);
$stmt->fetch();
$stmt->close();

// Hae kaikki tilaukset
$ordersQuery = "SELECT order_id, user_id, total_price, status, delivery_method, created_at FROM orders ORDER BY created_at DESC";
$result = mysqli_query($link, $ordersQuery);
$orders = [];

while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}

mysqli_close($link);
?>
    <h1>Welcome, Admin</h1>
    <h2>Your Profile</h2>
    <p>First Name: <?php echo htmlspecialchars($first_name); ?></p>
    <p>Last Name: <?php echo htmlspecialchars($last_name); ?></p>
    <p>Email: <?php echo htmlspecialchars($email); ?></p>

    <h2>Order History</h2>
    <?php if (empty($orders)): ?>
        <p>No orders found.</p>
    <?php else: ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>User ID</th>
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
                        <td><?php echo $order['user_id']; ?></td>
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

    <form action="index.php?page=add_product" method="post">
        <button type="submit" class="btn btn-hotpink mt-2">Add Products</button>
    </form>

    <form action="content/logout.php" method="post">
        <button type="submit" class="btn btn-secondary mt-2">Logout</button>
    </form>
    
    

