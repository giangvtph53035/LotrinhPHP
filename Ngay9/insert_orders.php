<?php
require 'db.php';

// Lấy danh sách sản phẩm để lấy id
$products = $pdo->query("SELECT id, unit_price FROM products LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
if (count($products) < 3) {
    die("Cần ít nhất 3 sản phẩm để tạo đơn hàng.");
}

// Tạo 3 đơn hàng, mỗi đơn 2-3 sản phẩm
$orders = [
    [
        'customer_name' => 'Nguyễn Văn A',
        'note' => 'Giao buổi sáng',
        'items' => [
            ['product_id' => $products[0]['id'], 'quantity' => 2, 'price' => $products[0]['unit_price']],
            ['product_id' => $products[1]['id'], 'quantity' => 1, 'price' => $products[1]['unit_price']],
        ]
    ],
    [
        'customer_name' => 'Trần Thị B',
        'note' => 'Giao buổi chiều',
        'items' => [
            ['product_id' => $products[1]['id'], 'quantity' => 3, 'price' => $products[1]['unit_price']],
            ['product_id' => $products[2]['id'], 'quantity' => 2, 'price' => $products[2]['unit_price']],
            ['product_id' => $products[0]['id'], 'quantity' => 1, 'price' => $products[0]['unit_price']],
        ]
    ],
    [
        'customer_name' => 'Lê Văn C',
        'note' => 'Giao nhanh',
        'items' => [
            ['product_id' => $products[2]['id'], 'quantity' => 1, 'price' => $products[2]['unit_price']],
            ['product_id' => $products[3]['id'], 'quantity' => 2, 'price' => $products[3]['unit_price']],
        ]
    ],
];

$orderStmt = $pdo->prepare("INSERT INTO orders (order_date, customer_name, note) VALUES (?, ?, ?)");
$itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_order_time) VALUES (?, ?, ?, ?)");

foreach ($orders as $order) {
    $orderStmt->execute([date('Y-m-d'), $order['customer_name'], $order['note']]);
    $orderId = $pdo->lastInsertId();
    foreach ($order['items'] as $item) {
        $itemStmt->execute([$orderId, $item['product_id'], $item['quantity'], $item['price']]);
    }
    echo "Đã thêm đơn hàng cho {$order['customer_name']}<br>";
}

echo "Đã thêm 3 đơn hàng mẫu.";
?>
