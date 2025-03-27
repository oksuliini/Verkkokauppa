<?php

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Product not found.</p>";
    exit();
}

$productId = intval($_GET['id']);
$link = getDbConnection();
$query = "SELECT * FROM products WHERE product_id = ?";
$stmt = mysqli_prepare($link, $query);

if (!$stmt) {
    die("Database error: " . mysqli_error($link));
}

mysqli_stmt_bind_param($stmt, "i", $productId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $name = htmlspecialchars($row['name']);
    $description = htmlspecialchars($row['description']);
    $price = number_format($row['price'], 2);
    $stock_quantity = $row['stock_quantity'];
    $imageUrl = file_exists($row['image_url']) ? $row['image_url'] : "images/placeholder.png";
    ?>
    <style>
        /* Pääsivun tyylit */
        .product-details-page {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .product-image-container {
            flex: 1;
            max-width: 40%;
            min-width: 300px;
            margin-right: 20px;
        }

        .product-image {
            width: 100%;
            height: auto;
            object-fit: contain;
            margin-bottom: 20px;
        }

        .product-info {
            flex: 2;
            max-width: 55%;
        }

        .product-details-page h1 {
            font-size: 2em;
            color: #333;
        }

        .product-details-page p {
            font-size: 1.2em;
            color: #555;
        }

        .add-to-cart-container {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            max-width: 300px;
        }

        .quantity-label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .quantity-input-group {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 10px;
        }

        .quantity-input {
            width: 60px;
            padding: 5px;
            font-size: 16px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .add-to-cart-btn {
            background-color: #ff4081;
            color: white;
            border: none;
            padding: 10px 15px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
            width: 100%;
        }

        .add-to-cart-btn:hover {
            background-color: #e60073;
            transform: scale(1.05);
        }

        /* Arvostelujen karusellityylit */
        .carousel-container {
            max-width: 800px;
            margin: 20px auto;
            position: relative;
        }

        .carousel-inner {
            display: flex;
            overflow: hidden;
        }

        .carousel-track {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }

        .carousel-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            z-index: 10;
        }

        .carousel-btn-left {
            left: 0;
        }

        .carousel-btn-right {
            right: 0;
        }

        .review-card {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            margin: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 250px;
            min-height: 200px;
            text-align: center;
            overflow: hidden;
        }

        .review-header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }

        .profile-icon {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }

        .rating {
            font-size: 18px;
            color: #ffd700;
            margin-top: 5px;
        }

        .comment {
            font-size: 14px;
            margin-bottom: 10px;
            max-height: 80px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            word-break: break-word;
        }

 
        /* Arvostelu-lomake */
        .review-form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 40px;
        }

        .review-form-container form {
            max-width: 600px;
            width: 100%;
        }

        .review-form-container label {
            display: block;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .review-form-container textarea {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 5px;
            height: 150px;
        }

        .submit-review-btn {
            width: 100%;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        .submit-review-btn:hover {
            background-color: #0056b3;
        }
                /* Star rating styles */
                .star-rating {
        display: flex;
        justify-content: center; /* Keskitetään tähdet */
        direction: rtl; /* Tähdet vasemmalta oikealle */
        margin-bottom: 10px; /* Lisää tilaa tähtien alle */
    }

    .star-rating input {
        display: none;
    }

    .star-rating label {
    cursor: pointer;
    color: #ddd;
    margin-right: 5px;
    font-size: 35px; /* Muutetaan tähtien kokoa suuremmaksi */
    transition: transform 0.3s; /* Pehmeä animaatio suurennukselle */
}

    .star-rating input:checked ~ label {
        color: #ffd700;
    }

    .star-rating label:hover,
    .star-rating label:hover ~ label {
        color: #ffd700;
        transform: scale(1.2); /* Suurennetaan tähtiä hoverilla */
    }
    </style>

    <div class='product-details-page'>
        <div class="product-image-container">
            <img src='<?= $imageUrl ?>' alt='<?= $name ?>' class='product-image'>
        </div>

        <div class="product-info">
            <h1><?= $name ?></h1>
            <p><?= $description ?></p>
            <p>Price: $<?= $price ?></p>
            <p>Stock: <?= $stock_quantity ?></p>

            <!-- Ostoskoriin lisääminen -->
            <?php if ($stock_quantity > 0): ?>
                <div class="add-to-cart-container">
                    <form action='content/cart_add.php' method='post'>
                        <input type='hidden' name='product_id' value='<?= $productId ?>'>
                        <input type='hidden' name='name' value='<?= $name ?>'>
                        <input type='hidden' name='price' value='<?= $row['price'] ?>'>
                        <label for='quantity_<?= $productId ?>' class='quantity-label'>Quantity:</label>
                        <div class='quantity-input-group'>
                            <input type='number' id='quantity_<?= $productId ?>' name='quantity' value='1' min='1' max='<?= $stock_quantity ?>' class='quantity-input'>
                        </div>
                        <button type='submit' class='btn add-to-cart-btn'>🛒 Add to Cart</button>
                    </form>
                </div>
            <?php else: ?>
                <p class="text-danger"><strong>Out of Stock</strong></p>
            <?php endif; ?>
        </div>
    </div>
<?php if (isset($_SESSION['SESS_ROLE']) && $_SESSION['SESS_ROLE'] === 'admin') {
        echo "<a href='index.php?page=edit_product&id=" . $productId . "' class='btn btn-warning mt-2'>Edit Product</a>";
    }?>
    <!-- Arvostelu ja arvostelulomake -->
    <?php if (isset($_SESSION['SESS_USER_ID'])): ?>
    <h3 class="text-center">Leave a Review</h3>
    <div class="review-form-container">
        <form action='content/submit_review.php' method='POST' id="reviewForm">
            <input type='hidden' name='product_id' value='<?= $productId ?>'>
            <label for='rating'>Rating:</label>
            <div class='star-rating'>
                <input type='radio' name='rating' value='1' id='star1'><label for='star1'>&#9733;</label>
                <input type='radio' name='rating' value='2' id='star2'><label for='star2'>&#9733;</label>
                <input type='radio' name='rating' value='3' id='star3'><label for='star3'>&#9733;</label>
                <input type='radio' name='rating' value='4' id='star4'><label for='star4'>&#9733;</label>
                <input type='radio' name='rating' value='5' id='star5'><label for='star5'>&#9733;</label>
            </div>
            <label for='comment'>Your Review:</label>
            <textarea name='comment' id='comment' class='review-textarea' placeholder="The character limit is 200" required></textarea>
            <button type='submit' class='btn submit-review-btn'>Submit Review</button>
        </form>
    </div>

    <script>
        document.getElementById("reviewForm").addEventListener("submit", function(event) {
            const ratingSelected = document.querySelector('input[name="rating"]:checked');
            if (!ratingSelected) {
                alert("Please select a star rating before submitting your review.");
                event.preventDefault(); // Estetään lomakkeen lähettäminen
            }
        });
    </script>
<?php endif; ?>


    <!-- Arvostelut -->
    <?php
    $reviewQuery = "SELECT pr.rating, pr.comment, u.username FROM product_reviews pr JOIN users u ON pr.user_id = u.user_id WHERE pr.product_id = ? ORDER BY pr.created_at DESC";
    $reviewStmt = mysqli_prepare($link, $reviewQuery);
    if (!$reviewStmt) {
        die("Database error: " . mysqli_error($link));
    }

    mysqli_stmt_bind_param($reviewStmt, "i", $productId);
    mysqli_stmt_execute($reviewStmt);
    $reviewResult = mysqli_stmt_get_result($reviewStmt);

    $reviews = [];
    while ($review = mysqli_fetch_assoc($reviewResult)) {
        $reviews[] = $review;
    }

    if (count($reviews) > 0): ?>
        <h3>Reviews</h3>
        <div class="carousel-container">
            <div class="carousel-inner">
                <div class="carousel-track">
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-card">
                            <div class="review-header">
                                <img src="https://www.gravatar.com/avatar/?d=mp&s=40" alt="Profile" class="profile-icon">
                                <strong><?= htmlspecialchars($review['username']) ?></strong>
                            </div>
                            <p class="comment"><?= htmlspecialchars($review['comment']) ?></p>
                            <p class="rating"><?= str_repeat("★", $review['rating']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <button class="carousel-btn carousel-btn-left">&#10094;</button>
            <button class="carousel-btn carousel-btn-right">&#10095;</button>
        </div>
    <?php else: ?>
        <p>No reviews yet.</p>
    <?php endif; ?>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let index = 0;
        const track = document.querySelector('.carousel-track');
        const cards = document.querySelectorAll('.review-card');

        if (cards.length <= 3) return;

        const cardsPerSlide = 3;
        const cardWidth = cards[0].offsetWidth + 20;
        const maxIndex = Math.ceil(cards.length / cardsPerSlide) - 1;

        function moveSlide(direction) {
            index += direction;
            if (index < 0) index = 0;
            if (index > maxIndex) index = maxIndex;

            track.style.transform = 'translateX(' + (-index * cardWidth * cardsPerSlide) + 'px)';
        }

        document.querySelector(".carousel-btn-left").addEventListener("click", () => moveSlide(-1));
        document.querySelector(".carousel-btn-right").addEventListener("click", () => moveSlide(1));
    });
</script>
<?php
} else {
    echo "<p>Product not found.</p>";
}

mysqli_stmt_close($stmt);
mysqli_close($link);
?>
