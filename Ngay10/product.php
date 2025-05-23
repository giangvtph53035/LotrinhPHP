<?php
include 'includes/db.php';
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($product) {
        echo "<h2>{$product['name']}</h2>";
        echo "<p>{$product['description']}</p>";
        echo "<p>Giá: {$product['price']} USD</p>";
        echo "<p>Tồn kho: {$product['stock']}</p>";
    } else {
        echo "<p>Sản phẩm không tồn tại.</p>";
    }
}
?>