<?php
require 'db.php';

// Thêm sản phẩm (Prepared)
function addProduct($name, $price, $stock) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO products (product_name, unit_price, stock_quantity) VALUES (?, ?, ?)");
    $stmt->execute([$name, $price, $stock]);
    return $pdo->lastInsertId();
}

// Lấy danh sách sản phẩm
function getAllProducts() {
    global $pdo;
    return $pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
}

// Lọc theo giá
function getProductsAbovePrice($price) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE unit_price > ?");
    $stmt->execute([$price]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Sắp xếp giá giảm
function getProductsDescPrice() {
    global $pdo;
    return $pdo->query("SELECT * FROM products ORDER BY unit_price DESC")->fetchAll(PDO::FETCH_ASSOC);
}

// Cập nhật sản phẩm
function updateProduct($id, $price, $stock) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE products SET unit_price = ?, stock_quantity = ? WHERE id = ?");
    return $stmt->execute([$price, $stock, $id]);
}

// Xóa sản phẩm
function deleteProduct($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    return $stmt->execute([$id]);
}

// Lấy 5 sản phẩm mới nhất
function getLatestProducts($limit = 5) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products ORDER BY created_at DESC LIMIT ?");
    $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
