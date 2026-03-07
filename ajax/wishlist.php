<?php
require_once __DIR__ . '/../init.php';
header('Content-Type: application/json');
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}

$action = $_POST['action'] ?? '';
$productId = intval($_POST['product_id'] ?? 0);
$userId = $_SESSION['user_id'];

if ($action === 'toggle') {
    if (isInWishlist($userId, $productId)) {
        $pdo->prepare("DELETE FROM wishlists WHERE user_id = ? AND product_id = ?")->execute([$userId, $productId]);
        echo json_encode(['success' => true, 'added' => false, 'message' => 'ลบออกจากรายการโปรดแล้ว']);
    } else {
        $pdo->prepare("INSERT INTO wishlists (user_id, product_id) VALUES (?, ?)")->execute([$userId, $productId]);
        echo json_encode(['success' => true, 'added' => true, 'message' => 'เพิ่มในรายการโปรดแล้ว']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
