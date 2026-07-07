<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
$product_id = isset($_POST['product_id']) ? $_POST['product_id'] : '';
$variant_id = isset($_POST['variant_id']) ? $_POST['variant_id'] : '0';
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
$is_ajax = isset($_POST['ajax']) ? true : false;

// Create a unique cart item key
$cart_key = $product_id . '-' . $variant_id;

if ($action == 'add' && $product_id) {
    if (isset($_SESSION['cart'][$cart_key])) {
        $_SESSION['cart'][$cart_key]['qty'] += $quantity;
    } else {
        $_SESSION['cart'][$cart_key] = [
            'product_id' => $product_id,
            'variant_id' => $variant_id,
            'qty' => $quantity
        ];
    }
}

if ($action == 'remove' && $cart_key) {
    unset($_SESSION['cart'][$cart_key]);
}

if ($action == 'clear') {
    $_SESSION['cart'] = [];
}

if ($is_ajax) {
    // Count total items in cart
    $cart_count = 0;
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['qty'];
    }
    echo json_encode(['success' => true, 'cart_count' => $cart_count]);
    exit;
}

header("location: cart.php");
exit;
