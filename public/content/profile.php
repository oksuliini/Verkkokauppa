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

<div class="container mt-5 profile-page">
    <h1 class="text-center mb-4">Profiilisi</h1>

    <!-- Profiilin tiedot -->
    <form id="profileForm" action="content/update_profile.php" method="post" class="profile-form">
        <div class="profile-info">
            <div class="form-group">
                <label for="first_name" class="form-label">Etunimi</label>
                <p id="first_name_display"><?php echo htmlspecialchars($first_name); ?></p>
                <input type="text" id="first_name_input" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" class="form-control" style="display: none;">
            </div>
            <div class="form-group">
                <label for="last_name" class="form-label">Sukunimi</label>
                <p id="last_name_display"><?php echo htmlspecialchars($last_name); ?></p>
                <input type="text" id="last_name_input" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" class="form-control" style="display: none;">
            </div>
            <div class="form-group">
                <label for="email" class="form-label">Sähköposti</label>
                <p id="email_display"><?php echo htmlspecialchars($email); ?></p>
                <input type="email" id="email_input" name="email" value="<?php echo htmlspecialchars($email); ?>" class="form-control" style="display: none;">
            </div>
            <div class="form-group">
                <label for="phone" class="form-label">Puhelinnumero</label>
                <p id="phone_display"><?php echo htmlspecialchars($phone); ?></p>
                <input type="text" id="phone_input" name="phone" value="<?php echo htmlspecialchars($phone); ?>" class="form-control" style="display: none;">
            </div>
            <div class="form-group">
                <label for="address" class="form-label">Osoite</label>
                <p id="address_display"><?php echo nl2br(htmlspecialchars($address)); ?></p>
                <textarea id="address_input" name="address" class="form-control" style="display: none;"><?php echo htmlspecialchars($address); ?></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="button" class="btn btn-primary" id="editButton" onclick="enableEdit()">Muokkaa profiilia</button>
            <button type="submit" class="btn btn-success" id="saveButton" style="display: none;">Tallenna muutokset</button>
        </div>
    </form>

    <!-- Linkki salasanan vaihtoon -->
    <div class="password-change">
        <h2>Vaihda salasana</h2>
        <p>Jos haluat vaihtaa salasanasi, klikkaa alla olevaa painiketta:</p>
        <a href="index.php?page=change_password_form" class="btn btn-warning">Vaihda salasana</a>
    </div>

    <!-- Tilaushistoria -->
    <div class="order-history">
        <h2>Tilaushistoria</h2>
        <?php if (empty($orders)): ?>
            <p>Ei tilaushistoriaa.</p>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Tilauksen ID</th>
                        <th>Kokonais summa</th>
                        <th>Status</th>
                        <th>Toimitustapa</th>
                        <th>Tilauspäivämäärä</th>
                        <th>Yksityiskohdat</th>
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
                            <td><a href="index.php?page=order_details&order_id=<?php echo $order['order_id']; ?>" class="btn btn-info btn-sm">Näytä</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Logout -->
    <div class="logout">
        <form action="content/logout.php" method="post">
            <button type="submit" class="btn btn-danger">Kirjaudu ulos</button>
        </form>
    </div>
</div>

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
</style>
