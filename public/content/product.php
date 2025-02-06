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

    echo "
    <style>

    
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
    width: 250px; /* Kiinteä leveys */
    min-height: 200px; /* Varmistaa tasakokoiset kortit */
    max-height: 250px; /* Varmistaa, että kortti ei kasva liian suureksi */
    text-align: center;
    overflow: hidden;
}

.comment {
    font-size: 14px;
    margin-bottom: 10px;
    max-height: 80px; /* Rajoittaa tekstikentän korkeuden */
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 4; /* Näyttää enintään 4 riviä */
    -webkit-box-orient: vertical;
    word-break: break-word; /* Estää pitkien sanojen ylivenymisen */
}

        .review-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            justify-content: center;
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

        .carousel-inner {
            display: flex;
            overflow: hidden;
        }

        .carousel-track {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }

        .carousel-container {
    position: relative;
    max-width: 800px;
    margin: auto;
    overflow: hidden;
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
    z-index: 10; /* Varmistaa, että nappi ei jää korttien taakse */
}


        .carousel-btn-left {
            left: 0;
        }

        .carousel-btn-right {
            right: 0;
        }
    </style>

    <div class='product-details-page'>
        <img src='$imageUrl' alt='$name' class='product-image img-fluid' style='height: 300px; object-fit: cover;'>
        <h1>$name</h1>
        <p>$description</p>
        <p>Price: $$price</p>
        <p>Stock: $stock_quantity</p>";

    if (isset($_SESSION['SESS_ROLE']) && $_SESSION['SESS_ROLE'] === 'admin') {
        echo "<a href='index.php?page=edit_product&id=" . $productId . "' class='btn btn-warning mt-2'>Edit Product</a>";
    }
    
    if (isset($_SESSION['SESS_USER_ID'])) {
        echo "
        <h3>Leave a Review</h3>
        <form action='content/submit_review.php' method='POST'>
            <input type='hidden' name='product_id' value='$productId'>
            <label for='rating'>Rating (1-5):</label>
            <select name='rating' required>
                <option value='1'>1</option>
                <option value='2'>2</option>
                <option value='3'>3</option>
                <option value='4'>4</option>
                <option value='5'>5</option>
            </select>
            <label for='comment'>Review:</label>
            <textarea name='comment' required></textarea>
            <button type='submit' class='btn btn-primary'>Submit Review</button>
        </form>";
    }
    
    $reviewQuery = "SELECT pr.rating, pr.comment, u.username FROM product_reviews pr 
    JOIN users u ON pr.user_id = u.user_id 
    WHERE pr.product_id = ? ORDER BY pr.created_at DESC";
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

if (count($reviews) > 0) {
echo "<h3>Reviews</h3>
<div class='carousel-container'>";

if (count($reviews) > 3) { // Karuselli näkyy vain jos arvosteluja on yli 2
echo "<button class='carousel-btn carousel-btn-left' onclick='moveSlide(-1)'>&lt;</button>";
}

echo "<div class='carousel-inner'>
<div class='carousel-track'>";

foreach ($reviews as $review) {
echo "<div class='review-card'>
    <div class='review-header'>
        <img src='https://www.gravatar.com/avatar/?d=mp&s=40' alt='Profile' class='profile-icon'>
        <strong>" . htmlspecialchars($review['username']) . "</strong>
    </div>
    <p class='comment'>" . htmlspecialchars($review['comment']) . "</p>
    <p class='rating'>" . str_repeat("★", $review['rating']) . "</p>
</div>";
}

echo "</div>
</div>";

if (count($reviews) > 2) {
echo "<button class='carousel-btn carousel-btn-right' onclick='moveSlide(1)'>&gt;</button>";
}

echo "</div>"; // Suljetaan PHP:n sisältö

?>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    let index = 0;
    const track = document.querySelector('.carousel-track');
    const cards = document.querySelectorAll('.review-card');
    
    if (cards.length <= 2) return; // Jos arvosteluja on vain 1-2, ei karusellia tarvita
    
    const cardsPerSlide = 3; // Kuinka monta korttia näkyy kerralla
    const cardWidth = cards[0].offsetWidth + 20; // Kortin leveys + marginaali
    const maxIndex = Math.ceil(cards.length / cardsPerSlide) - 1; // Maksimi indeksi
    
    function moveSlide(direction) {
        index += direction;
        if (index < 0) index = 0; // Estä siirtyminen negatiiviseen indeksiin
        if (index > maxIndex) index = maxIndex; // Estä liian pitkälle siirtyminen

        track.style.transform = `translateX(${-index * cardWidth * cardsPerSlide}px)`;
    }

    // Lisää event listenerit painikkeille
    document.querySelector(".carousel-btn-left")?.addEventListener("click", () => moveSlide(-1));
    document.querySelector(".carousel-btn-right")?.addEventListener("click", () => moveSlide(1));
});

</script>

<?php
} else {
    echo "<p>No reviews yet.</p>";
}

    echo "</div>";
} else {
    echo "<p>Product not found.</p>";
}

mysqli_stmt_close($stmt);
mysqli_close($link);

?>
