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
// Process form to add a product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock_quantity']);

    $query = "INSERT INTO products (name, description, price, stock_quantity) VALUES ('$name', '$description', $price, $stock)";
    if (mysqli_query($link, $query)) {
        echo "<p>Product added successfully!</p>";
    } else {
        echo "<p>Error: " . mysqli_error($link) . "</p>";
    }

    mysqli_close($link);
}
?>

    <!-- Adminin profiilitiedot -->
    <h1>Welcome, Admin</h1>
    <h2>Your Profile</h2>
    <p>First Name: <?php echo htmlspecialchars($first_name); ?></p>
    <p>Last Name: <?php echo htmlspecialchars($last_name); ?></p>
    <p>Email: <?php echo htmlspecialchars($email); ?></p>

    <h2>Change Your Password</h2>
    <p>If you want to change your password, click the button below:</p>
    <form action="index.php?page=update_profile" method="post">
        <button type="submit" class="btn btn-hotpink mt-2">Update Password</button>
    </form>

    <form action="content/logout.php" method="post">
        <button type="submit" class="btn btn-secondary mt-2">Logout</button>
    </form>
    <form action="index.php?page=add_product" method="post">
        <button type="submit" class="btn btn-hotpink mt-2">Add Produts</button>
    </form>

    <?php if (isset($product_message)) echo "<p>$product_message</p>"; ?> 

