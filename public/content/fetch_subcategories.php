<?php
require_once('../../config/config.php');

if (isset($_GET['category_id'])) {
    $categoryId = intval($_GET['category_id']);

    // Get database connection
    $link = getDbConnection();

    // Fetch subcategories for the selected category
    $query = "SELECT * FROM categories WHERE parent_id = ?";
    if ($stmt = mysqli_prepare($link, $query)) {
        mysqli_stmt_bind_param($stmt, "i", $categoryId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $subcategories = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $subcategories[] = $row; // Add subcategory data
        }

        // Return subcategories as JSON
        echo json_encode($subcategories);
    } else {
        echo json_encode([]);
    }

    mysqli_close($link);
} else {
    echo json_encode([]);
}
?>
