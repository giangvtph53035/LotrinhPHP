<?php
include 'includes/db.php';
if (isset($_GET['q'])) {
    $query = '%' . $_GET['q'] . '%';
    $stmt = $pdo->prepare("SELECT name, price, image FROM products WHERE name LIKE ?");
    $stmt->execute([$query]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($products as $product) {
        echo "<div class='product'>";
        echo "<img src='{$product['image']}' width='50'>";
        echo "<p>{$product['name']} - {$product['price']} USD</p>";
        echo "</div>";
    }
    if (empty($products)) {
        echo "<p>Không tìm thấy sản phẩm.</p>";
    }
}
?>