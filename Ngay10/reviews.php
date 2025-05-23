<?php
include 'includes/db.php';
if (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);
    $stmt = $pdo->prepare("SELECT user_name, rating, comment FROM reviews WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($reviews as $review) {
        echo "<div class='review'>";
        echo "<strong>{$review['user_name']}</strong> (Đánh giá: {$review['rating']}/5)<br>";
        echo "<p>{$review['comment']}</p>";
        echo "</div>";
    }
    if (empty($reviews)) {
        echo "<p>Chưa có đánh giá.</p>";
    }
}
?>