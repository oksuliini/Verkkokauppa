<?php
// Check if user is logged in
if (!isset($_SESSION['SESS_USER_ID'])) {
    header("Location: index.php?page=login"); 
    exit();
}

$user_id = $_SESSION['SESS_USER_ID'];
$link = getDbConnection();

// Fetch user data
$query = "SELECT first_name, last_name, email, phone, address FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $first_name, $last_name, $email, $phone, $address);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Fetch user order history
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

<div class="container mt-5 profile-page">
    <h1 class="text-center mb-4">Your Profile</h1>

    <!-- Profile information card -->
    <div class="card profile-card">
    <div class="card-body">
        <h2 class="card-title text-center">User Information</h2>
        <form id="profileForm" action="content/update_profile.php" method="post">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="first_name" class="form-label">First Name</label>
                        <p id="first_name_display"><?php echo htmlspecialchars($first_name); ?></p>
                        <input type="text" id="first_name_input" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" class="form-control" style="display: none;">
                    </div>
                    <div class="form-group">
                        <label for="last_name" class="form-label">Last Name</label>
                        <p id="last_name_display"><?php echo htmlspecialchars($last_name); ?></p>
                        <input type="text" id="last_name_input" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" class="form-control" style="display: none;">
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <p id="email_display"><?php echo htmlspecialchars($email); ?></p>
                        <input type="email" id="email_input" name="email" value="<?php echo htmlspecialchars($email); ?>" class="form-control" style="display: none;">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="phone" class="form-label">Phone Number</label>
                        <p id="phone_display"><?php echo htmlspecialchars($phone); ?></p>
                        <input type="text" id="phone_input" name="phone" value="<?php echo htmlspecialchars($phone); ?>" class="form-control" style="display: none;">
                    </div>
                    <div class="form-group">
                        <label for="address" class="form-label">Address</label>
                        <p id="address_display"><?php echo nl2br(htmlspecialchars($address)); ?></p>
                        <textarea id="address_input" name="address" class="form-control" style="display: none;"><?php echo htmlspecialchars($address); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="text-center mt-3">
                <button type="button" class="btn btn-primary" id="editButton" onclick="enableEdit()">Edit Profile</button>
                <button type="submit" class="btn btn-hotpink mt-2" id="saveButton" style="display: none;">Save Changes</button>
            </div>
        </form>
    </div>
</div>

    <!-- Change password link -->
    <div class="password-change">
        <h2>Change Password</h2>
        <p>If you want to change your password, click the button below:</p>
        <a href="index.php?page=change_password_form" class="btn btn-secondary">Change Password</a>
    </div>

    <!-- Order history -->
    <div class="order-history">
        <h2>Order History</h2>
        <?php if (empty($orders)): ?>
            <p>No order history.</p>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Total Amount</th>
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
                            <td><a href="index.php?page=order_details&order_id=<?php echo $order['order_id']; ?>" class="btn btn-info btn-sm">View</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Logout -->
    <div class="logout">
        <form action="content/logout.php" method="post">
            <button type="submit" class="btn btn-danger">Log Out</button>
        </form>
    </div>
</div>

<!-- JavaScript to enable editing -->
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


<style>
    /* Yleinen asettelu */
    .profile-page {
        max-width: 900px;
        margin: 0 auto;
        padding: 30px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-label {
        font-weight: bold;
        color: #555;
    }

    .profile-info p {
        font-size: 16px;
    }

    .form-control {
        border-radius: 8px;
        padding: 8px;
    }

    .form-actions button {
        margin-right: 10px;
    }

    .table {
        margin-top: 20px;
    }

    .btn {
        font-size: 16px;
    }

    .password-change, .order-history {
        margin-top: 30px;
    }

    /* Painikkeiden tyylit */
    .btn-primary {
        background-color: #FF66B2;
        border-color: #FF66B2;
    }

    .btn-primary:hover {
        background-color: #FF3385;
        border-color: #FF3385;
    }

    .btn-warning {
        background-color: #FFC107;
        color: white;
    }

    .btn-warning:hover {
        background-color: #FFA000;
    }

    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }

    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }
    .profile-card {
        background: #ffccd5;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        
    }

    .profile-info p {
        font-size: 16px;
    }
</style>
