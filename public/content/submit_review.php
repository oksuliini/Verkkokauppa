<?php
session_start();
require_once('../../config/config.php');

if (!isset($_SESSION['SESS_USER_ID'])) {
    die("Error: You must be logged in to leave a review.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = intval($_SESSION['SESS_USER_ID']);
    $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

    // Tarkista kelvollisuus
    if ($productId <= 0 || $rating < 1 || $rating > 5 || empty($comment)) {
        die("Error: Invalid input.");
    }

    $link = getDbConnection();
    $stmt = mysqli_prepare($link, "INSERT INTO product_reviews (user_id, product_id, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "iiis", $userId, $productId, $rating, $comment);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Review submitted successfully!'); window.history.back();</script>";
        } else {
            echo "Error: " . mysqli_error($link);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Database error.";
    }

    mysqli_close($link);
} else {
    header("Location: ../index.php");
    exit();
}
?>
