<?php
require_once __DIR__ . '/../init.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        $id = intval($_POST['product_id'] ?? 0);
        $qty = max(1, intval($_POST['qty'] ?? 1));
        if (addToCart($id, $qty)) {
            echo json_encode(['success' => true, 'count' => getCartCount(), 'message' => 'เพิ่มลงตะกร้าเรียบร้อย']);
        } else {
            echo json_encode(['success' => false, 'message' => 'ไม่พบสินค้า']);
        }
        break;
    case 'update':
        $id = intval($_POST['product_id'] ?? 0);
        $qty = intval($_POST['qty'] ?? 1);
        updateCartQty($id, $qty);
        echo json_encode(['success' => true, 'count' => getCartCount(), 'total' => getCartTotal()]);
        break;
    case 'remove':
        $id = intval($_POST['product_id'] ?? 0);
        removeFromCart($id);
        echo json_encode(['success' => true, 'count' => getCartCount(), 'total' => getCartTotal()]);
        break;
    case 'coupon':
        $result = applyCoupon($_POST['code'] ?? '');
        echo json_encode($result);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
