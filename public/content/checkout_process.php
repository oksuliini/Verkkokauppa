<?php

session_start();
require_once('../../config/config.php');

// Check if the user is logged in
if (!isset($_SESSION['SESS_USER_ID'])) {
    die("Error: You must be logged in to place an order.");
}

$userId = intval($_SESSION['SESS_USER_ID']);

// Check if the cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: ../index.php?page=cart");
    exit();
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $link = getDbConnection();

    // Get form data and ensure everything is valid
    $deliveryMethod = isset($_POST['delivery_method']) ? mysqli_real_escape_string($link, $_POST['delivery_method']) : '';

    // Check if the delivery method is a valid ENUM value
    $validDeliveryMethods = ['pickup', 'shipping'];
    if (!in_array($deliveryMethod, $validDeliveryMethods)) {
        die("Error: Invalid delivery method ($deliveryMethod)");
    }

    $name = mysqli_real_escape_string($link, $_POST['name']);
    $address = mysqli_real_escape_string($link, $_POST['address']);
    $email = mysqli_real_escape_string($link, $_POST['email']);

    // Begin database transaction
    mysqli_begin_transaction($link);

    try {
        // Calculate the total order price
        $total = 0;
        foreach ($_SESSION['cart'] as $productId => $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Insert the order into the database and associate it with the correct user_id
        $orderQuery = "INSERT INTO orders (`user_id`, `total_price`, `delivery_method`, `created_at`) 
                       VALUES ($userId, $total, '$deliveryMethod', NOW())";

        if (!mysqli_query($link, $orderQuery)) {
            throw new Exception("Could not save the order: " . mysqli_error($link));
        }

        // Get the ID of the created order
        $orderId = mysqli_insert_id($link);

        // Add each product to the order and update the stock
        foreach ($_SESSION['cart'] as $productId => $item) {
            $quantity = $item['quantity'];
            $price = $item['price'];

            // Insert the order items
            $orderItemQuery = "INSERT INTO order_items (`order_id`, `product_id`, `quantity`, `price`) 
                               VALUES ($orderId, $productId, $quantity, $price)";
            if (!mysqli_query($link, $orderItemQuery)) {
                throw new Exception("Failed to add product to the order: " . mysqli_error($link));
            }

            // Update the product stock
            $updateStockQuery = "UPDATE products SET stock_quantity = stock_quantity - $quantity 
                                 WHERE product_id = $productId AND stock_quantity >= $quantity";
            if (!mysqli_query($link, $updateStockQuery)) {
                throw new Exception("Failed to update stock: " . mysqli_error($link));
            }

            // Check if the stock is less than zero
            if (mysqli_affected_rows($link) === 0) {
                throw new Exception("Not enough stock for the product: " . htmlspecialchars($item['name']));
            }
        }

        // If everything succeeded, commit the transaction
        mysqli_commit($link);

        // Clear the cart
        unset($_SESSION['cart']);

        // Display JavaScript alert on success
        echo "<script>alert('Your order has been received! Thank you for your order.'); window.location.href='../index.php?page=home';</script>";
        exit();
    } catch (Exception $e) {
        // If an error occurs, roll back the transaction
        mysqli_rollback($link);
        echo "<p>Error: " . $e->getMessage() . "</p>";
        echo '<a href="../index.php?page=cart">Back to Cart</a>';
    } finally {
        mysqli_close($link);
    }
} else {
    // If the page is accessed without a POST request, redirect the user back to the cart
    header("Location: ../index.php?page=cart");
    exit();
}
