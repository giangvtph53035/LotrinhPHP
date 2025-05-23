<?php
require 'functions.php';
$products = getAllProducts();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách sản phẩm - TechFactory</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        h2 {
            color: #007BFF;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            max-width: 800px;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px 12px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .price {
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <h2>📦 Danh sách sản phẩm</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên sản phẩm</th>
                <th>Giá (VND)</th>
                <th>Số lượng tồn</th>
                <th>Ngày tạo</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><?= htmlspecialchars($p['product_name']) ?></td>
                    <td class="price"><?= number_format($p['unit_price'], 0, ',', '.') ?></td>
                    <td><?= $p['stock_quantity'] ?></td>
                    <td><?= $p['created_at'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>

