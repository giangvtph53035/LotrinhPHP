<?php
session_start();
header('Content-Type: application/json');
if (isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    if (!isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] = 1;
    } else {
        $_SESSION['cart'][$product_id]++;
    }
    $cartCount = array_sum($_SESSION['cart']);
    echo json_encode(['success' => true, 'cartCount' => $cartCount]);
} else {
    echo json_encode(['success' => false]);
}
?>