<?php
session_start();
require_once('../../config/config.php');
if ($_SESSION['SESS_ROLE'] !== 'admin') {
    header("Location: ../../errors/403.php");
    exit();
}
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_DATABASE);
if (!$link) {
    die('Failed to connect to server: ' . mysqli_connect_error());
}
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
    $stock = intval($_POST['stock']);

    $query = "INSERT INTO products (name, description, price, stock) VALUES ('$name', '$description', $price, $stock)";
    if (mysqli_query($link, $query)) {
        echo "<p>Product added successfully!</p>";
    } else {
        echo "<p>Error: " . mysqli_error($link) . "</p>";
    }

    mysqli_close($link);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
</head>
<body>
    <!-- Adminin profiilitiedot -->
    <h1>Welcome, Admin</h1>
    <h2>Your Profile</h2>
    <p>First Name: <?php echo htmlspecialchars($first_name); ?></p>
    <p>Last Name: <?php echo htmlspecialchars($last_name); ?></p>
    <p>Email: <?php echo htmlspecialchars($email); ?></p>

    <h2>Change Your Password</h2>
    <p>If you want to change your password, click the button below:</p>
    <form action="update_profile.php" method="get">
        <button type="submit">Update Password</button>
    </form>

    <form action="logout.php" method="post">
        <button type="submit">Logout</button>
    </form>

    <!-- Tuotehallinnan lomake -->
    <h2>Add a New Product</h2>
    <form action="admin_profile.php" method="POST">
        <label for="name">Product Name:</label>
        <input type="text" id="name" name="name" required><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea><br>

        <label for="price">Price:</label>
        <input type="number" step="0.01" id="price" name="price" required><br>

        <label for="stock">Stock:</label>
        <input type="number" id="stock" name="stock" required><br>

        <button type="submit">Add Product</button>
    </form>
    <?php if (isset($product_message)) echo "<p>$product_message</p>"; ?>
</body>
</html>
