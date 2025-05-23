<?php
require 'db.php';

// Thêm 5 sản phẩm mẫu
$products = [
    ['Động cơ A', 1500000, 20],
    ['Cảm biến B', 800000, 50],
    ['Bảng điều khiển C', 2200000, 15],
    ['Thiết bị đo D', 1250000, 25],
    ['Cảm biến E', 600000, 30],
];

$stmt = $pdo->prepare("INSERT INTO products (product_name, unit_price, stock_quantity) VALUES (?, ?, ?)");
foreach ($products as $p) {
    $stmt->execute($p);
    echo "Đã thêm: {$p[0]} - ID: " . $pdo->lastInsertId() . "<br>";
}
?>
