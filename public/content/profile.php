<?php
// Tarkista, että käyttäjä on kirjautunut
if (!isset($_SESSION['SESS_USER_ID'])) {
    header("Location: index.php?page=login"); 
    exit();
}

$user_id = $_SESSION['SESS_USER_ID'];
$link = getDbConnection();

// Hae käyttäjän tiedot
$query = "SELECT first_name, last_name, email, phone, address FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $first_name, $last_name, $email, $phone, $address);
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

<!-- Profiilin tiedot -->
<form id="profileForm" action="content/update_profile.php" method="post">
    <p><strong>First Name:</strong> <span id="first_name_display"><?php echo htmlspecialchars($first_name); ?></span></p>
    <input type="text" id="first_name_input" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" style="display: none;">

    <p><strong>Last Name:</strong> <span id="last_name_display"><?php echo htmlspecialchars($last_name); ?></span></p>
    <input type="text" id="last_name_input" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" style="display: none;">

    <p><strong>Email:</strong> <span id="email_display"><?php echo htmlspecialchars($email); ?></span></p>
    <input type="email" id="email_input" name="email" value="<?php echo htmlspecialchars($email); ?>" style="display: none;">

    <p><strong>Phone:</strong> <span id="phone_display"><?php echo htmlspecialchars($phone); ?></span></p>
    <input type="text" id="phone_input" name="phone" value="<?php echo htmlspecialchars($phone); ?>" style="display: none;">

    <p><strong>Address:</strong> <span id="address_display"><?php echo nl2br(htmlspecialchars($address)); ?></span></p>
    <textarea id="address_input" name="address" style="display: none;"><?php echo htmlspecialchars($address); ?></textarea>

    <button type="button" class="btn btn-hotpink mt-2" id="editButton" onclick="enableEdit()">Edit Profile</button>
    <button type="submit" class="btn btn-hotpink mt-2" id="saveButton" style="display: none;">Save Changes</button>
</form>

<!-- Linkki salasanan vaihtoon -->
<h2>Change Password</h2>
<p>If you want to change your password, click the button below:</p>
<a href="index.php?page=change_password_form" class="btn btn-hotpink mt-2">Change Password</a>

<!-- Tilaushistoria -->
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

<!-- Logout -->
<br>
<form action="content/logout.php" method="post">
    <button type="submit" class="btn btn-secondary">Logout</button>
</form>

<!-- JavaScript muokkauksen mahdollistamiseen -->
<script>
function enableEdit() {
    document.getElementById('first_name_display').style.display = 'none';
    document.getElementById('first_name_input').style.display = 'inline';

    document.getElementById('last_name_display').style.display = 'none';
    document.getElementById('last_name_input').style.display = 'inline';

    document.getElementById('email_display').style.display = 'none';
    document.getElementById('email_input').style.display = 'inline';

    document.getElementById('phone_display').style.display = 'none';
    document.getElementById('phone_input').style.display = 'inline';

    document.getElementById('address_display').style.display = 'none';
    document.getElementById('address_input').style.display = 'inline';

    document.getElementById('editButton').style.display = 'none';
    document.getElementById('saveButton').style.display = 'inline';
}
</script>
